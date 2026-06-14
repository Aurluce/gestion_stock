<?php
class CategorieClientModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT id_categorie_client, nom_categorie, taux_remise, description,
                   TO_CHAR(date_creation, 'DD/MM/YYYY HH24:MI') as date_creation_fr
            FROM structure.categorie_client
            ORDER BY nom_categorie
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM structure.categorie_client WHERE id_categorie_client = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
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
    
    public function create(array $data): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.categorie_client (nom_categorie, taux_remise, description)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $data['nom_categorie'],
            $data['taux_remise'] ?? 0,
            $data['description'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE structure.categorie_client SET
                nom_categorie = ?,
                taux_remise = ?,
                description = ?
            WHERE id_categorie_client = ?
        ");
        return $stmt->execute([
            $data['nom_categorie'],
            $data['taux_remise'] ?? 0,
            $data['description'] ?? null,
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM structure.categorie_client WHERE id_categorie_client = ?");
        return $stmt->execute([$id]);
    }
    
    public function isDeletable(int $id): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM structure.client WHERE id_categorie_client = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() == 0;
    }
}