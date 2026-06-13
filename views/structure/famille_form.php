<?php
$isEdit = isset($famille) && $famille !== null;
$title = $isEdit ? 'Modifier la famille' : 'Nouvelle famille';
$action = $isEdit ? '?action=famille_mettre_a_jour' : '?action=famille_enregistrer';
$backLink = '?action=familles';

$nom = $isEdit ? ($famille['nom_famille'] ?? '') : ($_SESSION['old']['nom_famille'] ?? '');
$description = $isEdit ? ($famille['description'] ?? '') : ($_SESSION['old']['description'] ?? '');
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

echo renderPageHeader($title, '', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $action ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_famille" value="<?= $famille['id_famille'] ?>">
            <?php endif; ?>
            
            <div class="space-y-4">
                <?= renderInput('nom_famille', 'Nom de la famille *', 'text', $nom, $errors['nom_famille'] ?? null, ['required' => 'required']) ?>
                <?= renderTextarea('description', 'Description', $description) ?>
                
                <div class="flex justify-end gap-2 pt-4">
                    <?= renderButton('Annuler', 'secondary', $backLink) ?>
                    <?= renderButton($isEdit ? 'Mettre à jour' : 'Créer', 'primary', null, ['icon' => $isEdit ? 'fa-save' : 'fa-plus']) ?>
                </div>
            </div>
        </form>
    </div>
</div>