<?php
class BanqueModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT id_banque, nom_banque, sigle, responsable, tel, email, adresse,
                   TO_CHAR(date_creation, 'DD/MM/YYYY') as date_creation_fr
            FROM structure.banque 
            ORDER BY nom_banque
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM structure.banque WHERE id_banque = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function create(array $data): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.banque (nom_banque, sigle, responsable, adresse, tel, email) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['nom_banque'],
            $data['sigle'] ?? null,
            $data['responsable'] ?? null,
            $data['adresse'] ?? null,
            $data['tel'] ?? null,
            $data['email'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE structure.banque SET 
                nom_banque = ?,
                sigle = ?,
                responsable = ?,
                adresse = ?,
                tel = ?,
                email = ?
            WHERE id_banque = ?
        ");
        return $stmt->execute([
            $data['nom_banque'],
            $data['sigle'] ?? null,
            $data['responsable'] ?? null,
            $data['adresse'] ?? null,
            $data['tel'] ?? null,
            $data['email'] ?? null,
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM structure.banque WHERE id_banque = ?");
        return $stmt->execute([$id]);
    }
    
    public function getForSelect(): array {
        $stmt = $this->pdo->query("SELECT id_banque, nom_banque FROM structure.banque ORDER BY nom_banque");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($rows as $row) {
            $select[$row['id_banque']] = $row['nom_banque'];
        }
        return $select;
    }
}