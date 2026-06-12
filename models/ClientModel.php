<?php
class ClientModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $sql = "SELECT c.id_client, c.nom, c.prenom, c.tel, c.email, c.ville, c.type_client, 
                       c.solde_credit, c.est_actif, cat.nom_categorie
                FROM structure.client c
                LEFT JOIN structure.categorie_client cat ON c.id_categorie_client = cat.id_categorie_client
                ORDER BY c.nom";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $sql = "SELECT c.*, cat.nom_categorie 
                FROM structure.client c
                LEFT JOIN structure.categorie_client cat ON c.id_categorie_client = cat.id_categorie_client
                WHERE c.id_client = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO structure.client 
                (id_categorie_client, nom, prenom, tel, email, ville, type_client, est_actif) 
                VALUES 
                (:id_categorie_client, :nom, :prenom, :tel, :email, :ville, :type_client, :est_actif)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_categorie_client' => $data['id_categorie_client'] ?? null,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'] ?? null,
            ':tel' => $data['tel'] ?? null,
            ':email' => $data['email'] ?? null,
            ':ville' => $data['ville'] ?? null,
            ':type_client' => $data['type_client'] ?? 'particulier',
            ':est_actif' => isset($data['est_actif']) ? true : false
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $sql = "UPDATE structure.client SET 
                    id_categorie_client = :id_categorie_client,
                    nom = :nom,
                    prenom = :prenom,
                    tel = :tel,
                    email = :email,
                    ville = :ville,
                    type_client = :type_client,
                    est_actif = :est_actif,
                    date_modif = CURRENT_TIMESTAMP
                WHERE id_client = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':id_categorie_client' => $data['id_categorie_client'] ?? null,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'] ?? null,
            ':tel' => $data['tel'] ?? null,
            ':email' => $data['email'] ?? null,
            ':ville' => $data['ville'] ?? null,
            ':type_client' => $data['type_client'] ?? 'particulier',
            ':est_actif' => isset($data['est_actif']) ? true : false
        ]);
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM structure.client WHERE id_client = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
