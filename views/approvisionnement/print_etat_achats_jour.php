<?php
$title = $title ?? 'État des achats';
$backUrl = '?action=etats_achats';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?><div class="print-header">
        <h1>ÉTAT DES ACHATS</h1>
        <p>Du <?= date('d/m/Y', strtotime($date)) ?></p>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th>Bon entrée</th>
                <th>Produit</th>
                <th style="text-align:right">Qté</th>
                <th style="text-align:right">Prix unit.</th>
                <th style="text-align:right">Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['reference']) ?></td>
                <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                <td style="text-align:right"><?= number_format($l['quantite'], 3, ',', ' ') ?></td>
                <td style="text-align:right"><?= number_format($l['prix_unitaire'], 0, ',', ' ') ?></td>
                <td style="text-align:right"><?= number_format($l['montant_ligne'], 0, ',', ' ') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="print-total">
        Total des achats : <?= number_format($totalAchats, 0, ',', ' ') ?> FCFA
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
