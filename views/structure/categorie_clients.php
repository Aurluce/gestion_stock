<?php
$title = 'Catégories clients';
$pageActions = renderButton('Nouvelle catégorie client', 'primary', '?action=categorie_client_creer', ['icon' => 'fa-plus']);
echo renderPageHeader('Catégories clients', 'Gérez les catégories et remises des clients', $pageActions);
?>

<?= renderFlashAlerts() ?>

<?php if (empty($categories)): ?>
    <?= renderEmptyState(
        'fa-tags',
        'Aucune catégorie client',
        'Créez une catégorie client pour organiser vos clients et appliquer des remises.',
        renderButton('Créer une catégorie client', 'primary', '?action=categorie_client_creer', ['icon' => 'fa-plus'])
    ) ?>
<?php else: ?>
    <?php
    $rows = array_map(function($categorie) {
        return [
            '<div class="flex items-center gap-2"><i class="fas fa-tags text-brand-500"></i>' . htmlspecialchars($categorie['nom_categorie']) . '</div>',
            htmlspecialchars($categorie['taux_remise']) . ' %',
            htmlspecialchars($categorie['description'] ?: '-'),
            $categorie['date_creation_fr'] ?? '-',
            renderButton('', 'icon', '?action=categorie_client_modifier&id=' . $categorie['id_categorie_client'], ['icon' => 'fa-edit', 'title' => 'Modifier']) .
            renderButton('', 'icon-danger', '?action=categorie_client_supprimer&id=' . $categorie['id_categorie_client'], [
                'icon' => 'fa-trash',
                'title' => 'Supprimer',
                'data-confirm' => 'Supprimer cette catégorie client ?',
                'data-confirm-type' => 'danger'
            ])
        ];
    }, $categories);

    echo renderTable(['Nom', 'Taux de remise', 'Description', 'Date création', 'Actions'], $rows);
    ?>
<?php endif; ?>
