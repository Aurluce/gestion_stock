<?php $title = "Groupes d'utilisateurs"; ob_start(); ?>
<div class="flex justify-between items-center mb-6"><h1 class="text-3xl font-bold">Groupes</h1><a href="index.php?action=dashboard" class="bg-gray-500 text-white px-4 py-2 rounded">Retour</a></div>
<?php if (!empty($message)): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="bg-white p-4 rounded shadow mb-6"><h2 class="text-xl font-semibold mb-4">Ajouter un groupe</h2>
<form method="post" class="grid grid-cols-2 gap-4"><input type="hidden" name="action" value="add">
<div><label>Nom</label><input type="text" name="nom_groupe" required class="w-full border p-2 rounded"></div>
<div><label>Description</label><input type="text" name="description" class="w-full border p-2 rounded"></div>
<div class="col-span-2"><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Ajouter</button></div>
</form></div>
<div class="bg-white rounded shadow overflow-hidden"><table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th>ID</th><th>Nom</th><th>Description</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($groupes as $g): ?>
<tr><td class="px-6 py-4"><?= $g['id_groupe'] ?></td><td><?= htmlspecialchars($g['nom_groupe']) ?></td><td><?= htmlspecialchars($g['description']) ?></td>
<td class="space-x-2"><button onclick="openEditModal(<?= htmlspecialchars(json_encode($g)) ?>)" class="bg-yellow-500 text-white px-3 py-1 rounded">Modifier</button>
<a href="?action=groupes&delete=<?= $g['id_groupe'] ?>" class="bg-red-500 text-white px-3 py-1 rounded" onclick="return confirm('Supprimer ?')">Supprimer</a>
<a href="?action=groupes_droits&groupe_id=<?= $g['id_groupe'] ?>" class="bg-indigo-500 text-white px-3 py-1 rounded">Droits</a></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center"><div class="bg-white p-6 rounded shadow-lg w-96"><h2 class="text-xl font-bold mb-4">Modifier groupe</h2>
<form method="post"><input type="hidden" name="action" value="edit"><input type="hidden" name="id_groupe" id="edit_id">
<div class="mb-4"><label>Nom</label><input type="text" name="nom_groupe" id="edit_nom" required class="w-full border p-2 rounded"></div>
<div class="mb-4"><label>Description</label><input type="text" name="description" id="edit_desc" class="w-full border p-2 rounded"></div>
<div class="flex justify-end space-x-2"><button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Annuler</button><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Enregistrer</button></div>
</form></div></div>
<script>
function openEditModal(g) { document.getElementById('edit_id').value = g.id_groupe; document.getElementById('edit_nom').value = g.nom_groupe; document.getElementById('edit_desc').value = g.description || ''; document.getElementById('editModal').classList.remove('hidden'); document.getElementById('editModal').classList.add('flex'); }
function closeModal() { document.getElementById('editModal').classList.add('hidden'); document.getElementById('editModal').classList.remove('flex'); }
</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>