<?php
$title = "Bons de réception";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'Réceptions']
]);
ob_start();
?>
<?= renderPageHeader(
    'Bons de réception',
    'Enregistrer les réceptions de commandes fournisseurs',
    checkRightIfLogged('creer_reception') ? renderButton('Nouvelle réception', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?= renderFilterBar('reception', [
    ['search', 'search', 'Référence, fournisseur'],
    ['select', 'statut', 'Statut', ['en_attente' => 'En attente', 'partiel' => 'Partiel', 'complet' => 'Complet']],
]) ?>

<?php
$statutBadges = [
    'en_attente' => 'neutral',
    'partiel' => 'warning',
    'complet' => 'success'
];

$actionsRenderer = function($row, $rowIndex) use ($receptions) {
    $r = $receptions[$rowIndex] ?? null;
    if (!$r) return '';
    $actions = '';
    if (checkRightIfLogged('valider_reception') && $r['statut'] === 'en_attente') {
        $actions .= renderButton('', 'icon', '?action=reception&valider=' . $r['id_br'], [
            'icon' => 'fa-check', 'title' => 'Valider',
            'data-confirm' => 'Valider cette réception ? Un bon d\'entrée sera généré.',
            'data-confirm-type' => 'success'
        ]);
    }
    if (checkRightIfLogged('imprimer_bon_reception')) {
        $actions .= renderButton('', 'icon', '?action=reception&print=' . $r['id_br'], [
            'icon' => 'fa-print', 'title' => 'Imprimer'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($r) use ($statutBadges) {
    return [
        $r['reference'],
        htmlspecialchars($r['fournisseur_nom'] ?? '-'),
        htmlspecialchars($r['commande_ref'] ?? '-'),
        date('d/m/Y', strtotime($r['date_reception'])),
        renderBadge(ucfirst(str_replace('_', ' ', $r['statut'])), $statutBadges[$r['statut']] ?? 'neutral')
    ];
}, $receptions);

echo renderResponsiveTable(
    ['Référence', 'Fournisseur', 'Commande liée', 'Date', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0, 'mobileSubtitle' => 1, 'mobileBadge' => 4,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune réception trouvée.'
    ]
);

$commandesOptions = [];
foreach ($commandesDispo as $c) {
    $commandesOptions[$c['id_bcf']] = $c['reference'] . ' - ' . $c['fournisseur_nom'];
}
$produitsJson = json_encode(array_map(fn($p) => [
    'id' => $p['id_produit'], 'nom' => $p['nom_produit'],
    'prix' => $p['prix_achat'], 'unite' => $p['unite']
], $produits));
$lignesBcfJson = json_encode($lignesBcfDispo);
?>

<script>
const PRODUITS_RECEPTION = <?= $produitsJson ?>;
const COMMANDES_BCF_RECEPTION = <?= $lignesBcfJson ?>;

function ligneReceptionTemplate(prefix, index, data = {}, bcfMode = false) {
    let options = '<option value="">-- Produit --</option>';
    PRODUITS_RECEPTION.forEach(p => {
        const selected = data.id_produit == p.id ? 'selected' : '';
        options += `<option value="${p.id}" data-prix="${p.prix}" ${selected}>${p.nom}</option>`;
    });
    const qte = data.qte_recue || '';
    const prix = data.prix_unitaire || '';
    const etat = data.etat_produit || 'bon';
    const maxAttr = data.max_qte ? `max="${data.max_qte}"` : '';
    // En mode BCF : champs fixes en visuel + hidden fields pour soumission, qte_recue seul editable
    let hidden = '';
    if (bcfMode && data.id_produit) {
        hidden = `<input type="hidden" name="id_produit[]" value="${data.id_produit}">
<input type="hidden" name="prix_unitaire[]" value="${prix}">
<input type="hidden" name="etat_produit[]" value="${etat}">`;
    }
    return `<div class="flex flex-wrap gap-2 items-end border border-neutral-90 rounded-lg p-3 ligne-produit">
        ${hidden}
        <div class="flex-1 min-w-[150px]">
            <select name="id_produit_display[]" class="form-select opacity-60 pointer-events-none" ${bcfMode ? 'disabled' : ''} onchange="majPrixReception(this)" required>${options}</select>
        </div>
        <div class="w-24">
            <input type="number" name="qte_recue[]" class="form-input" placeholder="Qté" step="0.001" min="0.001" value="${qte}" ${maxAttr} required>
        </div>
        <div class="w-28">
            <input type="number" name="prix_unitaire_display[]" class="form-input opacity-60 ${bcfMode ? 'pointer-events-none' : ''}" placeholder="Prix unit." step="0.01" min="0" value="${prix}" ${bcfMode ? 'readonly' : ''} required>
        </div>
        <div class="w-28">
            <select name="etat_produit_display[]" class="form-select opacity-60 pointer-events-none" ${bcfMode ? 'disabled' : ''}>
                <option value="bon" ${etat === 'bon' ? 'selected' : ''}>Bon</option>
                <option value="abime" ${etat === 'abime' ? 'selected' : ''}>Abîmé</option>
                <option value="perime" ${etat === 'perime' ? 'selected' : ''}>Périmé</option>
                <option value="a_verifier" ${etat === 'a_verifier' ? 'selected' : ''}>À vérifier</option>
            </select>
        </div>
        ${!bcfMode ? `<button type="button" class="btn-icon-danger" onclick="this.closest('.ligne-produit').remove()"><i class="fas fa-trash"></i></button>` : ''}
    </div>`;
}

function ajouterLigneReception(prefix, data = {}, bcfMode = false) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    const div = document.createElement('div');
    div.innerHTML = ligneReceptionTemplate(prefix, container.children.length, data, bcfMode);
    container.appendChild(div.firstElementChild);
}

function majPrixReception(select) {
    const option = select.options[select.selectedIndex];
    const prix = option.getAttribute('data-prix');
    const row = select.closest('.ligne-produit');
    const prixInput = row.querySelector('input[name="prix_unitaire[]"]');
    if (prix && !prixInput.value) {
        prixInput.value = prix;
    }
}

function chargerLignesBCF() {
    const select = document.getElementById('id_bcf_reception');
    const idBcf = select.value;
    const container = document.getElementById('lignesContainer-create');
    container.innerHTML = '';

    if (idBcf && COMMANDES_BCF_RECEPTION[idBcf]) {
        // Mode BCF lié — charger lignes fixes + qte_recue editable
        const lignes = COMMANDES_BCF_RECEPTION[idBcf];
        lignes.forEach(l => {
            ajouterLigneReception('create', {
                id_produit: l.id_produit,
                qte_recue: '',
                prix_unitaire: l.prix_unitaire,
                max_qte: l.qte_commandee,
                etat_produit: 'bon'
            }, true);
        });
        document.getElementById('btn-add-produit-reception').style.display = 'none';
    } else {
        // Mode sans BCF — saisie libre
        ajouterLigneReception('create');
        document.getElementById('btn-add-produit-reception').style.display = 'inline-flex';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    ajouterLigneReception('create');
});
</script>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=reception" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_bcf', 'Commande liée', $commandesOptions, null, null, ['id' => 'id_bcf_reception', 'onchange' => 'chargerLignesBCF()'], 'Sans commande (réception directe)') . '

    <div>
        <label class="form-label">Produits reçus</label>
        <div id="lignesContainer-create" class="space-y-2"></div>
        <button type="button" id="btn-add-produit-reception" class="btn-secondary mt-2" onclick="ajouterLigneReception(\'create\')"><i class="fas fa-plus mr-2"></i>Ajouter un produit</button>
    </div>

    ' . renderTextarea('observations', 'Observations', '') . '

    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle réception', $createBody, null, 'lg');
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
