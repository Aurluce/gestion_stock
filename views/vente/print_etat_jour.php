<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des ventes du <?= date('d/m/Y') ?></title>
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
</body>
</html>
