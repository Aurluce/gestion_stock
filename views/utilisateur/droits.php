<?php
$title = "Liste des droits disponibles";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Utilisateurs', 'href' => '?action=utilisateurs'],
    ['label' => 'Droits']
]);
ob_start();
?>

<?= renderPageHeader(
    'Droits disponibles',
    'Ensemble des permissions que l\'on peut affecter aux groupes'
) ?>

<?php
$cards = '';
foreach ($modules as $module => $dlist):
    $items = '<ul class="space-y-1">';
    foreach ($dlist as $d):
        $badge = renderBadge($d['module'], 'info');
        $items .= '<li class="flex items-center gap-2 py-1">'
               . $badge
               . '<span class="font-medium text-neutral-14">' . htmlspecialchars($d['nom_droit']) . '</span>'
               . '<span class="text-neutral-50">— ' . htmlspecialchars($d['description'] ?? '') . '</span>'
               . '</li>';
    endforeach;
    $items .= '</ul>';
    $cards .= renderCard($items, ucfirst($module));
    $cards .= "\n";
endforeach;

echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
echo $cards;
echo '</div>';
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
