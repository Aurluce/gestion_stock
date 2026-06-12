<?php
$isEdit = isset($client) && $client !== null;
$title = $isEdit ? 'Modifier le client' : 'Nouveau client';
$action = $isEdit ? '?action=client_mettre_a_jour' : '?action=client_enregistrer';

// Récupérer les valeurs
$id_categorie_client = '';
$nom = '';
$prenom = '';
$tel = '';
$email = '';
$ville = '';
$type_client = 'particulier';
$est_actif = true;

if ($isEdit) {
    $id_categorie_client = $client['id_categorie_client'] ?? '';
    $nom = htmlspecialchars($client['nom']);
    $prenom = htmlspecialchars($client['prenom'] ?? '');
    $tel = htmlspecialchars($client['tel'] ?? '');
    $email = htmlspecialchars($client['email'] ?? '');
    $ville = htmlspecialchars($client['ville'] ?? '');
    $type_client = $client['type_client'] ?? 'particulier';
    $est_actif = $client['est_actif'] ?? true;
} else {
    $old = $_SESSION['old'] ?? [];
    $id_categorie_client = $old['id_categorie_client'] ?? '';
    $nom = htmlspecialchars($old['nom'] ?? '');
    $prenom = htmlspecialchars($old['prenom'] ?? '');
    $tel = htmlspecialchars($old['tel'] ?? '');
    $email = htmlspecialchars($old['email'] ?? '');
    $ville = htmlspecialchars($old['ville'] ?? '');
    $type_client = $old['type_client'] ?? 'particulier';
    $est_actif = isset($old['est_actif']) ? (bool)$old['est_actif'] : true;
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
                <?= $isEdit ? 'Modifiez les informations du client' : 'Ajoutez un nouveau client' ?>
            </p>
        </div>
        <a href="?action=clients" 
           class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
            <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
            Retour
        </a>
    </div>

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
                <input type="hidden" name="id_client" value="<?= $client['id_client'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom" value="<?= $nom ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Nom du client" required>
                </div>
                
                <!-- Prénom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input type="text" name="prenom" value="<?= $prenom ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Prénom du client">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Téléphone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="tel" value="<?= $tel ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Ex: 699999999">
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= $email ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="client@email.com">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ville -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="ville" value="<?= $ville ?>" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Douala, Yaoundé...">
                </div>
                
                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select name="id_categorie_client" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">-- Aucune catégorie --</option>
                        <?php foreach ($categories as $id => $nomCat): ?>
                            <option value="<?= $id ?>" <?= $id_categorie_client == $id ? 'selected' : '' ?>><?= htmlspecialchars($nomCat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type client -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de client</label>
                    <select name="type_client" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="particulier" <?= $type_client == 'particulier' ? 'selected' : '' ?>>Particulier</option>
                        <option value="entreprise" <?= $type_client == 'entreprise' ? 'selected' : '' ?>>Entreprise</option>
                        <option value="administration" <?= $type_client == 'administration' ? 'selected' : '' ?>>Administration</option>
                    </select>
                </div>
                
                <!-- Actif -->
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="est_actif" <?= $est_actif ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Client actif</span>
                    </label>
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
                <a href="?action=clients" 
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
