<?php
$isEdit = isset($produit) && $produit !== null;
$title = $isEdit ? 'Modifier le produit' : 'Nouveau produit';
$action = $isEdit ? '?action=produit_mettre_a_jour' : '?action=produit_enregistrer';

$id_famille = $isEdit ? $produit['id_famille'] : ($_SESSION['old']['id_famille'] ?? '');
$id_produit_pere = $isEdit ? $produit['id_produit_pere'] : ($_SESSION['old']['id_produit_pere'] ?? '');
$code_barre = $isEdit ? htmlspecialchars($produit['code_barre'] ?? '') : '';
$nom_produit = $isEdit ? htmlspecialchars($produit['nom_produit']) : htmlspecialchars($_SESSION['old']['nom_produit'] ?? '');
$description = $isEdit ? htmlspecialchars($produit['description'] ?? '') : htmlspecialchars($_SESSION['old']['description'] ?? '');
$prix_achat = $isEdit ? $produit['prix_achat'] : ($_SESSION['old']['prix_achat'] ?? 0);
$prix_vente = $isEdit ? $produit['prix_vente'] : ($_SESSION['old']['prix_vente'] ?? 0);
$stock_actuel = $isEdit ? $produit['stock_actuel'] : ($_SESSION['old']['stock_actuel'] ?? 0);
$seuil_alerte = $isEdit ? $produit['seuil_alerte'] : ($_SESSION['old']['seuil_alerte'] ?? 0);
$perissable = $isEdit ? $produit['perissable'] : isset($_SESSION['old']['perissable']);
$date_peremption = $isEdit ? ($produit['date_peremption'] ?? '') : ($_SESSION['old']['date_peremption'] ?? '');
$unite = $isEdit ? ($produit['unite'] ?? 'pce') : ($_SESSION['old']['unite'] ?? 'pce');
$est_actif = $isEdit ? $produit['est_actif'] : (isset($_SESSION['old']['est_actif']) ? (bool)$_SESSION['old']['est_actif'] : true);

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="p-6 max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas <?= $isEdit ? 'fa-edit' : 'fa-plus-circle' ?> text-blue-600"></i>
                <?= $title ?>
            </h1>
        </div>
        <a href="?action=produits" class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
            <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Retour
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md mb-4 text-sm"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-md mb-4 text-sm"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="POST" action="<?= $action ?>" class="p-6 space-y-6">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_produit" value="<?= $produit['id_produit'] ?>">
                <input type="hidden" name="code_barre" value="<?= $code_barre ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Famille <span class="text-red-500">*</span></label>
                    <select name="id_famille" id="id_famille" class="w-full border border-gray-300 rounded-lg px-3 py-2" <?= $isEdit ? 'disabled' : '' ?> required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($familles as $id => $nom): ?>
                            <option value="<?= $id ?>" <?= $id_famille == $id ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id_famille" value="<?= $id_famille ?>">
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produit père</label>
                    <select name="id_produit_pere" id="id_produit_pere" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">-- Aucun (produit principal) --</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit <span class="text-red-500">*</span></label>
                    <input type="text" name="nom_produit" value="<?= $nom_produit ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                </div>
                <?php if ($isEdit): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code barre</label>
                    <input type="text" value="<?= $code_barre ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" readonly disabled>
                </div>
                <?php else: ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code barre</label>
                    <input type="text" value="Généré automatiquement" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" readonly disabled>
                </div>
                <?php endif; ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2"><?= $description ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix d'achat (FCFA)</label>
                    <input type="number" step="0.01" name="prix_achat" value="<?= $prix_achat ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix de vente (FCFA)</label>
                    <input type="number" step="0.01" name="prix_vente" value="<?= $prix_vente ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock actuel</label>
                    <input type="number" step="0.001" name="stock_actuel" value="<?= $stock_actuel ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                    <select name="unite" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="pce" <?= $unite == 'pce' ? 'selected' : '' ?>>Pièce (pce)</option>
                        <option value="kg" <?= $unite == 'kg' ? 'selected' : '' ?>>Kilogramme (kg)</option>
                        <option value="l" <?= $unite == 'l' ? 'selected' : '' ?>>Litre (l)</option>
                        <option value="m" <?= $unite == 'm' ? 'selected' : '' ?>>Mètre (m)</option>
                        <option value="carton" <?= $unite == 'carton' ? 'selected' : '' ?>>Carton</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seuil d'alerte</label>
                    <input type="number" step="0.001" name="seuil_alerte" value="<?= $seuil_alerte ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div class="flex items-center gap-6 pt-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="perissable" id="perissable" value="1" <?= $perissable ? 'checked' : '' ?>>
                        <span class="text-sm">Périssable</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="est_actif" id="est_actif" value="1" <?= $est_actif ? 'checked' : '' ?>>
                        <span class="text-sm">Actif</span>
                    </label>
                </div>
            </div>
            
            <div id="datePeremptionDiv" class="<?= $perissable ? 'block' : 'hidden' ?>">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de péremption</label>
                <input type="date" name="date_peremption" value="<?= $date_peremption ?>" class="w-full md:w-64 border border-gray-300 rounded-lg px-3 py-2">
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <?php foreach ($errors as $err): ?>
                        <p class="text-sm text-red-600">• <?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="?action=produits" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">Annuler</a>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
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
    if (this.checked) div.classList.remove('hidden');
    else div.classList.add('hidden');
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