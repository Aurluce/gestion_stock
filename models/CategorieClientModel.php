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

    public function create(string $nom, float $tauxRemise = 0.0, ?string $description = null): int {
        $sql = "INSERT INTO structure.categorie_client (nom_categorie, taux_remise, description)
                VALUES (:nom, :taux_remise, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':taux_remise' => $tauxRemise,
            ':description' => $description
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $nom, float $tauxRemise = 0.0, ?string $description = null): bool {
        $sql = "UPDATE structure.categorie_client SET 
                    nom_categorie = :nom,
                    taux_remise = :taux_remise,
                    description = :description
                WHERE id_categorie_client = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':taux_remise' => $tauxRemise,
            ':description' => $description
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM structure.categorie_client WHERE id_categorie_client = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
