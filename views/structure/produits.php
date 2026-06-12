<?php
$title = "Produits";
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-boxes text-blue-600"></i>
                Produits
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestion des produits et variantes</p>
        </div>
        <a href="?action=produit_creer" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
            <i class="fas fa-plus mr-1.5 text-xs"></i> Nouveau produit
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md mb-4 text-sm"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-md mb-4 text-sm"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Affichage :</span>
                <select class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm" onchange="window.location.href='?action=produits&mode='+this.value">
                    <option value="liste" <?= $mode == 'liste' ? 'selected' : '' ?>>📋 Liste simple</option>
                    <option value="par_famille" <?= $mode == 'par_famille' ? 'selected' : '' ?>>📁 Par famille</option>
                </select>
            </div>
            
            <?php if ($mode == 'liste'): ?>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Filtrer :</span>
                    <select class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm" onchange="window.location.href='?action=produits&mode=liste&id_famille='+this.value">
                        <option value="">Toutes les familles</option>
                        <?php foreach ($famillesSelect as $id => $nom): ?>
                            <option value="<?= $id ?>" <?= ($familleSelectionnee == $id) ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($mode == 'par_famille'): ?>
        <?php if (empty($produitsParFamille)): ?>
            <div class="bg-white rounded-lg border border-gray-200 text-center py-12">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Aucun produit enregistré.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($produitsParFamille as $famille): ?>
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h2 class="font-semibold text-gray-800">📁 <?= htmlspecialchars($famille['nom_famille']) ?></h2>
                        </div>
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr class="text-xs font-semibold text-gray-600 uppercase">
                                    <th class="px-4 py-2 text-left">Nom</th>
                                    <th class="px-4 py-2 text-left">Variante de</th>
                                    <th class="px-4 py-2 text-right">Prix vente</th>
                                    <th class="px-4 py-2 text-right">Stock</th>
                                    <th class="px-4 py-2 text-center">Statut</th>
                                    <th class="px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($famille['produits'] as $p): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($p['nom_produit']) ?></td>
                                    <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($p['nom_produit_pere'] ?? '-') ?></td>
                                    <td class="px-4 py-2 text-right"><?= number_format($p['prix_vente'], 0) ?> FCFA</td>
                                    <td class="px-4 py-2 text-right"><?= number_format($p['stock_actuel'], 2) ?></td>
                                    <td class="px-4 py-2 text-center">
                                        <?php if ($p['est_actif']): ?>
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Actif</span>
                                        <?php else: ?>
                                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="?action=produit_modifier&id=<?= $p['id_produit'] ?>" class="text-blue-600 hover:text-blue-800" title="Modifier"><i class="fas fa-edit"></i></a>
                                            <?php if ($p['est_actif']): ?>
                                                <a href="?action=produit_desactiver&id=<?= $p['id_produit'] ?>" onclick="return confirm('Désactiver ce produit ?')" class="text-green-600 hover:text-green-800" title="Désactiver"><i class="fas fa-toggle-on"></i></a>
                                            <?php else: ?>
                                                <a href="?action=produit_activer&id=<?= $p['id_produit'] ?>" onclick="return confirm('Activer ce produit ?')" class="text-orange-600 hover:text-orange-800" title="Activer"><i class="fas fa-toggle-off"></i></a>
                                            <?php endif; ?>
                                            <a href="?action=produit_supprimer&id=<?= $p['id_produit'] ?>" onclick="return confirm('Supprimer ?')" class="text-red-600 hover:text-red-800" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-300">
                    <tr class="text-xs font-semibold text-gray-600 uppercase">
                        <th class="px-4 py-3 text-left">Nom</th>
                        <th class="px-4 py-3 text-left">Famille</th>
                        <th class="px-4 py-3 text-left">Variante de</th>
                        <th class="px-4 py-3 text-right">Prix vente</th>
                        <th class="px-4 py-3 text-right">Stock</th>
                        <th class="px-4 py-3 text-center">Statut</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produitsListe as $index => $p): ?>
                    <tr class="hover:bg-gray-50 <?= $index > 0 ? 'border-t border-gray-100' : '' ?>">
                        <td class="px-4 py-3"><?= htmlspecialchars($p['nom_produit']) ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($p['nom_famille'] ?? '-') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($p['nom_produit_pere'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-right"><?= number_format($p['prix_vente'], 0) ?> FCFA</td>
                        <td class="px-4 py-3 text-right"><?= number_format($p['stock_actuel'], 2) ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($p['est_actif']): ?>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Actif</span>
                            <?php else: ?>
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="?action=produit_modifier&id=<?= $p['id_produit'] ?>" class="text-blue-600 hover:text-blue-800" title="Modifier"><i class="fas fa-edit"></i></a>
                                <?php if ($p['est_actif']): ?>
                                    <a href="?action=produit_desactiver&id=<?= $p['id_produit'] ?>" onclick="return confirm('Désactiver ce produit ?')" class="text-green-600 hover:text-green-800" title="Désactiver"><i class="fas fa-toggle-on"></i></a>
                                <?php else: ?>
                                    <a href="?action=produit_activer&id=<?= $p['id_produit'] ?>" onclick="return confirm('Activer ce produit ?')" class="text-orange-600 hover:text-orange-800" title="Activer"><i class="fas fa-toggle-off"></i></a>
                                <?php endif; ?>
                                <a href="?action=produit_supprimer&id=<?= $p['id_produit'] ?>" onclick="return confirm('Supprimer ?')" class="text-red-600 hover:text-red-800" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>