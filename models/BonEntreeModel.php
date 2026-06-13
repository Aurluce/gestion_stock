<?php
class BonEntreeModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT e.*, u.nom_complet AS utilisateur_nom,
                       b.reference AS commande_ref, d.donateur
                FROM approvisionnement.bon_entree e
                JOIN utilisateur.utilisateur u ON e.id_utilisateur = u.id_utilisateur
                LEFT JOIN approvisionnement.bon_reception r ON e.id_br = r.id_br
                LEFT JOIN approvisionnement.bon_commande_fourn b ON r.id_bcf = b.id_bcf
                LEFT JOIN approvisionnement.don d ON e.id_don = d.id_don
                WHERE 1=1";
        $params = [];

        if (!empty($filters['type_source'])) {
            $sql .= " AND e.type_source = ?";
            $params[] = $filters['type_source'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND e.reference ILIKE ?";
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY e.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT e.*, u.nom_complet AS utilisateur_nom,
                       b.reference AS commande_ref, d.donateur
                FROM approvisionnement.bon_entree e
                JOIN utilisateur.utilisateur u ON e.id_utilisateur = u.id_utilisateur
                LEFT JOIN approvisionnement.bon_reception r ON e.id_br = r.id_br
                LEFT JOIN approvisionnement.bon_commande_fourn b ON r.id_bcf = b.id_bcf
                LEFT JOIN approvisionnement.don d ON e.id_don = d.id_don
                WHERE e.id_be = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getLignes($idBe) {
        $stmt = $this->pdo->prepare("SELECT l.*, p.nom_produit, p.unite
                FROM approvisionnement.ligne_bon_entree l
                JOIN structure.produit p ON l.id_produit = p.id_produit
                WHERE l.id_be = ?
                ORDER BY l.id_lbe");
        $stmt->execute([$idBe]);
        return $stmt->fetchAll();
    }

    public function createFromDon($idDon, $idUtilisateur, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $reference = generateReference($this->pdo, 'BE', 'approvisionnement.bon_entree');

            $stmt = $this->pdo->prepare("INSERT INTO approvisionnement.bon_entree
                (id_don, id_utilisateur, reference, type_source, observations)
                VALUES (?, ?, ?, 'don', 'Généré depuis un don')
                RETURNING id_be");
            $stmt->execute([$idDon, $idUtilisateur, $reference]);
            $idBe = $stmt->fetchColumn();

            $stmtLbe = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_bon_entree
                (id_be, id_produit, quantite, prix_unitaire)
                VALUES (?, ?, ?, 0)");

            foreach ($lignes as $ligne) {
                $stmtLbe->execute([
                    $idBe,
                    $ligne['id_produit'],
                    $ligne['quantite']
                ]);
            }

            $this->pdo->commit();
            return $idBe;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
