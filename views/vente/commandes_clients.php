<?php
$title = "Commandes clients";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'Commandes clients']
]);
ob_start();
?>
<?= renderPageHeader(
    'Commandes clients',
    'Gérer les bons de commande des clients',
    checkRightIfLogged('creer_commande_client') ? renderButton('Nouvelle commande', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) : null
) ?>

<?= renderFilterBar('commande_client', [
    ['search', 'search', 'Référence, client'],
    ['select', 'statut', 'Statut', ['en_attente' => 'En attente', 'en_cours' => 'En cours', 'livree' => 'Livrée', 'facturee' => 'Facturée', 'reglee' => 'Réglée', 'annulee' => 'Annulée']],
]) ?>

<?php
$statutBadges = [
    'en_attente' => 'neutral',
    'en_cours'   => 'info',
    'livree'     => 'warning',
    'facturee'   => 'success',
    'reglee'     => 'success',
    'annulee'    => 'danger'
];

$actionsRenderer = function($row, $rowIndex) use ($commandes) {
    $cmd = $commandes[$rowIndex] ?? null;
    if (!$cmd) return '';
    $actions = '';
    if (checkRightIfLogged('modifier_commande_client') && $cmd['statut'] === 'en_cours') {
        $actions .= renderButton('', 'icon', '', [
            'icon' => 'fa-edit',
            'title' => 'Modifier',
            'data-modal-toggle' => 'editModal',
            'data-edit-cc' => $cmd['id_cc']
        ]);
    }
    if (checkRightIfLogged('imprimer_bon_commande_client')) {
        $actions .= renderButton('', 'icon', '?action=commande_client&print=' . $cmd['id_cc'], [
            'icon' => 'fa-print',
            'title' => 'Imprimer'
        ]);
    }
    if (checkRightIfLogged('annuler_commande_client') && $cmd['statut'] === 'en_cours') {
        $actions .= renderButton('', 'icon', '?action=commande_client&annuler=' . $cmd['id_cc'], [
            'icon' => 'fa-ban',
            'title' => 'Annuler',
            'data-confirm' => 'Annuler cette commande ?',
            'data-confirm-type' => 'warning'
        ]);
    }
    if (checkRightIfLogged('supprimer_commande_client') && $cmd['statut'] === 'en_cours') {
        $actions .= renderButton('', 'icon', '?action=commande_client&delete=' . $cmd['id_cc'], [
            'icon' => 'fa-trash',
            'title' => 'Supprimer',
            'data-confirm' => 'Supprimer cette commande ?',
            'data-confirm-type' => 'danger'
        ]);
    }
    return $actions;
};

$tableData = array_map(function($cmd) use ($statutBadges) {
    return [
        $cmd['reference'],
        htmlspecialchars($cmd['client_nom'] . ' ' . ($cmd['client_prenom'] ?? '')),
        date('d/m/Y', strtotime($cmd['date_commande'])),
        number_format($cmd['montant_total'], 0, ',', ' ') . ' FCFA',
        $cmd['type_vente'] === 'comptant' ? renderBadge('Comptant', 'info') : renderBadge('Crédit', 'neutral'),
        renderBadge(ucfirst(str_replace('_', ' ', $cmd['statut'])), $statutBadges[$cmd['statut']] ?? 'neutral')
    ];
}, $commandes);

echo renderResponsiveTable(
    ['Référence', 'Client', 'Date', 'Montant total', 'Type', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => 5,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune commande client trouvée.'
    ]
);

