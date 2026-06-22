<?php
$title = 'Catégories clients';
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Catégories clients']
]);
$pageActions = renderButton('Nouvelle catégorie client', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']);
echo renderPageHeader('Catégories clients', 'Gérez les catégories et remises des clients', $pageActions);
?>


<?php if (empty($categories)): ?>
    <?= renderEmptyState('fa-tags', 'Aucune catégorie client', 'Créez une catégorie client pour organiser vos clients et appliquer des remises.', renderButton('Créer une catégorie client', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
<?php else: ?>
    <?php
    $rows = array_map(function($categorie) {
        return [
            '<div class="flex items-center gap-2"><i class="fas fa-tags text-brand-500"></i>' . htmlspecialchars($categorie['nom_categorie']) . '</div>',
            htmlspecialchars($categorie['taux_remise']) . ' %',
            htmlspecialchars($categorie['description'] ?: '-'),
            $categorie['date_creation_fr'] ?? '-',
        ];
    }, $categories);

    $actionsRenderer = function($row, $rowIndex) use ($categories) {
        $c = $categories[$rowIndex] ?? null;
        if (!$c) return '';
        return renderButton('', 'icon', '', ['icon' => 'fa-edit', 'title' => 'Modifier', 'data-modal-toggle' => 'editModal', 'data-edit-cat' => $c['id_categorie_client']]) .
               renderButton('', 'icon-danger', '?action=categorie_client_supprimer&id=' . $c['id_categorie_client'], [
                   'icon' => 'fa-trash',
                   'title' => 'Supprimer',
                   'data-confirm' => 'Supprimer cette catégorie client ?',
                   'data-confirm-type' => 'danger'
               ]);
    };

    echo renderResponsiveTable(
        ['Nom', 'Taux de remise', 'Description', 'Date création'],
        $rows,
        [
            'mobileTitle' => 0,
            'mobileSubtitle' => 1,
            'mobileHidden' => [2, 3],
            'actions' => $actionsRenderer,
            'emptyMessage' => 'Aucune catégorie client trouvée.'
        ]
    );
    ?>
<?php endif; ?>

<?php
$createBody = '
<form method="POST" action="?action=categorie_clients" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderInput('nom_categorie', 'Nom de la catégorie *', 'text', '', null, ['required' => 'required']) . '
    ' . renderInput('taux_remise', 'Taux de remise (%)', 'number', 0, null, ['step' => '0.01', 'min' => '0']) . '
    ' . renderTextarea('description', 'Description', '') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-plus mr-1.5"></i>Créer</button>
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle catégorie client', $createBody);

$editBody = '
<form method="POST" action="?action=categorie_clients" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_categorie_client" id="edit_id_cat">
    ' . renderInput('nom_categorie', 'Nom de la catégorie *', 'text', '', null, ['id' => 'edit_nom_categorie', 'required' => 'required']) . '
    ' . renderInput('taux_remise', 'Taux de remise (%)', 'number', 0, null, ['id' => 'edit_taux_remise', 'step' => '0.01', 'min' => '0']) . '
    ' . renderTextarea('description', 'Description', '', null, ['id' => 'edit_description']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-save mr-1.5"></i>Mettre à jour</button>
    </div>
</form>';
echo renderModal('editModal', 'Modifier la catégorie client', $editBody);
?>

<script>
const CATEGORIES = <?= json_encode($categories) ?>;
document.querySelectorAll('[data-edit-cat]').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-edit-cat');
        const c = CATEGORIES.find(c => c.id_categorie_client == id);
        if (!c) return;
        document.getElementById('edit_id_cat').value = c.id_categorie_client;
        document.getElementById('edit_nom_categorie').value = c.nom_categorie || '';
        document.getElementById('edit_taux_remise').value = c.taux_remise || 0;
        document.getElementById('edit_description').value = c.description || '';
    });
});
</script>
