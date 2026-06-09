<?php
class JournalAuditModel {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll($limit, $offset) {
        $stmt = $this->pdo->prepare("
            SELECT ja.*, u.nom_complet as utilisateur_nom
            FROM utilisateur.journal_audit ja
            LEFT JOIN utilisateur.utilisateur u ON ja.id_utilisateur = u.id_utilisateur
            ORDER BY ja.date_heure DESC LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function count() {
        return $this->pdo->query("SELECT COUNT(*) FROM utilisateur.journal_audit")->fetchColumn();
    }
}
?>