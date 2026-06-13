<?php
$title = $title ?? 'Bon de réception';
$backUrl = '?action=reception';
$customCss = '';
$hidePrintFooter = false;
ob_start();
?><div class="print-header">
        <h1>BON DE RÉCEPTION</h1>
        <p>N° <?= htmlspecialchars($reception['reference']) ?></p>
    </div>

    <div class="print-info">
        <div>
            <strong>Fournisseur :</strong> <?= htmlspecialchars($reception['fournisseur_nom'] ?? '-') ?><br>
            <strong>Commande :</strong> <?= htmlspecialchars($reception['commande_ref'] ?? '-') ?>
        </div>
        <div style="text-align:right">
            <strong>Date :</strong> <?= date('d/m/Y', strtotime($reception['date_reception'])) ?><br>
            <strong>Statut :</strong> <?= ucfirst(str_replace('_', ' ', $reception['statut'])) ?>
        </div>
    </div>

    <?php if ($reception['observations']): ?>
    <p><strong>Observations :</strong> <?= nl2br(htmlspecialchars($reception['observations'])) ?></p>
    <?php endif; ?>

    <table class="print-table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Produit</th>
                <th style="text-align:right">Qté reçue</th>
                <th style="text-align:right">État</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($lignes as $l): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                <td style="text-align:right"><?= number_format($l['qte_recue'], 3, ',', ' ') ?></td>
                <td><?= htmlspecialchars($l['etat_produit']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
