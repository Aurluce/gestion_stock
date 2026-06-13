<?php
class FournisseurModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT id_fournisseur, nom, adresse, ville, code_postal, pays, tel, email, nif, est_actif,
                   TO_CHAR(date_creation, 'DD/MM/YYYY') as date_creation_fr
            FROM structure.fournisseur
            ORDER BY nom
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM structure.fournisseur WHERE id_fournisseur = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function create(array $data): int {
        $estActifVal = $data['est_actif'] ? 'true' : 'false';
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.fournisseur (nom, tel, email, adresse, ville, nif, est_actif) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['nom'],
            $data['tel'] ?? null,
            $data['email'] ?? null,
            $data['adresse'] ?? null,
            $data['ville'] ?? null,
            $data['nif'] ?? null,
            $estActifVal
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $estActifVal = $data['est_actif'] ? 'true' : 'false';
        
        $stmt = $this->pdo->prepare("
            UPDATE structure.fournisseur SET 
                nom = ?,
                tel = ?,
                email = ?,
                adresse = ?,
                ville = ?,
                nif = ?,
                est_actif = ?,
                date_modif = CURRENT_TIMESTAMP
            WHERE id_fournisseur = ?
        ");
        
        return $stmt->execute([
            $data['nom'],
            $data['tel'] ?? null,
            $data['email'] ?? null,
            $data['adresse'] ?? null,
            $data['ville'] ?? null,
            $data['nif'] ?? null,
            $estActifVal,
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM structure.fournisseur WHERE id_fournisseur = ?");
        return $stmt->execute([$id]);
    }
    
    public function disable(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE structure.fournisseur SET est_actif = false, date_modif = CURRENT_TIMESTAMP WHERE id_fournisseur = ?");
        return $stmt->execute([$id]);
    }
    
    public function enable(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE structure.fournisseur SET est_actif = true, date_modif = CURRENT_TIMESTAMP WHERE id_fournisseur = ?");
        return $stmt->execute([$id]);
    }
    
    public function getForSelect(): array {
        $stmt = $this->pdo->query("SELECT id_fournisseur, nom FROM structure.fournisseur WHERE est_actif = true ORDER BY nom");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($rows as $row) {
            $select[$row['id_fournisseur']] = $row['nom'];
        }
        return $select;
    }
}