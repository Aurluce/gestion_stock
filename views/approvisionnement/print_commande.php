<?php
$title = $title ?? 'Bon de commande';
$backUrl = '?action=commande_fourn';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?><div class="print-header">
        <h1>BON DE COMMANDE</h1>
        <p>N° <?= htmlspecialchars($commande['reference']) ?></p>
    </div>

    <div class="print-info">
        <div>
            <strong>Fournisseur :</strong><br>
            <?= htmlspecialchars($commande['fournisseur_nom']) ?>
        </div>
        <div style="text-align:right">
            <strong>Date :</strong> <?= date('d/m/Y', strtotime($commande['date_commande'])) ?><br>
            <strong>Statut :</strong> <?= ucfirst($commande['statut']) ?>
        </div>
    </div>

    <?php if ($commande['observations']): ?>
    <p><strong>Observations :</strong> <?= nl2br(htmlspecialchars($commande['observations'])) ?></p>
    <?php endif; ?>

    <table class="print-table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Produit</th>
                <th style="text-align:right">Qté</th>
                <th style="text-align:right">Prix unit.</th>
                <th style="text-align:right">Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; $total = 0; ?>
            <?php foreach ($lignes as $l): ?>
            <?php $montant = $l['qte_commandee'] * $l['prix_unitaire']; $total += $montant; ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                <td style="text-align:right"><?= number_format($l['qte_commandee'], 3, ',', ' ') ?></td>
                <td style="text-align:right"><?= number_format($l['prix_unitaire'], 0, ',', ' ') ?></td>
                <td style="text-align:right"><?= number_format($montant, 0, ',', ' ') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="print-total">
        Total : <?= number_format($total, 0, ',', ' ') ?> FCFA
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
