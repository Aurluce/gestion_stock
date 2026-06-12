<?php
$isEdit = isset($banque) && $banque !== null;
$title = $isEdit ? 'Modifier la banque' : 'Nouvelle banque';
$action = $isEdit ? '?action=banque_mettre_a_jour' : '?action=banque_enregistrer';

// Récupérer les valeurs
$nom_banque = '';
$sigle = '';
$responsable = '';
$adresse = '';
$tel = '';
$email = '';

if ($isEdit) {
    $nom_banque = htmlspecialchars($banque['nom_banque']);
    $sigle = htmlspecialchars($banque['sigle'] ?? '');
    $responsable = htmlspecialchars($banque['responsable'] ?? '');
    $adresse = htmlspecialchars($banque['adresse'] ?? '');
    $tel = htmlspecialchars($banque['tel'] ?? '');
    $email = htmlspecialchars($banque['email'] ?? '');
} else {
    $old = $_SESSION['old'] ?? [];
    $nom_banque = htmlspecialchars($old['nom_banque'] ?? '');
    $sigle = htmlspecialchars($old['sigle'] ?? '');
    $responsable = htmlspecialchars($old['responsable'] ?? '');
    $adresse = htmlspecialchars($old['adresse'] ?? '');
    $tel = htmlspecialchars($old['tel'] ?? '');
    $email = htmlspecialchars($old['email'] ?? '');
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
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
                <?= $isEdit ? 'Modifiez les informations de la banque' : 'Ajoutez une nouvelle banque' ?>
            </p>
        </div>
        <a href="?action=banques" 
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
        <form method="POST" action="<?= $action ?>" class="p-6 space-y-6">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_banque" value="<?= $banque['id_banque'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom de la banque -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom de la banque <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom_banque" value="<?= $nom_banque ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Ex: Société Générale" required>
                </div>
                
                <!-- Sigle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sigle</label>
                    <input type="text" name="sigle" value="<?= $sigle ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Ex: SG, BICEC, Afriland">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <input type="text" name="responsable" value="<?= $responsable ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Nom du responsable">
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="tel" value="<?= $tel ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Ex: 699999999">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= $email ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="contact@banque.com">
                </div>
                
                <!-- Adresse -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" name="adresse" value="<?= $adresse ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Adresse de l'agence">
                </div>
            </div>
            
            <!-- Affichage des erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <?php foreach ($errors as $err): ?>
                        <p class="text-sm text-red-600">• <?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Boutons d'action -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="?action=banques" 
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
