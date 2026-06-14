<?php
class ClientModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT c.id_client, c.nom, c.prenom, c.tel, c.email, c.ville, c.type_client, 
                   c.solde_credit, c.est_actif, cat.nom_categorie
            FROM structure.client c
            LEFT JOIN structure.categorie_client cat ON c.id_categorie_client = cat.id_categorie_client
            ORDER BY c.nom
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT c.*, cat.nom_categorie 
            FROM structure.client c
            LEFT JOIN structure.categorie_client cat ON c.id_categorie_client = cat.id_categorie_client
            WHERE c.id_client = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function create(array $data): int {
        $idCategorieClient = !empty($data['id_categorie_client']) ? $data['id_categorie_client'] : null;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.client 
            (id_categorie_client, nom, prenom, tel, email, ville, type_client, est_actif) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $idCategorieClient,
            $data['nom'],
            $data['prenom'] ?? null,
            $data['tel'] ?? null,
            $data['email'] ?? null,
            $data['ville'] ?? null,
            $data['type_client'] ?? 'particulier',
            isset($data['est_actif']) ? true : false
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $idCategorieClient = !empty($data['id_categorie_client']) ? $data['id_categorie_client'] : null;
        
        $stmt = $this->pdo->prepare("
            UPDATE structure.client SET 
                id_categorie_client = ?,
                nom = ?,
                prenom = ?,
                tel = ?,
                email = ?,
                ville = ?,
                type_client = ?,
                est_actif = ?,
                date_modif = CURRENT_TIMESTAMP
            WHERE id_client = ?
        ");
        
        return $stmt->execute([
            $idCategorieClient,
            $data['nom'],
            $data['prenom'] ?? null,
            $data['tel'] ?? null,
            $data['email'] ?? null,
            $data['ville'] ?? null,
            $data['type_client'] ?? 'particulier',
            isset($data['est_actif']) ? true : false,
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM structure.client WHERE id_client = ?");
        return $stmt->execute([$id]);
    }
}