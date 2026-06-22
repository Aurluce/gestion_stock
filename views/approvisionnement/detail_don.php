<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <p class="text-caption text-neutral-50 mb-1">Donateur</p>
        <p class="text-body font-medium text-neutral-14"><?= htmlspecialchars($don['donateur'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Contact</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars($don['contact_donateur'] ?? '—') ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date don</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y', strtotime($don['date_don'])) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date enregistrement</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y H:i', strtotime($don['date_creation'])) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Valeur estimée</p>
        <p class="text-body font-mono font-medium text-neutral-14"><?= number_format((float)$don['valeur_estimee'], 0, ',', ' ') ?> F</p>
    </div>
    <?php if (!empty($don['description'])): ?>
    <div class="md:col-span-2">
        <p class="text-caption text-neutral-50 mb-1">Description</p>
        <p class="text-body text-neutral-14"><?= nl2br(htmlspecialchars($don['description'])) ?></p>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($lignes)): ?>
<h4 class="text-sm font-semibold text-neutral-50 uppercase tracking-wider mb-3">Produits reçus</h4>
<div class="overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th class="text-right">Qté</th>
                <th>Unité</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $l): ?>
            <tr>
                <td class="font-medium"><?= htmlspecialchars($l['nom_produit'] ?? '—') ?></td>
                <td class="text-right font-mono"><?= number_format((float)$l['quantite'], 2, ',', ' ') ?></td>
                <td><?= htmlspecialchars($l['unite'] ?? '—') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
