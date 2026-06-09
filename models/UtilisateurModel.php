<?php
class UtilisateurModel {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT u.*, g.nom_groupe FROM utilisateur.utilisateur u LEFT JOIN utilisateur.groupe g ON u.id_groupe = g.id_groupe ORDER BY u.id_utilisateur");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur.utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur.utilisateur (id_groupe, nom_complet, login, password_hash, actif, date_expiration_mdp)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['id_groupe'], $data['nom_complet'], $data['login'],
            $passwordHash, $data['actif'] ?? true, $data['date_expiration_mdp'] ?? null
        ]);
    }
    
    public function update($id, $data) {
        $fields = []; $params = [];
        if (isset($data['nom_complet'])) { $fields[] = "nom_complet = ?"; $params[] = $data['nom_complet']; }
        if (isset($data['login'])) { $fields[] = "login = ?"; $params[] = $data['login']; }
        if (isset($data['id_groupe'])) { $fields[] = "id_groupe = ?"; $params[] = $data['id_groupe']; }
        if (isset($data['actif'])) { $fields[] = "actif = ?"; $params[] = $data['actif']; }
        if (isset($data['date_expiration_mdp'])) { $fields[] = "date_expiration_mdp = ?"; $params[] = $data['date_expiration_mdp']; }
        if (!empty($data['password'])) {
            $fields[] = "password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = "UPDATE utilisateur.utilisateur SET " . implode(', ', $fields) . " WHERE id_utilisateur = ?";
        return $this->pdo->prepare($sql)->execute($params);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur.utilisateur WHERE id_utilisateur = ?");
        return $stmt->execute([$id]);
    }
    
    public function findByLogin($login) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur.utilisateur WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch();
    }
}
?>