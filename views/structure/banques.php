<?php
/**
 * @var array $banques
 */
?>

<div class="p-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-university text-blue-600"></i>
                Banques
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestion des comptes bancaires de l'entreprise</p>
        </div>
        <a href="?action=banque_creer" 
           class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
            <i class="fas fa-plus mr-1.5 text-xs"></i>
            Nouvelle banque
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

    <!-- Tableau des banques -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($banques)): ?>
            <div class="text-center py-12">
                <i class="fas fa-university text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Aucune banque enregistrée.</p>
                <a href="?action=banque_creer" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">Ajouter une banque</a>
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-300">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sigle</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Responsable</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Téléphone</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-24">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banques as $index => $b): ?>
                    <tr class="hover:bg-gray-50 <?= $index > 0 ? 'border-t border-gray-200' : '' ?>">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-building-columns text-blue-500 text-sm"></i>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($b['nom_banque']) ?></span>
                            </div>
                            <?php if (!empty($b['adresse'])): ?>
                                <div class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($b['adresse']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?= htmlspecialchars($b['sigle'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?= htmlspecialchars($b['responsable'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?= htmlspecialchars($b['tel'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="?action=banque_versements&id_banque=<?= $b['id_banque'] ?>" 
                                   class="w-7 h-7 inline-flex items-center justify-center text-purple-600 hover:bg-purple-50 rounded transition" 
                                   title="Voir état des versements">
                                    <i class="fas fa-chart-line text-xs"></i>
                                </a>
                                <a href="?action=banque_modifier&id=<?= $b['id_banque'] ?>" 
                                   class="w-7 h-7 inline-flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded transition" 
                                   title="Modifier">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="?action=banque_supprimer&id=<?= $b['id_banque'] ?>" 
                                   onclick="return confirm('Supprimer définitivement cette banque ?')" 
                                   class="w-7 h-7 inline-flex items-center justify-center text-red-600 hover:bg-red-50 rounded transition" 
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
