<?php
$title = "État des ventes - Annuel";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'États ventes']
]);
ob_start();

$moisLabels = [
    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
    7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
];

// Sélecteur d'année
$anneeOptions = '';
foreach ($anneesDisponibles as $a) {
    $selected = ($a == $annee) ? 'selected' : '';
    $anneeOptions .= "<option value=\"$a\" $selected>$a</option>";
}
?>
<?= renderPageHeader(
    'État des ventes annuel - ' . htmlspecialchars($annee),
    'Synthèse mensuelle des ventes',
    renderButton('Imprimer', 'secondary', '?action=etats_ventes&type=annee&annee=' . $annee . '&print=1', ['icon' => 'fa-print', 'target' => '_blank'])
        . renderButton('Voir l\'état journalier', 'primary', '?action=etats_ventes&type=jour', ['icon' => 'fa-calendar-day'])
) ?>

<div class="card mb-6">
    <div class="card-body flex items-center gap-4">
        <label class="form-label mb-0">Année :</label>
        <form method="get" action="?action=etats_ventes" class="flex items-center gap-2">
            <input type="hidden" name="type" value="annee">
            <select name="annee" class="form-select" onchange="this.form.submit()" style="width: auto;">
                <?= $anneeOptions ?>
            </select>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Nombre de ventes (année)</p>
            <p class="text-h3 font-bold text-neutral-14"><?= $totalAnnee['nb_factures'] ?></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Total HT (année)</p>
            <p class="text-h3 font-bold text-neutral-14"><?= number_format($totalAnnee['total_ht'], 0, ',', ' ') ?> FCFA</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Total TTC (année)</p>
            <p class="text-h3 font-bold text-brand-600"><?= number_format($totalAnnee['total_ttc'], 0, ',', ' ') ?> FCFA</p>
        </div>
    </div>
</div>

<?php
$tableData = [];
foreach ($statsParMois as $row) {
    $tableData[] = [
        $moisLabels[$row['mois']],
        $row['nb_factures'],
        number_format($row['total_ht'], 0, ',', ' ') . ' FCFA',
        number_format($row['total_ttc'], 0, ',', ' ') . ' FCFA'
    ];
}

echo renderResponsiveTable(
    ['Mois', 'Nombre de ventes', 'Total HT', 'Total TTC'],
    $tableData,
    [
        'mobileTitle' => 0,
        'emptyMessage' => 'Aucune vente enregistrée pour l\'année ' . htmlspecialchars($annee) . '.'
    ]
);
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
