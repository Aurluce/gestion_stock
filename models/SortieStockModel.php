<?php
class SortieStockModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT s.*, p.nom_produit, p.unite, u.nom_complet AS utilisateur_nom,
                       c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.sortie_stock s
                JOIN structure.produit p ON s.id_produit = p.id_produit
                JOIN utilisateur.utilisateur u ON s.id_utilisateur = u.id_utilisateur
                LEFT JOIN structure.client c ON s.id_client = c.id_client
                WHERE 1=1";
        $params = [];

        if (!empty($filters['motif'])) {
            $sql .= " AND s.motif_sortie = ?";
            $params[] = $filters['motif'];
        }

        $sql .= " ORDER BY s.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT s.*, p.nom_produit, p.unite,
                       c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.sortie_stock s
                JOIN structure.produit p ON s.id_produit = p.id_produit
                LEFT JOIN structure.client c ON s.id_client = c.id_client
                WHERE s.id_sortie = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($idProduit, $idClient, $idUtilisateur, $quantite, $motif, $observations) {
        $reference = generateReference($this->pdo, 'SORT', 'vente.sortie_stock');
        $stmt = $this->pdo->prepare("INSERT INTO vente.sortie_stock
            (id_produit, id_client, id_utilisateur, quantite, motif_sortie, reference, observations)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            RETURNING id_sortie");
        $stmt->execute([$idProduit, $idClient ?: null, $idUtilisateur, $quantite, $motif, $reference, $observations]);
        return $stmt->fetchColumn();
    }
}
