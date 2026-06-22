<?php
$g = $data['greeting'];
$kpi = $data['kpi'];
$charts = $data['charts'];
$mini = $data['mini'];
$stockBas = $data['stock_bas'];
$recent = $data['recent_activity'];

function fmtMoney($v): string {
    return number_format((float)$v, 0, ',', ' ') . ' F';
}
function fmtCount($v): string {
    return number_format((int)$v, 0, ',', ' ');
}
?>

<!-- Welcome -->
<div style="background: linear-gradient(135deg, #0078D4 0%, #0D47A1 100%);" class="rounded-xl p-6 md:p-8 mb-8 text-white shadow-card relative overflow-hidden">
    <div class="flex items-start justify-between relative z-10">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">
                    <?= strtoupper(substr(htmlspecialchars($g['user_name']), 0, 2)) ?>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Bonjour, <?= htmlspecialchars($g['user_name']) ?></h1>
                    <p class="text-white/80 text-sm mt-0.5">
                        <?= htmlspecialchars($g['user_role']) ?>
                        <span class="mx-2 text-white/40">•</span>
                        <?= date('d/m/Y') ?>
                    </p>
                </div>
            </div>
        </div>
        <a href="?action=profil" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/15 hover:bg-white/25 transition-all text-sm font-medium">
            <i class="fas fa-user-cog"></i>
            <span>Profil</span>
        </a>
    </div>
</div>

<!-- Mini stats bar -->
<?php if (!empty($mini)): ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
    <?php if (isset($mini['clients'])): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg border border-neutral-90 bg-white">
        <div class="w-9 h-9 rounded-lg bg-info-50 flex items-center justify-center text-info-500">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <p class="text-xs text-neutral-50 leading-tight">Clients</p>
            <p class="text-sm font-bold text-neutral-14"><?= fmtCount($mini['clients']['value']) ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if (isset($mini['fournisseurs'])): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg border border-neutral-90 bg-white">
        <div class="w-9 h-9 rounded-lg bg-warning-50 flex items-center justify-center text-warning-500">
            <i class="fas fa-truck"></i>
        </div>
        <div>
            <p class="text-xs text-neutral-50 leading-tight">Fournisseurs</p>
            <p class="text-sm font-bold text-neutral-14"><?= fmtCount($mini['fournisseurs']['value']) ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if (isset($mini['factures_impayees'])): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg border border-neutral-90 bg-white">
        <div class="w-9 h-9 rounded-lg bg-danger-50 flex items-center justify-center text-danger-500">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div>
            <p class="text-xs text-neutral-50 leading-tight">Factures impayées</p>
            <p class="text-sm font-bold text-neutral-14"><?= fmtCount($mini['factures_impayees']['value']) ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if (isset($mini['banques'])): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg border border-neutral-90 bg-white">
        <div class="w-9 h-9 rounded-lg bg-success-50 flex items-center justify-center text-success-500">
            <i class="fas fa-university"></i>
        </div>
        <div>
            <p class="text-xs text-neutral-50 leading-tight">Banques</p>
            <p class="text-sm font-bold text-neutral-14"><?= fmtCount($mini['banques']['value']) ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>


