<?php
$title = "Familles de produits";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Familles']
]);
$pageActions = renderButton('Nouvelle famille', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']);
echo renderPageHeader('Familles', 'Gérez les catégories de produits', $pageActions);
?>


<?php if (empty($familles)): ?>
    <?= renderEmptyState('fa-folder-open', 'Aucune famille', 'Commencez par créer votre première famille de produits.', renderButton('Créer une famille', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
<?php else: ?>
    <?php
    $rows = array_map(function($famille) {
        return [
            '<div class="flex items-center gap-2"><i class="fas fa-folder text-brand-500"></i>' . htmlspecialchars($famille['nom_famille']) . '</div>',
            htmlspecialchars($famille['description'] ?? '-'),
            $famille['date_creation_fr'] ?? '-',
        ];
    }, $familles);

    $actionsRenderer = function($row, $rowIndex) use ($familles) {
        $f = $familles[$rowIndex] ?? null;
        if (!$f) return '';
        return renderButton('', 'icon', '', ['icon' => 'fa-eye', 'title' => 'Voir', 'data-detail' => '?action=famille_detail&id=' . $f['id_famille'], 'data-detail-title' => 'Famille : ' . $f['nom_famille']]) .
               renderButton('', 'icon', '', ['icon' => 'fa-edit', 'title' => 'Modifier', 'data-modal-toggle' => 'editModal', 'data-edit-famille' => $f['id_famille']]) .
               renderButton('', 'icon-danger', '?action=famille_supprimer&id=' . $f['id_famille'], [
                   'icon' => 'fa-trash',
                   'title' => 'Supprimer',
                   'data-confirm' => 'Supprimer cette famille ?',
                   'data-confirm-type' => 'danger'
               ]);
    };

    echo renderResponsiveTable(
        ['Nom', 'Description', 'Date création'],
        $rows,
        [
            'mobileTitle' => 0,
            'mobileSubtitle' => 1,
            'mobileHidden' => [2],
            'actions' => $actionsRenderer,
            'emptyMessage' => 'Aucune famille trouvée.'
        ]
    );
    ?>
<?php endif; ?>

<?php
$createBody = '
<form method="POST" action="?action=familles" class="space-y-4">
    <input type="hidden" name="action" value="add">
    ' . renderInput('nom_famille', 'Nom de la famille *', 'text', '', null, ['required' => 'required']) . '
    ' . renderTextarea('description', 'Description', '') . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-plus mr-1.5"></i>Créer</button>
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle famille', $createBody);

$editBody = '
<form method="POST" action="?action=familles" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_famille" id="edit_id_famille">
    ' . renderInput('nom_famille', 'Nom de la famille *', 'text', '', null, ['id' => 'edit_nom_famille', 'required' => 'required']) . '
    ' . renderTextarea('description', 'Description', '', null, ['id' => 'edit_description']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-save mr-1.5"></i>Mettre à jour</button>
    </div>
</form>';
echo renderModal('editModal', 'Modifier la famille', $editBody);
?>

<script>
const FAMILLES = <?= json_encode($familles) ?>;
document.querySelectorAll('[data-edit-famille]').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-edit-famille');
        const f = FAMILLES.find(f => f.id_famille == id);
        if (!f) return;
        document.getElementById('edit_id_famille').value = f.id_famille;
        document.getElementById('edit_nom_famille').value = f.nom_famille || '';
        document.getElementById('edit_description').value = f.description || '';
    });
});
</script>
