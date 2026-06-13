<?php
$title = "Bons de commande fournisseurs";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'Bons de commande']
]);
ob_start();
?>
<?= renderPageHeader(
    'Bons de commande',
    'Gérer les commandes aux fournisseurs',
    checkRightIfLogged('creer_bcf') ? renderButton('Nouveau bon', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?= renderFilterBar('commande_fourn', [
    ['search', 'search', 'Référence, fournisseur'],
    ['select', 'statut', 'Statut', ['brouillon' => 'Brouillon', 'envoye' => 'Envoyé', 'receptionne' => 'Réceptionné', 'annule' => 'Annulé']],
]) ?>

<?php
$statutBadges = [
    'brouillon' => 'neutral',
    'envoye' => 'info',
    'receptionne' => 'success',
    'annule' => 'danger'
];

$actionsRenderer = function($row, $rowIndex) use ($commandes) {
    $cmd = $commandes[$rowIndex] ?? null;
    if (!$cmd) return '';
    $actions = '';
    if (checkRightIfLogged('lister_bcf')) {
        $actions .= renderButton('', 'icon', '?action=commande_fourn&view=' . $cmd['id_bcf'], [
            'icon' => 'fa-eye', 'title' => 'Voir'
        ]);
    }
    if (checkRightIfLogged('modifier_bcf') && $cmd['statut'] === 'brouillon') {
        $actions .= renderButton('', 'icon', '', [
            'icon' => 'fa-edit', 'title' => 'Modifier',
            'data-modal-toggle' => 'editModal', 'data-edit-bcf' => $cmd['id_bcf']
        ]);
    }
    if (checkRightIfLogged('valider_bcf') && $cmd['statut'] === 'brouillon') {
        $actions .= renderButton('', 'icon', '?action=commande_fourn&valider=' . $cmd['id_bcf'], [
            'icon' => 'fa-check', 'title' => 'Valider',
            'data-confirm' => 'Valider et envoyer cette commande ?',
            'data-confirm-type' => 'success'
        ]);
    }
    if (checkRightIfLogged('imprimer_bcf')) {
        $actions .= renderButton('', 'icon', '?action=commande_fourn&print=' . $cmd['id_bcf'], [
            'icon' => 'fa-print', 'title' => 'Imprimer'
        ]);
    }
    if (checkRightIfLogged('annuler_bcf') && $cmd['statut'] !== 'annule' && $cmd['statut'] !== 'receptionne') {
        $actions .= renderButton('', 'icon', '?action=commande_fourn&annuler=' . $cmd['id_bcf'], [
            'icon' => 'fa-ban', 'title' => 'Annuler',
            'data-confirm' => 'Annuler cette commande ?',
            'data-confirm-type' => 'warning'
        ]);
    }
    if (checkRightIfLogged('supprimer_bcf') && $cmd['statut'] === 'brouillon') {
        $actions .= renderButton('', 'icon', '?action=commande_fourn&delete=' . $cmd['id_bcf'], [
            'icon' => 'fa-trash', 'title' => 'Supprimer',
            'data-confirm' => 'Supprimer cette commande ?',
            'data-confirm-type' => 'danger'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($cmd) use ($statutBadges) {
    return [
        $cmd['reference'],
        htmlspecialchars($cmd['fournisseur_nom']),
        date('d/m/Y', strtotime($cmd['date_commande'])),
        number_format($cmd['montant_total'], 0, ',', ' ') . ' FCFA',
        renderBadge(ucfirst($cmd['statut']), $statutBadges[$cmd['statut']] ?? 'neutral')
    ];
}, $commandes);

echo renderResponsiveTable(
    ['Référence', 'Fournisseur', 'Date', 'Montant total', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => 4,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun bon de commande trouvé.'
    ]
);

$fournisseursJson = json_encode(array_map(fn($f) => ['id' => $f['id_fournisseur'], 'nom' => $f['nom']], $fournisseurs));
$produitsJson = json_encode(array_map(fn($p) => [
    'id' => $p['id_produit'], 'nom' => $p['nom_produit'],
    'prix' => $p['prix_achat'], 'unite' => $p['unite']
], $produits));
$commandesDataJson = json_encode($commandesData);
$lignesJson = json_encode($lignesParCommande);
?>

<script>
const FOURNISSEURS = <?= $fournisseursJson ?>;
const PRODUITS = <?= $produitsJson ?>;
const COMMANDES_LIGNES = <?= $lignesJson ?>;
const COMMANDES_DATA = <?= $commandesDataJson ?>;

function ligneTemplate(prefix, index, data = {}) {
    let options = '<option value="">-- Produit --</option>';
    PRODUITS.forEach(p => {
        const selected = data.id_produit == p.id ? 'selected' : '';
        options += `<option value="${p.id}" data-prix="${p.prix}" data-unite="${p.unite}" ${selected}>${p.nom}</option>`;
    });
    const qte = data.qte_commandee || '';
    const prix = data.prix_unitaire || '';
    return `<div class="flex flex-wrap gap-2 items-end border border-neutral-90 rounded-lg p-3 ligne-produit">
        <div class="flex-1 min-w-[150px]">
            <select name="id_produit[]" class="form-select" onchange="majPrix(this)" required>${options}</select>
        </div>
        <div class="w-24">
            <input type="number" name="qte_commandee[]" class="form-input" placeholder="Qté" step="0.001" min="0.001" value="${qte}" onchange="calculTotal('${prefix}')" required>
        </div>
        <div class="w-28">
            <input type="number" name="prix_unitaire[]" class="form-input" placeholder="Prix unit." step="0.01" min="0" value="${prix}" onchange="calculTotal('${prefix}')" required>
        </div>
        <button type="button" class="btn-icon-danger" onclick="this.closest('.ligne-produit').remove(); calculTotal('${prefix}')"><i class="fas fa-trash"></i></button>
    </div>`;
}

function ajouterLigne(prefix, data = {}) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    const div = document.createElement('div');
    div.innerHTML = ligneTemplate(prefix, container.children.length, data);
    container.appendChild(div.firstElementChild);
}

function majPrix(select) {
    const option = select.options[select.selectedIndex];
    const prix = option.getAttribute('data-prix');
    const row = select.closest('.ligne-produit');
    const prixInput = row.querySelector('input[name="prix_unitaire[]"]');
    if (prix && !prixInput.value) {
        prixInput.value = prix;
    }
    const prefix = row.closest('[id^="lignesContainer-"]').id.replace('lignesContainer-', '');
    calculTotal(prefix);
}

function calculTotal(prefix) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    let total = 0;
    container.querySelectorAll('.ligne-produit').forEach(row => {
        const qte = parseFloat(row.querySelector('input[name="qte_commandee[]"]').value) || 0;
        const prix = parseFloat(row.querySelector('input[name="prix_unitaire[]"]').value) || 0;
        total += qte * prix;
    });
    const el = document.getElementById(`total-${prefix}`);
    if (el) el.textContent = total.toLocaleString('fr-FR');
}

document.addEventListener('DOMContentLoaded', function() {
    ajouterLigne('create');

    document.querySelectorAll('[data-edit-bcf]').forEach(btn => {
        btn.addEventListener('click', function() {
            const idBcf = this.getAttribute('data-edit-bcf');
            const data = COMMANDES_DATA[idBcf] || {};
            const lignes = COMMANDES_LIGNES[idBcf] || [];
            document.getElementById('edit_id_bcf').value = idBcf;
            document.getElementById('edit_id_fournisseur').value = data.id_fournisseur || '';
            document.getElementById('edit_observations').value = data.observations || '';
            const container = document.getElementById('lignesContainer-edit');
            container.innerHTML = '';
            lignes.forEach(l => ajouterLigne('edit', l));
            calculTotal('edit');
        });
    });
});
</script>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=commande_fourn" id="createForm" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_fournisseur', 'Fournisseur', array_combine(
        array_map(fn($f) => $f['id_fournisseur'], $fournisseurs),
        array_map(fn($f) => $f['nom'], $fournisseurs)
    ), null, null, ['required' => 'required'], 'Sélectionner un fournisseur') . '

    <div>
        <label class="form-label">Produits</label>
        <div id="lignesContainer-create" class="space-y-2"></div>
        <button type="button" class="btn-secondary mt-2" onclick="ajouterLigne(\'create\')"><i class="fas fa-plus mr-2"></i>Ajouter un produit</button>
    </div>

    <div class="text-right font-semibold text-body-lg">
        Total : <span id="total-create">0</span> FCFA
    </div>

    ' . renderTextarea('observations', 'Observations', '') . '

    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer le bon', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouveau bon de commande', $createBody, null, 'lg');
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=commande_fourn" id="editForm" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_bcf" id="edit_id_bcf">
    ' . renderSelect('id_fournisseur', 'Fournisseur', array_combine(
        array_map(fn($f) => $f['id_fournisseur'], $fournisseurs),
        array_map(fn($f) => $f['nom'], $fournisseurs)
    ), null, null, ['id' => 'edit_id_fournisseur', 'required' => 'required'], 'Sélectionner un fournisseur') . '

    <div>
        <label class="form-label">Produits</label>
        <div id="lignesContainer-edit" class="space-y-2"></div>
        <button type="button" class="btn-secondary mt-2" onclick="ajouterLigne(\'edit\')"><i class="fas fa-plus mr-2"></i>Ajouter un produit</button>
    </div>

    <div class="text-right font-semibold text-body-lg">
        Total : <span id="total-edit">0</span> FCFA
    </div>

    ' . renderTextarea('observations', 'Observations', '', null, ['id' => 'edit_observations']) . '

    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier le bon de commande', $editBody, null, 'lg');
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
