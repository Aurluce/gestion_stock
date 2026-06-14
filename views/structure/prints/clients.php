<?php
$title = "Liste des clients";
$backUrl = '?action=clients';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?>
<div class="print-header">
    <h1>LISTE DES CLIENTS</h1>
    <p>Date d'impression : <?= date('d/m/Y H:i:s') ?></p>
</div>

<table class="print-table">
    <thead>
        <tr>
            <th>Nom complet</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Ville</th>
            <th>Catégorie</th>
            <th style="text-align:right">Crédit (FCFA)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clients as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nom'] . ' ' . ($c['prenom'] ?? '')) ?></td>
            <td><?= htmlspecialchars($c['tel'] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['ville'] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['nom_categorie'] ?? '-') ?></td>
            <td style="text-align:right"><?= number_format($c['solde_credit'] ?? 0, 0) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($clients)): ?>
        <tr><td colspan="6" style="text-align: center;">Aucun client enregistré</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../../components/print_layout.php'; ?>