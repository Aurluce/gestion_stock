<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket <?= htmlspecialchars($facture['reference']) ?></title>
    <style>
        @page { margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Courier New', monospace; }
        body { padding: 20px; display: flex; justify-content: center; color: #242424; }
        .ticket { width: 280px; font-size: 12px; }
        .ticket h1 { text-align: center; font-size: 16px; margin-bottom: 4px; }
        .ticket .subtitle { text-align: center; font-size: 11px; color: #555; margin-bottom: 12px; }
        .ticket hr { border: none; border-top: 1px dashed #888; margin: 8px 0; }
        .ticket .row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .ticket table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        .ticket th, .ticket td { text-align: left; padding: 2px 0; font-size: 11px; }
        .ticket th.num, .ticket td.num { text-align: right; }
        .ticket .total-row { font-weight: 700; font-size: 14px; }
        .ticket .footer { text-align: center; margin-top: 16px; font-size: 11px; }
        .print-btn { text-align: center; margin-bottom: 20px; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div>
        <div class="print-btn">
            <button onclick="window.print()" style="padding: 10px 20px; background: #0078d4; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                Imprimer
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background: #ccc; color: #242424; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin-left: 10px;">
                Fermer
            </button>
        </div>

        <div class="ticket">
            <h1>GESTION DE STOCK</h1>
            <p class="subtitle">Ticket de vente</p>
            <hr>
            <div class="row"><span>Réf.</span><span><?= htmlspecialchars($facture['reference']) ?></span></div>
            <div class="row"><span>Date</span><span><?= date('d/m/Y H:i', strtotime($facture['date_creation'])) ?></span></div>
            <div class="row"><span>Client</span><span><?= htmlspecialchars($facture['client_nom'] . ' ' . ($facture['client_prenom'] ?? '')) ?></span></div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <th>Article</th>
                        <th class="num">Qté</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lignes as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                        <td class="num"><?= rtrim(rtrim(number_format($l['quantite'], 3, '.', ' '), '0'), '.') ?></td>
                        <td class="num"><?= number_format($l['montant_ligne'], 0, ',', ' ') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <hr>
            <div class="row"><span>Total HT</span><span><?= number_format($facture['montant_ht'], 0, ',', ' ') ?> FCFA</span></div>
            <div class="row"><span>TVA (<?= number_format($facture['taux_tva'], 2) ?>%)</span><span><?= number_format($facture['montant_ttc'] - $facture['montant_ht'], 0, ',', ' ') ?> FCFA</span></div>
            <div class="row total-row"><span>TOTAL TTC</span><span><?= number_format($facture['montant_ttc'], 0, ',', ' ') ?> FCFA</span></div>
            <hr>
            <?php foreach ($reglements as $r): ?>
            <div class="row"><span>Payé (<?= ucfirst(str_replace('_', ' ', $r['mode_paiement'])) ?>)</span><span><?= number_format($r['montant'], 0, ',', ' ') ?> FCFA</span></div>
            <?php endforeach; ?>
            <div class="footer">
                Merci pour votre achat !
            </div>
        </div>
    </div>
</body>
</html>
