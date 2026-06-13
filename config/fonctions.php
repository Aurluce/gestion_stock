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

/**
 * Génère une référence unique au format PREFIX-ANNEE-0001
 * @param PDO $pdo
 * @param string $prefix Préfixe (ex: 'CC', 'BL', 'FACT', 'SORT', 'REG')
 * @param string $schemaTable Table complète avec schéma (ex: 'vente.commande_client')
 * @param string $refColumn Nom de la colonne référence (par défaut 'reference')
 * @param string $dateColumn Colonne de date pour filtrer l'année (par défaut 'date_creation')
 * @return string
 */
function generateReference($pdo, $prefix, $schemaTable, $refColumn = 'reference', $dateColumn = 'date_creation') {
    $year = date('Y');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $schemaTable WHERE EXTRACT(YEAR FROM $dateColumn) = ?");
    $stmt->execute([$year]);
    $count = (int) $stmt->fetchColumn() + 1;
    do {
        $reference = sprintf('%s-%s-%04d', $prefix, $year, $count);
        $check = $pdo->prepare("SELECT COUNT(*) FROM $schemaTable WHERE $refColumn = ?");
        $check->execute([$reference]);
        $exists = (int) $check->fetchColumn() > 0;
        $count++;
    } while ($exists);
    return $reference;
}
