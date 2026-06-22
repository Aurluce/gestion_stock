<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Gestion Stock</title>
    <link rel="stylesheet" href="public/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="public/css/main.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            background: #f0f0f0;
        }

        .ms-wrapper {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .ms-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 0.75rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .ms-heading {
            margin-bottom: 1.5rem;
        }

        .ms-heading h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1a2e;
            letter-spacing: -0.02em;
        }

        .ms-heading p {
            font-size: 0.875rem;
            color: #888;
            margin-top: 0.25rem;
        }

        .ms-error {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            background: #FFEBEE;
            border: 1px solid rgba(229,57,53,0.2);
            border-radius: 0.5rem;
            color: #C62828;
            font-size: 0.8125rem;
            line-height: 1.4;
            margin-bottom: 1.5rem;
            animation: msFadeIn 0.25s ease-out;
        }

        .ms-error i { font-size: 0.875rem; flex-shrink: 0; }

        @keyframes msFadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

        .ms-field {
            margin-bottom: 1.25rem;
        }

        .ms-field label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.375rem;
        }

        .ms-input {
            width: 100%;
            height: 44px;
            padding: 0 0.75rem;
            border: 1px solid #d1d1d1;
            border-radius: 0.375rem;
            font-size: 0.9375rem;
            font-family: inherit;
            color: #242424;
            background: #fff;
            outline: none;
            transition: border-color 0.15s;
        }

        .ms-input::placeholder { color: #b0b0b0; }

        .ms-input:focus {
            border-color: #0078D4;
        }

        .ms-input.error {
            border-color: #E53935;
        }

        .ms-submit {
            width: 100%;
            height: 40px;
            border: none;
            border-radius: 0.375rem;
            background: #0078D4;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.15s;
            margin-top: 0.25rem;
        }

        .ms-submit:hover { background: #106ebe; }
        .ms-submit:active { background: #005a9e; }

        .ms-footer {
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #999;
        }

        @media (max-width: 480px) {
            .ms-card { padding: 2rem 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="ms-wrapper">
        <div class="ms-card">
            <div class="ms-heading">
                <h1>Connexion</h1>
                <p>Accédez à votre tableau de bord</p>
            </div>

            <?php if (isset($error)): ?>
            <div class="ms-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="post">
                <div class="ms-field">
                    <label for="login">Identifiant</label>
                    <input type="text" name="login" id="login" required
                           class="ms-input"
                           placeholder="exemple@domaine.com"
                           autocomplete="username">
                </div>

                <div class="ms-field">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required
                           class="ms-input"
                           placeholder="••••••••"
                           autocomplete="current-password">
                </div>

                <button type="submit" class="ms-submit">
                    <span>Se connecter</span>
                    <i class="fas fa-chevron-right" style="font-size:0.75rem"></i>
                </button>
            </form>

            <div class="ms-footer">
                &copy; <?= date('Y') ?> Gestion Stock
            </div>
        </div>
    </div>
</body>
</html>