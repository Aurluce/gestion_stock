<?php
$title = 'Bon de livraison ' . htmlspecialchars($bl['reference']);
$backUrl = null;
$customCss = <<<'CSS'
.observations { margin-top: 20px; font-size: 13px; }
.observations h3 { font-size: 12px; text-transform: uppercase; color: #808080; margin-bottom: 6px; }
.footer { margin-top: 60px; display: flex; justify-content: space-between; font-size: 13px; }
.footer .signature { text-align: center; width: 200px; }
.footer .signature .line { border-top: 1px solid #242424; margin-top: 50px; padding-top: 6px; }
@media print { body { padding: 0; } }
CSS;
$hidePrintFooter = true;
ob_start();
?><div class="header">
        <div>
            <h1>GESTION DE STOCK</h1>
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">Bon de livraison</p>
        </div>
        <div class="ref">
            <strong>Référence : <?= htmlspecialchars($bl['reference']) ?></strong><br>
            <span style="font-size: 13px; color: #808080;">Date : <?= date('d/m/Y', strtotime($bl['date_livraison'])) ?></span>
        </div>
    </div>

    <div class="infos">
        <div>
            <h3>Client</h3>
            <?= htmlspecialchars($bl['client_nom'] . ' ' . ($bl['client_prenom'] ?? '')) ?>
        </div>
        <div>
            <h3>Commande liée</h3>
            <?= htmlspecialchars($bl['cc_reference']) ?>
        </div>
        <div>
            <h3>Statut</h3>
            <?= ucfirst(str_replace('_', ' ', $bl['statut'])) ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th class="num">Quantité livrée</th>
                <th>Observations</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                <td class="num"><?= rtrim(rtrim(number_format($l['qte_livree'], 3, '.', ' '), '0'), '.') ?> <?= htmlspecialchars($l['unite']) ?></td>
                <td><?= htmlspecialchars($l['observations'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($bl['observations'])): ?>
    <div class="observations">
        <h3>Observations générales</h3>
        <p><?= nl2br(htmlspecialchars($bl['observations'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <div class="line">Signature du livreur</div>
        </div>
        <div class="signature">
            <div class="line">Signature du client (réception)</div>
        </div>
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
