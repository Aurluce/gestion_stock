<?php
class FamilleModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $sql = "SELECT id_famille, nom_famille, description, 
                       TO_CHAR(date_creation, 'DD/MM/YYYY') as date_creation_fr
                FROM structure.famille 
                ORDER BY nom_famille";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $sql = "SELECT id_famille, nom_famille, description FROM structure.famille WHERE id_famille = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function create(string $nom, ?string $description = null): int {
        $sql = "INSERT INTO structure.famille (nom_famille, description) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nom, $description]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, string $nom, ?string $description = null): bool {
        $sql = "UPDATE structure.famille SET nom_famille = ?, description = ?, date_modif = CURRENT_TIMESTAMP WHERE id_famille = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $description, $id]);
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM structure.famille WHERE id_famille = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function isDeletable(int $id): bool {
        $sql = "SELECT COUNT(*) FROM structure.produit WHERE id_famille = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchColumn() == 0;
    }
    
    public function getForSelect(): array {
        $sql = "SELECT id_famille, nom_famille FROM structure.famille ORDER BY nom_famille";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($rows as $row) {
            $select[$row['id_famille']] = $row['nom_famille'];
        }
        return $select;
    }
}