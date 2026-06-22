<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <p class="text-caption text-neutral-50 mb-1">Nom complet</p>
        <p class="text-body font-medium text-neutral-14"><?= htmlspecialchars(($client['nom'] ?? '') . ' ' . ($client['prenom'] ?? '')) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Type</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars(ucfirst($client['type_client'] ?? 'particulier')) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Catégorie</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($client['nom_categorie'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Téléphone</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($client['tel'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Email</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($client['email'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Ville</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($client['ville'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Crédit client</p>
        <p class="text-body text-neutral-14"><?= number_format((float)$creditClient, 0, ',', ' ') ?> F</p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Nb commandes</p>
        <p class="text-body text-neutral-14"><?= (int)$nbCommandes ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Statut</p>
        <p class="text-body text-neutral-14"><?= $client['est_actif'] ? 'Actif' : 'Inactif' ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date création</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y H:i', strtotime($client['date_creation'])) ?></p>
    </div>
</div>
