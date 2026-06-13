<?php
$isEdit = isset($banque) && $banque !== null;
$title = $isEdit ? 'Modifier la banque' : 'Nouvelle banque';
$action = $isEdit ? '?action=banque_mettre_a_jour' : '?action=banque_enregistrer';
$backLink = '?action=banques';

$nom_banque = $isEdit ? ($banque['nom_banque'] ?? '') : ($_SESSION['old']['nom_banque'] ?? '');
$sigle = $isEdit ? ($banque['sigle'] ?? '') : ($_SESSION['old']['sigle'] ?? '');
$responsable = $isEdit ? ($banque['responsable'] ?? '') : ($_SESSION['old']['responsable'] ?? '');
$adresse = $isEdit ? ($banque['adresse'] ?? '') : ($_SESSION['old']['adresse'] ?? '');
$tel = $isEdit ? ($banque['tel'] ?? '') : ($_SESSION['old']['tel'] ?? '');
$email = $isEdit ? ($banque['email'] ?? '') : ($_SESSION['old']['email'] ?? '');

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

echo renderPageHeader($title, '', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $action ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_banque" value="<?= $banque['id_banque'] ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Nom de la banque <span class="text-red-500">*</span></label>
                    <input type="text" name="nom_banque" value="<?= htmlspecialchars($nom_banque) ?>" class="input" required>
                </div>
                <div>
                    <label class="label">Sigle</label>
                    <input type="text" name="sigle" value="<?= htmlspecialchars($sigle) ?>" class="input">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Responsable</label>
                    <input type="text" name="responsable" value="<?= htmlspecialchars($responsable) ?>" class="input">
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
                    <label class="label">Adresse</label>
                    <input type="text" name="adresse" value="<?= htmlspecialchars($adresse) ?>" class="input">
                </div>
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