<?php
$title = "Familles";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=familles'],
    ['label' => 'Familles']
]);
ob_start();
?>

<?= renderPageHeader(
    'Familles',
    'Gérer les catégories de produits',
    renderButton('Nouvelle famille', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) .
    renderButton('Imprimer', 'secondary', '?action=familles&print=1', ['icon' => 'fa-print'])
) ?>

<?php
$actionsRenderer = function($row, $rowIndex) use ($familles) {
    $f = $familles[$rowIndex] ?? null;
    if (!$f) return '';
    
    $editData = htmlspecialchars(json_encode([
        'id' => $f['id_famille'],
        'nom' => $f['nom_famille'],
        'description' => $f['description'] ?? ''
    ]), ENT_QUOTES, 'UTF-8');
    
    $actions = '<button type="button" class="btn-icon" data-modal-toggle="editModal" data-edit-famille=\'' . $editData . '\' title="Modifier"><i class="fas fa-edit"></i></button>';
    $actions .= '<a href="?action=familles&delete=' . $f['id_famille'] . '" class="btn-icon-danger" onclick="return confirm(\'Supprimer cette famille ?\')" title="Supprimer"><i class="fas fa-trash-alt"></i></a>';
    return $actions;
};

$tableData = array_map(function($f) {
    return [
        '<div class="flex items-center gap-2"><i class="fas fa-folder text-brand-500"></i>' . htmlspecialchars($f['nom_famille']) . '</div>',
        htmlspecialchars($f['description'] ?? '-'),
        $f['date_creation_fr'] ?? '-'
    ];
}, $familles);

echo renderResponsiveTable(
    ['Nom', 'Description', 'Date création'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => null,
        'mobileHidden' => [2],
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune famille trouvée.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=familles" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderInput('nom_famille', 'Nom de la famille *', 'text', '', null, ['required' => 'required']) . '
    ' . renderTextarea('description', 'Description', '') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Ajouter une famille', $createBody);
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=familles">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_famille" id="edit_id">
    ' . renderInput('nom_famille', 'Nom de la famille *', 'text', '', null, ['id' => 'edit_nom', 'required' => 'required']) . '
    ' . renderTextarea('description', 'Description', '', null, ['id' => 'edit_desc']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier une famille', $editBody);
?>

<script>
document.querySelectorAll('[data-edit-famille]').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-edit-famille'));
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nom').value = data.nom;
        document.getElementById('edit_desc').value = data.description || '';
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>