<?php
$title = "Liste des produits";
$backUrl = '?action=produits';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?>
<div class="print-header">
    <h1>LISTE DES PRODUITS</h1>
    <p>Date d'impression : <?= date('d/m/Y H:i:s') ?></p>
</div>

<table class="print-table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Famille</th>
            <th>Variante de</th>
            <th style="text-align:right">Prix vente (FCFA)</th>
            <th style="text-align:right">Stock</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produits as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nom_produit']) ?></td>
            <td><?= htmlspecialchars($p['nom_famille'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['nom_produit_pere'] ?? '-') ?></td>
            <td style="text-align:right"><?= number_format($p['prix_vente'], 0) ?></td>
            <td style="text-align:right"><?= number_format($p['stock_actuel'], 2) ?></td>
            <td><?= $p['est_actif'] ? 'Actif' : 'Inactif' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($produits)): ?>
        <tr><td colspan="6" style="text-align: center;">Aucun produit enregistré</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../../components/print_layout.php'; ?>