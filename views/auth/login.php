<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Connexion</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold mb-6 text-center">Gestion de Stock</h1>
        <?php if (isset($error)): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post" class="space-y-4">
            <div><label class="block text-sm font-medium">Login</label><input type="text" name="login" required class="mt-1 w-full border p-2 rounded"></div>
            <div><label class="block text-sm font-medium">Mot de passe</label><input type="password" name="password" required class="mt-1 w-full border p-2 rounded"></div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Se connecter</button>
        </form>
    </div>
</body>
</html>