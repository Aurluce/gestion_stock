<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <p class="text-caption text-neutral-50 mb-1">Nom</p>
        <p class="text-body font-medium text-neutral-14"><?= htmlspecialchars($fournisseur['nom']) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">NIF</p>
        <p class="text-body font-mono text-neutral-14"><?= htmlspecialchars($fournisseur['nif'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Téléphone</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($fournisseur['tel'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Email</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($fournisseur['email'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Ville</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($fournisseur['ville'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Adresse</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($fournisseur['adresse'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Statut</p>
        <p class="text-body text-neutral-14"><?= $fournisseur['est_actif'] ? 'Actif' : 'Inactif' ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Nb commandes</p>
        <p class="text-body text-neutral-14"><?= (int)$nbCommandes ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date création</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y H:i', strtotime($fournisseur['date_creation'])) ?></p>
    </div>
</div>
