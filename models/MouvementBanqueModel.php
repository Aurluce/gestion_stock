<?php
class MouvementBanqueModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getByBanque(int $idBanque, string $dateDebut = '', string $dateFin = ''): array {
        $sql = "SELECT mb.*, u.nom_complet as utilisateur_nom
                FROM structure.mouvement_banque mb
                LEFT JOIN utilisateur.utilisateur u ON mb.id_utilisateur = u.id_utilisateur
                WHERE mb.id_banque = ?";
        $params = [$idBanque];
        
        if (!empty($dateDebut)) {
            $sql .= " AND mb.date_mouvement >= ?";
            $params[] = $dateDebut;
        }
        if (!empty($dateFin)) {
            $sql .= " AND mb.date_mouvement <= ?";
            $params[] = $dateFin;
        }
        
        $sql .= " ORDER BY mb.date_mouvement DESC, mb.id_mouvement_banque DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSolde(int $idBanque, string $date = ''): float {
        $sql = "SELECT COALESCE(SUM(
                    CASE 
                        WHEN type_mouvement IN ('versement') THEN montant
                        WHEN type_mouvement IN ('retrait') THEN -montant
                        ELSE 0
                    END
                ), 0) as solde
                FROM structure.mouvement_banque
                WHERE id_banque = ?";
        $params = [$idBanque];
        
        if (!empty($date)) {
            $sql .= " AND date_mouvement <= ?";
            $params[] = $date;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['solde'] ?? 0);
    }
    
    public function create(array $data): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.mouvement_banque 
            (id_banque, id_utilisateur, date_mouvement, type_mouvement, montant, reference, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['id_banque'],
            $_SESSION['user_id'],
            $data['date_mouvement'] ?? date('Y-m-d'),
            $data['type_mouvement'],
            $data['montant'],
            $data['reference'] ?? null,
            $data['description'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM structure.mouvement_banque WHERE id_mouvement_banque = ?");
        return $stmt->execute([$id]);
    }
}