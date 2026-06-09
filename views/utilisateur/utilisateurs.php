<?php $title = "Gestion des utilisateurs"; ob_start(); ?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Utilisateurs</h1>
    <div class="space-x-2">
        <a href="index.php?action=dashboard" class="bg-gray-500 text-white px-4 py-2 rounded">Retour</a>
        <?php if (checkRightIfLogged('creer_groupe')): ?>
            <a href="index.php?action=groupes" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                ➕ Nouveau groupe
            </a>
        <?php endif; ?>
    </div>
</div>
<?php if (!empty($message)): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="bg-white p-4 rounded shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Ajouter un utilisateur</h2>
    <form method="post" class="grid grid-cols-2 gap-4">
        <input type="hidden" name="action" value="add">
        <div><label>Nom complet</label><input type="text" name="nom_complet" required class="w-full border p-2 rounded"></div>
        <div><label>Login</label><input type="text" name="login" required class="w-full border p-2 rounded"></div>
        <div><label>Mot de passe</label><input type="password" name="password" required class="w-full border p-2 rounded"></div>
        <div>
            <label>Groupe</label>
            <select name="id_groupe" required class="w-full border p-2 rounded">
                <?php foreach($groupes as $g): ?>
                    <option value="<?= $g['id_groupe'] ?>"><?= htmlspecialchars($g['nom_groupe']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label>Actif</label><input type="checkbox" name="actif" value="1" checked></div>
        <div><label>Expiration MDP</label><input type="date" name="date_expiration_mdp" class="w-full border p-2 rounded"></div>
        <div class="col-span-2"><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Créer</button></div>
    </form>
</div>

<!-- Tableau des utilisateurs (identique) -->
<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">…
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td class="px-4 py-2"><?= $u['id_utilisateur'] ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($u['nom_complet']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($u['login']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($u['nom_groupe']) ?></td>
                <td class="px-4 py-2"><?= $u['actif'] ? 'Oui' : 'Non' ?></td>
                <td class="px-4 py-2"><?= $u['date_expiration_mdp'] ?? '-' ?></td>
                <td class="px-4 py-2">
                    <button onclick="openEditModal(<?= htmlspecialchars(json_encode($u)) ?>)" class="bg-yellow-500 text-white px-2 py-1 rounded">Modifier</button>
                    <a href="?action=utilisateurs&delete=<?= $u['id_utilisateur'] ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal modification (identique) -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Modifier utilisateur</h2>
        <form method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_utilisateur" id="edit_id">
            <div><label>Nom complet</label><input type="text" name="nom_complet" id="edit_nom" required class="w-full border p-2 rounded"></div>
            <div><label>Login</label><input type="text" name="login" id="edit_login" required class="w-full border p-2 rounded"></div>
            <div><label>Nouveau mot de passe (laisser vide)</label><input type="password" name="password" class="w-full border p-2 rounded"></div>
            <div><label>Groupe</label><select name="id_groupe" id="edit_groupe" class="w-full border p-2 rounded">
                <?php foreach($groupes as $g): ?>
                    <option value="<?= $g['id_groupe'] ?>"><?= htmlspecialchars($g['nom_groupe']) ?></option>
                <?php endforeach; ?>
            </select></div>
            <div><label>Actif</label><input type="checkbox" name="actif" id="edit_actif" value="1"></div>
            <div><label>Expiration MDP</label><input type="date" name="date_expiration_mdp" id="edit_exp" class="w-full border p-2 rounded"></div>
            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Annuler</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(u) {
    document.getElementById('edit_id').value = u.id_utilisateur;
    document.getElementById('edit_nom').value = u.nom_complet;
    document.getElementById('edit_login').value = u.login;
    document.getElementById('edit_groupe').value = u.id_groupe;
    document.getElementById('edit_actif').checked = u.actif == 1;
    document.getElementById('edit_exp').value = u.date_expiration_mdp || '';
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}
function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}
</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>