<?php
class ReceptionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT r.*, b.reference AS commande_ref, f.nom AS fournisseur_nom, u.nom_complet AS utilisateur_nom
                FROM approvisionnement.bon_reception r
                LEFT JOIN approvisionnement.bon_commande_fourn b ON r.id_bcf = b.id_bcf
                LEFT JOIN structure.fournisseur f ON b.id_fournisseur = f.id_fournisseur
                JOIN utilisateur.utilisateur u ON r.id_utilisateur = u.id_utilisateur
                WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND r.statut = ?";
            $params[] = $filters['statut'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (r.reference ILIKE ? OR f.nom ILIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY r.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT r.*, b.reference AS commande_ref, f.nom AS fournisseur_nom
                FROM approvisionnement.bon_reception r
                LEFT JOIN approvisionnement.bon_commande_fourn b ON r.id_bcf = b.id_bcf
                LEFT JOIN structure.fournisseur f ON b.id_fournisseur = f.id_fournisseur
                WHERE r.id_br = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getLignes($idBr) {
        $stmt = $this->pdo->prepare("SELECT l.*, p.nom_produit, p.unite
                FROM approvisionnement.ligne_reception l
                JOIN structure.produit p ON l.id_produit = p.id_produit
                WHERE l.id_br = ?
                ORDER BY l.id_lr");
        $stmt->execute([$idBr]);
        return $stmt->fetchAll();
    }

    public function getQteDejaRecue($idBcf, $idProduit) {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(lr.qte_recue), 0)
            FROM approvisionnement.ligne_reception lr
            JOIN approvisionnement.bon_reception br ON lr.id_br = br.id_br
            WHERE br.id_bcf = ? AND lr.id_produit = ?
        ");
        $stmt->execute([$idBcf, $idProduit]);
        return (float) $stmt->fetchColumn();
    }

    public function getLignesCommande($idBcf) {
        $stmt = $this->pdo->prepare("SELECT id_produit, qte_commandee FROM approvisionnement.ligne_commande_fourn WHERE id_bcf = ?");
        $stmt->execute([$idBcf]);
        return $stmt->fetchAll();
    }

    public function create($idBcf, $idUtilisateur, $observations, $lignes) {
        $this->pdo->beginTransaction();
        try {
            if ($idBcf) {
                $lignesCommande = $this->getLignesCommande($idBcf);
                $commandeQtes = [];
                foreach ($lignesCommande as $lc) {
                    $commandeQtes[$lc['id_produit']] = (float) $lc['qte_commandee'];
                }
                foreach ($lignes as $ligne) {
                    $idProd = $ligne['id_produit'];
                    if (isset($commandeQtes[$idProd])) {
                        $dejaRecu = $this->getQteDejaRecue($idBcf, $idProd);
                        $qteRecue = (float) $ligne['qte_recue'];
                        if ($qteRecue + $dejaRecu > $commandeQtes[$idProd]) {
                            throw new Exception("Quantité reçue dépasse la quantité commandée (commande #$idBcf, produit #$idProd, max " . ($commandeQtes[$idProd] - $dejaRecu) . ")");
                        }
                    }
                }
            }

            $reference = generateReference($this->pdo, 'BR', 'approvisionnement.bon_reception');

            $stmt = $this->pdo->prepare("INSERT INTO approvisionnement.bon_reception
                (id_bcf, id_utilisateur, reference, observations, statut)
                VALUES (?, ?, ?, ?, 'en_attente')
                RETURNING id_br");
            $stmt->execute([$idBcf ?: null, $idUtilisateur, $reference, $observations]);
            $idBr = $stmt->fetchColumn();

            $stmtLigne = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_reception
                (id_br, id_produit, qte_recue, prix_unitaire, etat_produit, observations)
                VALUES (?, ?, ?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $idBr,
                    $ligne['id_produit'],
                    $ligne['qte_recue'],
                    $ligne['prix_unitaire'],
                    $ligne['etat_produit'] ?? 'bon',
                    $ligne['observations'] ?? ''
                ]);
            }

            $this->pdo->commit();
            return $idBr;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function valider($idBr) {
        $this->pdo->beginTransaction();
        try {
            // Vérifier que la réception existe et est en attente
            $br = $this->find($idBr);
            if (!$br || $br['statut'] !== 'en_attente') {
                throw new Exception('Réception introuvable ou déjà traitée.');
            }

            $lignes = $this->getLignes($idBr);

            // Créer le bon d'entrée
            $refBonEntree = generateReference($this->pdo, 'BE', 'approvisionnement.bon_entree');
            $stmtBe = $this->pdo->prepare("INSERT INTO approvisionnement.bon_entree
                (id_br, id_utilisateur, reference, type_source, observations)
                VALUES (?, ?, ?, 'achat', 'Généré depuis la réception')
                RETURNING id_be");
            $stmtBe->execute([$idBr, $br['id_utilisateur'], $refBonEntree]);
            $idBe = $stmtBe->fetchColumn();

            $stmtLbe = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_bon_entree
                (id_be, id_produit, quantite, prix_unitaire)
                VALUES (?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLbe->execute([
                    $idBe,
                    $ligne['id_produit'],
                    $ligne['qte_recue'],
                    $ligne['prix_unitaire']
                ]);
            }

            // Déterminer le statut de la réception
            $nouveauStatut = 'complet';
            if ($br['id_bcf']) {
                $lignesCommande = $this->getLignesCommande($br['id_bcf']);
                $allReceived = true;
                foreach ($lignesCommande as $lc) {
                    $dejaRecu = $this->getQteDejaRecue($br['id_bcf'], $lc['id_produit']);
                    if ($dejaRecu < (float) $lc['qte_commandee']) {
                        $allReceived = false;
                        break;
                    }
                }
                $nouveauStatut = $allReceived ? 'complet' : 'partiel';

                // Si tout reçu, marquer le BCF comme réceptionné
                if ($allReceived) {
                    $this->pdo->prepare("UPDATE approvisionnement.bon_commande_fourn SET statut = 'receptionne', date_modif = CURRENT_TIMESTAMP WHERE id_bcf = ?")
                        ->execute([$br['id_bcf']]);
                }
            }

            $this->pdo->prepare("UPDATE approvisionnement.bon_reception SET statut = ? WHERE id_br = ?")
                ->execute([$nouveauStatut, $idBr]);

            $this->pdo->commit();
            return $idBe;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getCommandesDisponibles() {
        $stmt = $this->pdo->prepare("SELECT b.id_bcf, b.reference, f.nom AS fournisseur_nom
                FROM approvisionnement.bon_commande_fourn b
                JOIN structure.fournisseur f ON b.id_fournisseur = f.id_fournisseur
                WHERE b.statut IN ('envoye', 'receptionne')
                ORDER BY b.date_creation DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
