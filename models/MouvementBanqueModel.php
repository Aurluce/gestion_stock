<?php
/**
 * Modèle Mouvement Banque - Module Structure
 * Gère les opérations sur la table structure.mouvement_banque
 */
class MouvementBanqueModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Récupère tous les mouvements d'une banque
     */
    public function getByBanque(int $idBanque, string $dateDebut = '', string $dateFin = ''): array {
        $sql = "SELECT mb.*, u.nom_complet as utilisateur_nom
                FROM structure.mouvement_banque mb
                LEFT JOIN utilisateur.utilisateur u ON mb.id_utilisateur = u.id_utilisateur
                WHERE mb.id_banque = :id_banque";
        $params = [':id_banque' => $idBanque];
        
        if (!empty($dateDebut)) {
            $sql .= " AND mb.date_mouvement >= :date_debut";
            $params[':date_debut'] = $dateDebut;
        }
        if (!empty($dateFin)) {
            $sql .= " AND mb.date_mouvement <= :date_fin";
            $params[':date_fin'] = $dateFin;
        }
        
        $sql .= " ORDER BY mb.date_mouvement DESC, mb.id_mouvement_banque DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Calcule le solde d'une banque à une date donnée
     */
    public function getSolde(int $idBanque, string $date = ''): float {
        $sql = "SELECT COALESCE(SUM(
                    CASE 
                        WHEN type_mouvement IN ('versement', 'virement_entrant') THEN montant
                        WHEN type_mouvement IN ('retrait', 'virement_sortant') THEN -montant
                        ELSE 0
                    END
                ), 0) as solde
                FROM structure.mouvement_banque
                WHERE id_banque = :id_banque";
        $params = [':id_banque' => $idBanque];
        
        if (!empty($date)) {
            $sql .= " AND date_mouvement <= :date";
            $params[':date'] = $date;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['solde'] ?? 0);
    }
    
    /**
     * Récupère les résumés par jour pour une période
     */
    public function getResumeParJour(int $idBanque, string $dateDebut, string $dateFin): array {
        $sql = "SELECT 
                    date_mouvement,
                    SUM(CASE WHEN type_mouvement IN ('versement', 'virement_entrant') THEN montant ELSE 0 END) as total_entrees,
                    SUM(CASE WHEN type_mouvement IN ('retrait', 'virement_sortant') THEN montant ELSE 0 END) as total_sorties,
                    COUNT(*) as nb_operations
                FROM structure.mouvement_banque
                WHERE id_banque = :id_banque
                    AND date_mouvement BETWEEN :date_debut AND :date_fin
                GROUP BY date_mouvement
                ORDER BY date_mouvement DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_banque' => $idBanque,
            ':date_debut' => $dateDebut,
            ':date_fin' => $dateFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouveau mouvement bancaire
     */
    public function create(array $data): int {
        $sql = "INSERT INTO structure.mouvement_banque 
                (id_banque, id_utilisateur, date_mouvement, type_mouvement, montant, reference, description)
                VALUES 
                (:id_banque, :id_utilisateur, :date_mouvement, :type_mouvement, :montant, :reference, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_banque' => $data['id_banque'],
            ':id_utilisateur' => $_SESSION['user_id'],
            ':date_mouvement' => $data['date_mouvement'] ?? date('Y-m-d'),
            ':type_mouvement' => $data['type_mouvement'],
            ':montant' => $data['montant'],
            ':reference' => $data['reference'] ?? null,
            ':description' => $data['description'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    /**
     * Supprime un mouvement
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM structure.mouvement_banque WHERE id_mouvement_banque = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
