<?php
$title = "État des achats annuel";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'État des achats annuel']
]);
ob_start();
?>
<?= renderPageHeader(
    'État des achats annuel',
    'Récapitulatif mensuel des achats',
    renderButton('Imprimer', 'secondary', '?action=etats_achats&print=1&annee=' . urlencode($annee), ['icon' => 'fa-print'])
) ?>

<?php
$anneeOptions = [];
for ($a = date('Y'); $a >= 2023; $a--) {
    $anneeOptions[$a] = (string)$a;
}
echo renderFilterBar('etats_achats', [
    ['select', 'annee', 'Année', $anneeOptions],
]);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-chart-bar text-brand-600 mr-2"></i>
            Achats <?= $annee ?>
            <span class="text-body text-neutral-60 ml-2">(Total : <?= number_format($totalAnnuel, 0, ',', ' ') ?> FCFA)</span>
        </h2>
    </div>
    <div class="card-body">
        <?php if (empty($moisData)): ?>
            <?= renderEmptyState('fa-calendar-alt', 'Aucun achat', "Aucune entrée en stock pour l'année $annee.") ?>
        <?php else:
            $headers = ['Mois', 'Nb bons', 'Total achats'];
            $rows = [];
            foreach ($moisData as $m):
                $rows[] = [
                    $moisLabels[(int)$m['mois']] ?? 'Mois ' . $m['mois'],
                    $m['nb_bons'],
                    number_format($m['total_mois'], 0, ',', ' ') . ' FCFA',
                ];
            endforeach;
            echo renderResponsiveTable($headers, $rows, [
                'mobileTitle' => 0,
                'mobileFields' => [1 => 'Nb bons', 2 => 'Total achats'],
            ]);
        ?>
            <div class="flex justify-end items-center px-4 py-3 border-t border-neutral-90 bg-neutral-98 font-semibold">
                <span>Total <?= $annee ?> : <?= number_format($totalAnnuel, 0, ',', ' ') ?> FCFA (<?= array_sum(array_map(fn($m) => $m['nb_bons'], $moisData)) ?> bons)</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
