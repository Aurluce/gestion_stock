<?php
$title = "Bons de livraison";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'Bons de livraison']
]);
ob_start();
?>
<?= renderPageHeader(
    'Bons de livraison',
    'Livrer les commandes clients',
    checkRightIfLogged('livrer_commande') ? renderButton('Nouvelle livraison', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?php
$statutBadges = [
    'en_cours' => 'info',
    'livre'    => 'success',
    'partiel'  => 'warning',
    'annule'   => 'danger'
];

$actionsRenderer = function($row, $rowIndex) use ($livraisons) {
    $bl = $livraisons[$rowIndex] ?? null;
    if (!$bl) return '';
    $actions = '';
    if (checkRightIfLogged('imprimer_bon_livraison')) {
        $actions .= renderButton('', 'icon', '?action=bon_livraison&print=' . $bl['id_bl'], [
            'icon' => 'fa-print',
            'title' => 'Imprimer'
        ]);
    }
    if (checkRightIfLogged('annuler_livraison') && $bl['statut'] !== 'annule') {
        $actions .= renderButton('', 'icon', '?action=bon_livraison&annuler=' . $bl['id_bl'], [
            'icon' => 'fa-ban',
            'title' => 'Annuler',
            'data-confirm' => 'Annuler cette livraison ?',
            'data-confirm-type' => 'warning'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($bl) use ($statutBadges) {
    return [
        $bl['reference'],
        $bl['cc_reference'],
        htmlspecialchars($bl['client_nom'] . ' ' . ($bl['client_prenom'] ?? '')),
        date('d/m/Y', strtotime($bl['date_livraison'])),
        renderBadge(ucfirst(str_replace('_', ' ', $bl['statut'])), $statutBadges[$bl['statut']] ?? 'neutral')
    ];
}, $livraisons);

echo renderResponsiveTable(
    ['Référence BL', 'Commande', 'Client', 'Date', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 2,
        'mobileBadge' => 4,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun bon de livraison trouvé.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=bon_livraison" id="livraisonForm" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_cc', 'Commande à livrer', array_combine(
        array_map(fn($c) => $c['id_cc'], $commandesLivrables),
        array_map(fn($c) => $c['reference'] . ' - ' . $c['client_nom'] . ' ' . ($c['client_prenom'] ?? ''), $commandesLivrables)
    ), null, null, ['required' => 'required', 'id' => 'select_commande', 'onchange' => 'afficherLignesCommande(this.value)'], 'Sélectionner une commande') . '

    <div id="lignesLivraisonContainer"></div>

    ' . renderCheckbox('livraison_complete', 'Livraison complète (clôture la commande)', false) . '
    ' . renderTextarea('observations', 'Observations', '') . '

    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer la livraison', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle livraison', $createBody, null, 'lg');
?>

<script>
const LIGNES_PAR_COMMANDE = <?= json_encode($lignesParCommande) ?>;

function afficherLignesCommande(idCc) {
    const container = document.getElementById('lignesLivraisonContainer');
    const lignes = LIGNES_PAR_COMMANDE[idCc] || [];

    if (lignes.length === 0) {
        container.innerHTML = '<p class="text-body text-neutral-50">Aucun produit à livrer pour cette commande.</p>';
        return;
    }

    let html = '<label class="form-label">Produits à livrer</label><div class="space-y-2">';
    lignes.forEach(l => {
        const restant = parseFloat(l.qte_restante);
        html += `
        <div class="flex flex-wrap gap-2 items-center border border-neutral-90 rounded-lg p-3">
            <div class="flex-1 min-w-[150px]">
                <strong>${l.nom_produit}</strong><br>
                <span class="text-caption text-neutral-50">Commandé: ${parseFloat(l.qte_commandee)} ${l.unite} · Déjà livré: ${parseFloat(l.qte_livree)} ${l.unite} · Restant: ${restant} ${l.unite} · Stock dispo: ${parseFloat(l.stock_actuel)} ${l.unite}</span>
                <input type="hidden" name="id_produit[]" value="${l.id_produit}">
            </div>
            <div class="w-32">
                <input type="number" name="qte_livree[]" class="form-input" placeholder="Qté à livrer" step="0.001" min="0" max="${Math.min(restant, parseFloat(l.stock_actuel))}" value="${restant > 0 ? restant : 0}">
            </div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
