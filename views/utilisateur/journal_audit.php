<?php $title = "Journal d'audit"; ob_start(); 
// ignorer les errreurs dde deprecation pour json_encode de ressources PDO
error_reporting(E_ALL & ~E_DEPRECATED);

?>
<div class="flex justify-between items-center mb-6"><h1 class="text-3xl font-bold">Journal d'audit</h1><a href="index.php?action=dashboard" class="bg-gray-500 text-white px-4 py-2 rounded">Retour</a></div>
<div class="bg-white rounded shadow overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Table</th><th>ID</th><th>Détails</th></tr></thead><tbody>
<?php foreach ($logs as $log): ?>
<tr class="border-b"><td><?= $log['date_heure'] ?></td><td><?= htmlspecialchars($log['utilisateur_nom'] ?? 'N/A') ?></td><td><?= $log['action'] ?></td><td><?= $log['table_cible'] ?></td><td><?= $log['id_enregistrement'] ?></td>
<td><button onclick="toggleJson(<?= $log['id_audit'] ?>)" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Voir JSON</button>
<div id="json_<?= $log['id_audit'] ?>" class="hidden mt-2 bg-gray-100 p-2 rounded text-xs overflow-auto max-w-md"><strong>Ancien :</strong> <?= htmlspecialchars($log['ancienne_valeur']) ?><br><strong>Nouveau :</strong> <?= htmlspecialchars($log['nouvelle_valeur']) ?></div></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="flex justify-center space-x-2 mt-4"><?php for ($i=1;$i<=$pages;$i++): ?><a href="?action=journal_audit&page=<?= $i ?>" class="px-3 py-1 bg-gray-200 rounded <?= $i==$page?'bg-blue-600 text-white':'' ?>"><?= $i ?></a><?php endfor; ?></div>
<script>function toggleJson(id) { document.getElementById('json_' + id).classList.toggle('hidden'); }</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>