<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <p class="text-caption text-neutral-50 mb-1">Nom</p>
        <p class="text-body font-medium text-neutral-14"><?= htmlspecialchars($produit['nom_produit']) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Code barre</p>
        <p class="text-body font-mono text-neutral-14"><?= htmlspecialchars($produit['code_barre'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Famille</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($produit['nom_famille'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Produit père</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($produit['nom_produit_pere'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Prix achat</p>
        <p class="text-body font-medium text-neutral-14"><?= number_format((float)$produit['prix_achat'], 0, ',', ' ') ?> F</p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Prix vente</p>
        <p class="text-body font-medium text-neutral-14"><?= number_format((float)$produit['prix_vente'], 0, ',', ' ') ?> F</p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Stock actuel</p>
        <p class="text-body font-mono font-medium text-neutral-14"><?= number_format((float)$produit['stock_actuel'], 2, ',', ' ') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Seuil alerte</p>
        <p class="text-body font-mono text-neutral-14"><?= number_format((float)$produit['seuil_alerte'], 2, ',', ' ') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Unité</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($produit['unite'] ?? 'pce') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Périssable</p>
        <p class="text-body text-neutral-14"><?= $produit['perissable'] ? 'Oui' : 'Non' ?></p>
    </div>
    <?php if ($produit['perissable'] && $produit['date_peremption']): ?>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date péremption</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y', strtotime($produit['date_peremption'])) ?></p>
    </div>
    <?php endif; ?>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Statut</p>
        <p class="text-body text-neutral-14"><?= $produit['est_actif'] ? 'Actif' : 'Inactif' ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date création</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y H:i', strtotime($produit['date_creation'])) ?></p>
    </div>
    <?php if (!empty($produit['description'])): ?>
    <div class="md:col-span-2">
        <p class="text-caption text-neutral-50 mb-1">Description</p>
        <p class="text-body text-neutral-14"><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
    </div>
    <?php endif; ?>
</div>
