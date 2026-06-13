<?php
$title = $title ?? "Bon d'entrée en stock";
$backUrl = '?action=bon_entree';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?><div class="print-header">
        <h1>BON D'ENTRÉE EN STOCK</h1>
        <p>N° <?= htmlspecialchars($bonEntree['reference']) ?></p>
    </div>

    <div class="print-info">
        <div>
            <strong>Type :</strong> <?= ucfirst($bonEntree['type_source']) ?><br>
            <strong>Source :</strong> 
            <?php if ($bonEntree['type_source'] === 'achat'): ?>
                <?= htmlspecialchars($bonEntree['commande_ref'] ?? 'Achat direct') ?>
            <?php else: ?>
                <?= htmlspecialchars($bonEntree['donateur'] ?? '') ?>
            <?php endif; ?>
        </div>
        <div style="text-align:right">
            <strong>Date :</strong> <?= date('d/m/Y', strtotime($bonEntree['date_entree'])) ?><br>
            <strong>Utilisateur :</strong> <?= htmlspecialchars($bonEntree['utilisateur_nom']) ?>
        </div>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Produit</th>
                <th style="text-align:right">Quantité</th>
                <th style="text-align:right">Prix unit.</th>
                <th style="text-align:right">Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; $total = 0; ?>
            <?php foreach ($lignes as $l): ?>
            <?php $montant = $l['quantite'] * $l['prix_unitaire']; $total += $montant; ?>
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

    <div class="print-total" style="text-align:right;font-weight:bold;font-size:14px;margin-top:10px">
        Total : <?= number_format($total, 0, ',', ' ') ?> FCFA
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
