<?php
$title = "Droits du groupe " . htmlspecialchars($groupe['nom_groupe']);
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Utilisateurs', 'href' => '?action=utilisateurs'],
    ['label' => 'Groupes', 'href' => '?action=groupes'],
    ['label' => $groupe['nom_groupe']]
]);
ob_start();
?>

<?= renderPageHeader(
    'Droits du groupe',
    'Affecter ou retirer des permissions au groupe « ' . htmlspecialchars($groupe['nom_groupe']) . ' »',
    renderButton('Retour aux groupes', 'ghost', '?action=groupes', ['icon' => 'fa-arrow-left'])
) ?>

<?php
$moduleList = '';
$currentModule = '';
foreach ($tousDroits as $d):
    if ($currentModule != $d['module']):
        if ($currentModule != '') $moduleList .= '</div></div>';
        $currentModule = $d['module'];
        $moduleList .= '<div class="card mb-4"><div class="card-header">'
                    . '<h3 class="font-semibold text-neutral-14 capitalize">' . htmlspecialchars($currentModule) . '</h3>'
                    . '</div><div class="card-body">';
    endif;
    $checked = in_array($d['id_droit'], $actuelsIds) ? 'checked' : '';
    $moduleList .= '<label class="flex items-center gap-2 py-1 hover:bg-neutral-95 rounded px-2 cursor-pointer">'
                . '<input type="checkbox" name="droits[]" value="' . $d['id_droit'] . '" ' . $checked . ' class="checkbox">'
                . '<span class="text-body text-neutral-30">' . htmlspecialchars($d['nom_droit']) . '</span>'
                . '<span class="text-caption text-neutral-50 ml-2">— ' . htmlspecialchars($d['description'] ?? '') . '</span>'
                . '</label>';
endforeach;
$moduleList .= '</div></div>';

$cardBody = '
<form method="post" action="?action=groupes_droits&amp;groupe_id=' . $groupeId . '">
    <label class="flex items-center gap-2 mb-4 px-2 cursor-pointer">
        <input type="checkbox" id="selectAll" class="checkbox">
        <span class="text-body font-medium text-neutral-30">Tout cocher / décocher</span>
    </label>
    ' . $moduleList . '
    <div class="mt-6 pt-4 border-t border-neutral-90">
        ' . renderButton('Enregistrer les droits', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';

echo renderCard($cardBody, 'Droits : ' . htmlspecialchars($groupe['nom_groupe']));
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
