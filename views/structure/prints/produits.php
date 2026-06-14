<?php
$title = "Liste des produits";
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; font-size: 12px; text-align: center; color: #666; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 5px 10px;">🖨️ Imprimer</button>
        <a href="?action=produits" style="padding: 5px 10px; text-decoration: none; background: #f0f0f0; color: #333; border: 1px solid #ccc;">↩️ Retour</a>
    </div>
    <h1><?= $title ?></h1>
    <p>Date d'impression : <?= date('d/m/Y H:i:s') ?></p>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Famille</th>
                <th>Variante de</th>
                <th>Prix vente (FCFA)</th>
                <th>Stock</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nom_produit']) ?></td>
                <td><?= htmlspecialchars($p['nom_famille'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['nom_produit_pere'] ?? '-') ?></td>
                <td><?= number_format($p['prix_vente'], 0) ?></td>
                <td><?= number_format($p['stock_actuel'], 2) ?></td>
                <td><?= $p['est_actif'] ? 'Actif' : 'Inactif' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="footer">Application Gestion de Stock - Tous droits réservés</div>
</body>
</html>