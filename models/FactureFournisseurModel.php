<?php
class FactureFournisseurModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT f.*, four.nom AS fournisseur_nom, b.reference AS commande_ref
                FROM approvisionnement.facture_fournisseur f
                JOIN structure.fournisseur four ON f.id_fournisseur = four.id_fournisseur
                LEFT JOIN approvisionnement.bon_commande_fourn b ON f.id_bcf = b.id_bcf
                WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND f.statut = ?";
            $params[] = $filters['statut'];
        }
        if (!empty($filters['id_fournisseur'])) {
            $sql .= " AND f.id_fournisseur = ?";
            $params[] = $filters['id_fournisseur'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (f.numero_facture ILIKE ? OR four.nom ILIKE ? OR f.reference ILIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY f.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT f.*, four.nom AS fournisseur_nom, b.reference AS commande_ref
                FROM approvisionnement.facture_fournisseur f
                JOIN structure.fournisseur four ON f.id_fournisseur = four.id_fournisseur
                LEFT JOIN approvisionnement.bon_commande_fourn b ON f.id_bcf = b.id_bcf
                WHERE f.id_facture_f = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getLignes($idFactureF) {
        $stmt = $this->pdo->prepare("SELECT l.*, p.nom_produit, p.unite
                FROM approvisionnement.ligne_facture_fourn l
                JOIN structure.produit p ON l.id_produit = p.id_produit
                WHERE l.id_facture_f = ?
                ORDER BY l.id_ligne_ff");
        $stmt->execute([$idFactureF]);
        return $stmt->fetchAll();
    }

    public function create($idFournisseur, $idBcf, $numeroFacture, $dateFacture, $montantHt, $tauxTva, $dateEcheance, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $reference = generateReference($this->pdo, 'FF', 'approvisionnement.facture_fournisseur');
            $montantTtc = $montantHt * (1 + $tauxTva / 100);

            $stmt = $this->pdo->prepare("INSERT INTO approvisionnement.facture_fournisseur
                (id_fournisseur, id_bcf, numero_facture, date_facture, montant_ht, taux_tva, montant_ttc, reference, statut, date_echeance)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'impayee', ?)
                RETURNING id_facture_f");
            $stmt->execute([$idFournisseur, $idBcf ?: null, $numeroFacture, $dateFacture, $montantHt, $tauxTva, $montantTtc, $reference, $dateEcheance ?: null]);
            $idFactureF = $stmt->fetchColumn();

            $stmtLigne = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_facture_fourn
                (id_facture_f, id_produit, quantite, prix_unitaire)
                VALUES (?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $idFactureF,
                    $ligne['id_produit'],
                    $ligne['quantite'],
                    $ligne['prix_unitaire']
                ]);
            }

            $this->pdo->commit();
            return $idFactureF;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update($idFactureF, $idFournisseur, $idBcf, $numeroFacture, $dateFacture, $montantHt, $tauxTva, $dateEcheance, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $montantTtc = $montantHt * (1 + $tauxTva / 100);

            $this->pdo->prepare("UPDATE approvisionnement.facture_fournisseur
                SET id_fournisseur = ?, id_bcf = ?, numero_facture = ?, date_facture = ?,
                    montant_ht = ?, taux_tva = ?, montant_ttc = ?, date_echeance = ?, date_modif = CURRENT_TIMESTAMP
                WHERE id_facture_f = ?")
                ->execute([$idFournisseur, $idBcf ?: null, $numeroFacture, $dateFacture, $montantHt, $tauxTva, $montantTtc, $dateEcheance ?: null, $idFactureF]);

            $this->pdo->prepare("DELETE FROM approvisionnement.ligne_facture_fourn WHERE id_facture_f = ?")
                ->execute([$idFactureF]);

            $stmtLigne = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_facture_fourn
                (id_facture_f, id_produit, quantite, prix_unitaire)
                VALUES (?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $idFactureF,
                    $ligne['id_produit'],
                    $ligne['quantite'],
                    $ligne['prix_unitaire']
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete($idFactureF) {
        $stmt = $this->pdo->prepare("DELETE FROM approvisionnement.facture_fournisseur WHERE id_facture_f = ?");
        return $stmt->execute([$idFactureF]);
    }
}
