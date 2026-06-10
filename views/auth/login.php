<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Gestion Stock</title>
    <link rel="stylesheet" href="public/css/main.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-neutral-95 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm mx-4">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-50 mb-4">
                <i class="fas fa-boxes-stacked text-h3 text-brand-600"></i>
            </div>
            <h1 class="text-h3 font-bold text-neutral-14">Gestion de Stock</h1>
            <p class="text-body text-neutral-50 mt-1">Connectez-vous pour continuer</p>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-neutral-90">
            <?php if (isset($error)): ?>
            <div class="alert-danger animate-fade-in mb-4">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <div>
                    <label class="label" for="login">Identifiant</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-neutral-60">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="login" id="login" required
                               class="input pl-10"
                               placeholder="Votre identifiant">
                    </div>
                </div>

                <div>
                    <label class="label" for="password">Mot de passe</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-neutral-60">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               class="input pl-10"
                               placeholder="Votre mot de passe">
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full text-center">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Se connecter</span>
                </button>
            </form>
        </div>

        <p class="text-center text-caption text-neutral-60 mt-6">
            &copy; <?= date('Y') ?> Gestion Stock
        </p>
    </div>
</body>
</html>
