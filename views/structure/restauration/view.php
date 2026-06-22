<?php
$title = "Détail de l'élément supprimé";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Corbeille', 'href' => '?action=restauration'],
    ['label' => 'Détail']
]);
$backLink = '?action=restauration';
$rawXml = $element['donnees_xml'];
$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$valid = @$dom->loadXML($rawXml);
$xmlContent = htmlspecialchars($valid ? $dom->saveXML() : $rawXml);
$badgeType = match($element['type_objet']) {
    'PRODUIT_COMPLET' => 'success',
    'FOURNISSEUR_COMPLET' => 'info',
    'MOUVEMENT_BANQUE' => 'warning',
    default => 'neutral'
};
$labelMap = [
    'nom' => 'Nom',
    'nom_produit' => 'Nom du produit',
    'description' => 'Description',
    'prix_achat' => "Prix d'achat",
    'prix_vente' => 'Prix de vente',
    'stock_actuel' => 'Stock actuel',
    'seuil_alerte' => "Seuil d'alerte",
    'unite' => 'Unité',
    'id_famille' => 'ID Famille',
    'id_produit_pere' => 'ID Produit père',
    'tel' => 'Téléphone',
    'email' => 'Email',
    'adresse' => 'Adresse',
    'ville' => 'Ville',
    'nif' => 'NIF',
    'id_banque' => 'ID Banque',
    'type_mouvement' => 'Type mouvement',
    'montant' => 'Montant',
    'date_mouvement' => 'Date mouvement',
    'reference' => 'Référence',
];

echo renderPageHeader('Détail de l\'élément', 'Contenu sauvegardé', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <?= renderBadge($element['type_objet'], $badgeType) ?>
                <span class="text-sm text-neutral-50">ID original: <?= $element['id_objet'] ?></span>
            </div>
            <div class="flex gap-2">
                <?= renderButton('Restaurer', 'success', '?action=restauration_restore&id=' . $element['id_corbeille'], ['icon' => 'fa-trash-restore', 'data-confirm' => 'Restaurer cet élément ?', 'data-confirm-type' => 'success']) ?>
                <?= renderButton('Supprimer définitivement', 'danger', '?action=restauration_delete&id=' . $element['id_corbeille'], ['icon' => 'fa-trash', 'data-confirm' => 'Supprimer définitivement ?', 'data-confirm-type' => 'danger']) ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-6">
            <p class="text-caption text-neutral-50">Date de suppression</p>
            <p class="text-body"><?= date('d/m/Y H:i:s', strtotime($element['date_suppression'])) ?></p>
        </div>

        <?php if (!empty($parsedData)): ?>
            <div class="mb-6">
                <p class="text-caption text-neutral-50 mb-3">Données restaurées</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                    <?php foreach ($parsedData as $key => $value): ?>
                        <?php if (is_array($value)) continue; ?>
                        <div class="flex flex-col">
                            <span class="text-xs text-neutral-50"><?= htmlspecialchars($labelMap[$key] ?? $key) ?></span>
                            <span class="text-body-sm"><?= htmlspecialchars((string)$value ?: '-') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div>
            <details class="group">
                <summary class="text-caption text-neutral-50 cursor-pointer hover:text-neutral-30 select-none">
                    <i class="fas fa-chevron-right mr-1.5 transition-transform group-open:rotate-90"></i>
                    Voir le XML brut
                </summary>
                <pre class="bg-neutral-98 p-4 rounded-lg overflow-x-auto text-xs font-mono leading-relaxed mt-3"><?= $xmlContent ?></pre>
            </details>
        </div>
    </div>
</div>
