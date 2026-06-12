<?php
$isEdit = isset($fournisseur) && $fournisseur !== null;
$title = $isEdit ? 'Modifier le fournisseur' : 'Nouveau fournisseur';
$action = $isEdit ? '?action=fournisseur_mettre_a_jour' : '?action=fournisseur_enregistrer';

$nom = $isEdit ? htmlspecialchars($fournisseur['nom']) : (htmlspecialchars($_SESSION['old']['nom'] ?? ''));
$adresse = $isEdit ? htmlspecialchars($fournisseur['adresse'] ?? '') : (htmlspecialchars($_SESSION['old']['adresse'] ?? ''));
$ville = $isEdit ? htmlspecialchars($fournisseur['ville'] ?? '') : (htmlspecialchars($_SESSION['old']['ville'] ?? ''));
$tel = $isEdit ? htmlspecialchars($fournisseur['tel'] ?? '') : (htmlspecialchars($_SESSION['old']['tel'] ?? ''));
$email = $isEdit ? htmlspecialchars($fournisseur['email'] ?? '') : (htmlspecialchars($_SESSION['old']['email'] ?? ''));
$nif = $isEdit ? htmlspecialchars($fournisseur['nif'] ?? '') : (htmlspecialchars($_SESSION['old']['nif'] ?? ''));
$est_actif = $isEdit ? $fournisseur['est_actif'] : (isset($_SESSION['old']['est_actif']) ? (bool)$_SESSION['old']['est_actif'] : true);

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="p-6 max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas <?= $isEdit ? 'fa-edit' : 'fa-plus-circle' ?> text-blue-600"></i>
                <?= $title ?>
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <?= $isEdit ? 'Modifiez les informations du fournisseur' : 'Ajoutez un nouveau fournisseur' ?>
            </p>
        </div>
        <a href="?action=fournisseurs" 
           class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
            <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
            Retour
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
                <input type="hidden" name="id_fournisseur" value="<?= $fournisseur['id_fournisseur'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom" value="<?= $nom ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="tel" value="<?= $tel ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= $email ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIF</label>
                    <input type="text" name="nif" value="<?= $nif ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="ville" value="<?= $ville ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" name="adresse" value="<?= $adresse ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>
            
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="est_actif" value="1" <?= $est_actif ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600">
                    <span class="text-sm text-gray-700">Fournisseur actif</span>
                </label>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <?php foreach ($errors as $err): ?>
                        <p class="text-sm text-red-600">• <?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="?action=fournisseurs" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">Annuler</a>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus' ?> mr-1.5 text-xs"></i>
                    <?= $isEdit ? 'Mettre à jour' : 'Créer' ?>
                </button>
            </div>
        </form>
    </div>
</div>