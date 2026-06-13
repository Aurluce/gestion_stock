<?php
$title = "État des achats du jour";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'État des achats du jour']
]);
ob_start();
?>
<?= renderPageHeader(
    'État des achats',
    'Détail des achats par jour',
    renderButton('Imprimer', 'secondary', '?action=etats_achats&print=1&date=' . urlencode($date), ['icon' => 'fa-print'])
) ?>

<?= renderFilterBar('etats_achats', [
    ['date', 'date', 'Date'],
]) ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-shopping-cart text-brand-600 mr-2"></i>
            Achats du <?= date('d/m/Y', strtotime($date)) ?>
            <span class="text-body text-neutral-60 ml-2">(<?= count($lignes) ?> lignes)</span>
        </h2>
    </div>
    <div class="card-body">
        <?php if (empty($lignes)): ?>
            <?= renderEmptyState('fa-calendar-day', 'Aucun achat', 'Aucune entrée en stock enregistrée pour cette date.') ?>
        <?php else:
            $headers = ['Bon entrée', 'Produit', 'Qté', 'Prix unit.', 'Montant'];
            $rows = [];
            foreach ($lignes as $l):
                $rows[] = [
                    htmlspecialchars($l['reference']),
                    htmlspecialchars($l['nom_produit']),
                    number_format($l['quantite'], 3, ',', ' '),
                    number_format($l['prix_unitaire'], 0, ',', ' '),
                    number_format($l['montant_ligne'], 0, ',', ' '),
                ];
            endforeach;
            echo renderResponsiveTable($headers, $rows, [
                'mobileTitle' => 1,
                'mobileSubtitle' => 0,
                'mobileFields' => [2 => 'Qté', 3 => 'Prix unit.', 4 => 'Montant'],
            ]);
        ?>
            <div class="flex justify-end items-center px-4 py-3 border-t border-neutral-90 bg-neutral-98 font-semibold">
                <span>Total des achats : <?= number_format($totalAchats, 0, ',', ' ') ?> FCFA</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
