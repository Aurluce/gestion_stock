<?php
require_once 'config/database.php';
require_once 'config/session.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$action = $_GET['action'] ?? 'dashboard';
$publicActions = ['login', 'logout'];

if (!in_array($action, $publicActions) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

switch ($action) {
    case 'login':
        require_once 'controllers/AuthController.php';
        (new AuthController($pdo))->login();
        break;
    case 'logout':
        require_once 'controllers/AuthController.php';
        (new AuthController($pdo))->logout();
        break;
    case 'groupes':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->groupes();
        break;
    case 'droits':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->droits();
        break;
    case 'groupes_droits':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->groupesDroits();
        break;
    case 'utilisateurs':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->utilisateurs();
        break;
    case 'profil':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->profil();
        break;
    case 'journal_audit':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->journalAudit();
        break;
case 'dashboard':
default:
    $title = "Tableau de bord";
    ob_start();
    echo "<h1 class='text-2xl font-bold'>Bienvenue " . htmlspecialchars($_SESSION['user_name'] ?? '') . "</h1>";
    echo "<p>Module utilisateur opérationnel.</p>";
    $content = ob_get_clean();
    require 'views/layouts/main.php';
    break;
}
?>