<!-- Main KPIs -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <?php if (isset($kpi['produits'])): ?>
    <div class="bg-white rounded-xl shadow-card overflow-hidden hover:shadow-card transition-all duration-200">
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-neutral-50 uppercase tracking-wider">Produits</span>
                <span class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600">
                    <i class="fas fa-cubes"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-neutral-14"><?= fmtCount($kpi['produits']['value']) ?></p>
            <p class="text-xs mt-1.5 flex items-center gap-1 <?= $kpi['produits']['sub_type'] === 'danger' ? 'text-danger-500' : 'text-success-500' ?>">
                <i class="fas fa-<?= $kpi['produits']['sub_type'] === 'danger' ? 'exclamation-circle' : 'check-circle' ?>"></i>
                <span><?= $kpi['produits']['sub'] ?></span>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <?php
    $cmdCount = 0;
    $hasCmdBcf = isset($kpi['bcf_en_cours']);
    $hasCmdClient = isset($kpi['commandes_en_cours']);
    if ($hasCmdBcf || $hasCmdClient):
        $cmdCount = ($kpi['bcf_en_cours']['value'] ?? 0) + ($kpi['commandes_en_cours']['value'] ?? 0);
    ?>
    <div class="bg-white rounded-xl shadow-card overflow-hidden hover:shadow-card transition-all duration-200">
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-neutral-50 uppercase tracking-wider">Commandes</span>
                <span class="w-9 h-9 rounded-lg bg-info-50 flex items-center justify-center text-info-500">
                    <i class="fas fa-clipboard-list"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-neutral-14"><?= fmtCount($cmdCount) ?></p>
            <p class="text-xs mt-1.5 text-neutral-50">
                <?php if ($hasCmdBcf): ?><span class="mr-3"><i class="fas fa-truck mr-1"></i>BCF: <?= fmtCount($kpi['bcf_en_cours']['value']) ?></span><?php endif; ?>
                <?php if ($hasCmdClient): ?><span><i class="fas fa-shopping-cart mr-1"></i>Clients: <?= fmtCount($kpi['commandes_en_cours']['value']) ?></span><?php endif; ?>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($kpi['ca_mois'])): ?>
    <div class="bg-white rounded-xl shadow-card overflow-hidden hover:shadow-card transition-all duration-200">
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-neutral-50 uppercase tracking-wider">CA du mois</span>
                <span class="w-9 h-9 rounded-lg bg-success-50 flex items-center justify-center text-success-500">
                    <i class="fas fa-chart-line"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-neutral-14"><?= fmtMoney($kpi['ca_mois']['value']) ?></p>
            <?php if (isset($kpi['encaissements_mois'])): ?>
            <p class="text-xs mt-1.5 text-success-600">
                <i class="fas fa-money-bill-wave mr-1"></i>Encaissé: <?= fmtMoney($kpi['encaissements_mois']['value']) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($kpi['achats_mois'])): ?>
    <div class="bg-white rounded-xl shadow-card overflow-hidden hover:shadow-card transition-all duration-200">
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-neutral-50 uppercase tracking-wider">Achats du mois</span>
                <span class="w-9 h-9 rounded-lg bg-warning-50 flex items-center justify-center text-warning-500">
                    <i class="fas fa-shopping-basket"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-neutral-14"><?= fmtMoney($kpi['achats_mois']['value']) ?></p>
            <?php if (isset($kpi['paiements_mois'])): ?>
            <p class="text-xs mt-1.5 text-warning-600">
                <i class="fas fa-credit-card mr-1"></i>Payé: <?= fmtMoney($kpi['paiements_mois']['value']) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>


<!-- Charts -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
    <?php if (isset($charts['ventes'])): ?>
    <div class="bg-white rounded-xl shadow-card">
        <div class="px-5 py-4 border-b border-neutral-90 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-brand-600"></div>
            <h3 class="text-sm font-semibold text-neutral-14">Évolution des ventes</h3>
            <span class="ml-auto text-caption text-neutral-50">TTC</span>
        </div>
        <div>
            <canvas id="ventesChart" height="200"></canvas>
        </div>
    </div>
    <?php endif; ?>
    <?php if (isset($charts['achats'])): ?>
    <div class="bg-white rounded-xl shadow-card">
        <div class="px-5 py-4 border-b border-neutral-90 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-success-500"></div>
            <h3 class="text-sm font-semibold text-neutral-14">Évolution des achats</h3>
            <span class="ml-auto text-caption text-neutral-50">TTC</span>
        </div>
        <div>
            <canvas id="achatsChart" height="200"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>


