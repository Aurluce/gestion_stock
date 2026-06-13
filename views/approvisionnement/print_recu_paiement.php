<?php
$title = $title ?? 'Reçu de paiement';
$backUrl = '?action=paiement_fourn';
$customCss = <<<'CSS'
.print-recu-box { border: 2px solid #0078D4; border-radius: 8px; padding: 30px; max-width: 400px; margin: 0 auto; text-align: center; }
.print-recu-box .montant { font-size: 24px; font-weight: bold; color: #0078D4; margin: 15px 0; }
CSS;
$hidePrintFooter = false;
ob_start();
?><div class="print-header">
        <h1>REÇU DE PAIEMENT</h1>
    </div>

    <div class="print-recu-box">
        <p style="font-size:12px;color:#666;margin-bottom:5px">N° <?= htmlspecialchars($paiement['reference']) ?></p>
        <p style="margin:5px 0"><strong>Date :</strong> <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?></p>
        <hr style="margin:15px 0">
        <p style="margin:5px 0"><strong>Fournisseur :</strong> <?= htmlspecialchars($paiement['fournisseur_nom']) ?></p>
        <p style="margin:5px 0"><strong>Facture :</strong> <?= htmlspecialchars($paiement['numero_facture']) ?></p>
        <p style="margin:5px 0"><strong>Mode :</strong> <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $paiement['mode_paiement']))) ?></p>
        <div class="montant"><?= number_format($paiement['montant'], 0, ',', ' ') ?> FCFA</div>
        <p style="font-size:11px;color:#666;margin-top:10px">Payé le <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?></p>
    </div>

    <?php if ($paiement['observations']): ?>
    <div style="margin-top:20px;text-align:center">
        <p><strong>Observations :</strong> <?= nl2br(htmlspecialchars($paiement['observations'])) ?></p>
    </div>
    <?php endif; ?>
<?php require __DIR__ . '/../components/print_layout.php'; ?>
