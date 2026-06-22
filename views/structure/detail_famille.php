<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <p class="text-caption text-neutral-50 mb-1">Nom</p>
        <p class="text-body font-medium text-neutral-14"><?= htmlspecialchars($famille['nom_famille'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date création</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y H:i', strtotime($famille['date_creation'])) ?></p>
    </div>
    <div class="md:col-span-2">
        <p class="text-caption text-neutral-50 mb-1">Description</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($famille['description'] ?? 'Aucune description') ?></p>
    </div>
    <div class="md:col-span-2">
        <p class="text-caption text-neutral-50 mb-1">Produits liés</p>
        <p class="text-body text-neutral-14"><?= (int)$nbProduits ?> produit(s)</p>
    </div>
</div>
