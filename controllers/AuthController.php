<?php
require_once __DIR__ . '/../models/UtilisateurModel.php';
class AuthController {
    private $userModel, $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new UtilisateurModel($pdo);
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userModel->findByLogin($_POST['login'] ?? '');
            if ($user && $user['actif'] && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
                session_start();
                $_SESSION['user_id'] = $user['id_utilisateur'];
                $_SESSION['user_name'] = $user['nom_complet'];
                $this->pdo->prepare("UPDATE utilisateur.utilisateur SET derniere_connexion = NOW(), ip_derniere_connexion = ? WHERE id_utilisateur = ?")
                         ->execute([$_SERVER['REMOTE_ADDR'], $user['id_utilisateur']]);
                logAudit($this->pdo, $user['id_utilisateur'], 'LOGIN', null, null, null, null);
                header('Location: index.php?action=dashboard');
                exit;
            }
            $error = "Identifiants incorrects ou compte désactivé.";
        }
        require __DIR__ . '/../views/auth/login.php';
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['user_id'])) logAudit($this->pdo, $_SESSION['user_id'], 'LOGOUT', null, null, null, null);
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
?>