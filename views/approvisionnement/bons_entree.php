<?php
$title = "Bons d'entrée";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'Bons d\'entrée']
]);
ob_start();
?>
<?= renderPageHeader(
    'Bons d\'entrée',
    'Consulter les entrées en stock (achats et dons)'
) ?>

<?= renderFilterBar('bon_entree', [
    ['search', 'search', 'Référence'],
    ['select', 'type_source', 'Type', ['achat' => 'Achat', 'don' => 'Don', 'retour' => 'Retour', 'autre' => 'Autre']],
]) ?>

<?php
$actionsRenderer = function($row, $rowIndex) use ($bonsEntree) {
    $b = $bonsEntree[$rowIndex] ?? null;
    if (!$b) return '';
    $actions = '';
    if (checkRightIfLogged('imprimer_bon_entree')) {
        $actions .= renderButton('', 'icon', '?action=bon_entree&print=' . $b['id_be'], [
            'icon' => 'fa-print', 'title' => 'Imprimer'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($b) {
    $source = $b['type_source'] === 'achat' ? ($b['commande_ref'] ?? 'Achat direct') : ($b['donateur'] ?? ucfirst($b['type_source']));
    return [
        $b['reference'],
        ucfirst($b['type_source']),
        htmlspecialchars($source),
        date('d/m/Y', strtotime($b['date_entree'])),
        htmlspecialchars($b['utilisateur_nom'])
    ];
}, $bonsEntree);

echo renderResponsiveTable(
    ['Référence', 'Type', 'Source', 'Date', 'Utilisateur'],
    $tableData,
    [
        'mobileTitle' => 0, 'mobileSubtitle' => 3, 'mobileBadge' => 1,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun bon d\'entrée trouvé.'
    ]
);

$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
