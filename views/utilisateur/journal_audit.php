<?php
$title = "Journal d'audit";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Utilisateurs', 'href' => '?action=utilisateurs'],
    ['label' => 'Journal d\'audit']
]);
error_reporting(E_ALL & ~E_DEPRECATED);
ob_start();
?>

<?= renderPageHeader(
    'Journal d\'audit',
    'Historique des actions réalisées dans l\'application'
) ?>

<?php
// Fonction de rendu des actions (détails)
$actionsRenderer = function($row, $rowIndex) use ($logs) {
    $log = $logs[$rowIndex] ?? null;
    if (!$log) return '';
    
    return '<button onclick="toggleJson(' . $log['id_audit'] . ')" class="btn-icon" title="Voir les détails JSON">'
         . '<i class="fas fa-code"></i></button>'
         . '<div id="json_' . $log['id_audit'] . '" class="hidden mt-2 bg-neutral-95 p-3 rounded text-caption overflow-auto max-w-md border border-neutral-90">'
         . '<div class="font-medium text-neutral-14">Ancienne valeur :</div>'
         . '<pre class="text-neutral-30 text-xs whitespace-pre-wrap">' . htmlspecialchars($log['ancienne_valeur'] ?? 'N/A') . '</pre>'
         . '<div class="font-medium text-neutral-14 mt-2">Nouvelle valeur :</div>'
         . '<pre class="text-neutral-30 text-xs whitespace-pre-wrap">' . htmlspecialchars($log['nouvelle_valeur'] ?? 'N/A') . '</pre>'
         . '</div>';
};

// Préparer les données pour le tableau responsive
$tableData = array_map(function($log) {
    $actionBadge = renderBadge(
        $log['action'], 
        $log['action'] === 'LOGIN' || $log['action'] === 'LOGOUT' ? 'info' : 'neutral'
    );
    
    return [
        '<span class="text-caption text-neutral-50">' . htmlspecialchars($log['date_heure']) . '</span>',
        htmlspecialchars($log['utilisateur_nom'] ?? 'N/A'),
        $actionBadge,
        htmlspecialchars($log['table_cible'] ?? '-'),
        htmlspecialchars($log['id_enregistrement'] ?? '-')
    ];
}, $logs);

echo renderResponsiveTable(
    ['Date', 'Utilisateur', 'Action', 'Table', 'ID'],
    $tableData,
    [
        'mobileTitle' => 1,        // Utilisateur comme titre
        'mobileSubtitle' => 0,     // Date comme sous-titre
        'mobileBadge' => 2,        // Action en badge
        'mobileHidden' => [4],     // Cacher ID sur mobile
        'mobileFields' => [        // Champs personnalisés pour mobile
            3 => 'Table'
        ],
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune entrée d\'audit.'
    ]
);

echo renderPagination($page, $pages, '?action=journal_audit');
?>

<script>
function toggleJson(id) {
    var el = document.getElementById('json_' + id);
    if (el) el.classList.toggle('hidden');
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
