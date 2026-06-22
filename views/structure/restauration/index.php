<?php
$title = "Corbeille - Restauration";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Corbeille']
]);
$pageActions = renderButton('Vider la corbeille', 'danger', '?action=restauration_clear', ['icon' => 'fa-trash', 'data-confirm' => 'Vider définitivement la corbeille ? Cette action est irréversible.', 'data-confirm-type' => 'danger']);
echo renderPageHeader('Corbeille', 'Éléments supprimés et sauvegardés en XML', $pageActions);
?>

<?= renderFilterBar('restauration', [
    ['select', 'type', "Type d'objet", !empty($types) ? array_combine($types, $types) : [], 'Tous les types'],
    ['search', 'search', 'Rechercher...'],
]) ?>

<?php if (empty($elements)): ?>
    <?= renderEmptyState('fa-trash-restore', 'Corbeille vide', 'Aucun élément supprimé pour le moment.') ?>
<?php else: ?>
    <?php
    $badgeMap = [
        'PRODUIT_COMPLET' => 'success',
        'FOURNISSEUR_COMPLET' => 'info',
        'MOUVEMENT_BANQUE' => 'warning',
    ];
    $rows = array_map(function($e) use ($badgeMap) {
        $badgeType = $badgeMap[$e['type_objet']] ?? 'neutral';
        return [
            renderBadge(htmlspecialchars($e['type_objet']), $badgeType),
            htmlspecialchars($e['nom'] ?: '-'),
            $e['id_objet'],
            date('d/m/Y H:i', strtotime($e['date_suppression'])),
            htmlspecialchars($e['supprime_par_nom'] ?? 'Système'),
        ];
    }, $elements);
    $actions = function($row, $rowIndex) use ($elements) {
        $e = $elements[$rowIndex] ?? null;
        if (!$e) return '';
        return renderButton('', 'icon', '?action=restauration_view&id=' . $e['id_corbeille'], ['icon' => 'fa-eye', 'title' => 'Voir le détail']) .
               renderButton('', 'icon', '?action=restauration_restore&id=' . $e['id_corbeille'], ['icon' => 'fa-trash-restore', 'title' => 'Restaurer', 'data-confirm' => 'Restaurer cet élément ?', 'data-confirm-type' => 'success']) .
               renderButton('', 'icon-danger', '?action=restauration_delete&id=' . $e['id_corbeille'], ['icon' => 'fa-trash', 'title' => 'Supprimer définitivement', 'data-confirm' => 'Supprimer définitivement ?', 'data-confirm-type' => 'danger']);
    };
    echo renderResponsiveTable(
        ['Type', 'Nom / Référence', 'ID original', 'Date suppression', 'Supprimé par'],
        $rows,
        [
            'mobileTitle' => 0,
            'mobileSubtitle' => 1,
            'mobileBadge' => 0,
            'mobileHidden' => [2, 4],
            'actions' => $actions,
            'emptyMessage' => 'Aucun élément dans la corbeille.'
        ]
    );
    ?>
<?php endif; ?>
