<?php
$title = "Mon profil";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Mon profil']
]);
?>

<?= renderPageHeader('Mon profil', 'Informations personnelles et sécurité du compte') ?>

<div class="max-w-lg mx-auto">
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="text-body-lg font-semibold text-neutral-14">
                <i class="fas fa-user-circle text-brand-600 mr-2"></i>Mon profil
            </h2>
        </div>
        <div class="card-body space-y-3">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-brand-600 text-white flex items-center justify-center text-h4 font-bold">
                    <?= strtoupper(substr(htmlspecialchars($user['nom_complet'] ?? 'U'), 0, 2)) ?>
                </div>
                <div>
                    <p class="text-body-lg font-semibold text-neutral-14"><?= htmlspecialchars($user['nom_complet'] ?? '') ?></p>
                    <p class="text-body text-neutral-50"><?= htmlspecialchars($user['login'] ?? '') ?></p>
                </div>
            </div>
            <hr class="border-neutral-90">
            <div class="grid grid-cols-2 gap-2 text-body">
                <span class="text-neutral-50">Dernière connexion :</span>
                <span class="text-neutral-14"><?= htmlspecialchars($user['derniere_connexion'] ?? 'Jamais') ?></span>
                <span class="text-neutral-50">Compte actif :</span>
                <span><?= $user['actif'] ? renderBadge('Oui', 'success') : renderBadge('Non', 'danger') ?></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="text-body-lg font-semibold text-neutral-14">
                <i class="fas fa-key text-brand-600 mr-2"></i>Changer le mot de passe
            </h2>
        </div>
        <div class="card-body">
            <form method="post" class="space-y-4">
                <div>
                    <label class="label" for="old_password">Ancien mot de passe</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-neutral-60">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="old_password" id="old_password" required class="input pl-10">
                    </div>
                </div>
                <div>
                    <label class="label" for="new_password">Nouveau mot de passe</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-neutral-60">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password" name="new_password" id="new_password" required class="input pl-10">
                    </div>
                </div>
                <div>
                    <label class="label" for="confirm_password">Confirmer le mot de passe</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-neutral-60">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <input type="password" name="confirm_password" id="confirm_password" required class="input pl-10">
                    </div>
                </div>
                <?= renderButton('Modifier le mot de passe', 'primary', null, ['icon' => 'fa-save', 'class' => 'w-full']) ?>
            </form>
        </div>
    </div>
</div>
