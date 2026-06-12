<?php
/**
 * Modèle Banque - Module Structure
 */
class BanqueModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $sql = "SELECT id_banque, nom_banque, sigle, responsable, tel, email, 
                       TO_CHAR(date_creation, 'DD/MM/YYYY') as date_creation_fr
                FROM structure.banque 
                ORDER BY nom_banque";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM structure.banque WHERE id_banque = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO structure.banque (nom_banque, sigle, responsable, adresse, tel, email) 
                VALUES (:nom, :sigle, :responsable, :adresse, :tel, :email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom_banque'],
            ':sigle' => $data['sigle'] ?? null,
            ':responsable' => $data['responsable'] ?? null,
            ':adresse' => $data['adresse'] ?? null,
            ':tel' => $data['tel'] ?? null,
            ':email' => $data['email'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $sql = "UPDATE structure.banque SET 
                    nom_banque = :nom, 
                    sigle = :sigle, 
                    responsable = :responsable, 
                    adresse = :adresse, 
                    tel = :tel, 
                    email = :email
                WHERE id_banque = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $data['nom_banque'],
            ':sigle' => $data['sigle'] ?? null,
            ':responsable' => $data['responsable'] ?? null,
            ':adresse' => $data['adresse'] ?? null,
            ':tel' => $data['tel'] ?? null,
            ':email' => $data['email'] ?? null
        ]);
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM structure.banque WHERE id_banque = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getForSelect(): array {
        $sql = "SELECT id_banque, nom_banque FROM structure.banque ORDER BY nom_banque";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($rows as $row) {
            $select[$row['id_banque']] = $row['nom_banque'];
        }
        return $select;
    }
}
