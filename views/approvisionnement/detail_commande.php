<?php
$title = "Détail : " . $commande['reference'];
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Approvisionnements'],
    ['label' => 'Bons de commande', 'href' => '?action=commande_fourn'],
    ['label' => $commande['reference']]
]);
$statutBadges = [
    'brouillon' => 'neutral',
    'envoye' => 'info',
    'receptionne' => 'success',
    'annule' => 'danger'
];
ob_start();
?>
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-heading-lg font-bold">Bon de commande <?= htmlspecialchars($commande['reference']) ?></h1>
            <p class="text-body text-neutral-50">Créé le <?= date('d/m/Y', strtotime($commande['date_commande'])) ?></p>
        </div>
        <div class="flex gap-2">
            <?php if (checkRightIfLogged('imprimer_bcf')): ?>
            <a href="?action=commande_fourn&print=<?= $commande['id_bcf'] ?>" class="btn-secondary"><i class="fas fa-print mr-2"></i>Imprimer</a>
            <?php endif; ?>
            <a href="?action=commande_fourn" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Retour</a>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="form-label">Fournisseur</span>
                <p class="font-semibold"><?= htmlspecialchars($commande['fournisseur_nom']) ?></p>
            </div>
            <div>
                <span class="form-label">Statut</span>
                <p><?= renderBadge(ucfirst($commande['statut']), $statutBadges[$commande['statut']] ?? 'neutral') ?></p>
            </div>
            <div>
                <span class="form-label">Montant total</span>
                <p class="font-semibold"><?= number_format($commande['montant_total'], 0, ',', ' ') ?> FCFA</p>
            </div>
        </div>
        <?php if (!empty($commande['observations'])): ?>
        <div class="card-body border-t border-neutral-90">
            <span class="form-label">Observations</span>
            <p class="text-body whitespace-pre-wrap"><?= nl2br(htmlspecialchars($commande['observations'])) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header px-4 py-3 border-b border-neutral-90">
            <h2 class="text-body-lg font-semibold">Lignes de commande</h2>
        </div>
        <?php
        $headers = ['Produit', 'Qté', 'Prix unit.', 'Montant'];
        $rows = [];
        foreach ($lignes as $l):
            $montant = $l['qte_commandee'] * $l['prix_unitaire'];
            $rows[] = [
                htmlspecialchars($l['nom_produit']),
                number_format($l['qte_commandee'], 2, ',', ' ') . ' ' . htmlspecialchars($l['unite'] ?? ''),
                number_format($l['prix_unitaire'], 0, ',', ' ') . ' FCFA',
                number_format($montant, 0, ',', ' ') . ' FCFA',
            ];
        endforeach;
        echo renderResponsiveTable($headers, $rows, [
            'mobileTitle' => 0,
            'mobileSubtitle' => 1,
            'mobileFields' => [2 => 'Prix unit.', 3 => 'Montant'],
            'emptyMessage' => 'Aucune ligne de commande.'
        ]);
        ?>
        <div class="flex justify-end items-center px-4 py-3 bg-neutral-98 font-semibold">
            <span>Total : <?= number_format($commande['montant_total'], 0, ',', ' ') ?> FCFA</span>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
