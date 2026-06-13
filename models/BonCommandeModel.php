<?php
class BonCommandeModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT b.*, f.nom AS fournisseur_nom, u.nom_complet AS utilisateur_nom
                FROM approvisionnement.bon_commande_fourn b
                JOIN structure.fournisseur f ON b.id_fournisseur = f.id_fournisseur
                JOIN utilisateur.utilisateur u ON b.id_utilisateur = u.id_utilisateur
                WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND b.statut = ?";
            $params[] = $filters['statut'];
        }
        if (!empty($filters['id_fournisseur'])) {
            $sql .= " AND b.id_fournisseur = ?";
            $params[] = $filters['id_fournisseur'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (b.reference ILIKE ? OR f.nom ILIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY b.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT b.*, f.nom AS fournisseur_nom
                FROM approvisionnement.bon_commande_fourn b
                JOIN structure.fournisseur f ON b.id_fournisseur = f.id_fournisseur
                WHERE b.id_bcf = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getLignes($idBcf) {
        $stmt = $this->pdo->prepare("SELECT l.*, p.nom_produit, p.unite, p.prix_achat
                FROM approvisionnement.ligne_commande_fourn l
                JOIN structure.produit p ON l.id_produit = p.id_produit
                WHERE l.id_bcf = ?
                ORDER BY l.id_lcf");
        $stmt->execute([$idBcf]);
        return $stmt->fetchAll();
    }

    public function create($idFournisseur, $idUtilisateur, $observations, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $reference = generateReference($this->pdo, 'BCF', 'approvisionnement.bon_commande_fourn');

            $stmt = $this->pdo->prepare("INSERT INTO approvisionnement.bon_commande_fourn
                (id_fournisseur, id_utilisateur, reference, observations, statut)
                VALUES (?, ?, ?, ?, 'brouillon')
                RETURNING id_bcf");
            $stmt->execute([$idFournisseur, $idUtilisateur, $reference, $observations]);
            $idBcf = $stmt->fetchColumn();

            $montantTotal = 0;
            $stmtLigne = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_commande_fourn
                (id_bcf, id_produit, qte_commandee, prix_unitaire)
                VALUES (?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $idBcf,
                    $ligne['id_produit'],
                    $ligne['qte_commandee'],
                    $ligne['prix_unitaire']
                ]);
                $montantTotal += $ligne['qte_commandee'] * $ligne['prix_unitaire'];
            }

            $this->pdo->prepare("UPDATE approvisionnement.bon_commande_fourn SET montant_total = ? WHERE id_bcf = ?")
                ->execute([$montantTotal, $idBcf]);

            $this->pdo->commit();
            return $idBcf;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update($idBcf, $idFournisseur, $observations, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("UPDATE approvisionnement.bon_commande_fourn
                SET id_fournisseur = ?, observations = ?, date_modif = CURRENT_TIMESTAMP
                WHERE id_bcf = ?")
                ->execute([$idFournisseur, $observations, $idBcf]);

            $this->pdo->prepare("DELETE FROM approvisionnement.ligne_commande_fourn WHERE id_bcf = ?")
                ->execute([$idBcf]);

            $montantTotal = 0;
            $stmtLigne = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_commande_fourn
                (id_bcf, id_produit, qte_commandee, prix_unitaire)
                VALUES (?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $idBcf,
                    $ligne['id_produit'],
                    $ligne['qte_commandee'],
                    $ligne['prix_unitaire']
                ]);
                $montantTotal += $ligne['qte_commandee'] * $ligne['prix_unitaire'];
            }

            $this->pdo->prepare("UPDATE approvisionnement.bon_commande_fourn SET montant_total = ? WHERE id_bcf = ?")
                ->execute([$montantTotal, $idBcf]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function valider($idBcf) {
        $stmt = $this->pdo->prepare("UPDATE approvisionnement.bon_commande_fourn SET statut = 'envoye', date_modif = CURRENT_TIMESTAMP WHERE id_bcf = ? AND statut = 'brouillon'");
        $stmt->execute([$idBcf]);
        return $stmt->rowCount() > 0;
    }

    public function annuler($idBcf) {
        $stmt = $this->pdo->prepare("UPDATE approvisionnement.bon_commande_fourn SET statut = 'annule', date_modif = CURRENT_TIMESTAMP WHERE id_bcf = ? AND statut != 'annule'");
        return $stmt->execute([$idBcf]);
    }

    public function delete($idBcf) {
        $stmt = $this->pdo->prepare("DELETE FROM approvisionnement.bon_commande_fourn WHERE id_bcf = ?");
        return $stmt->execute([$idBcf]);
    }
}
