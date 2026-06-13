<?php
$isEdit = isset($fournisseur) && $fournisseur !== null;
$title = $isEdit ? 'Modifier le fournisseur' : 'Nouveau fournisseur';
$action = $isEdit ? '?action=fournisseur_mettre_a_jour' : '?action=fournisseur_enregistrer';
$backLink = '?action=fournisseurs';

$nom = $isEdit ? ($fournisseur['nom'] ?? '') : ($_SESSION['old']['nom'] ?? '');
$tel = $isEdit ? ($fournisseur['tel'] ?? '') : ($_SESSION['old']['tel'] ?? '');
$email = $isEdit ? ($fournisseur['email'] ?? '') : ($_SESSION['old']['email'] ?? '');
$adresse = $isEdit ? ($fournisseur['adresse'] ?? '') : ($_SESSION['old']['adresse'] ?? '');
$ville = $isEdit ? ($fournisseur['ville'] ?? '') : ($_SESSION['old']['ville'] ?? '');
$nif = $isEdit ? ($fournisseur['nif'] ?? '') : ($_SESSION['old']['nif'] ?? '');
$est_actif = $isEdit ? ($fournisseur['est_actif'] ?? true) : (!isset($_SESSION['old']['est_actif']) || $_SESSION['old']['est_actif']);

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

echo renderPageHeader($title, '', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $action ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_fournisseur" value="<?= $fournisseur['id_fournisseur'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" class="input" required>
                </div>
                <div>
                    <label class="label">Téléphone</label>
                    <input type="text" name="tel" value="<?= htmlspecialchars($tel) ?>" class="input">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="input">
                </div>
                <div>
                    <label class="label">NIF</label>
                    <input type="text" name="nif" value="<?= htmlspecialchars($nif) ?>" class="input">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Ville</label>
                    <input type="text" name="ville" value="<?= htmlspecialchars($ville) ?>" class="input">
                </div>
                <div>
                    <label class="label">Adresse</label>
                    <input type="text" name="adresse" value="<?= htmlspecialchars($adresse) ?>" class="input">
                </div>
            </div>
            
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="est_actif" value="1" <?= $est_actif ? 'checked' : '' ?> class="checkbox">
                    <span class="text-sm">Fournisseur actif</span>
                </label>
            </div>
            
            <?php if (!empty($errors)): ?>
                <?= renderAlert(implode('<br>', $errors), 'danger') ?>
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