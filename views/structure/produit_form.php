<?php
$isEdit = isset($produit) && $produit !== null;
$title = $isEdit ? 'Modifier le produit' : 'Nouveau produit';
$action = $isEdit ? '?action=produit_mettre_a_jour' : '?action=produit_enregistrer';
$backLink = '?action=produits';

// Récupérer les valeurs
$id_famille = '';
$id_produit_pere = '';
$code_barre = '';
$nom_produit = '';
$description = '';
$prix_achat = 0;
$prix_vente = 0;
$stock_actuel = 0;
$seuil_alerte = 0;
$perissable = false;
$date_peremption = '';
$unite = 'pce';
$est_actif = true;

if ($isEdit) {
    $id_famille = $produit['id_famille'] ?? '';
    $id_produit_pere = $produit['id_produit_pere'] ?? '';
    $code_barre = htmlspecialchars($produit['code_barre'] ?? '');
    $nom_produit = htmlspecialchars($produit['nom_produit'] ?? '');
    $description = htmlspecialchars($produit['description'] ?? '');
    $prix_achat = $produit['prix_achat'] ?? 0;
    $prix_vente = $produit['prix_vente'] ?? 0;
    $stock_actuel = $produit['stock_actuel'] ?? 0;
    $seuil_alerte = $produit['seuil_alerte'] ?? 0;
    $perissable = $produit['perissable'] ?? false;
    $date_peremption = $produit['date_peremption'] ?? '';
    $unite = $produit['unite'] ?? 'pce';
    $est_actif = $produit['est_actif'] ?? true;
} else {
    $old = $_SESSION['old'] ?? [];
    $id_famille = $old['id_famille'] ?? '';
    $id_produit_pere = $old['id_produit_pere'] ?? '';
    $code_barre = htmlspecialchars($old['code_barre'] ?? '');
    $nom_produit = htmlspecialchars($old['nom_produit'] ?? '');
    $description = htmlspecialchars($old['description'] ?? '');
    $prix_achat = $old['prix_achat'] ?? 0;
    $prix_vente = $old['prix_vente'] ?? 0;
    $stock_actuel = $old['stock_actuel'] ?? 0;
    $seuil_alerte = $old['seuil_alerte'] ?? 0;
    $perissable = isset($old['perissable']);
    $date_peremption = $old['date_peremption'] ?? '';
    $unite = $old['unite'] ?? 'pce';
    $est_actif = isset($old['est_actif']) ? (bool)$old['est_actif'] : true;
}

// Initialiser $produitsPeres à un tableau vide si non défini
if (!isset($produitsPeres) || !is_array($produitsPeres)) {
    $produitsPeres = ['' => '-- Aucun (produit principal) --'];
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

echo renderPageHeader($title, '', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $action ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_produit" value="<?= $produit['id_produit'] ?>">
                <input type="hidden" name="code_barre" value="<?= $code_barre ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Famille <span class="text-red-500">*</span></label>
                    <select name="id_famille" id="id_famille" class="select" <?= $isEdit ? 'disabled' : '' ?> required>
                        <option value="">-- Sélectionner une famille --</option>
                        <?php foreach ($familles as $id => $nom): ?>
                            <option value="<?= $id ?>" <?= $id_famille == $id ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id_famille" value="<?= $id_famille ?>">
                    <?php endif; ?>
                </div>
                <div>
                    <label class="label">Produit père</label>
                    <select name="id_produit_pere" id="id_produit_pere" class="select">
                        <option value="">-- Aucun (produit principal) --</option>
                        <?php if ($isEdit && !empty($produitsPeres)): ?>
                            <?php foreach ($produitsPeres as $id => $nom): ?>
                                <option value="<?= $id ?>" <?= $id_produit_pere == $id ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="label">Nom du produit <span class="text-red-500">*</span></label>
                <input type="text" name="nom_produit" value="<?= $nom_produit ?>" class="input" required>
            </div>
            
            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="3" class="textarea"><?= $description ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Prix d'achat (FCFA)</label>
                    <input type="number" step="0.01" name="prix_achat" value="<?= $prix_achat ?>" class="input">
                </div>
                <div>
                    <label class="label">Prix de vente (FCFA)</label>
                    <input type="number" step="0.01" name="prix_vente" value="<?= $prix_vente ?>" class="input">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Stock actuel</label>
                    <input type="number" step="0.001" name="stock_actuel" value="<?= $stock_actuel ?>" class="input">
                </div>
                <div>
                    <label class="label">Unité</label>
                    <select name="unite" class="select">
                        <option value="pce" <?= $unite == 'pce' ? 'selected' : '' ?>>Pièce (pce)</option>
                        <option value="kg" <?= $unite == 'kg' ? 'selected' : '' ?>>Kilogramme (kg)</option>
                        <option value="l" <?= $unite == 'l' ? 'selected' : '' ?>>Litre (l)</option>
                        <option value="m" <?= $unite == 'm' ? 'selected' : '' ?>>Mètre (m)</option>
                        <option value="carton" <?= $unite == 'carton' ? 'selected' : '' ?>>Carton</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Seuil d'alerte</label>
                    <input type="number" step="0.001" name="seuil_alerte" value="<?= $seuil_alerte ?>" class="input">
                </div>
                <div class="flex items-center gap-4 pt-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="perissable" id="perissable" value="1" <?= $perissable ? 'checked' : '' ?> class="checkbox">
                        <span class="text-sm">Périssable</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="est_actif" id="est_actif" value="1" <?= $est_actif ? 'checked' : '' ?> class="checkbox">
                        <span class="text-sm">Actif</span>
                    </label>
                </div>
            </div>
            
            <div id="datePeremptionDiv" class="<?= $perissable ? 'block' : 'hidden' ?>">
                <label class="label">Date de péremption</label>
                <input type="date" name="date_peremption" value="<?= $date_peremption ?>" class="input w-full md:w-64">
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <?php foreach ($errors as $err): ?>
                        <p class="text-sm text-red-600">• <?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="flex justify-end gap-2 pt-4">
                <a href="<?= $backLink ?>" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus' ?> mr-1.5 text-xs"></i>
                    <?= $isEdit ? 'Mettre à jour' : 'Créer' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('perissable')?.addEventListener('change', function() {
    let div = document.getElementById('datePeremptionDiv');
    if (this.checked) {
        div.classList.remove('hidden');
    } else {
        div.classList.add('hidden');
        document.querySelector('input[name="date_peremption"]').value = '';
    }
});

<?php if (!$isEdit): ?>
document.getElementById('id_famille')?.addEventListener('change', function() {
    let familleId = this.value;
    let pereSelect = document.getElementById('id_produit_pere');
    if (!familleId) {
        pereSelect.innerHTML = '<option value="">-- Sélectionnez une famille d\'abord --</option>';
        return;
    }
    fetch('index.php?action=ajax_produits_peres&id_famille=' + familleId)
        .then(response => response.json())
        .then(data => {
            pereSelect.innerHTML = '<option value="">-- Aucun (produit principal) --</option>';
            for (let id in data) {
                let opt = document.createElement('option');
                opt.value = id;
                opt.textContent = data[id];
                pereSelect.appendChild(opt);
            }
        });
});
<?php endif; ?>
</script>