// Options pour les selects (utilisées en JS)
$produitsJson = json_encode(array_map(function($p) {
    return [
        'id' => $p['id_produit'],
        'nom' => $p['nom_produit'],
        'prix' => $p['prix_vente'],
        'unite' => $p['unite']
    ];
}, $produits));
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=commande_client" id="createForm" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderSelect('id_client', 'Client', array_combine(
        array_map(fn($c) => $c['id_client'], $clients),
        array_map(fn($c) => $c['nom'] . ' ' . ($c['prenom'] ?? ''), $clients)
    ), null, null, ['required' => 'required'], 'Sélectionner un client') . '
    ' . renderSelect('type_vente', 'Type de vente', ['credit' => 'Crédit', 'comptant' => 'Comptant'], 'credit', null, ['required' => 'required']) . '

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
        ' . renderButton('Créer la commande', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle commande client', $createBody, null, 'lg');
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=commande_client" id="editForm" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_cc" id="edit_id_cc">
    ' . renderSelect('id_client', 'Client', array_combine(
        array_map(fn($c) => $c['id_client'], $clients),
        array_map(fn($c) => $c['nom'] . ' ' . ($c['prenom'] ?? ''), $clients)
    ), null, null, ['id' => 'edit_id_client', 'required' => 'required'], 'Sélectionner un client') . '
    ' . renderSelect('type_vente', 'Type de vente', ['credit' => 'Crédit', 'comptant' => 'Comptant'], null, null, ['id' => 'edit_type_vente', 'required' => 'required']) . '

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
echo renderModal('editModal', 'Modifier la commande', $editBody, null, 'lg');
?>

<script>
const PRODUITS = <?= $produitsJson ?>;
const COMMANDES_LIGNES = <?= json_encode($lignesParCommande) ?>;

function ligneTemplate(prefix, index, data = {}) {
    let options = '<option value="">-- Produit --</option>';
    PRODUITS.forEach(p => {
        const selected = data.id_produit == p.id ? 'selected' : '';
        options += `<option value="${p.id}" data-prix="${p.prix}" data-unite="${p.unite}" ${selected}>${p.nom}</option>`;
    });
    const qte = data.quantite || '';
    const prix = data.prix_unitaire || '';
    const remise = data.taux_remise || 0;
    return `
    <div class="flex flex-wrap gap-2 items-end border border-neutral-90 rounded-lg p-3 ligne-produit">
        <div class="flex-1 min-w-[150px]">
            <select name="id_produit[]" class="form-select" onchange="majPrix(this)" required>${options}</select>
        </div>
        <div class="w-24">
            <input type="number" name="quantite[]" class="form-input" placeholder="Qté" step="0.001" min="0.001" value="${qte}" onchange="calculTotal('${prefix}')" required>
        </div>
        <div class="w-28">
            <input type="number" name="prix_unitaire[]" class="form-input" placeholder="Prix unit." step="0.01" min="0" value="${prix}" onchange="calculTotal('${prefix}')" required>
        </div>
        <div class="w-20">
            <input type="number" name="taux_remise[]" class="form-input" placeholder="Remise %" step="0.01" min="0" max="100" value="${remise}" onchange="calculTotal('${prefix}')">
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
        const qte = parseFloat(row.querySelector('input[name="quantite[]"]').value) || 0;
        const prix = parseFloat(row.querySelector('input[name="prix_unitaire[]"]').value) || 0;
        const remise = parseFloat(row.querySelector('input[name="taux_remise[]"]').value) || 0;
        total += qte * prix * (1 - remise / 100);
    });
    document.getElementById(`total-${prefix}`).textContent = total.toLocaleString('fr-FR');
}

// Initialisation : une ligne vide au chargement pour la création
document.addEventListener('DOMContentLoaded', function() {
    ajouterLigne('create');

    // Gestion de l'édition
    document.querySelectorAll('[data-edit-cc]').forEach(btn => {
        btn.addEventListener('click', function() {
            const idCc = this.getAttribute('data-edit-cc');
            const lignes = COMMANDES_LIGNES[idCc] || [];
            document.getElementById('edit_id_cc').value = idCc;

            const container = document.getElementById('lignesContainer-edit');
            container.innerHTML = '';
            lignes.forEach(l => ajouterLigne('edit', l));
            calculTotal('edit');
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
