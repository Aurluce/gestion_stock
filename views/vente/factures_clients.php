<?php
$title = "Factures clients";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'Factures clients']
]);
ob_start();
?>
<?= renderPageHeader(
    'Factures clients',
    'Gérer la facturation des commandes livrées',
    checkRightIfLogged('creer_facture_client') ? renderButton('Nouvelle facture', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?= renderFilterBar('facture_client', [
    ['select', 'statut', 'Statut', ['impayee' => 'Impayée', 'partielle' => 'Partielle', 'payee' => 'Payée', 'annulee' => 'Annulée']],
]) ?>

<?php
$statutBadges = [
    'impayee'   => 'danger',
    'partielle' => 'warning',
    'payee'     => 'success',
    'annulee'   => 'neutral'
];

$actionsRenderer = function($row, $rowIndex) use ($factures) {
    $f = $factures[$rowIndex] ?? null;
    if (!$f) return '';
    $actions = '';
    if (checkRightIfLogged('imprimer_facture_client')) {
        $actions .= renderButton('', 'icon', '?action=facture_client&print=' . $f['id_facture'], [
            'icon' => 'fa-print',
            'title' => 'Imprimer'
        ]);
    }
    if (checkRightIfLogged('enregistrer_reglement_client') && in_array($f['statut'], ['impayee', 'partielle'])) {
        $actions .= renderButton('', 'icon', '?action=reglement_client&id_facture=' . $f['id_facture'], [
            'icon' => 'fa-money-bill-wave',
            'title' => 'Enregistrer un règlement'
        ]);
    }
    if (checkRightIfLogged('annuler_facture_client') && $f['statut'] === 'impayee') {
        $actions .= renderButton('', 'icon', '?action=facture_client&annuler=' . $f['id_facture'], [
            'icon' => 'fa-ban',
            'title' => 'Annuler',
            'data-confirm' => 'Annuler cette facture ?',
            'data-confirm-type' => 'warning'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($f) use ($statutBadges) {
    $reste = $f['montant_ttc'] - $f['montant_regle'];
    return [
        $f['reference'],
        $f['cc_reference'],
        htmlspecialchars($f['client_nom'] . ' ' . ($f['client_prenom'] ?? '')),
        date('d/m/Y', strtotime($f['date_facture'])),
        number_format($f['montant_ttc'], 0, ',', ' ') . ' FCFA',
        number_format($reste, 0, ',', ' ') . ' FCFA',
        renderBadge(ucfirst($f['statut']), $statutBadges[$f['statut']] ?? 'neutral')
    ];
}, $factures);

echo renderResponsiveTable(
    ['Référence', 'Commande', 'Client', 'Date', 'Montant TTC', 'Reste à payer', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 2,
        'mobileBadge' => 6,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune facture trouvée.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=facture_client" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_cc', 'Commande livrée à facturer', array_combine(
        array_map(fn($c) => $c['id_cc'], $commandesFacturables),
        array_map(fn($c) => $c['reference'] . ' - ' . $c['client_nom'] . ' ' . ($c['client_prenom'] ?? '') . ' (' . number_format($c['montant_total'], 0, ',', ' ') . ' FCFA)', $commandesFacturables)
    ), null, null, ['required' => 'required'], 'Sélectionner une commande') . '
    ' . renderInput('taux_tva', 'Taux TVA (%)', 'number', '19.25', null, ['step' => '0.01', 'min' => '0', 'required' => 'required']) . '
    ' . renderInput('date_echeance', 'Date d\'échéance', 'date') . '

    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer la facture', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle facture', $createBody);
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
