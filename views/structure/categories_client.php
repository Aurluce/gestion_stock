<?php
$title = "Catégories clients";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=categories_client'],
    ['label' => 'Catégories clients']
]);
ob_start();
?>

<?= renderPageHeader(
    'Catégories clients',
    'Gérer les catégories de clients (taux de remise)',
    renderButton('Nouvelle catégorie', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) .
    renderButton('Imprimer', 'secondary', '?action=categories_client&print=1', ['icon' => 'fa-print'])
) ?>

<?php
$actionsRenderer = function($row, $rowIndex) use ($categories) {
    $c = $categories[$rowIndex] ?? null;
    if (!$c) return '';
    
    $actions = renderButton('', 'icon', '', [
        'icon' => 'fa-edit',
        'title' => 'Modifier',
        'data-modal-toggle' => 'editModal',
        'data-edit-categorie' => json_encode([
            'id' => $c['id_categorie_client'],
            'nom' => $c['nom_categorie'],
            'taux' => $c['taux_remise'],
            'description' => $c['description'] ?? ''
        ])
    ]);
    $actions .= renderButton('', 'icon-danger', '?action=categories_client&delete=' . $c['id_categorie_client'], [
        'icon' => 'fa-trash',
        'title' => 'Supprimer',
        'data-confirm' => 'Supprimer cette catégorie ?'
    ]);
    return $actions;
};

$tableData = array_map(function($c) {
    return [
        htmlspecialchars($c['nom_categorie']),
        number_format($c['taux_remise'], 0) . '%',
        htmlspecialchars($c['description'] ?? '-'),
        $c['date_creation_fr'] ?? '-'
    ];
}, $categories);

echo renderResponsiveTable(
    ['Nom', 'Remise', 'Description', 'Date création'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 2,
        'mobileBadge' => null,
        'mobileHidden' => [3],
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune catégorie trouvée. Cliquez sur "Nouvelle catégorie" pour en créer une.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=categories_client" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderInput('nom_categorie', 'Nom de la catégorie *', 'text', '', null, ['required' => 'required', 'placeholder' => 'Ex: Premium, VIP, Standard']) . '
    ' . renderInput('taux_remise', 'Taux de remise (%)', 'number', '', null, ['step' => '0.01', 'min' => '0', 'max' => '100', 'placeholder' => '0']) . '
    ' . renderTextarea('description', 'Description', '') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Ajouter une catégorie client', $createBody);
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=categories_client">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_categorie_client" id="edit_id">
    ' . renderInput('nom_categorie', 'Nom de la catégorie *', 'text', '', null, ['id' => 'edit_nom', 'required' => 'required']) . '
    ' . renderInput('taux_remise', 'Taux de remise (%)', 'number', '', null, ['id' => 'edit_taux', 'step' => '0.01', 'min' => '0', 'max' => '100']) . '
    ' . renderTextarea('description', 'Description', '', null, ['id' => 'edit_desc']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier une catégorie client', $editBody);
?>

<script>
document.querySelectorAll('[data-edit-categorie]').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-edit-categorie'));
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nom').value = data.nom;
        document.getElementById('edit_taux').value = data.taux;
        document.getElementById('edit_desc').value = data.description || '';
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>