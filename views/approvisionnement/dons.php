<?php
$title = "Dons";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'Dons']
]);
ob_start();
?>
<?= renderPageHeader(
    'Dons reçus',
    'Enregistrer les dons de produits',
    checkRightIfLogged('saisir_don') ? renderButton('Nouveau don', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?php
// Build don lines data for display
$donsArr = array_map(function($d) use ($donsLignes) {
    return [
        'don' => $d,
        'lignes' => $donsLignes[$d['id_don']] ?? []
    ];
}, $dons);

$actionsRenderer = function($row, $rowIndex) use ($donsArr) {
    $d = $donsArr[$rowIndex]['don'] ?? null;
    if (!$d) return '';
    $actions = '';
    if (checkRightIfLogged('modifier_don')) {
        $actions .= renderButton('', 'icon', '', [
            'icon' => 'fa-edit', 'title' => 'Modifier',
            'data-modal-toggle' => 'editModal', 'data-edit-don' => $d['id_don']
        ]);
    }
    if (checkRightIfLogged('supprimer_don')) {
        $actions .= renderButton('', 'icon', '?action=don&delete=' . $d['id_don'], [
            'icon' => 'fa-trash', 'title' => 'Supprimer',
            'data-confirm' => 'Supprimer ce don ?',
            'data-confirm-type' => 'danger'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($item) {
    $d = $item['don'];
    $nbProduits = count($item['lignes']);
    return [
        htmlspecialchars($d['donateur']),
        htmlspecialchars($d['contact_donateur'] ?? '-'),
        date('d/m/Y', strtotime($d['date_don'])),
        $nbProduits > 0 ? $nbProduits . ' produit(s)' : '-',
        htmlspecialchars($d['description'] ?? '-')
    ];
}, $donsArr);

echo renderResponsiveTable(
    ['Donateur', 'Contact', 'Date', 'Produits', 'Description'],
    $tableData,
    [
        'mobileTitle' => 0, 'mobileSubtitle' => 2, 'mobileFields' => [1 => 'Contact', 3 => 'Produits', 4 => 'Description'],
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun don enregistré.'
    ]
);

$donsJson = json_encode($dons);
$produitsJson = json_encode(array_map(fn($p) => ['id' => $p['id_produit'], 'nom' => $p['nom_produit'], 'unite' => $p['unite']], $produits));
?>

<script>
const DONS = <?= $donsJson ?>;
const PRODUITS_DON = <?= $produitsJson ?>;

function ligneDonTemplate(prefix, index, data = {}) {
    let options = '<option value="">-- Produit --</option>';
    PRODUITS_DON.forEach(p => {
        const selected = data.id_produit == p.id ? 'selected' : '';
        options += `<option value="${p.id}" ${selected}>${p.nom}</option>`;
    });
    const qte = data.quantite || '';
    return `<div class="flex flex-wrap gap-2 items-end border border-neutral-90 rounded-lg p-3 ligne-produit">
        <div class="flex-1 min-w-[150px]">
            <select name="id_produit[]" class="form-select" required>${options}</select>
        </div>
        <div class="w-24">
            <input type="number" name="quantite[]" class="form-input" placeholder="Qté" step="0.001" min="0.001" value="${qte}" required>
        </div>
        <button type="button" class="btn-icon-danger" onclick="this.closest('.ligne-produit').remove()"><i class="fas fa-trash"></i></button>
    </div>`;
}

function ajouterLigneDon(prefix, data = {}) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    const div = document.createElement('div');
    div.innerHTML = ligneDonTemplate(prefix, container.children.length, data);
    container.appendChild(div.firstElementChild);
}

document.addEventListener('DOMContentLoaded', function() {
    ajouterLigneDon('create');

    document.querySelectorAll('[data-edit-don]').forEach(btn => {
        btn.addEventListener('click', function() {
            const idDon = this.getAttribute('data-edit-don');
            const don = DONS.find(d => d.id_don == idDon);
            if (!don) return;
            document.getElementById('edit_id_don').value = don.id_don;
            document.getElementById('edit_donateur').value = don.donateur || '';
            document.getElementById('edit_contact_donateur').value = don.contact_donateur || '';
            document.getElementById('edit_date_don').value = don.date_don || '';
            document.getElementById('edit_description').value = don.description || '';
        });
    });
});
</script>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=don" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('donateur', 'Donateur', 'text', '', null, ['required' => 'required']) . '
        ' . renderInput('contact_donateur', 'Contact', 'text', '') . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('date_don', 'Date du don', 'date', date('Y-m-d')) . '
    </div>
    ' . renderTextarea('description', 'Description', '') . '
    <div>
        <label class="form-label">Produits reçus</label>
        <div id="lignesContainer-create" class="space-y-2"></div>
        <button type="button" class="btn-secondary mt-2" onclick="ajouterLigneDon(\'create\')"><i class="fas fa-plus mr-2"></i>Ajouter un produit</button>
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer le don', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouveau don', $createBody, null, 'lg');
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=don" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_don" id="edit_id_don">
    ' . renderInput('donateur', 'Donateur', 'text', '', null, ['id' => 'edit_donateur', 'required' => 'required']) . '
    ' . renderInput('contact_donateur', 'Contact', 'text', '', null, ['id' => 'edit_contact_donateur']) . '
    ' . renderInput('date_don', 'Date du don', 'date', '', null, ['id' => 'edit_date_don']) . '
    ' . renderTextarea('description', 'Description', '', null, ['id' => 'edit_description']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier le don', $editBody, null, 'default');
?>

<!-- entreeModal supprimé (remplacé par création directe dans le formulaire create) -->

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
