<?php
class ProduitModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT p.*, f.nom_famille, pp.nom_produit as nom_produit_pere 
            FROM structure.produit p
            LEFT JOIN structure.famille f ON p.id_famille = f.id_famille
            LEFT JOIN structure.produit pp ON p.id_produit_pere = pp.id_produit
            ORDER BY p.nom_produit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT p.*, f.nom_famille, pp.nom_produit as nom_produit_pere
            FROM structure.produit p
            LEFT JOIN structure.famille f ON p.id_famille = f.id_famille
            LEFT JOIN structure.produit pp ON p.id_produit_pere = pp.id_produit
            WHERE p.id_produit = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function getByFamille(int $idFamille): array {
        $stmt = $this->pdo->prepare("
            SELECT p.id_produit, p.nom_produit, p.prix_vente, p.stock_actuel, p.est_actif,
                   pp.nom_produit as nom_produit_pere
            FROM structure.produit p
            LEFT JOIN structure.produit pp ON p.id_produit_pere = pp.id_produit
            WHERE p.id_famille = ? 
            ORDER BY p.nom_produit
        ");
        $stmt->execute([$idFamille]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProduitsPeresByFamille(int $idFamille): array {
        $stmt = $this->pdo->prepare("
            SELECT id_produit, nom_produit 
            FROM structure.produit 
            WHERE id_produit_pere IS NULL 
            AND id_famille = ? 
            AND est_actif = true
            ORDER BY nom_produit
        ");
        $stmt->execute([$idFamille]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $select = [];
        foreach ($rows as $row) {
            $select[$row['id_produit']] = $row['nom_produit'];
        }
        return $select;
    }
    
    public function generateCodeBarre(): string {
        $code = 'PRD' . date('Ymd') . rand(100, 999);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM structure.produit WHERE code_barre = ?");
        $stmt->execute([$code]);
        if ($stmt->fetchColumn() > 0) {
            return $this->generateCodeBarre();
        }
        return $code;
    }
    
    public function create(array $data): int {
        $perissableVal = $data['perissable'] ? 'true' : 'false';
        $estActifVal = $data['est_actif'] ? 'true' : 'false';
        $datePeremption = !empty($data['date_peremption']) ? $data['date_peremption'] : null;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.produit 
            (id_famille, id_produit_pere, code_barre, nom_produit, description, 
             prix_achat, prix_vente, stock_actuel, seuil_alerte, perissable, 
             date_peremption, unite, est_actif)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['id_famille'],
            $data['id_produit_pere'] ?? null,
            $data['code_barre'],
            $data['nom_produit'],
            $data['description'] ?? null,
            $data['prix_achat'] ?? 0,
            $data['prix_vente'] ?? 0,
            $data['stock_actuel'] ?? 0,
            $data['seuil_alerte'] ?? 0,
            $perissableVal,
            $datePeremption,
            $data['unite'] ?? 'pce',
            $estActifVal
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $perissableVal = $data['perissable'] ? 'true' : 'false';
        $estActifVal = $data['est_actif'] ? 'true' : 'false';
        $datePeremption = !empty($data['date_peremption']) ? $data['date_peremption'] : null;
        
        $stmt = $this->pdo->prepare("
            UPDATE structure.produit SET 
                id_famille = ?, 
                id_produit_pere = ?, 
                code_barre = ?, 
                nom_produit = ?, 
                description = ?,
                prix_achat = ?, 
                prix_vente = ?, 
                stock_actuel = ?, 
                seuil_alerte = ?, 
                perissable = ?,
                date_peremption = ?, 
                unite = ?, 
                est_actif = ?, 
                date_modif = CURRENT_TIMESTAMP
            WHERE id_produit = ?
        ");
        
        return $stmt->execute([
            $data['id_famille'],
            $data['id_produit_pere'] ?? null,
            $data['code_barre'],
            $data['nom_produit'],
            $data['description'] ?? null,
            $data['prix_achat'] ?? 0,
            $data['prix_vente'] ?? 0,
            $data['stock_actuel'] ?? 0,
            $data['seuil_alerte'] ?? 0,
            $perissableVal,
            $datePeremption,
            $data['unite'] ?? 'pce',
            $estActifVal,
            $id
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM structure.produit WHERE id_produit = ?");
        return $stmt->execute([$id]);
    }
    
    public function disable(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE structure.produit SET est_actif = false, date_modif = CURRENT_TIMESTAMP WHERE id_produit = ?");
        return $stmt->execute([$id]);
    }
    
    public function enable(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE structure.produit SET est_actif = true, date_modif = CURRENT_TIMESTAMP WHERE id_produit = ?");
        return $stmt->execute([$id]);
    }
}