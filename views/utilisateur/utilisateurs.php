<?php
$title = "Gestion des utilisateurs";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Utilisateurs']
]);
ob_start();
?>

<?= renderPageHeader(
    'Utilisateurs',
    'Gérer les comptes et les accès des utilisateurs',
    renderButton('Ajouter un utilisateur', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])
) ?>

<?php
// Fonction de rendu des actions
$actionsRenderer = function($row, $rowIndex) use ($users) {
    $u = $users[$rowIndex] ?? null;
    if (!$u) return '';
    
    $actions = renderButton('', 'icon', '', [
        'icon' => 'fa-edit',
        'title' => 'Modifier',
        'data-modal-toggle' => 'editModal',
        'data-edit-user' => json_encode($u)
    ]);
    $actions .= renderButton('', 'icon-danger', '?action=utilisateurs&delete=' . $u['id_utilisateur'], [
        'icon' => 'fa-trash',
        'title' => 'Supprimer',
        'data-confirm' => 'Supprimer cet utilisateur ?'
    ]);
    return $actions;
};

// Préparer les données pour le tableau responsive
$tableData = array_map(function($u) {
    return [
        '#' . $u['id_utilisateur'],
        htmlspecialchars($u['nom_complet']),
        htmlspecialchars($u['login']),
        htmlspecialchars($u['nom_groupe'] ?? '-'),
        $u['actif'] ? renderBadge('Actif', 'success') : renderBadge('Inactif', 'danger'),
        $u['date_expiration_mdp'] ?? '-'
    ];
}, $users);

echo renderResponsiveTable(
    ['ID', 'Nom complet', 'Identifiant', 'Groupe', 'Statut', 'Exp. MDP'],
    $tableData,
    [
        'mobileTitle' => 1,        // Nom complet comme titre
        'mobileSubtitle' => 2,     // Login comme sous-titre
        'mobileBadge' => 4,        // Statut en badge
        'mobileHidden' => [0, 5],  // Cacher ID et date expiration sur mobile
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun utilisateur trouvé.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=utilisateurs" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderInput('nom_complet', 'Nom complet', 'text', '', null, ['required' => 'required']) . '
    ' . renderInput('login', 'Identifiant', 'text', '', null, ['required' => 'required']) . '
    ' . renderInput('password', 'Mot de passe', 'password', '', null, ['required' => 'required']) . '
    ' . renderSelect('id_groupe', 'Groupe', array_column($groupes, 'nom_groupe', 'id_groupe'), null, null, ['required' => 'required'], 'Sélectionner un groupe') . '
    ' . renderCheckbox('actif', 'Actif', true) . '
    ' . renderInput('date_expiration_mdp', 'Expiration du mot de passe', 'date') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer l\'utilisateur', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';

echo renderModal('createModal', 'Ajouter un utilisateur', $createBody);
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=utilisateurs">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_utilisateur" id="edit_id">
    <div class="space-y-4">
        ' . renderInput('nom_complet', 'Nom complet', 'text', '', null, ['id' => 'edit_nom', 'required' => 'required']) . '
        ' . renderInput('login', 'Identifiant', 'text', '', null, ['id' => 'edit_login', 'required' => 'required']) . '
        ' . renderInput('password', 'Nouveau mot de passe (laisser vide pour conserver)', 'password', '', null, ['id' => 'edit_password']) . '
        ' . renderSelect('id_groupe', 'Groupe', array_column($groupes, 'nom_groupe', 'id_groupe'), null, null, ['id' => 'edit_groupe', 'required' => 'required'], 'Sélectionner un groupe') . '
        ' . renderCheckbox('actif', 'Actif', true, ['id' => 'edit_actif']) . '
        ' . renderInput('date_expiration_mdp', 'Expiration', 'date', '', null, ['id' => 'edit_exp']) . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';

echo renderModal('editModal', 'Modifier un utilisateur', $editBody);
?>



<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
