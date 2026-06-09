<?php $title = "Mon profil"; ?>
<div class="max-w-md mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Mon profil</h1>
    <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom_complet'] ?? '') ?></p>
    <p><strong>Login :</strong> <?= htmlspecialchars($user['login'] ?? '') ?></p>
    <hr class="my-4">
    <h2 class="text-xl font-semibold mb-4">Changer le mot de passe</h2>
    <?php if (!empty($message)): ?>
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
        <div>
            <label>Ancien mot de passe</label>
            <input type="password" name="old_password" required class="w-full border p-2 rounded">
        </div>
        <div>
            <label>Nouveau mot de passe</label>
            <input type="password" name="new_password" required class="w-full border p-2 rounded">
        </div>
        <div>
            <label>Confirmer</label>
            <input type="password" name="confirm_password" required class="w-full border p-2 rounded">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Modifier</button>
    </form>
    <div class="mt-4 text-center">
        <a href="index.php?action=dashboard" class="text-blue-600">Retour à l'accueil</a>
    </div>
</div>