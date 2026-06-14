<?php
$isEdit = isset($categorie) && $categorie !== null;
$title = $isEdit ? 'Modifier la catégorie client' : 'Nouvelle catégorie client';
$action = $isEdit ? '?action=categorie_client_mettre_a_jour' : '?action=categorie_client_enregistrer';
$backLink = '?action=categorie_clients';

$nom = $isEdit ? ($categorie['nom_categorie'] ?? '') : ($_SESSION['old']['nom_categorie'] ?? '');
$taux_remise = $isEdit ? ($categorie['taux_remise'] ?? 0) : ($_SESSION['old']['taux_remise'] ?? 0);
$description = $isEdit ? ($categorie['description'] ?? '') : ($_SESSION['old']['description'] ?? '');
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

echo renderPageHeader($title, '', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $action ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_categorie_client" value="<?= $categorie['id_categorie_client'] ?>">
            <?php endif; ?>

            <div class="space-y-4">
                <?= renderInput('nom_categorie', 'Nom de la catégorie *', 'text', $nom, $errors['nom_categorie'] ?? null, ['required' => 'required']) ?>
                <?= renderInput('taux_remise', 'Taux de remise (%)', 'number', $taux_remise, $errors['taux_remise'] ?? null, ['step' => '0.01', 'min' => '0']) ?>
                <?= renderTextarea('description', 'Description', $description, $errors['description'] ?? null) ?>

                <div class="flex justify-end gap-2 pt-4">
                    <?= renderButton('Annuler', 'secondary', $backLink) ?>
                    <?= renderButton($isEdit ? 'Mettre à jour' : 'Créer', 'primary', null, ['icon' => $isEdit ? 'fa-save' : 'fa-plus']) ?>
                </div>
            </div>
        </form>
    </div>
</div>
