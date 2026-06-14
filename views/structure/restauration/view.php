<?php
$title = "Détail de l'élément supprimé";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=restauration'],
    ['label' => 'Détail']
]);
ob_start();
?>

<?= renderPageHeader(
    'Détail de l\'élément',
    'Contenu XML sauvegardé',
    renderButton('Retour', 'secondary', '?action=restauration', ['icon' => 'fa-arrow-left'])
) ?>

<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <div class="flex items-center gap-2 flex-wrap">
                <?php
                $badgeType = match($element['type_objet']) {
                    'PRODUIT_COMPLET' => 'success',
                    'FOURNISSEUR_COMPLET' => 'info',
                    'MOUVEMENT_BANQUE' => 'warning',
                    default => 'neutral'
                };
                echo renderBadge(htmlspecialchars($element['type_objet']), $badgeType);
                ?>
                <span class="text-sm text-neutral-50">ID original: <?= $element['id_objet'] ?></span>
            </div>
            <div class="flex gap-2">
                <?= renderButton('Restaurer', 'success', '?action=restauration_restore&id=' . $element['id_corbeille'], ['icon' => 'fa-trash-restore', 'data-confirm' => 'Restaurer cet élément ?']) ?>
                <?= renderButton('Supprimer définitivement', 'danger', '?action=restauration_delete&id=' . $element['id_corbeille'], ['icon' => 'fa-trash', 'data-confirm' => 'Supprimer définitivement cet élément ?']) ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <p class="text-sm text-neutral-50">Date de suppression</p>
            <p class="text-body"><?= date('d/m/Y H:i:s', strtotime($element['date_suppression'])) ?></p>
        </div>
        <div>
            <p class="text-sm text-neutral-50 mb-2">Contenu XML</p>
            <pre class="bg-neutral-98 p-4 rounded-lg overflow-x-auto text-xs font-mono"><?= htmlspecialchars($element['donnees_xml']) ?></pre>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>