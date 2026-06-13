<?php
$title = "État des ventes - Journalier";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'États ventes']
]);
ob_start();
?>
<?= renderPageHeader(
    'État des ventes du jour',
    'Synthèse des ventes pour le ' . date('d/m/Y'),
    renderButton('Imprimer', 'secondary', '?action=etats_ventes&type=jour&print=1', ['icon' => 'fa-print', 'target' => '_blank'])
        . renderButton('Voir l\'état annuel', 'primary', '?action=etats_ventes&type=annee', ['icon' => 'fa-chart-bar'])
) ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Nombre de ventes</p>
            <p class="text-h3 font-bold text-neutral-14"><?= $stats['nb_factures'] ?></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Total HT</p>
            <p class="text-h3 font-bold text-neutral-14"><?= number_format($stats['total_ht'], 0, ',', ' ') ?> FCFA</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Total TTC</p>
            <p class="text-h3 font-bold text-brand-600"><?= number_format($stats['total_ttc'], 0, ',', ' ') ?> FCFA</p>
        </div>
    </div>
</div>

<?php
$statutBadges = [
    'impayee'   => 'danger',
    'partielle' => 'warning',
    'payee'     => 'success',
    'annulee'   => 'neutral'
];

$tableData = array_map(function($f) use ($statutBadges) {
    return [
        $f['reference'],
        $f['cc_reference'],
        htmlspecialchars($f['client_nom'] . ' ' . ($f['client_prenom'] ?? '')),
        number_format($f['montant_ht'], 0, ',', ' ') . ' FCFA',
        number_format($f['montant_ttc'], 0, ',', ' ') . ' FCFA',
        renderBadge(ucfirst($f['statut']), $statutBadges[$f['statut']] ?? 'neutral')
    ];
}, $factures);

echo renderResponsiveTable(
    ['Référence', 'Commande', 'Client', 'Montant HT', 'Montant TTC', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 2,
        'mobileBadge' => 5,
        'emptyMessage' => 'Aucune vente enregistrée aujourd\'hui.'
    ]
);
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
