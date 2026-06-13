<?php
$title = 'État des ventes du ' . date('d/m/Y');
$backUrl = null;
$customCss = <<<'CSS'
.stats { display: flex; gap: 20px; margin-bottom: 30px; }
.stat-box { flex: 1; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; text-align: center; }
.stat-box .label { font-size: 11px; text-transform: uppercase; color: #808080; letter-spacing: 0.05em; }
.stat-box .value { font-size: 22px; font-weight: 700; color: #0078d4; margin-top: 4px; }
@media print { body { padding: 0; } }
CSS;
$hidePrintFooter = true;
ob_start();
?><div class="header">
        <div>
            <h1>GESTION DE STOCK</h1>
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">État des ventes journalier</p>
        </div>
        <div class="date">
            Date : <?= date('d/m/Y') ?><br>
            Généré le <?= date('d/m/Y à H:i') ?>
        </div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="label">Nombre de ventes</div>
            <div class="value"><?= $stats['nb_factures'] ?></div>
        </div>
        <div class="stat-box">
            <div class="label">Total HT</div>
            <div class="value"><?= number_format($stats['total_ht'], 0, ',', ' ') ?> FCFA</div>
        </div>
        <div class="stat-box">
            <div class="label">Total TTC</div>
            <div class="value"><?= number_format($stats['total_ttc'], 0, ',', ' ') ?> FCFA</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Commande</th>
                <th>Client</th>
                <th class="num">Montant HT</th>
                <th class="num">Montant TTC</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($factures)): ?>
            <tr><td colspan="6" style="text-align: center; color: #808080;">Aucune vente enregistrée aujourd'hui.</td></tr>
            <?php endif; ?>
            <?php foreach ($factures as $f): ?>
            <tr>
                <td><?= htmlspecialchars($f['reference']) ?></td>
                <td><?= htmlspecialchars($f['cc_reference']) ?></td>
                <td><?= htmlspecialchars($f['client_nom'] . ' ' . ($f['client_prenom'] ?? '')) ?></td>
                <td class="num"><?= number_format($f['montant_ht'], 0, ',', ' ') ?> FCFA</td>
                <td class="num"><?= number_format($f['montant_ttc'], 0, ',', ' ') ?> FCFA</td>
                <td><?= ucfirst($f['statut']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
