<?php
$title = $title ?? 'État des achats annuel';
$backUrl = '?action=etats_achats&annee=' . urlencode($annee);
$customCss = '';
$hidePrintFooter = false;
ob_start();
?><div class="print-header">
        <h1>ÉTAT DES ACHATS ANNUEL</h1>
        <p>Année <?= $annee ?></p>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th>Mois</th>
                <th style="text-align:right">Nb bons</th>
                <th style="text-align:right">Total achats</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($moisData as $m): ?>
            <tr>
                <td><?= $moisLabels[(int)$m['mois']] ?? 'Mois ' . $m['mois'] ?></td>
                <td style="text-align:right"><?= $m['nb_bons'] ?></td>
                <td style="text-align:right"><?= number_format($m['total_mois'], 0, ',', ' ') ?> FCFA</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:bold">
                <td>TOTAL</td>
                <td style="text-align:right"><?= array_sum(array_map(fn($m) => $m['nb_bons'], $moisData)) ?></td>
                <td style="text-align:right"><?= number_format($totalAnnuel, 0, ',', ' ') ?> FCFA</td>
            </tr>
        </tfoot>
    </table>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
