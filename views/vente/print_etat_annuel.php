<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des ventes annuel <?= htmlspecialchars($annee) ?></title>
    <style>
        @page { margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 40px; color: #242424; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0078d4; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 22px; color: #0078d4; }
        .header .date { text-align: right; font-size: 14px; color: #808080; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-box { flex: 1; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; text-align: center; }
        .stat-box .label { font-size: 11px; text-transform: uppercase; color: #808080; letter-spacing: 0.05em; }
        .stat-box .value { font-size: 22px; font-weight: 700; color: #0078d4; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e0e0e0; padding: 10px 12px; font-size: 13px; text-align: left; }
        th { background: #f5f5f5; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; color: #808080; }
        td.num, th.num { text-align: right; }
        tr.total-row td { font-weight: 700; border-top: 2px solid #0078d4; }
        .print-btn { margin-bottom: 20px; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0078d4; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            Imprimer
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #ccc; color: #242424; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            Fermer
        </button>
    </div>

    <div class="header">
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
</body>
</html>
