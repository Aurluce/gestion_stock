<?php
$title = "Dashboard Ventes";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'Dashboard']
]);
ob_start();

$joursLabels = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
$maxTtc = max(array_map(fn($v) => $v['total_ttc'], $ventesParJour)) ?: 1;
?>
<?= renderPageHeader(
    'Dashboard Ventes',
    'Vue d\'ensemble de l\'activité commerciale',
    renderButton('États détaillés', 'secondary', '?action=etats_ventes', ['icon' => 'fa-chart-bar'])
) ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Ventes des 7 derniers jours</p>
            <p class="text-h3 font-bold text-neutral-14"><?= array_sum(array_map(fn($v) => $v['nb_factures'], $ventesParJour)) ?></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">CA TTC (7 jours)</p>
            <p class="text-h3 font-bold text-brand-600"><?= number_format(array_sum(array_map(fn($v) => $v['total_ttc'], $ventesParJour)), 0, ',', ' ') ?> FCFA</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Factures impayées</p>
            <p class="text-h3 font-bold text-danger-500"><?= $facturesImpayees['nb'] ?></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-caption text-neutral-50">Montant dû</p>
            <p class="text-h3 font-bold text-danger-500"><?= number_format($facturesImpayees['montant_du'], 0, ',', ' ') ?> FCFA</p>
        </div>
    </div>
</div>

<!-- Graphique ventes 7 jours -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="text-body-lg font-semibold text-neutral-14"><i class="fas fa-chart-line text-brand-600 mr-2"></i>Ventes des 7 derniers jours</h2>
    </div>
    <div class="card-body">
        <div style="display: flex; align-items: flex-end; gap: 12px; height: 180px;">
            <?php foreach ($ventesParJour as $v): ?>
            <?php
                $hauteur = $maxTtc > 0 ? max(4, ($v['total_ttc'] / $maxTtc) * 150) : 4;
                $jourSemaine = $joursLabels[date('w', strtotime($v['jour']))];
            ?>
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%;">
                <div style="font-size: 11px; color: #808080; margin-bottom: 4px;"><?= number_format($v['total_ttc'], 0, ',', ' ') ?></div>
                <div style="width: 100%; max-width: 40px; background: #0078d4; border-radius: 4px 4px 0 0; height: <?= $hauteur ?>px;"></div>
                <div style="font-size: 12px; color: #808080; margin-top: 6px; font-weight: 600;"><?= $jourSemaine ?></div>
                <div style="font-size: 11px; color: #b3b3b3;"><?= date('d/m', strtotime($v['jour'])) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Top produits -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-body-lg font-semibold text-neutral-14"><i class="fas fa-box text-brand-600 mr-2"></i>Top 5 produits vendus</h2>
        </div>
        <div class="card-body">
            <?php if (empty($topProduits)): ?>
            <p class="text-body text-neutral-50">Aucune vente enregistrée.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($topProduits as $i => $p): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-50 text-brand-600 text-caption font-bold"><?= $i + 1 ?></span>
                        <span class="text-body"><?= htmlspecialchars($p['nom_produit']) ?></span>
                    </div>
                    <div class="text-right">
                        <div class="text-body font-semibold"><?= rtrim(rtrim(number_format($p['qte_totale'], 3, '.', ' '), '0'), '.') ?> <?= htmlspecialchars($p['unite']) ?></div>
                        <div class="text-caption text-neutral-50"><?= number_format($p['montant_total'], 0, ',', ' ') ?> FCFA</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top clients -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-body-lg font-semibold text-neutral-14"><i class="fas fa-users text-brand-600 mr-2"></i>Top 5 clients</h2>
        </div>
        <div class="card-body">
            <?php if (empty($topClients)): ?>
            <p class="text-body text-neutral-50">Aucune facture enregistrée.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($topClients as $i => $c): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-success-50 text-success-500 text-caption font-bold"><?= $i + 1 ?></span>
                        <span class="text-body"><?= htmlspecialchars($c['nom'] . ' ' . ($c['prenom'] ?? '')) ?></span>
                    </div>
                    <div class="text-right">
                        <div class="text-body font-semibold"><?= number_format($c['montant_total'], 0, ',', ' ') ?> FCFA</div>
                        <div class="text-caption text-neutral-50"><?= $c['nb_factures'] ?> facture(s)</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alertes stock -->
<div class="card">
    <div class="card-header">
        <h2 class="text-body-lg font-semibold text-neutral-14"><i class="fas fa-exclamation-triangle text-warning-500 mr-2"></i>Produits en alerte de stock</h2>
    </div>
    <div class="card-body">
        <?php if (empty($produitsAlerte)): ?>
        <p class="text-body text-neutral-50">Aucun produit en alerte de stock.</p>
        <?php else: ?>
        <?php
        $tableData = array_map(function($p) {
            return [
                htmlspecialchars($p['nom_produit']),
                rtrim(rtrim(number_format($p['stock_actuel'], 3, '.', ' '), '0'), '.') . ' ' . htmlspecialchars($p['unite']),
                rtrim(rtrim(number_format($p['seuil_alerte'], 3, '.', ' '), '0'), '.') . ' ' . htmlspecialchars($p['unite']),
                renderBadge('Stock bas', 'danger')
            ];
        }, $produitsAlerte);
        echo renderResponsiveTable(
            ['Produit', 'Stock actuel', 'Seuil alerte', 'Statut'],
            $tableData,
            ['mobileTitle' => 0, 'mobileBadge' => 3]
        );
        ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
