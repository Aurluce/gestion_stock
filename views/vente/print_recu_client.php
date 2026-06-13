<?php
$title = 'Reçu de paiement';
$backUrl = null;
$customCss = <<<'CSS'
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
body { padding: 40px; color: #242424; display: flex; justify-content: center; }
.recu { width: 400px; border: 2px solid #0078d4; border-radius: 8px; padding: 30px; }
.recu h1 { font-size: 18px; color: #0078d4; text-align: center; margin-bottom: 4px; }
.recu .subtitle { text-align: center; font-size: 12px; color: #808080; margin-bottom: 20px; }
.recu .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #e0e0e0; font-size: 14px; }
.recu .row .label { color: #808080; }
.recu .row .value { font-weight: 600; }
.recu .montant { text-align: center; margin: 20px 0; padding: 16px; background: #f5f5f5; border-radius: 8px; }
.recu .montant .label { font-size: 12px; color: #808080; text-transform: uppercase; letter-spacing: 0.05em; }
.recu .montant .value { font-size: 28px; font-weight: 700; color: #0078d4; margin-top: 4px; }
.recu .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #808080; }
@media print { body { padding: 0; } }
CSS;
$hidePrintFooter = true;
ob_start();
?><div style="display:flex;justify-content:center;"><div class="recu">
            <h1>GESTION DE STOCK</h1>
            <p class="subtitle">Reçu de paiement</p>

            <div class="row">
                <span class="label">Reçu N°</span>
                <span class="value">#<?= $recu['id_reglement'] ?></span>
            </div>
            <div class="row">
                <span class="label">Date</span>
                <span class="value"><?= date('d/m/Y', strtotime($recu['date_reglement'])) ?></span>
            </div>
            <div class="row">
                <span class="label">Client</span>
                <span class="value"><?= htmlspecialchars($recu['client_nom'] . ' ' . ($recu['client_prenom'] ?? '')) ?></span>
            </div>
            <div class="row">
                <span class="label">Facture</span>
                <span class="value"><?= htmlspecialchars($recu['facture_reference']) ?></span>
            </div>
            <div class="row">
                <span class="label">Mode de paiement</span>
                <span class="value"><?= ucfirst(str_replace('_', ' ', $recu['mode_paiement'])) ?></span>
            </div>
            <?php if (!empty($recu['reference'])): ?>
            <div class="row">
                <span class="label">Référence</span>
                <span class="value"><?= htmlspecialchars($recu['reference']) ?></span>
            </div>
            <?php endif; ?>

            <div class="montant">
                <div class="label">Montant payé</div>
                <div class="value"><?= number_format($recu['montant'], 0, ',', ' ') ?> FCFA</div>
            </div>

            <?php if (!empty($recu['observations'])): ?>
            <p style="font-size: 13px; margin-top: 10px;"><?= nl2br(htmlspecialchars($recu['observations'])) ?></p>
            <?php endif; ?>

            <div class="footer">
                Merci pour votre confiance.
            </div>
        </div>
    </div>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
