<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';
require_once __DIR__ . '/autoload.php';

session_start();

function checkRight($rightName) {
    global $pdo;
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM utilisateur.utilisateur u
        JOIN utilisateur.groupe_droit gd ON u.id_groupe = gd.id_groupe
        JOIN utilisateur.droit d ON gd.id_droit = d.id_droit
        WHERE u.id_utilisateur = ? AND d.nom_droit = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $rightName]);
    if ($stmt->fetchColumn() == 0) {
        renderErrorPage(403, "Droit requis : " . htmlspecialchars($rightName));
    }
}

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur.utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
    if (!$currentUser || !$currentUser['actif']) {
        session_destroy();
        header('Location: index.php?action=login&error=inactive');
        exit;
    }
}

function checkRightIfLogged($rightName) {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return false;
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM utilisateur.utilisateur u
        JOIN utilisateur.groupe_droit gd ON u.id_groupe = gd.id_groupe
        JOIN utilisateur.droit d ON gd.id_droit = d.id_droit
        WHERE u.id_utilisateur = ? AND d.nom_droit = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $rightName]);
    return $stmt->fetchColumn() > 0;
}

// Chargement des composants du design system
require_once __DIR__ . '/../views/components/autoload.php';
?>