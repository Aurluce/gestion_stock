<?php
$title = "Produits";
$mode = $_GET['mode'] ?? 'liste';
$familleSelectionnee = (int)($_GET['id_famille'] ?? 0);

// Barre d'outils
$toolbar = '<div class="flex flex-wrap items-center gap-4">';
$toolbar .= '<div class="flex items-center gap-2">';
$toolbar .= '<span class="text-sm text-neutral-50">Affichage :</span>';
$toolbar .= '<select class="border border-neutral-80 rounded-lg px-3 py-1.5 text-sm" onchange="window.location.href=\'?action=produits&mode=\'+this.value">';
$toolbar .= '<option value="liste" ' . ($mode == 'liste' ? 'selected' : '') . '> Liste simple</option>';
$toolbar .= '<option value="par_famille" ' . ($mode == 'par_famille' ? 'selected' : '') . '> Par famille</option>';
$toolbar .= '</select></div>';

if ($mode == 'liste') {
    $toolbar .= '<div class="flex items-center gap-2">';
    $toolbar .= '<span class="text-sm text-neutral-50">Filtrer :</span>';
    $toolbar .= '<select class="border border-neutral-80 rounded-lg px-3 py-1.5 text-sm" onchange="window.location.href=\'?action=produits&mode=liste&id_famille=\'+this.value">';
    $toolbar .= '<option value="">Toutes les familles</option>';
    foreach ($famillesSelect as $id => $nom) {
        $selected = ($familleSelectionnee == $id) ? 'selected' : '';
        $toolbar .= '<option value="' . $id . '" ' . $selected . '>' . htmlspecialchars($nom) . '</option>';
    }
    $toolbar .= '</select></div>';
}
$toolbar .= '</div>';

$pageActions = renderButton('Nouveau produit', 'primary', '?action=produit_creer', ['icon' => 'fa-plus']);
echo renderPageHeader('Produits', 'Gérez votre catalogue de produits', $pageActions);
?>

<?= renderFlashAlerts() ?>

<?= renderCard($toolbar, 'Filtres') ?>

<?php if ($mode == 'par_famille'): ?>
    <?php if (empty($produitsParFamille)): ?>
        <?= renderEmptyState('fa-box-open', 'Aucun produit', 'Commencez par ajouter votre premier produit.', renderButton('Créer un produit', 'primary', '?action=produit_creer')) ?>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($produitsParFamille as $famille): ?>
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-body-lg font-semibold text-neutral-14">
                            <i class="fas fa-folder text-brand-600 mr-2"></i>
                            <?= htmlspecialchars($famille['nom_famille']) ?>
                            <span class="badge-neutral ml-2"><?= count($famille['produits']) ?> produit(s)</span>
                        </h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left">Nom</th>
                                        <th class="text-left">Produit père</th>
                                        <th class="text-right">Prix vente</th>
                                        <th class="text-right">Stock</th>
                                        <th class="text-center">Statut</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($famille['produits'] as $p): ?>
                                    <tr>
                                        <td class="text-left"><?= htmlspecialchars($p['nom_produit']) ?></td>
                                        <td class="text-left"><?= htmlspecialchars($p['nom_produit_pere'] ?? '-') ?></td>
                                        <td class="text-right"><?= number_format($p['prix_vente'], 0) ?> FCFA</td>
                                        <td class="text-right"><?= number_format($p['stock_actuel'], 2) ?></td>
                                        <td class="text-center">
                                            <?php if ($p['est_actif']): ?>
                                                <?= renderBadge('Actif', 'success') ?>
                                            <?php else: ?>
                                                <?= renderBadge('Inactif', 'danger') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= renderButton('', 'icon', '?action=produit_modifier&id=' . $p['id_produit'], ['icon' => 'fa-edit', 'title' => 'Modifier']) ?>
                                            <?= renderButton('', 'icon-danger', '?action=produit_supprimer&id=' . $p['id_produit'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce produit ?']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?php if (empty($produitsListe)): ?>
        <?= renderEmptyState('fa-box-open', 'Aucun produit', 'Commencez par ajouter votre premier produit.', renderButton('Créer un produit', 'primary', '?action=produit_creer')) ?>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Nom</th>
                                <th class="text-left">Famille</th>
                                <th class="text-left">Produit père</th>
                                <th class="text-right">Prix vente</th>
                                <th class="text-right">Stock</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produitsListe as $p): ?>
                            <tr>
                                <td class="text-left"><?= htmlspecialchars($p['nom_produit']) ?></td>
                                <td class="text-left"><?= htmlspecialchars($p['nom_famille'] ?? '-') ?></td>
                                <td class="text-left"><?= htmlspecialchars($p['nom_produit_pere'] ?? '-') ?></td>
                                <td class="text-right"><?= number_format($p['prix_vente'], 0) ?> FCFA</td>
                                <td class="text-right"><?= number_format($p['stock_actuel'], 2) ?></td>
                                <td class="text-center">
                                    <?php if ($p['est_actif']): ?>
                                        <?= renderBadge('Actif', 'success') ?>
                                    <?php else: ?>
                                        <?= renderBadge('Inactif', 'danger') ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center gap-1">
                                        <?= renderButton('', 'icon', '?action=produit_modifier&id=' . $p['id_produit'], ['icon' => 'fa-edit', 'title' => 'Modifier']) ?>
                                        <?php if ($p['est_actif']): ?>
                                            <?= renderButton('', 'icon', '?action=produit_desactiver&id=' . $p['id_produit'], ['icon' => 'fa-toggle-on', 'title' => 'Désactiver', 'data-confirm' => 'Désactiver ce produit ?']) ?>
                                        <?php else: ?>
                                            <?= renderButton('', 'icon', '?action=produit_activer&id=' . $p['id_produit'], ['icon' => 'fa-toggle-off', 'title' => 'Activer', 'data-confirm' => 'Activer ce produit ?']) ?>
                                        <?php endif; ?>
                                        <?= renderButton('', 'icon-danger', '?action=produit_supprimer&id=' . $p['id_produit'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce produit ?']) ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>