<?php
class GroupeModel {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        return $this->pdo->query("SELECT * FROM utilisateur.groupe ORDER BY id_groupe")->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur.groupe WHERE id_groupe = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($nom, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO utilisateur.groupe (nom_groupe, description) VALUES (?, ?)");
        return $stmt->execute([$nom, $description]);
    }
    
    public function update($id, $nom, $description) {
        $stmt = $this->pdo->prepare("UPDATE utilisateur.groupe SET nom_groupe = ?, description = ? WHERE id_groupe = ?");
        return $stmt->execute([$nom, $description, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur.groupe WHERE id_groupe = ?");
        return $stmt->execute([$id]);
    }
    
    public function hasUsers($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateur.utilisateur WHERE id_groupe = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}
?>