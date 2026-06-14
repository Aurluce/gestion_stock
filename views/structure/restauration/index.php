<?php
$title = "Corbeille - Restauration";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=restauration'],
    ['label' => 'Corbeille']
]);
ob_start();
?>

<?= renderPageHeader(
    'Corbeille',
    'Éléments supprimés et sauvegardés en XML',
    renderButton('Vider la corbeille', 'danger', '?action=restauration_clear', ['icon' => 'fa-trash', 'data-confirm' => 'Vider définitivement la corbeille ? Cette action est irréversible.'])
) ?>

<!-- Formulaire de filtrage -->
<?= renderFilterBar('restauration', [
    ['select', 'type', 'Type d\'objet', array_merge(['' => '-- Tous --'], array_combine($types, $types)), $typeFiltre],
    ['search', 'search', 'Rechercher', $search],
]) ?>

<?php if (empty($elements)): ?>
    <?= renderEmptyState('fa-trash-restore', 'Corbeille vide', 'Aucun élément supprimé pour le moment.') ?>
<?php else: ?>
    <?php
    $actionsRenderer = function($row, $rowIndex) use ($elements) {
        $e = $elements[$rowIndex] ?? null;
        if (!$e) return '';
        
        $actions = renderButton('', 'icon', '?action=restauration_view&id=' . $e['id_corbeille'], [
            'icon' => 'fa-eye',
            'title' => 'Voir le détail'
        ]);
        $actions .= renderButton('', 'icon', '?action=restauration_restore&id=' . $e['id_corbeille'], [
            'icon' => 'fa-trash-restore',
            'title' => 'Restaurer',
            'data-confirm' => 'Restaurer cet élément ?'
        ]);
        $actions .= renderButton('', 'icon-danger', '?action=restauration_delete&id=' . $e['id_corbeille'], [
            'icon' => 'fa-trash',
            'title' => 'Supprimer définitivement',
            'data-confirm' => 'Supprimer définitivement cet élément ?'
        ]);
        return $actions;
    };
    
    $tableData = array_map(function($e) {
        // Déterminer la couleur du badge selon le type (version étendue)
        $typeUpper = strtoupper($e['type_objet']);
        
        if (strpos($typeUpper, 'PRODUIT') !== false) {
            $badgeType = 'success';
        } elseif (strpos($typeUpper, 'FOURNISSEUR') !== false) {
            $badgeType = 'info';
        } elseif (strpos($typeUpper, 'CLIENT') !== false) {
            $badgeType = 'primary';
        } elseif (strpos($typeUpper, 'BANQUE') !== false) {
            $badgeType = 'warning';
        } elseif (strpos($typeUpper, 'FAMILLE') !== false) {
            $badgeType = 'secondary';
        } elseif (strpos($typeUpper, 'CATEGORIE') !== false) {
            $badgeType = 'dark';
        } elseif (strpos($typeUpper, 'MOUVEMENT') !== false) {
            $badgeType = 'danger';
        } else {
            $badgeType = 'neutral';
        }
        
        $xml = @simplexml_load_string($e['donnees_xml']);
        $nom = '';
        if ($xml) {
            if (isset($xml->nom)) $nom = (string)$xml->nom;
            elseif (isset($xml->nom_produit)) $nom = (string)$xml->nom_produit;
            elseif (isset($xml->nom_banque)) $nom = (string)$xml->nom_banque;
            elseif (isset($xml->nom_famille)) $nom = (string)$xml->nom_famille;
            elseif (isset($xml->nom_categorie)) $nom = (string)$xml->nom_categorie;
            elseif (isset($xml->reference)) $nom = (string)$xml->reference;
        }
        
        return [
            renderBadge(htmlspecialchars($e['type_objet']), $badgeType),
            htmlspecialchars($nom ?: '-'),
            $e['id_objet'],
            date('d/m/Y H:i:s', strtotime($e['date_suppression'])),
            htmlspecialchars($e['supprime_par_nom'] ?? 'Système')
        ];
    }, $elements);
    
    echo renderResponsiveTable(
        ['Type', 'Nom / Référence', 'ID original', 'Date suppression', 'Supprimé par'],
        $tableData,
        [
            'mobileTitle' => 1,
            'mobileSubtitle' => 0,
            'mobileBadge' => null,
            'mobileHidden' => [2, 3],
            'actions' => $actionsRenderer,
            'emptyMessage' => 'Aucun élément trouvé.'
        ]
    );
    ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>