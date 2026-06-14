<?php
$title = "Clients";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=clients'],
    ['label' => 'Clients']
]);
ob_start();
?>

<?= renderPageHeader(
    'Clients',
    'Gérer vos clients',
    renderButton('Nouveau client', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) .
    renderButton('Imprimer', 'secondary', '?action=clients&print=1', ['icon' => 'fa-print'])
) ?>

<?php
$actionsRenderer = function($row, $rowIndex) use ($clients) {
    $c = $clients[$rowIndex] ?? null;
    if (!$c) return '';
    
    $actions = renderButton('', 'icon', '', [
        'icon' => 'fa-edit',
        'title' => 'Modifier',
        'data-modal-toggle' => 'editModal',
        'data-edit-client' => json_encode($c)
    ]);
    $actions .= renderButton('', 'icon-danger', '?action=clients&delete=' . $c['id_client'], [
        'icon' => 'fa-trash',
        'title' => 'Supprimer',
        'data-confirm' => 'Supprimer ce client ?'
    ]);
    return $actions;
};

$tableData = array_map(function($c) {
    $nomComplet = '<div class="flex items-center gap-2"><i class="fas fa-user text-brand-500"></i>' . htmlspecialchars($c['nom'] . ' ' . ($c['prenom'] ?? '')) . '</div>';
    $typeLabel = match($c['type_client'] ?? 'particulier') {
        'particulier' => 'Particulier',
        'entreprise' => 'Entreprise',
        'administration' => 'Administration',
        default => $c['type_client']
    };
    return [
        $nomComplet,
        htmlspecialchars($c['tel'] ?? '-'),
        htmlspecialchars($c['email'] ?? '-'),
        htmlspecialchars($c['ville'] ?? '-'),
        renderBadge($typeLabel, 'info')
    ];
}, $clients);

echo renderResponsiveTable(
    ['Nom complet', 'Téléphone', 'Email', 'Ville', 'Type'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => 4,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun client trouvé.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=clients" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('nom', 'Nom *', 'text', '', null, ['required' => 'required']) . '
        ' . renderInput('prenom', 'Prénom', 'text', '') . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('tel', 'Téléphone', 'tel', '') . '
        ' . renderInput('email', 'Email', 'email', '') . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('ville', 'Ville', 'text', '') . '
        ' . renderSelect('type_client', 'Type de client', ['particulier' => 'Particulier', 'entreprise' => 'Entreprise', 'administration' => 'Administration'], 'particulier') . '
    </div>
    ' . renderCheckbox('est_actif', 'Client actif', true) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Ajouter un client', $createBody);
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=clients">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_client" id="edit_id">
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('nom', 'Nom *', 'text', '', null, ['id' => 'edit_nom', 'required' => 'required']) . '
        ' . renderInput('prenom', 'Prénom', 'text', '', null, ['id' => 'edit_prenom']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('tel', 'Téléphone', 'tel', '', null, ['id' => 'edit_tel']) . '
        ' . renderInput('email', 'Email', 'email', '', null, ['id' => 'edit_email']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('ville', 'Ville', 'text', '', null, ['id' => 'edit_ville']) . '
        ' . renderSelect('type_client', 'Type de client', ['particulier' => 'Particulier', 'entreprise' => 'Entreprise', 'administration' => 'Administration'], 'particulier', null, ['id' => 'edit_type_client']) . '
    </div>
    ' . renderCheckbox('est_actif', 'Client actif', true, ['id' => 'edit_est_actif']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier un client', $editBody);
?>

<script>
document.querySelectorAll('[data-edit-client]').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-edit-client'));
        document.getElementById('edit_id').value = data.id_client;
        document.getElementById('edit_nom').value = data.nom;
        document.getElementById('edit_prenom').value = data.prenom || '';
        document.getElementById('edit_tel').value = data.tel || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_ville').value = data.ville || '';
        document.getElementById('edit_type_client').value = data.type_client || 'particulier';
        document.getElementById('edit_est_actif').checked = data.est_actif;
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>