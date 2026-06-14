<?php
class CategorieClientModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getForSelect(): array {
        $stmt = $this->pdo->query("SELECT id_categorie_client, nom_categorie FROM structure.categorie_client ORDER BY nom_categorie");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($rows as $row) {
            $select[$row['id_categorie_client']] = $row['nom_categorie'];
        }
        return $select;
    }
}