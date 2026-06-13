<?php
$title = "Détail de l'élément supprimé";
$backLink = '?action=restauration';
$xmlContent = htmlspecialchars($element['donnees_xml']);

echo renderPageHeader('Détail de l\'élément', 'Contenu XML sauvegardé', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <div>
                <span class="badge-info"><?= htmlspecialchars($element['type_objet']) ?></span>
                <span class="ml-2 text-sm text-neutral-50">ID original: <?= $element['id_objet'] ?></span>
            </div>
            <div class="flex gap-2">
                <?= renderButton('Restaurer', 'success', '?action=restauration_restore&id=' . $element['id_corbeille'], ['icon' => 'fa-trash-restore', 'data-confirm' => 'Restaurer cet élément ?', 'data-confirm-type' => 'success']) ?>
                <?= renderButton('Supprimer définitivement', 'danger', '?action=restauration_delete&id=' . $element['id_corbeille'], ['icon' => 'fa-trash', 'data-confirm' => 'Supprimer définitivement ?', 'data-confirm-type' => 'danger']) ?>
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
            <pre class="bg-neutral-98 p-4 rounded-lg overflow-x-auto text-xs font-mono"><?= $xmlContent ?></pre>
        </div>
    </div>
</div>