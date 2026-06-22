<?php
class CategorieClientModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getForSelect(): array {
        $sql = "SELECT id_categorie_client, nom_categorie FROM structure.categorie_client ORDER BY nom_categorie";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($rows as $row) {
            $select[$row['id_categorie_client']] = $row['nom_categorie'];
        }
        return $select;
    }

    public function getAll(): array {
        $sql = "SELECT id_categorie_client, nom_categorie, taux_remise, description, 
                       TO_CHAR(date_creation, 'DD/MM/YYYY HH24:MI') as date_creation_fr
                FROM structure.categorie_client
                ORDER BY nom_categorie";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT id_categorie_client, nom_categorie, taux_remise, description
                FROM structure.categorie_client
                WHERE id_categorie_client = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO structure.categorie_client (nom_categorie, taux_remise, description)
                VALUES (:nom, :taux_remise, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom_categorie'] ?? '',
            ':taux_remise' => $data['taux_remise'] ?? 0,
            ':description' => $data['description'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE structure.categorie_client SET 
                    nom_categorie = :nom,
                    taux_remise = :taux_remise,
                    description = :description
                WHERE id_categorie_client = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $data['nom_categorie'] ?? '',
            ':taux_remise' => $data['taux_remise'] ?? 0,
            ':description' => $data['description'] ?? null
        ]);
    }

    public function isDeletable(int $id): bool {
        $sql = "SELECT COUNT(*) FROM structure.client WHERE id_categorie_client = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() == 0;
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM structure.categorie_client WHERE id_categorie_client = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
