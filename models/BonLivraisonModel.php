<?php
class BonLivraisonModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT bl.*, cc.reference AS cc_reference, c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.bon_livraison bl
                JOIN vente.commande_client cc ON bl.id_cc = cc.id_cc
                JOIN structure.client c ON cc.id_client = c.id_client
                WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND bl.statut = ?";
            $params[] = $filters['statut'];
        }

        $sql .= " ORDER BY bl.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT bl.*, cc.reference AS cc_reference, c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.bon_livraison bl
                JOIN vente.commande_client cc ON bl.id_cc = cc.id_cc
                JOIN structure.client c ON cc.id_client = c.id_client
                WHERE bl.id_bl = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getLignes($idBl) {
        $stmt = $this->pdo->prepare("SELECT ll.*, p.nom_produit, p.unite
                FROM vente.ligne_livraison ll
                JOIN structure.produit p ON ll.id_produit = p.id_produit
                WHERE ll.id_bl = ?
                ORDER BY ll.id_ll");
        $stmt->execute([$idBl]);
        return $stmt->fetchAll();
    }

    /**
     * Retourne pour chaque produit de la commande : quantité commandée, déjà livrée, et restante
     */
    public function getLignesAvecRestant($idCc) {
        $stmt = $this->pdo->prepare("
            SELECT lcc.id_produit, p.nom_produit, p.unite, p.stock_actuel,
                   lcc.quantite AS qte_commandee,
                   COALESCE(SUM(ll.qte_livree), 0) AS qte_livree,
                   lcc.quantite - COALESCE(SUM(ll.qte_livree), 0) AS qte_restante
            FROM vente.ligne_commande_client lcc
            JOIN structure.produit p ON p.id_produit = lcc.id_produit
            LEFT JOIN vente.bon_livraison bl ON bl.id_cc = lcc.id_cc
            LEFT JOIN vente.ligne_livraison ll ON ll.id_bl = bl.id_bl AND ll.id_produit = lcc.id_produit
            WHERE lcc.id_cc = ?
            GROUP BY lcc.id_produit, p.nom_produit, p.unite, p.stock_actuel, lcc.quantite
            ORDER BY p.nom_produit");
        $stmt->execute([$idCc]);
        return $stmt->fetchAll();
    }

    public function create($idCc, $idUtilisateur, $observations, $lignes, $livraisonComplete) {
        $this->pdo->beginTransaction();
        try {
            $reference = generateReference($this->pdo, 'BL', 'vente.bon_livraison');
            $statut = $livraisonComplete ? 'livre' : 'partiel';

            $stmt = $this->pdo->prepare("INSERT INTO vente.bon_livraison
                (id_cc, id_utilisateur, reference, statut, observations)
                VALUES (?, ?, ?, ?, ?)
                RETURNING id_bl");
            $stmt->execute([$idCc, $idUtilisateur, $reference, $statut, $observations]);
            $idBl = $stmt->fetchColumn();

            $stmtLigne = $this->pdo->prepare("INSERT INTO vente.ligne_livraison
                (id_bl, id_produit, qte_livree, observations)
                VALUES (?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                if ($ligne['qte_livree'] <= 0) continue;
                $stmtLigne->execute([
                    $idBl,
                    $ligne['id_produit'],
                    $ligne['qte_livree'],
                    $ligne['observations'] ?? null
                ]);
            }

            // Mise à jour du statut de la commande
            if ($livraisonComplete) {
                $this->pdo->prepare("UPDATE vente.commande_client SET statut = 'livree', date_modif = CURRENT_TIMESTAMP WHERE id_cc = ?")
                    ->execute([$idCc]);
            }

            $this->pdo->commit();
            return $idBl;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function annuler($idBl) {
        $stmt = $this->pdo->prepare("UPDATE vente.bon_livraison SET statut = 'annule' WHERE id_bl = ?");
        return $stmt->execute([$idBl]);
    }
}
