<?php
$title = "Paiements fournisseurs";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'Paiements fournisseurs']
]);
ob_start();
?>
<?= renderPageHeader(
    'Paiements fournisseurs',
    'Enregistrer les paiements aux fournisseurs',
    checkRightIfLogged('payer_fournisseur') ? renderButton('Nouveau paiement', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?php
$modePaiementLabels = [
    'espece' => 'Espèces',
    'cheque' => 'Chèque',
    'virement' => 'Virement',
    'mobile_money' => 'Mobile Money',
    'carte' => 'Carte'
];

$actionsRenderer = function($row, $rowIndex) use ($paiements, $modePaiementLabels) {
    $p = $paiements[$rowIndex] ?? null;
    if (!$p) return '';
    $actions = '';
    if (checkRightIfLogged('imprimer_recu_fournisseur')) {
        $actions .= renderButton('', 'icon', '?action=paiement_fourn&print=' . $p['id_paiement'], [
            'icon' => 'fa-print', 'title' => 'Imprimer reçu'
        ]);
    }
    if (checkRightIfLogged('supprimer_paiement_fournisseur')) {
        $actions .= renderButton('', 'icon', '?action=paiement_fourn&delete=' . $p['id_paiement'], [
            'icon' => 'fa-trash', 'title' => 'Supprimer',
            'data-confirm' => 'Supprimer ce paiement ?',
            'data-confirm-type' => 'danger'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($p) use ($modePaiementLabels) {
    return [
        htmlspecialchars($p['reference'] ?? '-'),
        htmlspecialchars($p['fournisseur_nom']),
        htmlspecialchars($p['numero_facture']),
        number_format($p['montant'], 0, ',', ' ') . ' FCFA',
        date('d/m/Y', strtotime($p['date_paiement'])),
        $modePaiementLabels[$p['mode_paiement']] ?? $p['mode_paiement']
    ];
}, $paiements);

echo renderResponsiveTable(
    ['Référence', 'Fournisseur', 'Facture', 'Montant', 'Date', 'Mode'],
    $tableData,
    [
        'mobileTitle' => 0, 'mobileSubtitle' => 1, 'mobileFields' => [2 => 'Facture', 3 => 'Montant', 4 => 'Date', 5 => 'Mode'],
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun paiement enregistré.'
    ]
);

$facturesOptions = [];
foreach ($facturesImpayees as $f) {
    $facturesOptions[$f['id_facture_f']] = $f['numero_facture'] . ' - ' . $f['fournisseur_nom'] . ' (' . number_format($f['montant_ttc'], 0, ',', ' ') . ' FCFA)';
}
$fournisseursOptions = array_combine(
    array_map(fn($f) => $f['id_fournisseur'], $fournisseurs),
    array_map(fn($f) => $f['nom'], $fournisseurs)
);
$modesPaiement = [
    'espece' => 'Espèces',
    'cheque' => 'Chèque',
    'virement' => 'Virement',
    'mobile_money' => 'Mobile Money',
    'carte' => 'Carte'
];
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('[name="id_facture_f"]').addEventListener('change', function() {
        const factureId = this.value;
        if (!factureId) return;
        const factures = <?= json_encode($facturesImpayees) ?>;
        const facture = factures.find(f => f.id_facture_f == factureId);
        if (facture) {
            const fournisseurSelect = document.querySelector('[name="id_fournisseur"]');
            if (fournisseurSelect) fournisseurSelect.value = facture.id_fournisseur;
            const montantInput = document.querySelector('[name="montant"]');
            if (montantInput) montantInput.value = facture.montant_ttc;
        }
    });
});
</script>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=paiement_fourn" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_facture_f', 'Facture à payer', $facturesOptions, null, null, ['required' => 'required'], 'Sélectionner une facture') . '
    ' . renderSelect('id_fournisseur', 'Fournisseur', $fournisseursOptions, null, null, ['required' => 'required'], 'Sélectionner') . '
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('montant', 'Montant (FCFA)', 'number', '', null, ['required' => 'required', 'step' => '0.01', 'min' => '0']) . '
        ' . renderSelect('mode_paiement', 'Mode de paiement', $modesPaiement, 'espece', null, ['required' => 'required']) . '
    </div>
    ' . renderInput('date_paiement', 'Date de paiement', 'date', date('Y-m-d')) . '
    ' . renderTextarea('observations', 'Observations', '') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer le paiement', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouveau paiement fournisseur', $createBody, null, 'default');
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
