<?php
// logout.php
require_once 'config/database.php';
require_once 'config/fonctions.php';
session_start();
if (isset($_SESSION['user_id'])) {
    logAudit($pdo, $_SESSION['user_id'], 'LOGOUT', null, null, null, null);
}
session_destroy();
header('Location: login.php');
exit;
?>