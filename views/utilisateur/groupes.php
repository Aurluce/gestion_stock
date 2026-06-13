<?php
$title = "Groupes d'utilisateurs";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Utilisateurs', 'href' => '?action=utilisateurs'],
    ['label' => 'Groupes']
]);
ob_start();
?>

<?= renderPageHeader(
    'Groupes',
    'Organiser les utilisateurs par groupes et gérer leurs droits',
    renderButton('Ajouter un groupe', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])
) ?>

<?php
// Préparer les données pour le tableau responsive
$tableData = [];
foreach ($groupes as $g) {
    $tableData[] = [
        '#' . $g['id_groupe'],
        htmlspecialchars($g['nom_groupe']),
        htmlspecialchars($g['description'] ?? '-')
    ];
}

// Fonction de rendu des actions
$actionsRenderer = function($row, $rowIndex) use ($groupes) {
    $g = $groupes[$rowIndex] ?? null;
    if (!$g) return '';
    
    $actions = renderButton('', 'icon', '', [
        'icon' => 'fa-edit',
        'title' => 'Modifier',
        'data-modal-toggle' => 'editModal',
        'data-edit-group' => json_encode($g)
    ]);
    $actions .= renderButton('', 'icon', '?action=groupes_droits&groupe_id=' . $g['id_groupe'], [
        'icon' => 'fa-shield-alt',
        'title' => 'Gérer les droits'
    ]);
    $actions .= renderButton('', 'icon-danger', '?action=groupes&delete=' . $g['id_groupe'], [
        'icon' => 'fa-trash',
        'title' => 'Supprimer',
        'data-confirm' => 'Supprimer ce groupe ?',
        'data-confirm-type' => 'danger'
    ]);
    return $actions;
};

echo renderResponsiveTable(
    ['ID', 'Nom', 'Description'],
    $tableData,
    [
        'mobileTitle' => 1,        // Nom comme titre
        'mobileSubtitle' => 2,     // Description comme sous-titre
        'mobileHidden' => [0],     // Cacher ID sur mobile
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun groupe trouvé.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=groupes" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderInput('nom_groupe', 'Nom du groupe', 'text', '', null, ['required' => 'required']) . '
    ' . renderInput('description', 'Description') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Ajouter le groupe', 'primary', null, ['icon' => 'fa-plus']) . '
    </div>
</form>';

echo renderModal('createModal', 'Ajouter un groupe', $createBody);
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=groupes">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_groupe" id="edit_id">
    <div class="space-y-4">
        ' . renderInput('nom_groupe', 'Nom du groupe', 'text', '', null, ['id' => 'edit_nom', 'required' => 'required']) . '
        ' . renderInput('description', 'Description', 'text', '', null, ['id' => 'edit_desc']) . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';

echo renderModal('editModal', 'Modifier un groupe', $editBody);
?>



<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
