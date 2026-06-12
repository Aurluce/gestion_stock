<?php
$isEdit = isset($famille) && $famille !== null;
$title = $isEdit ? 'Modifier la famille' : 'Nouvelle famille';
$action = $isEdit ? '?action=famille_mettre_a_jour' : '?action=famille_enregistrer';

// Récupérer les valeurs
$nom = '';
$description = '';

if ($isEdit) {
    $nom = htmlspecialchars($famille['nom_famille'] ?? '');
    $description = htmlspecialchars($famille['description'] ?? '');
} elseif (isset($_SESSION['old'])) {
    $nom = htmlspecialchars($_SESSION['old']['nom_famille'] ?? '');
    $description = htmlspecialchars($_SESSION['old']['description'] ?? '');
    unset($_SESSION['old']);
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="p-6 max-w-2xl mx-auto">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas <?= $isEdit ? 'fa-edit' : 'fa-plus-circle' ?> text-blue-600"></i>
                <?= $title ?>
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <?= $isEdit ? 'Modifiez les informations de la famille' : 'Ajoutez une nouvelle catégorie de produits' ?>
            </p>
        </div>
        <a href="?action=familles" 
           class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
            <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
            Retour
        </a>
    </div>

    <!-- Messages flash -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md mb-4 text-sm flex items-center justify-between">
            <span><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button type="button" class="text-green-500 hover:text-green-700" onclick="this.closest('div').remove()">×</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-md mb-4 text-sm flex items-center justify-between">
            <span><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('div').remove()">×</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="POST" action="<?= $action ?>" class="p-6 space-y-5">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_famille" value="<?= $famille['id_famille'] ?>">
            <?php endif; ?>
            
            <!-- Champ Nom -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nom de la famille <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-tag text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" 
                           name="nom_famille" 
                           value="<?= $nom ?>" 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                           placeholder="Ex: Électronique, Alimentation, Vêtements..."
                           required>
                </div>
            </div>
            
            <!-- Champ Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-3 pointer-events-none">
                        <i class="fas fa-align-left text-gray-400 text-sm"></i>
                    </div>
                    <textarea name="description" 
                              rows="4" 
                              class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                              placeholder="Description optionnelle..."><?= $description ?></textarea>
                </div>
            </div>
            
            <!-- Affichage des erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <?php foreach ($errors as $err): ?>
                        <p class="text-sm text-red-600 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Boutons d'action -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="?action=familles" 
                   class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
                    Annuler
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus' ?> mr-1.5 text-xs"></i>
                    <?= $isEdit ? 'Mettre à jour' : 'Créer' ?>
                </button>
            </div>
        </form>
    </div>
</div>
