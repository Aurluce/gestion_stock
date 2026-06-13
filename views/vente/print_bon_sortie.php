<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de sortie <?= htmlspecialchars($sortie['reference']) ?></title>
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
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">Bon de sortie</p>
        </div>
        <div class="ref">
            <strong>Référence : <?= htmlspecialchars($sortie['reference']) ?></strong><br>
            <span style="font-size: 13px; color: #808080;">Date : <?= date('d/m/Y', strtotime($sortie['date_sortie'])) ?></span>
        </div>
    </div>

    <div class="infos">
        <div>
            <h3>Produit</h3>
            <?= htmlspecialchars($sortie['nom_produit']) ?>
        </div>
        <div>
            <h3>Motif</h3>
            <?php
            $motifLabels = [
                'perime' => 'Périmé', 'non_vendu' => 'Non vendu', 'retour_client' => 'Retour client',
                'casse' => 'Casse', 'don' => 'Don', 'autre' => 'Autre'
            ];
            echo $motifLabels[$sortie['motif_sortie']] ?? $sortie['motif_sortie'];
            ?>
        </div>
        <?php if (!empty($sortie['client_nom'])): ?>
        <div>
            <h3>Client (retour)</h3>
            <?= htmlspecialchars($sortie['client_nom'] . ' ' . ($sortie['client_prenom'] ?? '')) ?>
        </div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th class="num">Quantité sortie</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlspecialchars($sortie['nom_produit']) ?></td>
                <td class="num"><?= rtrim(rtrim(number_format($sortie['quantite'], 3, '.', ' '), '0'), '.') ?> <?= htmlspecialchars($sortie['unite']) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if (!empty($sortie['observations'])): ?>
    <div class="observations">
        <h3>Observations</h3>
        <p><?= nl2br(htmlspecialchars($sortie['observations'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <div class="line">Effectué par</div>
        </div>
        <div class="signature">
            <div class="line">Validé par</div>
        </div>
    </div>
</body>
</html>
