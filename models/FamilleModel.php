<?php
class FamilleModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $sql = "SELECT id_famille, nom_famille, description, 
                       TO_CHAR(date_creation, 'DD/MM/YYYY HH24:MI') as date_creation_fr,
                       date_creation
                FROM structure.famille 
                ORDER BY nom_famille";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $sql = "SELECT id_famille, nom_famille, description, date_creation
                FROM structure.famille 
                WHERE id_famille = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function create(string $nom, ?string $description = null): int {
        $sql = "INSERT INTO structure.famille (nom_famille, description) 
                VALUES (:nom, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, string $nom, ?string $description = null): bool {
        $sql = "UPDATE structure.famille 
                SET nom_famille = :nom, 
                    description = :description,
                    date_modif = CURRENT_TIMESTAMP
                WHERE id_famille = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description
        ]);
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM structure.famille WHERE id_famille = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function isDeletable(int $id): bool {
        $sql = "SELECT COUNT(*) FROM structure.produit WHERE id_famille = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() == 0;
    }
    
    public function getForSelect(): array {
        $sql = "SELECT id_famille, nom_famille FROM structure.famille ORDER BY nom_famille";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($results as $row) {
            $select[$row['id_famille']] = $row['nom_famille'];
        }
        return $select;
    }
}