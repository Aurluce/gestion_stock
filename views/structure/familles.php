<?php
/**
 * @var array $familles
 */
?>

<div class="p-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-folder-tree text-blue-600"></i>
                Familles de produits
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestion des catégories de produits</p>
        </div>
        <a href="?action=famille_creer" 
           class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
            <i class="fas fa-plus mr-1.5 text-xs"></i>
            Nouvelle famille
        </a>
    </div>

    <!-- Messages flash -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md mb-4 text-sm flex items-center justify-between">
            <span><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button type="button" class="text-green-500 hover:text-green-700" onclick="this.closest('div').remove()">×</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-md mb-4 text-sm flex items-center justify-between">
            <span><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('div').remove()">×</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Tableau des familles -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($familles)): ?>
            <div class="text-center py-12">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Aucune famille enregistrée.</p>
                <a href="?action=famille_creer" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">Créer la première famille</a>
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-300">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date création</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-28">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($familles as $index => $famille): ?>
                    <tr class="hover:bg-gray-50 <?= $index > 0 ? 'border-t border-gray-200' : '' ?>">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-folder text-blue-500 text-sm"></i>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($famille['nom_famille']) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-sm">
                            <?= htmlspecialchars($famille['description'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-sm">
                            <?= $famille['date_creation_fr'] ?? '-' ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="?action=famille_modifier&id=<?= $famille['id_famille'] ?>" 
                                   class="inline-flex items-center gap-1 px-2 py-1 text-blue-600 hover:bg-blue-50 rounded transition text-sm" 
                                   title="Modifier">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="?action=famille_supprimer&id=<?= $famille['id_famille'] ?>" 
                                   onclick="return confirm('Supprimer cette famille ?')" 
                                   class="inline-flex items-center gap-1 px-2 py-1 text-red-600 hover:bg-red-50 rounded transition text-sm" 
                                   title="Supprimer">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
