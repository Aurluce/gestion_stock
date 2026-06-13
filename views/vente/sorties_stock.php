<?php
$title = "Sorties de stock";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'Sorties de stock']
]);
ob_start();
?>
<?= renderPageHeader(
    'Sorties de stock',
    'Enregistrer les sorties hors-vente (péremption, casse, perte...)',
    checkRightIfLogged('enregistrer_sortie_stock') ? renderButton('Nouvelle sortie', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<div class="card mb-6">
    <div class="card-body">
        <form method="get" action="?action=sortie_stock" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="action" value="sortie_stock">
            <div class="min-w-[200px]">
                <label class="form-label">Motif</label>
                <select name="motif" class="form-select">
                    <option value="">Tous les motifs</option>
                    <?php
                    $motifsFiltre = ['perime' => 'Périmé', 'non_vendu' => 'Non vendu', 'retour_client' => 'Retour client', 'casse' => 'Casse', 'don' => 'Don', 'autre' => 'Autre'];
                    foreach ($motifsFiltre as $val => $label):
                        $selected = (($_GET['motif'] ?? '') === $val) ? 'selected' : '';
                    ?>
                    <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary"><i class="fas fa-filter mr-2"></i>Filtrer</button>
                <a href="?action=sortie_stock" class="btn-secondary">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<?php
$motifLabels = [
    'perime' => 'Périmé',
    'non_vendu' => 'Non vendu',
    'retour_client' => 'Retour client',
    'casse' => 'Casse',
    'don' => 'Don',
    'autre' => 'Autre'
];
$motifBadges = [
    'perime' => 'danger',
    'non_vendu' => 'warning',
    'retour_client' => 'info',
    'casse' => 'danger',
    'don' => 'success',
    'autre' => 'neutral'
];

$actionsRenderer = function($row, $rowIndex) use ($sorties) {
    $s = $sorties[$rowIndex] ?? null;
    if (!$s) return '';
    $actions = '';
    if (checkRightIfLogged('imprimer_bon_sortie')) {
        $actions .= renderButton('', 'icon', '?action=sortie_stock&print=' . $s['id_sortie'], [
            'icon' => 'fa-print',
            'title' => 'Imprimer'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($s) use ($motifLabels, $motifBadges) {
    return [
        $s['reference'],
        htmlspecialchars($s['nom_produit']),
        date('d/m/Y', strtotime($s['date_sortie'])),
        rtrim(rtrim(number_format($s['quantite'], 3, '.', ' '), '0'), '.') . ' ' . htmlspecialchars($s['unite']),
        renderBadge($motifLabels[$s['motif_sortie']] ?? $s['motif_sortie'], $motifBadges[$s['motif_sortie']] ?? 'neutral'),
        $s['client_nom'] ? htmlspecialchars($s['client_nom'] . ' ' . ($s['client_prenom'] ?? '')) : '-'
    ];
}, $sorties);

echo renderResponsiveTable(
    ['Référence', 'Produit', 'Date', 'Quantité', 'Motif', 'Client'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => 4,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune sortie de stock enregistrée.'
    ]
);

$produitsJson = json_encode(array_map(function($p) {
    return ['id' => $p['id_produit'], 'stock' => $p['stock_actuel'], 'unite' => $p['unite']];
}, $produits));
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=sortie_stock" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_produit', 'Produit', array_combine(
        array_map(fn($p) => $p['id_produit'], $produits),
        array_map(fn($p) => $p['nom_produit'] . ' (Stock: ' . rtrim(rtrim(number_format($p['stock_actuel'], 3, '.', ' '), '0'), '.') . ' ' . $p['unite'] . ')', $produits)
    ), null, null, ['required' => 'required', 'id' => 'select_produit_sortie'], 'Sélectionner un produit') . '
    ' . renderInput('quantite', 'Quantité à sortir', 'number', '', null, ['step' => '0.001', 'min' => '0.001', 'required' => 'required', 'id' => 'input_quantite_sortie']) . '
    <p id="stock_info" class="text-caption text-neutral-50"></p>
    ' . renderSelect('motif_sortie', 'Motif', [
        'perime' => 'Périmé',
        'non_vendu' => 'Non vendu',
        'retour_client' => 'Retour client',
        'casse' => 'Casse',
        'don' => 'Don',
        'autre' => 'Autre'
    ], null, null, ['required' => 'required'], 'Sélectionner un motif') . '
    ' . renderSelect('id_client', 'Client (si retour client)', array_combine(
        array_map(fn($c) => $c['id_client'], $clients),
        array_map(fn($c) => $c['nom'] . ' ' . ($c['prenom'] ?? ''), $clients)
    ), null, null, [], 'Aucun client') . '
    ' . renderTextarea('observations', 'Observations', '') . '

    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer la sortie', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle sortie de stock', $createBody);
?>

<script>
const PRODUITS_STOCK = <?= $produitsJson ?>;

document.getElementById('select_produit_sortie')?.addEventListener('change', function() {
    const produit = PRODUITS_STOCK.find(p => p.id == this.value);
    const info = document.getElementById('stock_info');
    const qteInput = document.getElementById('input_quantite_sortie');
    if (produit) {
        info.textContent = 'Stock disponible : ' + parseFloat(produit.stock) + ' ' + produit.unite;
        qteInput.max = produit.stock;
    } else {
        info.textContent = '';
        qteInput.removeAttribute('max');
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
