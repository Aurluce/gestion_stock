<?php
$title = 'État des ventes annuel ' . htmlspecialchars($annee);
$backUrl = null;
$customCss = <<<'CSS'
.stats { display: flex; gap: 20px; margin-bottom: 30px; }
.stat-box { flex: 1; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; text-align: center; }
.stat-box .label { font-size: 11px; text-transform: uppercase; color: #808080; letter-spacing: 0.05em; }
.stat-box .value { font-size: 22px; font-weight: 700; color: #0078d4; margin-top: 4px; }
tr.total-row td { font-weight: 700; border-top: 2px solid #0078d4; }
@media print { body { padding: 0; } }
CSS;
$hidePrintFooter = true;
ob_start();
?><div class="header">
        <div>
            <h1>GESTION DE STOCK</h1>
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">État des ventes annuel - <?= htmlspecialchars($annee) ?></p>
        </div>
        <div class="date">
            Généré le <?= date('d/m/Y à H:i') ?>
        </div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="label">Nombre de ventes</div>
            <div class="value"><?= $totalAnnee['nb_factures'] ?></div>
        </div>
        <div class="stat-box">
            <div class="label">Total HT</div>
            <div class="value"><?= number_format($totalAnnee['total_ht'], 0, ',', ' ') ?> FCFA</div>
        </div>
        <div class="stat-box">
            <div class="label">Total TTC</div>
            <div class="value"><?= number_format($totalAnnee['total_ttc'], 0, ',', ' ') ?> FCFA</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mois</th>
                <th class="num">Nombre de ventes</th>
                <th class="num">Total HT</th>
                <th class="num">Total TTC</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $moisLabels = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];
            if (empty($statsParMois)):
            ?>
            <tr><td colspan="4" style="text-align: center; color: #808080;">Aucune vente enregistrée pour cette année.</td></tr>
            <?php endif; ?>
            <?php foreach ($statsParMois as $row): ?>
            <tr>
                <td><?= $moisLabels[$row['mois']] ?></td>
                <td class="num"><?= $row['nb_factures'] ?></td>
                <td class="num"><?= number_format($row['total_ht'], 0, ',', ' ') ?> FCFA</td>
                <td class="num"><?= number_format($row['total_ttc'], 0, ',', ' ') ?> FCFA</td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td>Total <?= htmlspecialchars($annee) ?></td>
                <td class="num"><?= $totalAnnee['nb_factures'] ?></td>
                <td class="num"><?= number_format($totalAnnee['total_ht'], 0, ',', ' ') ?> FCFA</td>
                <td class="num"><?= number_format($totalAnnee['total_ttc'], 0, ',', ' ') ?> FCFA</td>
            </tr>
        </tbody>
    </table>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
