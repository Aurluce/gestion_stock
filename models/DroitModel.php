<?php
class DroitModel {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        return $this->pdo->query("SELECT * FROM utilisateur.droit ORDER BY module, id_droit")->fetchAll();
    }
    
    public function getByGroupe($groupeId) {
        $stmt = $this->pdo->prepare("
            SELECT d.* FROM utilisateur.droit d
            JOIN utilisateur.groupe_droit gd ON d.id_droit = gd.id_droit
            WHERE gd.id_groupe = ? ORDER BY d.module, d.nom_droit
        ");
        $stmt->execute([$groupeId]);
        return $stmt->fetchAll();
    }
    
    public function assignToGroup($groupeId, $droitsIds) {
        $this->pdo->prepare("DELETE FROM utilisateur.groupe_droit WHERE id_groupe = ?")->execute([$groupeId]);
        if (!empty($droitsIds)) {
            $insert = $this->pdo->prepare("INSERT INTO utilisateur.groupe_droit (id_groupe, id_droit) VALUES (?, ?)");
            foreach ($droitsIds as $droitId) {
                $insert->execute([$groupeId, $droitId]);
            }
        }
        return true;
    }
}
?>