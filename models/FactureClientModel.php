<?php
class FactureClientModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT f.*, cc.reference AS cc_reference, c.nom AS client_nom, c.prenom AS client_prenom,
                       COALESCE(SUM(r.montant), 0) AS montant_regle
                FROM vente.facture_client f
                JOIN vente.commande_client cc ON f.id_cc = cc.id_cc
                JOIN structure.client c ON cc.id_client = c.id_client
                LEFT JOIN vente.reglement_client r ON r.id_facture = f.id_facture
                WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND f.statut = ?";
            $params[] = $filters['statut'];
        }

        $sql .= " GROUP BY f.id_facture, cc.reference, c.nom, c.prenom
                  ORDER BY f.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT f.*, cc.reference AS cc_reference, cc.observations AS cc_observations,
                       c.nom AS client_nom, c.prenom AS client_prenom, c.adresse, c.ville, c.tel, c.email
                FROM vente.facture_client f
                JOIN vente.commande_client cc ON f.id_cc = cc.id_cc
                JOIN structure.client c ON cc.id_client = c.id_client
                WHERE f.id_facture = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByCc($idCc) {
        $stmt = $this->pdo->prepare("SELECT * FROM vente.facture_client WHERE id_cc = ?");
        $stmt->execute([$idCc]);
        return $stmt->fetch();
    }

    public function getLignes($idCc) {
        $stmt = $this->pdo->prepare("SELECT lcc.*, p.nom_produit, p.unite
                FROM vente.ligne_commande_client lcc
                JOIN structure.produit p ON lcc.id_produit = p.id_produit
                WHERE lcc.id_cc = ?
                ORDER BY lcc.id_lcc");
        $stmt->execute([$idCc]);
        return $stmt->fetchAll();
    }

    public function create($idCc, $idUtilisateur, $tauxTva, $dateEcheance) {
        // Vérifier qu'aucune facture n'existe déjà pour cette commande
        if ($this->findByCc($idCc)) {
            throw new Exception('Une facture existe déjà pour cette commande.');
        }

        $this->pdo->beginTransaction();
        try {
            $stmtCc = $this->pdo->prepare("SELECT montant_total FROM vente.commande_client WHERE id_cc = ?");
            $stmtCc->execute([$idCc]);
            $montantHt = $stmtCc->fetchColumn();

            if ($montantHt === false) {
                throw new Exception('Commande introuvable.');
            }

            $montantTtc = round($montantHt * (1 + $tauxTva / 100), 2);
            $reference = generateReference($this->pdo, 'FACT', 'vente.facture_client');

            $stmt = $this->pdo->prepare("INSERT INTO vente.facture_client
                (id_cc, id_utilisateur, reference, montant_ht, taux_tva, montant_ttc, statut, date_echeance)
                VALUES (?, ?, ?, ?, ?, ?, 'impayee', ?)
                RETURNING id_facture");
            $stmt->execute([$idCc, $idUtilisateur, $reference, $montantHt, $tauxTva, $montantTtc, $dateEcheance ?: null]);
            $idFacture = $stmt->fetchColumn();

            $this->pdo->prepare("UPDATE vente.commande_client SET statut = 'facturee', date_modif = CURRENT_TIMESTAMP WHERE id_cc = ?")
                ->execute([$idCc]);

            $this->pdo->commit();
            return $idFacture;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function annuler($idFacture) {
        $stmt = $this->pdo->prepare("UPDATE vente.facture_client SET statut = 'annulee', date_modif = CURRENT_TIMESTAMP WHERE id_facture = ?");
        return $stmt->execute([$idFacture]);
    }

    public function updateStatutFromReglements($idFacture) {
        $stmt = $this->pdo->prepare("
            SELECT f.montant_ttc, COALESCE(SUM(r.montant), 0) AS total_regle
            FROM vente.facture_client f
            LEFT JOIN vente.reglement_client r ON r.id_facture = f.id_facture
            WHERE f.id_facture = ?
            GROUP BY f.montant_ttc");
        $stmt->execute([$idFacture]);
        $row = $stmt->fetch();

        if (!$row) return;

        $statut = 'impayee';
        if ($row['total_regle'] >= $row['montant_ttc']) {
            $statut = 'payee';
        } elseif ($row['total_regle'] > 0) {
            $statut = 'partielle';
        }

        $this->pdo->prepare("UPDATE vente.facture_client SET statut = ?, date_modif = CURRENT_TIMESTAMP WHERE id_facture = ?")
            ->execute([$statut, $idFacture]);

        // Si payée, marquer la commande comme réglée
        if ($statut === 'payee') {
            $this->pdo->prepare("UPDATE vente.commande_client SET statut = 'reglee', date_modif = CURRENT_TIMESTAMP
                WHERE id_cc = (SELECT id_cc FROM vente.facture_client WHERE id_facture = ?)")
                ->execute([$idFacture]);
        }
    }
}
