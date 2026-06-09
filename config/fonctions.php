<?php
function logAudit($pdo, $userId, $action, $table, $recordId, $oldData = null, $newData = null, $ip = null, $userAgent = null) {
    if ($ip === null) $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    if ($userAgent === null) $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $oldJson = $oldData ? json_encode($oldData) : null;
    $newJson = $newData ? json_encode($newData) : null;
    $stmt = $pdo->prepare("
        INSERT INTO utilisateur.journal_audit (id_utilisateur, action, table_cible, id_enregistrement, ancienne_valeur, nouvelle_valeur, ip_adresse, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $action, $table, $recordId, $oldJson, $newJson, $ip, $userAgent]);
}
?>