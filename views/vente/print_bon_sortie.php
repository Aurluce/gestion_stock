<?php
$title = 'Bon de sortie ' . htmlspecialchars($sortie['reference']);
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
            <p style="font-size: 13px; color: #808080; margin-top: 4px;">Bon de sortie</p>
        </div>
        <div class="ref">
            <strong>Référence : <?= htmlspecialchars($sortie['reference']) ?></strong><br>
            <span style="font-size: 13px; color: #808080;">Date : <?= date('d/m/Y', strtotime($sortie['date_sortie'])) ?></span>
        </div>
    </div>

    <div class="infos">
        <div>
            <h3>Produit</h3>
            <?= htmlspecialchars($sortie['nom_produit']) ?>
        </div>
        <div>
            <h3>Motif</h3>
            <?php
            $motifLabels = [
                'perime' => 'Périmé', 'non_vendu' => 'Non vendu', 'retour_client' => 'Retour client',
                'casse' => 'Casse', 'don' => 'Don', 'autre' => 'Autre'
            ];
            echo $motifLabels[$sortie['motif_sortie']] ?? $sortie['motif_sortie'];
            ?>
        </div>
        <?php if (!empty($sortie['client_nom'])): ?>
        <div>
            <h3>Client (retour)</h3>
            <?= htmlspecialchars($sortie['client_nom'] . ' ' . ($sortie['client_prenom'] ?? '')) ?>
        </div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th class="num">Quantité sortie</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlspecialchars($sortie['nom_produit']) ?></td>
                <td class="num"><?= rtrim(rtrim(number_format($sortie['quantite'], 3, '.', ' '), '0'), '.') ?> <?= htmlspecialchars($sortie['unite']) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if (!empty($sortie['observations'])): ?>
    <div class="observations">
        <h3>Observations</h3>
        <p><?= nl2br(htmlspecialchars($sortie['observations'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <div class="line">Effectué par</div>
        </div>
        <div class="signature">
            <div class="line">Validé par</div>
        </div>
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
