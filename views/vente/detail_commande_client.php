<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <p class="text-caption text-neutral-50 mb-1">Référence</p>
        <p class="text-body font-mono font-medium text-neutral-14"><?= htmlspecialchars($commande['reference']) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Date</p>
        <p class="text-body text-neutral-14"><?= date('d/m/Y', strtotime($commande['date_commande'])) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Client</p>
        <p class="text-body font-medium text-neutral-14"><?= htmlspecialchars(($commande['client_nom'] ?? '') . ' ' . ($commande['client_prenom'] ?? '')) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Type vente</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars(ucfirst($commande['type_vente'] ?? '—')) ?></p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Montant total</p>
        <p class="text-body font-mono font-bold text-neutral-14"><?= number_format((float)$commande['montant_total'], 0, ',', ' ') ?> FCFA</p>
    </div>
    <div>
        <p class="text-caption text-neutral-50 mb-1">Statut</p>
        <p class="text-body text-neutral-14"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $commande['statut']))) ?></p>
    </div>
    <?php if (!empty($commande['observations'])): ?>
    <div class="md:col-span-2">
        <p class="text-caption text-neutral-50 mb-1">Observations</p>
        <p class="text-body text-neutral-14"><?= nl2br(htmlspecialchars($commande['observations'])) ?></p>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($lignes)): ?>
<h4 class="text-sm font-semibold text-neutral-50 uppercase tracking-wider mb-3">Lignes de commande</h4>
<div class="overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Prix unit.</th>
                <th class="text-right">Remise</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $l):
                $totalLigne = (float)$l['quantite'] * (float)$l['prix_unitaire'] * (1 - ((float)($l['taux_remise'] ?? 0) / 100));
            ?>
            <tr>
                <td class="font-medium"><?= htmlspecialchars($l['nom_produit'] ?? '—') ?></td>
                <td class="text-right font-mono"><?= number_format((float)$l['quantite'], 2, ',', ' ') ?></td>
                <td class="text-right font-mono"><?= number_format((float)$l['prix_unitaire'], 0, ',', ' ') ?></td>
                <td class="text-right"><?= $l['taux_remise'] ? (float)$l['taux_remise'] . '%' : '—' ?></td>
                <td class="text-right font-mono font-medium"><?= number_format($totalLigne, 0, ',', ' ') ?> F</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
