<?php
$title = 'Facture ' . htmlspecialchars($facture['reference']);
$backUrl = null;
$customCss = <<<'CSS'
.totaux { display: flex; justify-content: flex-end; }
.totaux table { width: 320px; }
.totaux td { border: none; padding: 6px 12px; }
.totaux .total-row td { font-weight: 700; font-size: 16px; border-top: 2px solid #0078d4; }
.statut-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
.statut-impayee { background: #ffebee; color: #c62828; }
.statut-partielle { background: #fff8e1; color: #f57f17; }
.statut-payee { background: #e8f5e9; color: #2e7d32; }
.statut-annulee { background: #f0f0f0; color: #808080; }
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
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">Facture</p>
        </div>
        <div class="ref">
            <strong>Référence : <?= htmlspecialchars($facture['reference']) ?></strong><br>
            <span style="font-size: 13px; color: #808080;">Date : <?= date('d/m/Y', strtotime($facture['date_facture'])) ?></span>
            <?php if (!empty($facture['date_echeance'])): ?>
            <br><span style="font-size: 13px; color: #808080;">Échéance : <?= date('d/m/Y', strtotime($facture['date_echeance'])) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="infos">
        <div>
            <h3>Client</h3>
            <?= htmlspecialchars($facture['client_nom'] . ' ' . ($facture['client_prenom'] ?? '')) ?><br>
            <?php if (!empty($facture['adresse'])): ?><?= htmlspecialchars($facture['adresse']) ?><br><?php endif; ?>
            <?php if (!empty($facture['ville'])): ?><?= htmlspecialchars($facture['ville']) ?><br><?php endif; ?>
            <?php if (!empty($facture['tel'])): ?>Tél : <?= htmlspecialchars($facture['tel']) ?><?php endif; ?>
        </div>
        <div>
            <h3>Commande liée</h3>
            <?= htmlspecialchars($facture['cc_reference']) ?>
        </div>
        <div>
            <h3>Statut</h3>
            <span class="statut-badge statut-<?= $facture['statut'] ?>"><?= ucfirst($facture['statut']) ?></span>
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
            <tr>
                <td>Total HT</td>
                <td class="num"><?= number_format($facture['montant_ht'], 0, ',', ' ') ?> FCFA</td>
            </tr>
            <tr>
                <td>TVA (<?= number_format($facture['taux_tva'], 2) ?>%)</td>
                <td class="num"><?= number_format($facture['montant_ttc'] - $facture['montant_ht'], 0, ',', ' ') ?> FCFA</td>
            </tr>
            <tr class="total-row">
                <td>Total TTC</td>
                <td class="num"><?= number_format($facture['montant_ttc'], 0, ',', ' ') ?> FCFA</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="signature">
            <div class="line">Signature du responsable</div>
        </div>
        <div class="signature">
            <div class="line">Cachet de l'entreprise</div>
        </div>
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