<!-- Bottom: Stock alerts + Activity -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
    <?php if ($stockBas !== null): ?>
    <div class="bg-white rounded-xl shadow-card">
        <div class="px-5 py-4 border-b border-neutral-90 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-danger-500"></div>
            <h3 class="text-sm font-semibold text-neutral-14">Alertes stock</h3>
            <?php if (count($stockBas) > 0): ?>
            <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-danger-50 text-danger-500">
                <?= count($stockBas) ?> produit(s)
            </span>
            <?php endif; ?>
        </div>
        <div class="p-0">
            <?php if (count($stockBas) > 0): ?>
            <div class="divide-y divide-neutral-90">
                <?php foreach ($stockBas as $p):
                    $ratio = $p['seuil_alerte'] > 0 ? min($p['stock_actuel'] / $p['seuil_alerte'], 1) : 0;
                    $pct = round($ratio * 100);
                    $barColor = $ratio <= 0.5 ? '#E53935' : ($ratio <= 0.75 ? '#FFA000' : '#0288D1');
                    $badge = $ratio <= 0.5 ? 'danger' : ($ratio <= 0.75 ? 'warning' : 'info');
                    $badgeLabel = $ratio <= 0.5 ? 'Critique' : ($ratio <= 0.75 ? 'Faible' : 'Moyen');
                ?>
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-neutral-14 truncate mr-3"><?= htmlspecialchars($p['nom_produit']) ?></span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap <?= $badge === 'danger' ? 'bg-danger-50 text-danger-500' : ($badge === 'warning' ? 'bg-warning-50 text-warning-500' : 'bg-info-50 text-info-500') ?>">
                            <?= $badgeLabel ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-2 rounded-full bg-neutral-95 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" style="width:<?= $pct ?>%;background:<?= $barColor ?>;"></div>
                        </div>
                        <span class="text-xs font-mono text-neutral-50 whitespace-nowrap">
                            <?= fmtCount($p['stock_actuel']) ?> / <?= fmtCount($p['seuil_alerte']) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-10 text-neutral-50">
                <div class="w-12 h-12 rounded-full bg-success-50 flex items-center justify-center text-success-500 mb-3">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <p class="text-sm font-medium text-neutral-30">Stock sous contrôle</p>
                <p class="text-xs mt-1">Aucun produit en dessous du seuil d'alerte.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($recent !== null): ?>
    <div class="bg-white rounded-xl shadow-card">
        <div class="px-5 py-4 border-b border-neutral-90 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-brand-600"></div>
            <h3 class="text-sm font-semibold text-neutral-14">Activités récentes</h3>
        </div>
        <div class="p-0">
            <?php if (count($recent) > 0): ?>
            <div class="divide-y divide-neutral-90">
                <?php foreach ($recent as $i => $a):
                    $colors = match($a['action']) {
                        'INSERT' => ['bg-success-50', 'text-success-500', 'fa-plus-circle'],
                        'UPDATE' => ['bg-info-50', 'text-info-500', 'fa-pen'],
                        'DELETE' => ['bg-danger-50', 'text-danger-500', 'fa-trash-can'],
                        default => ['bg-neutral-95', 'text-neutral-50', 'fa-circle'],
                    };
                ?>
                <div class="flex items-start gap-3 px-5 py-3.5">
                    <div class="w-8 h-8 rounded-full <?= $colors[0] ?> flex items-center justify-center <?= $colors[1] ?> flex-shrink-0 text-xs">
                        <i class="fas <?= $colors[2] ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-neutral-14">
                            <span class="font-semibold"><?= htmlspecialchars($a['action']) ?></span>
                            <span class="text-neutral-50">sur</span>
                            <span class="font-medium"><?= htmlspecialchars($a['table_cible'] ?? '—') ?></span>
                            <?php if ($a['id_enregistrement']): ?>
                            <span class="text-neutral-50">#</span><span class="font-mono text-xs text-neutral-50"><?= htmlspecialchars($a['id_enregistrement']) ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="text-xs text-neutral-50 mt-0.5 flex items-center gap-3">
                            <?php if ($a['nom_complet']): ?>
                            <span><i class="far fa-user mr-1"></i><?= htmlspecialchars($a['nom_complet']) ?></span>
                            <?php endif; ?>
                            <span><i class="far fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($a['date_heure'])) ?></span>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-10 text-neutral-50">
                <div class="w-12 h-12 rounded-full bg-neutral-95 flex items-center justify-center text-neutral-60 mb-3">
                    <i class="fas fa-inbox text-lg"></i>
                </div>
                <p class="text-sm font-medium text-neutral-30">Aucune activité</p>
                <p class="text-xs mt-1">Les actions récentes apparaîtront ici.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>


<script src="public/vendor/chart.umd.min.js"></script>
<script>
(function() {
    function fmt(y) {
        return y.toLocaleString('fr-FR', {minimumFractionDigits:0, maximumFractionDigits:0}) + ' F';
    }

    function buildChart(id, labels, data, label, color, gradient) {
        const el = document.getElementById(id);
        if (!el) return;
        const ctx = el.getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 200);
        grad.addColorStop(0, gradient);
        grad.addColorStop(1, 'rgba(255,255,255,0)');

        new Chart(el, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: grad,
                    borderColor: color,
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#242424',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return label + ': ' + fmt(ctx.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: {
                            color: '#999',
                            font: { size: 11 },
                            callback: function(v) {
                                if (v >= 1000000) return (v/1000000).toFixed(1) + 'M';
                                if (v >= 1000) return (v/1000).toFixed(0) + 'k';
                                return v;
                            }
                        },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        border: { display: false },
                        ticks: {
                            color: '#999',
                            font: { size: 11 }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    <?php if (isset($charts['ventes'])): ?>
    buildChart('ventesChart',
        <?= json_encode($charts['ventes']['labels']) ?>,
        <?= json_encode($charts['ventes']['data']) ?>,
        'Ventes',
        '#0078D4',
        'rgba(0,120,212,0.25)'
    );
    <?php endif; ?>
    <?php if (isset($charts['achats'])): ?>
    buildChart('achatsChart',
        <?= json_encode($charts['achats']['labels']) ?>,
        <?= json_encode($charts['achats']['data']) ?>,
        'Achats',
        '#43A047',
        'rgba(67,160,71,0.25)'
    );
    <?php endif; ?>
})();
</script>
