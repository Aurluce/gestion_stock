<?php
$title = "Produits";
$mode = $_GET['mode'] ?? 'liste';
$familleSelectionnee = (int)($_GET['id_famille'] ?? 0);
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=produits'],
    ['label' => 'Produits']
]);
ob_start();
?>

<?= renderPageHeader(
    'Produits',
    'Gérer votre catalogue de produits',
    renderButton('Nouveau produit', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) .
    renderButton('Imprimer', 'secondary', '?action=produits&print=1', ['icon' => 'fa-print'])
) ?>

<!-- Barre d'outils -->
<div class="card mb-6">
    <div class="card-body">
        <form method="get" action="?action=produits" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="action" value="produits">
            <div class="flex-1 min-w-[200px]">
                <label class="label">Affichage</label>
                <select name="mode" class="select" onchange="this.form.submit()">
                    <option value="liste" <?= $mode == 'liste' ? 'selected' : '' ?>> Liste simple</option>
                    <option value="par_famille" <?= $mode == 'par_famille' ? 'selected' : '' ?>> Par famille</option>
                </select>
            </div>
            <?php if ($mode == 'liste'): ?>
            <div class="flex-1 min-w-[200px]">
                <label class="label">Filtrer par famille</label>
                <select name="id_famille" class="select" onchange="this.form.submit()">
                    <option value="">Toutes les familles</option>
                    <?php foreach ($famillesSelect as $id => $nom): ?>
                        <option value="<?= $id ?>" <?= $familleSelectionnee == $id ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($familleSelectionnee): ?>
                <div class="flex items-end">
                    <a href="?action=produits&mode=liste" class="btn-secondary">Réinitialiser</a>
                </div>
            <?php endif; ?>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($mode == 'par_famille'): ?>
    <?php if (empty($produitsParFamille)): ?>
        <?= renderEmptyState('fa-box-open', 'Aucun produit', 'Commencez par ajouter votre premier produit.', renderButton('Créer un produit', 'primary', '?action=produit_creer')) ?>
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
                        $tableData = array_map(function($p) {
                            return [
                                htmlspecialchars($p['nom_produit']),
                                htmlspecialchars($p['nom_produit_pere'] ?? '-'),
                                number_format($p['prix_vente'], 0) . ' FCFA',
                                number_format($p['stock_actuel'], 2),
                                $p['est_actif'] ? renderBadge('Actif', 'success') : renderBadge('Inactif', 'danger')
                            ];
                        }, $famille['produits']);
                        
                        $actionsRenderer = function($row, $rowIndex) use ($famille) {
                            $p = $famille['produits'][$rowIndex] ?? null;
                            if (!$p) return '';
                            $editData = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                            return '<button type="button" class="btn-icon" data-modal-toggle="editModal" data-edit-produit=\'' . $editData . '\' title="Modifier"><i class="fas fa-edit"></i></button>' .
                                   renderButton('', 'icon-danger', '?action=produit_supprimer&id=' . $p['id_produit'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce produit ?']);
                        };
                        
                        echo renderResponsiveTable(
                            ['Nom', 'Variante de', 'Prix vente', 'Stock', 'Statut'],
                            $tableData,
                            [
                                'mobileTitle' => 0,
                                'mobileSubtitle' => 1,
                                'mobileBadge' => 4,
                                'actions' => $actionsRenderer,
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
    $actionsRenderer = function($row, $rowIndex) use ($produitsListe) {
        $p = $produitsListe[$rowIndex] ?? null;
        if (!$p) return '';
        
        $editData = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
        $actions = '<button type="button" class="btn-icon" data-modal-toggle="editModal" data-edit-produit=\'' . $editData . '\' title="Modifier"><i class="fas fa-edit"></i></button>';
        $actions .= renderButton('', 'icon-danger', '?action=produit_supprimer&id=' . $p['id_produit'], [
            'icon' => 'fa-trash',
            'title' => 'Supprimer',
            'data-confirm' => 'Supprimer ce produit ?'
        ]);
        return $actions;
    };

    $tableData = array_map(function($p) {
        return [
            htmlspecialchars($p['nom_produit']),
            htmlspecialchars($p['nom_famille'] ?? '-'),
            htmlspecialchars($p['nom_produit_pere'] ?? '-'),
            number_format($p['prix_vente'], 0) . ' FCFA',
            number_format($p['stock_actuel'], 2),
            $p['est_actif'] ? renderBadge('Actif', 'success') : renderBadge('Inactif', 'danger')
        ];
    }, $produitsListe);

    echo renderResponsiveTable(
        ['Nom', 'Famille', 'Variante de', 'Prix vente', 'Stock', 'Statut'],
        $tableData,
        [
            'mobileTitle' => 0,
            'mobileSubtitle' => 1,
            'mobileBadge' => 5,
            'actions' => $actionsRenderer,
            'emptyMessage' => 'Aucun produit trouvé.'
        ]
    );
    ?>
<?php endif; ?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=produit_enregistrer" id="createForm" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-2 gap-3">
        ' . renderSelect('id_famille', 'Famille *', $familles, null, null, ['required' => 'required', 'id' => 'create_id_famille']) . '
        ' . renderSelect('id_produit_pere', 'Produit père', ['' => '-- Aucun (produit principal) --'], null, null, ['id' => 'create_id_produit_pere']) . '
    </div>
    ' . renderInput('nom_produit', 'Nom du produit *', 'text', '', null, ['required' => 'required']) . '
    ' . renderTextarea('description', 'Description', '') . '
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('prix_achat', "Prix d'achat", 'number', '', null, ['step' => '0.01']) . '
        ' . renderInput('prix_vente', 'Prix de vente', 'number', '', null, ['step' => '0.01']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('stock_actuel', 'Stock actuel', 'number', '', null, ['step' => '0.001']) . '
        ' . renderSelect('unite', 'Unité', ['pce' => 'Pièce', 'kg' => 'Kg', 'l' => 'Litre', 'm' => 'Mètre', 'carton' => 'Carton'], 'pce') . '
    </div>
    ' . renderInput('seuil_alerte', "Seuil d'alerte", 'number', '', null, ['step' => '0.001']) . '
    <div class="flex gap-4">
        ' . renderCheckbox('perissable', 'Périssable', false, ['id' => 'create_perissable']) . '
        ' . renderCheckbox('est_actif', 'Actif', true) . '
    </div>
    <div id="create_date_peremption_div" class="hidden">
        ' . renderInput('date_peremption', 'Date de péremption', 'date', '') . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer le produit', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Ajouter un produit', $createBody, null, 'lg');
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=produit_mettre_a_jour" id="editForm" class="space-y-4">
    <input type="hidden" name="id_produit" id="edit_id">
    <div class="grid grid-cols-2 gap-3">
        ' . renderSelect('id_famille', 'Famille *', $familles, null, null, ['required' => 'required', 'id' => 'edit_id_famille', 'disabled' => 'disabled']) . '
        <input type="hidden" name="id_famille" id="edit_id_famille_hidden">
        ' . renderSelect('id_produit_pere', 'Produit père', ['' => '-- Aucun (produit principal) --'], null, null, ['id' => 'edit_id_produit_pere']) . '
    </div>
    ' . renderInput('nom_produit', 'Nom du produit *', 'text', '', null, ['id' => 'edit_nom_produit', 'required' => 'required']) . '
    ' . renderTextarea('description', 'Description', '', null, ['id' => 'edit_description']) . '
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('prix_achat', "Prix d'achat", 'number', '', null, ['id' => 'edit_prix_achat', 'step' => '0.01']) . '
        ' . renderInput('prix_vente', 'Prix de vente', 'number', '', null, ['id' => 'edit_prix_vente', 'step' => '0.01']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('stock_actuel', 'Stock actuel', 'number', '', null, ['id' => 'edit_stock_actuel', 'step' => '0.001']) . '
        ' . renderSelect('unite', 'Unité', ['pce' => 'Pièce', 'kg' => 'Kg', 'l' => 'Litre', 'm' => 'Mètre', 'carton' => 'Carton'], 'pce', null, ['id' => 'edit_unite']) . '
    </div>
    ' . renderInput('seuil_alerte', "Seuil d'alerte", 'number', '', null, ['id' => 'edit_seuil_alerte', 'step' => '0.001']) . '
    <div class="flex gap-4">
        ' . renderCheckbox('perissable', 'Périssable', false, ['id' => 'edit_perissable']) . '
        ' . renderCheckbox('est_actif', 'Actif', true, ['id' => 'edit_est_actif']) . '
    </div>
    <div id="edit_date_peremption_div" class="hidden">
        ' . renderInput('date_peremption', 'Date de péremption', 'date', '', null, ['id' => 'edit_date_peremption']) . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier un produit', $editBody, null, 'lg');
?>

<script>
document.getElementById('create_perissable')?.addEventListener('change', function() {
    const div = document.getElementById('create_date_peremption_div');
    if (this.checked) {
        div.classList.remove('hidden');
    } else {
        div.classList.add('hidden');
    }
});

document.getElementById('edit_perissable')?.addEventListener('change', function() {
    const div = document.getElementById('edit_date_peremption_div');
    if (this.checked) {
        div.classList.remove('hidden');
    } else {
        div.classList.add('hidden');
    }
});

document.getElementById('create_id_famille')?.addEventListener('change', function() {
    const familleId = this.value;
    const pereSelect = document.getElementById('create_id_produit_pere');
    if (!familleId) {
        pereSelect.innerHTML = '<option value="">-- Sélectionnez une famille d\'abord --</option>';
        return;
    }
    fetch('?action=ajax_produits_peres&id_famille=' + familleId)
        .then(response => response.json())
        .then(data => {
            pereSelect.innerHTML = '<option value="">-- Aucun (produit principal) --</option>';
            for (let id in data) {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = data[id];
                pereSelect.appendChild(opt);
            }
        });
});

document.querySelectorAll('[data-edit-produit]').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-edit-produit'));
        document.getElementById('edit_id').value = data.id_produit;
        document.getElementById('edit_id_famille_hidden').value = data.id_famille;
        document.getElementById('edit_id_produit_pere').value = data.id_produit_pere || '';
        document.getElementById('edit_nom_produit').value = data.nom_produit;
        document.getElementById('edit_description').value = data.description || '';
        document.getElementById('edit_prix_achat').value = data.prix_achat;
        document.getElementById('edit_prix_vente').value = data.prix_vente;
        document.getElementById('edit_stock_actuel').value = data.stock_actuel;
        document.getElementById('edit_unite').value = data.unite || 'pce';
        document.getElementById('edit_seuil_alerte').value = data.seuil_alerte;
        document.getElementById('edit_est_actif').checked = data.est_actif;
        document.getElementById('edit_perissable').checked = data.perissable;
        if (data.perissable && data.date_peremption) {
            document.getElementById('edit_date_peremption').value = data.date_peremption;
            document.getElementById('edit_date_peremption_div').classList.remove('hidden');
        } else {
            document.getElementById('edit_date_peremption_div').classList.add('hidden');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>