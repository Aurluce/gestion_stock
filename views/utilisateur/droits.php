<?php $title = "Liste des droits"; ob_start(); ?>
<div class="flex justify-between items-center mb-6"><h1 class="text-3xl font-bold">Droits disponibles</h1><a href="index.php?action=dashboard" class="bg-gray-500 text-white px-4 py-2 rounded">Retour</a></div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<?php foreach ($modules as $module => $dlist): ?>
<div class="bg-white rounded shadow p-4"><h2 class="text-xl font-semibold mb-2 capitalize"><?= $module ?></h2><ul class="list-disc pl-5">
<?php foreach ($dlist as $d): ?><li><?= htmlspecialchars($d['nom_droit']) ?> – <?= htmlspecialchars($d['description']) ?></li><?php endforeach; ?>
</ul></div>
<?php endforeach; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>