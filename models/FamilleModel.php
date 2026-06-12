<?php
class FamilleModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $sql = "SELECT id_famille, nom_famille, description, TO_CHAR(date_creation, 'DD/MM/YYYY') as date_creation_fr FROM structure.famille ORDER BY nom_famille";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $sql = "SELECT id_famille, nom_famille, description FROM structure.famille WHERE id_famille = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
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
