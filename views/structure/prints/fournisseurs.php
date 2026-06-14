<?php
$title = "Liste des fournisseurs";
$backUrl = '?action=fournisseurs';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?>
<div class="print-header">
    <h1>LISTE DES FOURNISSEURS</h1>
    <p>Date d'impression : <?= date('d/m/Y H:i:s') ?></p>
</div>

<table class="print-table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Ville</th>
            <th>NIF</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fournisseurs as $f): ?>
        <tr>
            <td><?= htmlspecialchars($f['nom']) ?></td>
            <td><?= htmlspecialchars($f['tel'] ?? '-') ?></td>
            <td><?= htmlspecialchars($f['email'] ?? '-') ?></td>
            <td><?= htmlspecialchars($f['ville'] ?? '-') ?></td>
            <td><?= htmlspecialchars($f['nif'] ?? '-') ?></td>
            <td><?= $f['est_actif'] ? 'Actif' : 'Inactif' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($fournisseurs)): ?>
        <tr><td colspan="6" style="text-align: center;">Aucun fournisseur enregistré</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../../components/print_layout.php'; ?>