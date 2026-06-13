<?php
$title = "Familles de produits";
$pageActions = renderButton('Nouvelle famille', 'primary', '?action=famille_creer', ['icon' => 'fa-plus']);
echo renderPageHeader('Familles', 'Gérez les catégories de produits', $pageActions);
?>

<?= renderFlashAlerts() ?>

<?php if (empty($familles)): ?>
    <?= renderEmptyState(
        'fa-folder-open', 
        'Aucune famille', 
        'Commencez par créer votre première famille de produits.',
        renderButton('Créer une famille', 'primary', '?action=famille_creer', ['icon' => 'fa-plus'])
    ) ?>
<?php else: ?>
    <?php
    $rows = array_map(function($famille) {
        return [
            '<div class="flex items-center gap-2"><i class="fas fa-folder text-brand-500"></i>' . htmlspecialchars($famille['nom_famille']) . '</div>',
            htmlspecialchars($famille['description'] ?? '-'),
            $famille['date_creation_fr'] ?? '-',
            renderButton('', 'icon', '?action=famille_modifier&id=' . $famille['id_famille'], ['icon' => 'fa-edit', 'title' => 'Modifier']) .
            renderButton('', 'icon-danger', '?action=famille_supprimer&id=' . $famille['id_famille'], [
                'icon' => 'fa-trash', 
                'title' => 'Supprimer',
                'data-confirm' => 'Supprimer cette famille ?',
                'data-confirm-type' => 'danger'
            ])
        ];
    }, $familles);
    
    echo renderTable(['Nom', 'Description', 'Date création', 'Actions'], $rows);
    ?>
<?php endif; ?>