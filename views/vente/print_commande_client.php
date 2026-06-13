<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de commande <?= htmlspecialchars($commande['reference']) ?></title>
    <style>
        @page { margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 40px; color: #242424; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0078d4; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 22px; color: #0078d4; }
        .header .ref { text-align: right; }
        .header .ref strong { font-size: 18px; }
        .infos { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .infos div { font-size: 14px; line-height: 1.6; }
        .infos h3 { font-size: 12px; text-transform: uppercase; color: #808080; margin-bottom: 6px; letter-spacing: 0.05em; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #e0e0e0; padding: 10px 12px; font-size: 13px; text-align: left; }
        th { background: #f5f5f5; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; color: #808080; }
        td.num, th.num { text-align: right; }
        .totaux { display: flex; justify-content: flex-end; }
        .totaux table { width: 300px; }
        .totaux td { border: none; padding: 6px 12px; }
        .totaux .total-row td { font-weight: 700; font-size: 16px; border-top: 2px solid #0078d4; }
        .observations { margin-top: 20px; font-size: 13px; }
        .observations h3 { font-size: 12px; text-transform: uppercase; color: #808080; margin-bottom: 6px; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; font-size: 13px; }
        .footer .signature { text-align: center; width: 200px; }
        .footer .signature .line { border-top: 1px solid #242424; margin-top: 50px; padding-top: 6px; }
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
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">Bon de commande client</p>
        </div>
        <div class="ref">
            <strong>Référence : <?= htmlspecialchars($commande['reference']) ?></strong><br>
            <span style="font-size: 13px; color: #808080;">Date : <?= date('d/m/Y', strtotime($commande['date_commande'])) ?></span>
        </div>
    </div>

    <div class="infos">
        <div>
            <h3>Client</h3>
            <?= htmlspecialchars($commande['client_nom'] . ' ' . ($commande['client_prenom'] ?? '')) ?>
        </div>
        <div>
            <h3>Type de vente</h3>
            <?= $commande['type_vente'] === 'comptant' ? 'Comptant' : 'Crédit' ?>
        </div>
        <div>
            <h3>Statut</h3>
            <?= ucfirst(str_replace('_', ' ', $commande['statut'])) ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th class="num">Quantité</th>
                <th class="num">Prix unitaire</th>
                <th class="num">Remise (%)</th>
                <th class="num">Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                <td class="num"><?= rtrim(rtrim(number_format($l['quantite'], 3, '.', ' '), '0'), '.') ?> <?= htmlspecialchars($l['unite']) ?></td>
                <td class="num"><?= number_format($l['prix_unitaire'], 0, ',', ' ') ?> FCFA</td>
                <td class="num"><?= number_format($l['taux_remise'], 2) ?>%</td>
                <td class="num"><?= number_format($l['montant_ligne'], 0, ',', ' ') ?> FCFA</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totaux">
        <table>
            <tr class="total-row">
                <td>Total</td>
                <td class="num"><?= number_format($commande['montant_total'], 0, ',', ' ') ?> FCFA</td>
            </tr>
        </table>
    </div>

    <?php if (!empty($commande['observations'])): ?>
    <div class="observations">
        <h3>Observations</h3>
        <p><?= nl2br(htmlspecialchars($commande['observations'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <div class="line">Signature du client</div>
        </div>
        <div class="signature">
            <div class="line">Signature du responsable</div>
        </div>
    </div>
</body>
</html>
