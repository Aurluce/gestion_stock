<?php
$title = "Liste des familles";
$backUrl = '?action=familles';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?>
<div class="print-header">
    <h1>LISTE DES FAMILLES</h1>
    <p>Date d'impression : <?= date('d/m/Y H:i:s') ?></p>
</div>

<table class="print-table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Date création</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($familles as $f): ?>
        <tr>
            <td><?= htmlspecialchars($f['nom_famille']) ?></td>
            <td><?= htmlspecialchars($f['description'] ?? '-') ?></td>
            <td><?= $f['date_creation_fr'] ?? '-' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($familles)): ?>
        <tr><td colspan="3" style="text-align: center;">Aucune famille enregistrée</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../../components/print_layout.php'; ?>