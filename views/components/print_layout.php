<?php
// print_layout.php — Centralized print layout.
// Expects: $content (ob_get_clean), $title, $backUrl, $customCss, $hidePrintFooter
$content = ob_get_clean();
$useFermer = ($backUrl === null);
$backUrlSafe = $backUrl !== null ? htmlspecialchars($backUrl) : 'javascript:void(0)';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="public/css/main.min.css">
    <link rel="stylesheet" href="public/css/print.css">
    <?php if (!empty($customCss)): ?>
    <style>
<?= trim($customCss) ?>
    </style>
    <?php endif; ?>
</head>
<body>
    <div class="no-print mb-4">
        <button onclick="window.print()" class="btn-primary"><i class="fas fa-print mr-2"></i>Imprimer</button>
        <?php if ($useFermer): ?>
        <button onclick="window.close()" class="btn-secondary ml-2"><i class="fas fa-times mr-2"></i>Fermer</button>
        <?php else: ?>
        <a href="<?= $backUrlSafe ?>" class="btn-secondary ml-2"><i class="fas fa-arrow-left mr-2"></i>Retour</a>
        <?php endif; ?>
    </div>

    <?= $content ?>

    <?php if (!$hidePrintFooter): ?>
    <div style="margin-top:40px;border-top:1px solid #ccc;padding-top:10px;font-size:10px;color:#666;text-align:center;">
        <p>Document imprimé le <?= date('d/m/Y à H:i') ?></p>
    </div>
    <?php endif; ?>
</body>
</html>
