<?php
$title = "Liste des banques";
$backUrl = '?action=banques';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?>
<div class="print-header">
    <h1>LISTE DES BANQUES</h1>
    <p>Date d'impression : <?= date('d/m/Y H:i:s') ?></p>
</div>

<table class="print-table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Sigle</th>
            <th>Responsable</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Adresse</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($banques as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['nom_banque']) ?></td>
            <td><?= htmlspecialchars($b['sigle'] ?? '-') ?></td>
            <td><?= htmlspecialchars($b['responsable'] ?? '-') ?></td>
            <td><?= htmlspecialchars($b['tel'] ?? '-') ?></td>
            <td><?= htmlspecialchars($b['email'] ?? '-') ?></td>
            <td><?= htmlspecialchars($b['adresse'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($banques)): ?>
        <tr><td colspan="6" style="text-align: center;">Aucune banque enregistrée</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../../components/print_layout.php'; ?>