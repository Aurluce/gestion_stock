<?php
$title = 'Bon de commande ' . htmlspecialchars($commande['reference']);
$backUrl = null;
$customCss = <<<'CSS'
.totaux { display: flex; justify-content: flex-end; }
.totaux table { width: 300px; }
.totaux td { border: none; padding: 6px 12px; }
.totaux .total-row td { font-weight: 700; font-size: 16px; border-top: 2px solid #0078d4; }
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
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">Bon de commande client</p>
        </div>
        <div class="ref">
            <strong>Référence : <?= htmlspecialchars($commande['reference']) ?></strong><br>
            <span style="font-size: 13px; color: #808080;">Date : <?= date('d/m/Y', strtotime($commande['date_commande'])) ?></span>
        </div>
    </div>

    <div class="infos">
        <div>
            <h3>Client</h3>
            <?= htmlspecialchars($commande['client_nom'] . ' ' . ($commande['client_prenom'] ?? '')) ?>
        </div>
        <div>
            <h3>Type de vente</h3>
            <?= $commande['type_vente'] === 'comptant' ? 'Comptant' : 'Crédit' ?>
        </div>
        <div>
            <h3>Statut</h3>
            <?= ucfirst(str_replace('_', ' ', $commande['statut'])) ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th class="num">Quantité</th>
                <th class="num">Prix unitaire</th>
                <th class="num">Remise (%)</th>
                <th class="num">Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['nom_produit']) ?></td>
                <td class="num"><?= rtrim(rtrim(number_format($l['quantite'], 3, '.', ' '), '0'), '.') ?> <?= htmlspecialchars($l['unite']) ?></td>
                <td class="num"><?= number_format($l['prix_unitaire'], 0, ',', ' ') ?> FCFA</td>
                <td class="num"><?= number_format($l['taux_remise'], 2) ?>%</td>
                <td class="num"><?= number_format($l['montant_ligne'], 0, ',', ' ') ?> FCFA</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totaux">
        <table>
            <tr class="total-row">
                <td>Total</td>
                <td class="num"><?= number_format($commande['montant_total'], 0, ',', ' ') ?> FCFA</td>
            </tr>
        </table>
    </div>

    <?php if (!empty($commande['observations'])): ?>
    <div class="observations">
        <h3>Observations</h3>
        <p><?= nl2br(htmlspecialchars($commande['observations'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <div class="line">Signature du client</div>
        </div>
        <div class="signature">
            <div class="line">Signature du responsable</div>
        </div>
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
