<?php
$title = $title ?? 'Facture fournisseur';
$backUrl = '?action=facture_fourn';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?><div class="print-header">
        <h1>FACTURE FOURNISSEUR</h1>
        <p>N° <?= htmlspecialchars($facture['numero_facture']) ?></p>
        <?php if ($facture['reference']): ?>
        <p>Réf. interne : <?= htmlspecialchars($facture['reference']) ?></p>
        <?php endif; ?>
    </div>

    <div class="print-info">
        <div>
            <strong>Fournisseur :</strong><br>
            <?= htmlspecialchars($facture['fournisseur_nom']) ?>
        </div>
        <div style="text-align:right">
            <strong>Date :</strong> <?= date('d/m/Y', strtotime($facture['date_facture'])) ?><br>
            <strong>Échéance :</strong> <?= $facture['date_echeance'] ? date('d/m/Y', strtotime($facture['date_echeance'])) : '-' ?><br>
            <strong>Statut :</strong> <?= ucfirst($facture['statut']) ?>
        </div>
    </div>

    <?php if ($facture['commande_ref']): ?>
    <p><strong>Commande liée :</strong> <?= htmlspecialchars($facture['commande_ref']) ?></p>
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
            <?php $i = 1; $totalHt = 0; ?>
            <?php foreach ($lignes as $l): ?>
            <?php $montant = $l['quantite'] * $l['prix_unitaire']; $totalHt += $montant; ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                <td style="text-align:right"><?= number_format($l['quantite'], 3, ',', ' ') ?></td>
                <td style="text-align:right"><?= number_format($l['prix_unitaire'], 0, ',', ' ') ?></td>
                <td style="text-align:right"><?= number_format($montant, 0, ',', ' ') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="print-total">
        <p><strong>Total HT :</strong> <?= number_format($facture['montant_ht'], 0, ',', ' ') ?> FCFA</p>
        <p><strong>TVA (<?= number_format($facture['taux_tva'], 2, ',', ' ') ?>%) :</strong> <?= number_format($facture['montant_ht'] * $facture['taux_tva'] / 100, 0, ',', ' ') ?> FCFA</p>
        <p style="font-size:14px"><strong>Total TTC : <?= number_format($facture['montant_ttc'], 0, ',', ' ') ?> FCFA</strong></p>
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
