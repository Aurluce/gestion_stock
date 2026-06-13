<?php
class PaiementFournisseurModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT p.*, f.nom AS fournisseur_nom, fact.numero_facture, fact.reference AS facture_ref, u.nom_complet AS utilisateur_nom
                FROM approvisionnement.paiement_fournisseur p
                JOIN structure.fournisseur f ON p.id_fournisseur = f.id_fournisseur
                JOIN approvisionnement.facture_fournisseur fact ON p.id_facture_f = fact.id_facture_f
                JOIN utilisateur.utilisateur u ON p.id_utilisateur = u.id_utilisateur
                WHERE 1=1";
        $params = [];

        if (!empty($filters['id_fournisseur'])) {
            $sql .= " AND p.id_fournisseur = ?";
            $params[] = $filters['id_fournisseur'];
        }
        if (!empty($filters['id_facture_f'])) {
            $sql .= " AND p.id_facture_f = ?";
            $params[] = $filters['id_facture_f'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.reference ILIKE ? OR f.nom ILIKE ? OR fact.numero_facture ILIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY p.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT p.*, f.nom AS fournisseur_nom, fact.numero_facture, fact.montant_ttc, fact.reference AS facture_ref
                FROM approvisionnement.paiement_fournisseur p
                JOIN structure.fournisseur f ON p.id_fournisseur = f.id_fournisseur
                JOIN approvisionnement.facture_fournisseur fact ON p.id_facture_f = fact.id_facture_f
                WHERE p.id_paiement = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($idFournisseur, $idFactureF, $idUtilisateur, $montant, $datePaiement, $modePaiement, $observations) {
        $this->pdo->beginTransaction();
        try {
            $reference = generateReference($this->pdo, 'PF', 'approvisionnement.paiement_fournisseur');

            $stmt = $this->pdo->prepare("INSERT INTO approvisionnement.paiement_fournisseur
                (id_fournisseur, id_facture_f, id_utilisateur, montant, date_paiement, mode_paiement, reference, observations)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                RETURNING id_paiement");
            $stmt->execute([$idFournisseur, $idFactureF, $idUtilisateur, $montant, $datePaiement, $modePaiement, $reference, $observations]);
            $idPaiement = $stmt->fetchColumn();

            $totalPaye = $this->pdo->prepare("SELECT COALESCE(SUM(montant), 0) FROM approvisionnement.paiement_fournisseur WHERE id_facture_f = ?");
            $totalPaye->execute([$idFactureF]);
            $paye = $totalPaye->fetchColumn();

            $facture = $this->pdo->prepare("SELECT montant_ttc FROM approvisionnement.facture_fournisseur WHERE id_facture_f = ?");
            $facture->execute([$idFactureF]);
            $montantTtc = $facture->fetchColumn();

            if ($paye >= $montantTtc) {
                $this->pdo->prepare("UPDATE approvisionnement.facture_fournisseur SET statut = 'payee', date_modif = CURRENT_TIMESTAMP WHERE id_facture_f = ?")
                    ->execute([$idFactureF]);
            } else {
                $this->pdo->prepare("UPDATE approvisionnement.facture_fournisseur SET statut = 'partielle', date_modif = CURRENT_TIMESTAMP WHERE id_facture_f = ? AND statut = 'impayee'")
                    ->execute([$idFactureF]);
            }

            $this->pdo->commit();
            return $idPaiement;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete($idPaiement) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("SELECT id_facture_f FROM approvisionnement.paiement_fournisseur WHERE id_paiement = ?");
            $stmt->execute([$idPaiement]);
            $idFactureF = $stmt->fetchColumn();

            $this->pdo->prepare("DELETE FROM approvisionnement.paiement_fournisseur WHERE id_paiement = ?")
                ->execute([$idPaiement]);

            $totalPaye = $this->pdo->prepare("SELECT COALESCE(SUM(montant), 0) FROM approvisionnement.paiement_fournisseur WHERE id_facture_f = ?");
            $totalPaye->execute([$idFactureF]);
            $paye = $totalPaye->fetchColumn();

            if ($paye <= 0) {
                $this->pdo->prepare("UPDATE approvisionnement.facture_fournisseur SET statut = 'impayee', date_modif = CURRENT_TIMESTAMP WHERE id_facture_f = ?")
                    ->execute([$idFactureF]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getFacturesImpayees() {
        $stmt = $this->pdo->prepare("SELECT f.*, four.nom AS fournisseur_nom
                FROM approvisionnement.facture_fournisseur f
                JOIN structure.fournisseur four ON f.id_fournisseur = four.id_fournisseur
                WHERE f.statut IN ('impayee', 'partielle')
                ORDER BY f.date_echeance ASC, f.date_creation DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
