<?php
$title = "Produits";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Produits']
]);
$mode = $_GET['mode'] ?? 'liste';
$familleSelectionnee = (int)($_GET['id_famille'] ?? 0);
$pageActions = renderButton('Nouveau produit', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']);
echo renderPageHeader('Produits', 'Gérez votre catalogue de produits', $pageActions);
?>


<div class="flex flex-wrap items-center gap-4 mb-4">
    <div class="flex items-center gap-2">
        <span class="text-sm text-neutral-50">Affichage :</span>
        <select class="filter-select" onchange="window.location.href='?action=produits&mode='+this.value">
            <option value="liste" <?= $mode == 'liste' ? 'selected' : '' ?>> Liste simple</option>
            <option value="par_famille" <?= $mode == 'par_famille' ? 'selected' : '' ?>> Par famille</option>
        </select>
    </div>
</div>

<?php if ($mode == 'liste'): ?>
    <?= renderFilterBar('produits', [
        ['search', 'search', 'Rechercher par nom...'],
        ['select', 'id_famille', 'Famille', $famillesSelect, 'Toutes les familles'],
    ]) ?>
<?php endif; ?>

<?php
$produitRow = function($p) {
    return [
        htmlspecialchars($p['nom_produit']),
        htmlspecialchars($p['nom_produit_pere'] ?? '-'),
        number_format($p['prix_vente'], 0) . ' FCFA',
        number_format($p['stock_actuel'], 2),
        $p['est_actif'] ? renderBadge('Actif', 'success') : renderBadge('Inactif', 'danger'),
    ];
};
?>

<?php if ($mode == 'par_famille'): ?>
    <?php if (empty($produitsParFamille)): ?>
        <?= renderEmptyState('fa-box-open', 'Aucun produit', 'Commencez par ajouter votre premier produit.', renderButton('Créer un produit', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($produitsParFamille as $famille): ?>
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-body-lg font-semibold text-neutral-14">
                            <i class="fas fa-folder text-brand-600 mr-2"></i>
                            <?= htmlspecialchars($famille['nom_famille']) ?>
                            <span class="badge-neutral ml-2"><?= count($famille['produits']) ?> produit(s)</span>
                        </h2>
                    </div>
                    <div class="card-body p-0">
                        <?php
                        $familleRows = array_map($produitRow, $famille['produits']);
                        $familleActions = function($row, $rowIndex) use ($famille) {
                            $p = $famille['produits'][$rowIndex] ?? null;
                            if (!$p) return '';
                            return renderButton('', 'icon', '', ['icon' => 'fa-eye', 'title' => 'Voir', 'data-detail' => '?action=produit_detail&id=' . $p['id_produit'], 'data-detail-title' => 'Produit : ' . $p['nom_produit']]) .
                                   renderButton('', 'icon', '', ['icon' => 'fa-edit', 'title' => 'Modifier', 'data-modal-toggle' => 'editModal', 'data-edit-prod' => $p['id_produit']]) .
                                   renderButton('', 'icon-danger', '?action=produit_supprimer&id=' . $p['id_produit'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce produit ?', 'data-confirm-type' => 'danger']);
                        };
                        echo renderResponsiveTable(
                            ['Nom', 'Produit père', 'Prix vente', 'Stock', 'Statut'],
                            $familleRows,
                            [
                                'mobileTitle' => 0,
                                'mobileSubtitle' => 1,
                                'mobileBadge' => 4,
                                'mobileHidden' => [2, 3],
                                'actions' => $familleActions,
                                'emptyMessage' => 'Aucun produit dans cette famille.'
                            ]
                        );
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?php
    $produitActionsRenderer = function($row, $rowIndex) use ($produitsListe) {
        $p = $produitsListe[$rowIndex] ?? null;
        if (!$p) return '';
        $actions = renderButton('', 'icon', '', ['icon' => 'fa-eye', 'title' => 'Voir', 'data-detail' => '?action=produit_detail&id=' . $p['id_produit'], 'data-detail-title' => 'Produit : ' . $p['nom_produit']]);
        $actions .= renderButton('', 'icon', '', ['icon' => 'fa-edit', 'title' => 'Modifier', 'data-modal-toggle' => 'editModal', 'data-edit-prod' => $p['id_produit']]);
        if ($p['est_actif']) {
            $actions .= renderButton('', 'icon', '?action=produit_desactiver&id=' . $p['id_produit'], ['icon' => 'fa-toggle-on', 'title' => 'Désactiver', 'data-confirm' => 'Désactiver ce produit ?', 'data-confirm-type' => 'warning']);
        } else {
            $actions .= renderButton('', 'icon', '?action=produit_activer&id=' . $p['id_produit'], ['icon' => 'fa-toggle-off', 'title' => 'Activer', 'data-confirm' => 'Activer ce produit ?', 'data-confirm-type' => 'success']);
        }
        $actions .= renderButton('', 'icon-danger', '?action=produit_supprimer&id=' . $p['id_produit'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce produit ?', 'data-confirm-type' => 'danger']);
        return $actions;
    };

    if (empty($produitsListe)): ?>
        <?= renderEmptyState('fa-box-open', 'Aucun produit', 'Commencez par ajouter votre premier produit.', renderButton('Créer un produit', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
    <?php else:
        $listeRows = array_map($produitRow, $produitsListe);
        echo renderResponsiveTable(
            ['Nom', 'Produit père', 'Prix vente', 'Stock', 'Statut'],
            $listeRows,
            [
                'mobileTitle' => 0,
                'mobileSubtitle' => 1,
                'mobileBadge' => 4,
                'mobileHidden' => [2, 3],
                'actions' => $produitActionsRenderer,
                'emptyMessage' => 'Aucun produit trouvé.'
            ]
        );
    endif; ?>
<?php endif; ?>

<?php
$familleOpts = '<option value="">-- Sélectionner une famille --</option>';
foreach ($famillesSelect as $id => $nom) {
    $familleOpts .= '<option value="' . $id . '">' . htmlspecialchars($nom) . '</option>';
}

$uniteOpts = '';
$unites = ['pce' => 'Pièce (pce)', 'kg' => 'Kilogramme (kg)', 'l' => 'Litre (l)', 'm' => 'Mètre (m)', 'carton' => 'Carton'];
foreach ($unites as $val => $lbl) {
    $uniteOpts .= '<option value="' . $val . '">' . $lbl . '</option>';
}

$createBody = '
<form method="POST" action="?action=produits" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Famille <span class="text-red-500">*</span></label>
            <select name="id_famille" id="create_id_famille" class="select" required>' . $familleOpts . '</select>
        </div>
        <div>
            <label class="label">Produit père</label>
            <select name="id_produit_pere" id="create_id_produit_pere" class="select">
                <option value="">-- Sélectionnez une famille d\'abord --</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Nom du produit <span class="text-red-500">*</span></label>
            <input type="text" name="nom_produit" class="input" required>
        </div>
        <div>
            <label class="label">Code barre</label>
            <input type="text" name="code_barre" class="input" placeholder="Généré automatiquement">
        </div>
    </div>
    <div>
        <label class="label">Description</label>
        <textarea name="description" rows="2" class="textarea"></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Prix d\'achat (FCFA)</label>
            <input type="number" step="0.01" name="prix_achat" value="0" class="input">
        </div>
        <div>
            <label class="label">Prix de vente (FCFA)</label>
            <input type="number" step="0.01" name="prix_vente" value="0" class="input">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Stock actuel</label>
            <input type="number" step="0.001" name="stock_actuel" value="0" class="input">
        </div>
        <div>
            <label class="label">Unité</label>
            <select name="unite" class="select">' . $uniteOpts . '</select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Seuil d\'alerte</label>
            <input type="number" step="0.001" name="seuil_alerte" value="0" class="input">
        </div>
        <div class="flex items-center gap-4 pt-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="perissable" value="1" class="checkbox">
                <span class="text-sm">Périssable</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="est_actif" value="1" checked class="checkbox">
                <span class="text-sm">Actif</span>
            </label>
        </div>
    </div>
    <div id="create_date_peremption_div" class="hidden">
        <label class="label">Date de péremption</label>
        <input type="date" name="date_peremption" class="input w-full md:w-64">
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-plus mr-1.5"></i>Créer</button>
    </div>
</form>';
echo renderModal('createModal', 'Nouveau produit', $createBody, null, 'lg');

$editBody = '
<form method="POST" action="?action=produits" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_produit" id="edit_id_produit">
    <input type="hidden" name="id_famille" id="edit_id_famille_hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Famille <span class="text-red-500">*</span></label>
            <select id="edit_id_famille" class="select" disabled>' . $familleOpts . '</select>
        </div>
        <div>
            <label class="label">Produit père</label>
            <select name="id_produit_pere" id="edit_id_produit_pere" class="select">
                <option value="">-- Aucun (produit principal) --</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Nom du produit <span class="text-red-500">*</span></label>
            <input type="text" name="nom_produit" id="edit_nom_produit" class="input" required>
        </div>
        <div>
            <label class="label">Code barre</label>
            <input type="text" name="code_barre" id="edit_code_barre" class="input">
        </div>
    </div>
    <div>
        <label class="label">Description</label>
        <textarea name="description" id="edit_description" rows="2" class="textarea"></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Prix d\'achat (FCFA)</label>
            <input type="number" step="0.01" name="prix_achat" id="edit_prix_achat" class="input">
        </div>
        <div>
            <label class="label">Prix de vente (FCFA)</label>
            <input type="number" step="0.01" name="prix_vente" id="edit_prix_vente" class="input">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Stock actuel</label>
            <input type="number" step="0.001" name="stock_actuel" id="edit_stock_actuel" class="input">
        </div>
        <div>
            <label class="label">Unité</label>
            <select name="unite" id="edit_unite" class="select">' . $uniteOpts . '</select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Seuil d\'alerte</label>
            <input type="number" step="0.001" name="seuil_alerte" id="edit_seuil_alerte" class="input">
        </div>
        <div class="flex items-center gap-4 pt-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="perissable" value="1" id="edit_perissable" class="checkbox">
                <span class="text-sm">Périssable</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="est_actif" value="1" id="edit_est_actif" class="checkbox">
                <span class="text-sm">Actif</span>
            </label>
        </div>
    </div>
    <div id="edit_date_peremption_div" class="hidden">
        <label class="label">Date de péremption</label>
        <input type="date" name="date_peremption" id="edit_date_peremption" class="input w-full md:w-64">
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-save mr-1.5"></i>Mettre à jour</button>
    </div>
</form>';
echo renderModal('editModal', 'Modifier le produit', $editBody, null, 'lg');
?>

<script>
// Périssable toggle
document.addEventListener('change', function(e) {
    if (e.target.matches('[name="perissable"]')) {
        const prefix = e.target.closest('.modal-overlay')?.id === 'createModal' ? 'create' : 'edit';
        const div = document.getElementById(prefix + '_date_peremption_div');
        if (e.target.checked) {
            div.classList.remove('hidden');
        } else {
            div.classList.add('hidden');
            document.querySelector('#' + prefix + 'Modal input[name="date_peremption"]').value = '';
        }
    }
});

// AJAX load produit père
function loadProduitsPeres(familleId, prefix, selectedId, excludeId) {
    const pereSelect = document.getElementById(prefix + '_id_produit_pere');
    if (!familleId) {
        pereSelect.innerHTML = '<option value="">-- Sélectionnez une famille d\'abord --</option>';
        return;
    }
    let url = '?action=ajax_produits_peres&id_famille=' + familleId;
    if (excludeId) url += '&exclude_id=' + excludeId;
    fetch(url)
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            pereSelect.innerHTML = '<option value="">-- Aucun (produit principal) --</option>';
            for (let id in data) {
                let opt = document.createElement('option');
                opt.value = id;
                opt.textContent = data[id];
                if (selectedId && id == selectedId) opt.selected = true;
                pereSelect.appendChild(opt);
            }
        })
        .catch(err => {
            pereSelect.innerHTML = '<option value="">-- Erreur chargement --</option>';
        });
}

// Famille change on create modal (no exclude needed for new product)
document.getElementById('create_id_famille')?.addEventListener('change', function() {
    loadProduitsPeres(this.value, 'create', null, null);
});

// Edit modal pre-fill
const PRODUITS = <?= json_encode($produitsListe) ?>;
document.querySelectorAll('[data-edit-prod]').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-edit-prod');
        const p = PRODUITS.find(p => p.id_produit == id);
        if (!p) return;
        document.getElementById('edit_id_produit').value = p.id_produit;
        document.getElementById('edit_code_barre').value = p.code_barre || '';
        document.getElementById('edit_nom_produit').value = p.nom_produit || '';
        document.getElementById('edit_description').value = p.description || '';
        document.getElementById('edit_prix_achat').value = p.prix_achat || 0;
        document.getElementById('edit_prix_vente').value = p.prix_vente || 0;
        document.getElementById('edit_stock_actuel').value = p.stock_actuel || 0;
        document.getElementById('edit_seuil_alerte').value = p.seuil_alerte || 0;
        document.getElementById('edit_unite').value = p.unite || 'pce';
        document.getElementById('edit_perissable').checked = !!p.perissable;
        document.getElementById('edit_est_actif').checked = !!p.est_actif;
        document.getElementById('edit_id_famille_hidden').value = p.id_famille || '';
        document.getElementById('edit_id_famille').value = p.id_famille || '';
        if (p.perissable) {
            document.getElementById('edit_date_peremption_div').classList.remove('hidden');
            document.getElementById('edit_date_peremption').value = p.date_peremption || '';
        } else {
            document.getElementById('edit_date_peremption_div').classList.add('hidden');
            document.getElementById('edit_date_peremption').value = '';
        }
        loadProduitsPeres(p.id_famille, 'edit', p.id_produit_pere, p.id_produit);
    });
});
</script>
