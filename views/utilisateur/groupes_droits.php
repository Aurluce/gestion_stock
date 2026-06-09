<?php $title = "Droits du groupe " . htmlspecialchars($groupe['nom_groupe']); ob_start(); ?>
<div class="flex justify-between items-center mb-6"><h1 class="text-3xl font-bold">Droits : <?= htmlspecialchars($groupe['nom_groupe']) ?></h1><a href="?action=groupes" class="bg-gray-500 text-white px-4 py-2 rounded">← Retour</a></div>
<?php if (!empty($message)): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $message ?></div><?php endif; ?>
<form method="post" class="bg-white p-4 rounded shadow"><div class="mb-4"><label><input type="checkbox" id="selectAll"> Tout cocher/décocher</label></div>
<?php
$currentModule = '';
foreach ($tousDroits as $d):
    if ($currentModule != $d['module']):
        if ($currentModule != '') echo '</div>';
        $currentModule = $d['module'];
        echo "<div class='mb-4'><h3 class='font-semibold text-lg capitalize'>$currentModule</h3><div class='grid grid-cols-2 gap-2 ml-4'>";
    endif;
    $checked = in_array($d['id_droit'], $actuelsIds) ? 'checked' : '';
    echo "<label><input type='checkbox' name='droits[]' value='{$d['id_droit']}' $checked> {$d['nom_droit']}</label>";
endforeach;
echo '</div></div>';
?>
<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Enregistrer</button>
</form>
<script>document.getElementById('selectAll')?.addEventListener('change', e => document.querySelectorAll('input[name="droits[]"]').forEach(cb => cb.checked = e.target.checked));</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>