<?php
$title = "Factures fournisseurs";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'Factures fournisseurs']
]);
ob_start();
?>
<?= renderPageHeader(
    'Factures fournisseurs',
    'Gérer les factures reçues des fournisseurs',
    checkRightIfLogged('creer_facture_fournisseur') ? renderButton('Nouvelle facture', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?= renderFilterBar('facture_fourn', [
    ['search', 'search', 'N° facture, fournisseur'],
    ['select', 'statut', 'Statut', ['impayee' => 'Impayée', 'partielle' => 'Partielle', 'payee' => 'Payée', 'annulee' => 'Annulée']],
]) ?>

<?php
$statutBadges = [
    'impayee' => 'danger',
    'partielle' => 'warning',
    'payee' => 'success',
    'annulee' => 'neutral'
];

$actionsRenderer = function($row, $rowIndex) use ($factures) {
    $f = $factures[$rowIndex] ?? null;
    if (!$f) return '';
    $actions = '';
    if (checkRightIfLogged('modifier_facture_fournisseur') && $f['statut'] === 'impayee') {
        $actions .= renderButton('', 'icon', '', [
            'icon' => 'fa-edit', 'title' => 'Modifier',
            'data-modal-toggle' => 'editModal', 'data-edit-ff' => $f['id_facture_f']
        ]);
    }
    if (checkRightIfLogged('imprimer_facture_fournisseur')) {
        $actions .= renderButton('', 'icon', '?action=facture_fourn&print=' . $f['id_facture_f'], [
            'icon' => 'fa-print', 'title' => 'Imprimer'
        ]);
    }
    if (checkRightIfLogged('supprimer_facture_fournisseur') && $f['statut'] === 'impayee') {
        $actions .= renderButton('', 'icon', '?action=facture_fourn&delete=' . $f['id_facture_f'], [
            'icon' => 'fa-trash', 'title' => 'Supprimer',
            'data-confirm' => 'Supprimer cette facture ?',
            'data-confirm-type' => 'danger'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($f) use ($statutBadges) {
    return [
        $f['numero_facture'],
        htmlspecialchars($f['fournisseur_nom']),
        htmlspecialchars($f['commande_ref'] ?? '-'),
        date('d/m/Y', strtotime($f['date_facture'])),
        number_format($f['montant_ht'], 0, ',', ' ') . ' FCFA',
        number_format($f['montant_ttc'], 0, ',', ' ') . ' FCFA',
        renderBadge(ucfirst($f['statut']), $statutBadges[$f['statut']] ?? 'neutral')
    ];
}, $factures);

echo renderResponsiveTable(
    ['N° facture', 'Fournisseur', 'Commande', 'Date', 'Montant HT', 'Montant TTC', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0, 'mobileSubtitle' => 1, 'mobileBadge' => 6,
        'mobileFields' => [2 => 'Commande', 3 => 'Date', 4 => 'HT', 5 => 'TTC'],
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune facture trouvée.'
    ]
);

$fournisseursOptions = array_combine(
    array_map(fn($f) => $f['id_fournisseur'], $fournisseurs),
    array_map(fn($f) => $f['nom'], $fournisseurs)
);
$commandesOptions = ['' => 'Aucune (sans commande)'] + array_combine(
    array_map(fn($c) => $c['id_bcf'], $commandes),
    array_map(fn($c) => $c['reference'], $commandes)
);
$produitsJson = json_encode(array_map(fn($p) => [
    'id' => $p['id_produit'], 'nom' => $p['nom_produit'],
    'prix' => $p['prix_achat'], 'unite' => $p['unite']
], $produits));
$facturesLignesJson = json_encode($lignesParFacture);
$lignesBcfFactureJson = json_encode($lignesBcfDispo);
?>

<script>
const PRODUITS_FACTURE = <?= $produitsJson ?>;
const FACTURES_LIGNES = <?= $facturesLignesJson ?>;
const COMMANDES_BCF_FACTURE = <?= $lignesBcfFactureJson ?>;

function ligneFactureTemplate(prefix, index, data = {}, readonly = false) {
    let options = '<option value="">-- Produit --</option>';
    PRODUITS_FACTURE.forEach(p => {
        const selected = data.id_produit == p.id ? 'selected' : '';
        options += `<option value="${p.id}" data-prix="${p.prix}" ${selected}>${p.nom}</option>`;
    });
    const qte = data.quantite || '';
    const prix = data.prix_unitaire || '';
    const roAttr = readonly ? 'readonly disabled' : '';
    const selDis = readonly ? 'disabled' : '';
    return `<div class="flex flex-wrap gap-2 items-end border border-neutral-90 rounded-lg p-3 ligne-produit">
        <div class="flex-1 min-w-[150px]">
            <select name="id_produit[]" class="form-select" onchange="majPrixFacture(this)" required ${selDis}>${options}</select>
        </div>
        <div class="w-24">
            <input type="number" name="quantite[]" class="form-input" placeholder="Qté" step="0.001" min="0.001" value="${qte}" onchange="calcFactTotal('${prefix}')" required ${roAttr}>
        </div>
        <div class="w-28">
            <input type="number" name="prix_unitaire[]" class="form-input" placeholder="Prix unit." step="0.01" min="0" value="${prix}" onchange="calcFactTotal('${prefix}')" required ${roAttr}>
        </div>
        ${!readonly ? `<button type="button" class="btn-icon-danger" onclick="this.closest('.ligne-produit').remove(); calcFactTotal('${prefix}')"><i class="fas fa-trash"></i></button>` : ''}
    </div>`;
}

function ajouterLigneFacture(prefix, data = {}, readonly = false) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    const div = document.createElement('div');
    div.innerHTML = ligneFactureTemplate(prefix, container.children.length, data, readonly);
    container.appendChild(div.firstElementChild);
}

function majPrixFacture(select) {
    const option = select.options[select.selectedIndex];
    const prix = option.getAttribute('data-prix');
    const row = select.closest('.ligne-produit');
    const prixInput = row.querySelector('input[name="prix_unitaire[]"]');
    if (prix && !prixInput.value) {
        prixInput.value = prix;
    }
    const prefix = row.closest('[id^="lignesContainer-"]').id.replace('lignesContainer-', '');
    calcFactTotal(prefix);
}

function calcFactTotal(prefix) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    let total = 0;
    container.querySelectorAll('.ligne-produit').forEach(row => {
        const qte = parseFloat(row.querySelector('input[name="quantite[]"]').value) || 0;
        const prix = parseFloat(row.querySelector('input[name="prix_unitaire[]"]').value) || 0;
        total += qte * prix;
    });
    const el = document.getElementById(`ht-${prefix}`);
    if (el) el.value = total.toFixed(2);
    updateTTC(prefix);
}

function updateTTC(prefix) {
    const ht = parseFloat(document.getElementById(`ht-${prefix}`).value) || 0;
    const tvaInput = document.getElementById(`tva-${prefix}`);
    const tvaCheck = document.getElementById(`appliquer_tva-${prefix}`);
    let taux = 0;
    if (tvaCheck && tvaCheck.checked) {
        taux = parseFloat(tvaInput.value) || 0;
    }
    document.getElementById(`ttc-${prefix}`).textContent = (ht * (1 + taux / 100)).toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function toggleTva(prefix) {
    const tvaInput = document.getElementById(`tva-${prefix}`);
    const tvaCheck = document.getElementById(`appliquer_tva-${prefix}`);
    if (tvaCheck.checked) {
        tvaInput.disabled = false;
        tvaInput.classList.remove('opacity-50');
    } else {
        tvaInput.disabled = true;
        tvaInput.classList.add('opacity-50');
        tvaInput.value = '0';
    }
    updateTTC(prefix);
}

function chargerLignesBCFFacture(prefix) {
    const select = document.getElementById(`${prefix}_id_bcf`);
    const idBcf = select.value;
    const container = document.getElementById(`lignesContainer-${prefix}`);
    const btnAdd = document.getElementById(`btn-add-produit-${prefix}`);
    container.innerHTML = '';

    if (idBcf && COMMANDES_BCF_FACTURE[idBcf]) {
        const lignes = COMMANDES_BCF_FACTURE[idBcf];
        lignes.forEach(l => {
            ajouterLigneFacture(prefix, {
                id_produit: l.id_produit,
                quantite: l.qte_commandee,
                prix_unitaire: l.prix_unitaire
            }, false);
        });
        // Pre-fill HT = sum of lignes
        let total = 0;
        lignes.forEach(l => { total += l.qte_commandee * l.prix_unitaire; });
        document.getElementById(`ht-${prefix}`).value = total.toFixed(2);
        updateTTC(prefix);
        if (btnAdd) btnAdd.style.display = 'none';
    } else {
        ajouterLigneFacture(prefix);
        if (btnAdd) btnAdd.style.display = 'inline-flex';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Init TVA toggle
    ['create', 'edit'].forEach(p => {
        const chk = document.getElementById(`appliquer_tva-${p}`);
        if (chk) { chk.checked = true; toggleTva(p); }
    });

    ajouterLigneFacture('create');

    document.querySelectorAll('[data-edit-ff]').forEach(btn => {
        btn.addEventListener('click', function() {
            const idFf = this.getAttribute('data-edit-ff');
            const lignes = FACTURES_LIGNES[idFf] || [];
            document.getElementById('edit_id_facture_f').value = idFf;
            const container = document.getElementById('lignesContainer-edit');
            container.innerHTML = '';
            lignes.forEach(l => ajouterLigneFacture('edit', l));
            calcFactTotal('edit');
        });
    });
});
</script>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=facture_fourn" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderSelect('id_fournisseur', 'Fournisseur', $fournisseursOptions, null, null, ['required' => 'required'], 'Sélectionner') . '
        ' . renderSelect('id_bcf', 'Commande liée', $commandesOptions, null, null, ['id' => 'create_id_bcf', 'onchange' => "chargerLignesBCFFacture('create')"]) . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('numero_facture', 'N° facture', 'text', '', null, ['required' => 'required']) . '
        ' . renderInput('date_facture', 'Date facture', 'date', date('Y-m-d')) . '
    </div>
    <div>
        <label class="form-label">Lignes de facture</label>
        <div id="lignesContainer-create" class="space-y-2"></div>
        <button type="button" id="btn-add-produit-create" class="btn-secondary mt-2" onclick="ajouterLigneFacture(\'create\')"><i class="fas fa-plus mr-2"></i>Ajouter un produit</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('montant_ht', 'Montant HT', 'number', '0', null, ['id' => 'ht-create', 'step' => '0.01', 'min' => '0', 'oninput' => "updateTTC('create')"]) . '
        <div>
            <label class="flex items-center gap-2 mb-1 cursor-pointer">
                <input type="checkbox" name="appliquer_tva" id="appliquer_tva-create" value="1" checked onchange="toggleTva(\'create\')" class="form-checkbox">
                <span class="form-label mb-0 cursor-pointer">Appliquer TVA</span>
            </label>
            ' . renderInput('taux_tva', 'TVA (%)', 'number', '19.25', null, ['id' => 'tva-create', 'step' => '0.01', 'min' => '0', 'oninput' => "updateTTC('create')"]) . '
        </div>
    </div>
    <div class="text-right font-semibold text-body-lg">
        TTC : <span id="ttc-create">0</span> FCFA
    </div>
    ' . renderInput('date_echeance', 'Date d\'échéance', 'date', '') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer la facture', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle facture fournisseur', $createBody, null, 'lg');
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=facture_fourn" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_facture_f" id="edit_id_facture_f">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderSelect('id_fournisseur', 'Fournisseur', $fournisseursOptions, null, null, ['id' => 'edit_id_fournisseur', 'required' => 'required'], 'Sélectionner') . '
        ' . renderSelect('id_bcf', 'Commande liée', $commandesOptions, null, null, ['id' => 'edit_id_bcf']) . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('numero_facture', 'N° facture', 'text', '', null, ['id' => 'edit_numero_facture', 'required' => 'required']) . '
        ' . renderInput('date_facture', 'Date facture', 'date', '', null, ['id' => 'edit_date_facture']) . '
    </div>
    <div>
        <label class="form-label">Lignes de facture</label>
        <div id="lignesContainer-edit" class="space-y-2"></div>
        <button type="button" id="btn-add-produit-edit" class="btn-secondary mt-2" onclick="ajouterLigneFacture(\'edit\')"><i class="fas fa-plus mr-2"></i>Ajouter un produit</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('montant_ht', 'Montant HT', 'number', '0', null, ['id' => 'ht-edit', 'step' => '0.01', 'min' => '0', 'oninput' => "updateTTC('edit')"]) . '
        <div>
            <label class="flex items-center gap-2 mb-1 cursor-pointer">
                <input type="checkbox" name="appliquer_tva" id="appliquer_tva-edit" value="1" checked onchange="toggleTva(\'edit\')" class="form-checkbox">
                <span class="form-label mb-0 cursor-pointer">Appliquer TVA</span>
            </label>
            ' . renderInput('taux_tva', 'TVA (%)', 'number', '19.25', null, ['id' => 'tva-edit', 'step' => '0.01', 'min' => '0', 'oninput' => "updateTTC('edit')"]) . '
        </div>
    </div>
    <div class="text-right font-semibold text-body-lg">
        TTC : <span id="ttc-edit">0</span> FCFA
    </div>
    ' . renderInput('date_echeance', 'Date d\'échéance', 'date', '', null, ['id' => 'edit_date_echeance']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier la facture', $editBody, null, 'lg');
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
