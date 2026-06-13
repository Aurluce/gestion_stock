<?php
$title = "Règlements clients";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'Règlements clients']
]);
ob_start();
?>
<?= renderPageHeader(
    'Règlements clients',
    'Enregistrer et suivre les paiements des factures',
    checkRightIfLogged('enregistrer_reglement_client') ? renderButton('Nouveau règlement', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?php if ($factureSelectionnee): ?>
<div class="card mb-6">
    <div class="card-body">
        <p class="text-body">
            <strong>Facture filtrée :</strong> <?= htmlspecialchars($factureSelectionnee['reference']) ?>
            (<?= number_format($factureSelectionnee['montant_ttc'], 0, ',', ' ') ?> FCFA)
            — <a href="?action=reglement_client" class="text-brand-600">Voir tous les règlements</a>
        </p>
    </div>
</div>
<?php endif; ?>

<?php
$modePaiementLabels = [
    'espece' => 'Espèce',
    'cheque' => 'Chèque',
    'virement' => 'Virement',
    'mobile_money' => 'Mobile Money',
    'carte' => 'Carte'
];

$actionsRenderer = function($row, $rowIndex) use ($reglements) {
    $r = $reglements[$rowIndex] ?? null;
    if (!$r) return '';
    $actions = '';
    if (checkRightIfLogged('imprimer_recu_client')) {
        $actions .= renderButton('', 'icon', '?action=reglement_client&print=' . $r['id_reglement'], [
            'icon' => 'fa-print',
            'title' => 'Imprimer reçu'
        ]);
    }
    if (checkRightIfLogged('supprimer_reglement_client')) {
        $actions .= renderButton('', 'icon', '?action=reglement_client&delete=' . $r['id_reglement'], [
            'icon' => 'fa-trash',
            'title' => 'Supprimer',
            'data-confirm' => 'Supprimer ce règlement ? Le statut de la facture sera recalculé.'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($r) use ($modePaiementLabels) {
    return [
        $r['facture_reference'],
        htmlspecialchars($r['client_nom'] . ' ' . ($r['client_prenom'] ?? '')),
        date('d/m/Y', strtotime($r['date_reglement'])),
        number_format($r['montant'], 0, ',', ' ') . ' FCFA',
        $modePaiementLabels[$r['mode_paiement']] ?? $r['mode_paiement'],
        htmlspecialchars($r['reference'] ?? '-')
    ];
}, $reglements);

echo renderResponsiveTable(
    ['Facture', 'Client', 'Date', 'Montant', 'Mode de paiement', 'Référence'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun règlement trouvé.'
    ]
);

// Données JS pour pré-remplir le reste à payer
$facturesJson = json_encode(array_map(function($f) {
    return [
        'id' => $f['id_facture'],
        'reste' => $f['reste']
    ];
}, $facturesAPayer));
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=reglement_client" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_facture', 'Facture', array_combine(
        array_map(fn($f) => $f['id_facture'], $facturesAPayer),
        array_map(fn($f) => $f['reference'] . ' - ' . $f['client_nom'] . ' ' . ($f['client_prenom'] ?? '') . ' (Reste : ' . number_format($f['reste'], 0, ',', ' ') . ' FCFA)', $facturesAPayer)
    ), $idFactureFiltre, null, ['required' => 'required', 'id' => 'select_facture', 'onchange' => 'majMontantSuggere(this.value)'], 'Sélectionner une facture') . '
    ' . renderInput('montant', 'Montant payé (FCFA)', 'number', '', null, ['step' => '1', 'min' => '1', 'required' => 'required', 'id' => 'input_montant']) . '
    ' . renderSelect('mode_paiement', 'Mode de paiement', [
        'espece' => 'Espèce',
        'cheque' => 'Chèque',
        'virement' => 'Virement',
        'mobile_money' => 'Mobile Money',
        'carte' => 'Carte'
    ], 'espece', null, ['required' => 'required']) . '
    ' . renderInput('reference', 'Référence (n° chèque, transaction...)', 'text') . '
    ' . renderTextarea('observations', 'Observations', '') . '

    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer le règlement', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouveau règlement', $createBody);
?>

<script>
const FACTURES_RESTE = <?= $facturesJson ?>;

function majMontantSuggere(idFacture) {
    const facture = FACTURES_RESTE.find(f => f.id == idFacture);
    if (facture) {
        document.getElementById('input_montant').value = parseFloat(facture.reste).toFixed(0);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('select_facture');
    if (select && select.value) {
        majMontantSuggere(select.value);
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
