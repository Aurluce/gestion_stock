<?php
$isEdit = isset($client) && $client !== null;
$title = $isEdit ? 'Modifier le client' : 'Nouveau client';
$action = $isEdit ? '?action=client_mettre_a_jour' : '?action=client_enregistrer';
$backLink = '?action=clients';

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
    $nom = $client['nom'] ?? '';
    $prenom = $client['prenom'] ?? '';
    $tel = $client['tel'] ?? '';
    $email = $client['email'] ?? '';
    $ville = $client['ville'] ?? '';
    $type_client = $client['type_client'] ?? 'particulier';
    $est_actif = $client['est_actif'] ?? true;
} else {
    $old = $_SESSION['old'] ?? [];
    $id_categorie_client = $old['id_categorie_client'] ?? '';
    $nom = $old['nom'] ?? '';
    $prenom = $old['prenom'] ?? '';
    $tel = $old['tel'] ?? '';
    $email = $old['email'] ?? '';
    $ville = $old['ville'] ?? '';
    $type_client = $old['type_client'] ?? 'particulier';
    $est_actif = isset($old['est_actif']) ? (bool)$old['est_actif'] : true;
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

echo renderPageHeader($title, '', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $action ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_client" value="<?= $client['id_client'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" class="input" required>
                </div>
                <div>
                    <label class="label">Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>" class="input">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Téléphone</label>
                    <input type="text" name="tel" value="<?= htmlspecialchars($tel) ?>" class="input">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="input">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Ville</label>
                    <input type="text" name="ville" value="<?= htmlspecialchars($ville) ?>" class="input">
                </div>
                <div>
                    <label class="label">Catégorie <span class="text-red-500">*</span></label>
                    <?php if (!empty($categories)): ?>
                        <select name="id_categorie_client" class="select" required>
                            <option value="">-- Sélectionnez une catégorie --</option>
                            <?php foreach ($categories as $id => $nomCat): ?>
                                <option value="<?= $id ?>" <?= $id_categorie_client == $id ? 'selected' : '' ?>><?= htmlspecialchars($nomCat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <div class="p-3 border border-yellow-300 bg-yellow-50 rounded text-yellow-800 space-y-2">
                            <p>Aucune catégorie client disponible. Veuillez créer une catégorie client avant d'ajouter un client.</p>
                            <?= renderButton('Créer une catégorie client', 'secondary', '?action=categorie_client_creer', ['icon' => 'fa-plus']) ?>
                        </div>
                    <?php endif; ?>
                      <?= renderButton('Créer une catégorie client', 'secondary', '?action=categorie_client_creer', ['icon' => 'fa-plus']) ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Type de client</label>
                    <select name="type_client" class="select">
                        <option value="particulier" <?= $type_client == 'particulier' ? 'selected' : '' ?>>Particulier</option>
                        <option value="entreprise" <?= $type_client == 'entreprise' ? 'selected' : '' ?>>Entreprise</option>
                        <option value="administration" <?= $type_client == 'administration' ? 'selected' : '' ?>>Administration</option>
                    </select>
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="est_actif" value="1" <?= $est_actif ? 'checked' : '' ?> class="checkbox">
                        <span class="text-sm">Client actif</span>
                    </label>
                </div>
            </div>
            
            <?php if (!empty($errors)): ?>
                <?= renderAlert(implode('<br>', $errors), 'danger') ?>
            <?php endif; ?>
            
            <div class="flex justify-end gap-2 pt-4">
                <a href="<?= $backLink ?>" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary" <?= empty($categories) ? 'disabled' : '' ?>>
                    <i class="fas <?= $isEdit ? 'fa-save' : 'fa-plus' ?> mr-1.5 text-xs"></i>
                    <?= $isEdit ? 'Mettre à jour' : 'Créer' ?>
                </button>
            </div>
        </form>
    </div>
</div>