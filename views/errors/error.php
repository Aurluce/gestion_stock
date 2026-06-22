<div class="flex items-center justify-center py-12">
    <div class="card max-w-lg w-full">
        <div class="card-body text-center py-10">
            <div class="w-24 h-24 rounded-2xl <?= $cfg['bg'] ?> flex items-center justify-center <?= $cfg['text'] ?> text-h1 mx-auto mb-6 <?= $code === 403 ? 'animate-pulse' : '' ?>">
                <i class="fas <?= $cfg['icon'] ?> <?= $code === 500 ? 'fa-spin' : '' ?>"></i>
            </div>

            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?= $cfg['bg'] ?> <?= $cfg['text'] ?> mb-4">
                Code <?= $code ?>
            </span>

            <h2 class="text-h2 font-bold text-neutral-14 mb-3"><?= $cfg['title'] ?></h2>

            <p class="text-body text-neutral-50 max-w-sm mx-auto mb-2"><?= $cfg['desc'] ?></p>

            <?php if ($message): ?>
                <p class="text-xs text-neutral-40 font-mono bg-neutral-98 inline-block px-3 py-1 rounded mb-6">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <?php if ($cfg['hint']): ?>
                <p class="text-sm text-neutral-40 max-w-sm mx-auto mb-8">
                    <i class="fas fa-lightbulb text-warning-500 mr-1"></i>
                    <?= $cfg['hint'] ?>
                </p>
            <?php endif; ?>

            <div class="flex items-center justify-center gap-3 pt-2">
                <a href="javascript:history.back()" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-1.5"></i>Retour
                </a>
                <a href="?action=dashboard" class="btn-primary">
                    <i class="fas fa-home mr-1.5"></i>Tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>
