<?php
/**
 * @var array $fournisseurs
 */
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-truck text-blue-600"></i>
                Fournisseurs
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestion des fournisseurs de l'entreprise</p>
        </div>
        <a href="?action=fournisseur_creer" 
           class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
            <i class="fas fa-plus mr-1.5 text-xs"></i>
            Nouveau fournisseur
        </a>
    </div>

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

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($fournisseurs)): ?>
            <div class="text-center py-12">
                <i class="fas fa-truck text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Aucun fournisseur enregistré.</p>
                <a href="?action=fournisseur_creer" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">Ajouter un fournisseur</a>
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-300">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Téléphone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ville</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-24">Statut</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-28">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fournisseurs as $index => $f): ?>
                    <tr class="hover:bg-gray-50 <?= $index > 0 ? 'border-t border-gray-200' : '' ?>">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-building text-blue-500 text-sm"></i>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($f['nom']) ?></span>
                            </div>
                            <?php if (!empty($f['nif'])): ?>
                                <div class="text-xs text-gray-400 mt-0.5">NIF: <?= htmlspecialchars($f['nif']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($f['tel'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($f['email'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($f['ville'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($f['est_actif']): ?>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Actif</span>
                            <?php else: ?>
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="?action=fournisseur_modifier&id=<?= $f['id_fournisseur'] ?>" 
                                   class="w-7 h-7 inline-flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded transition" 
                                   title="Modifier">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <?php if ($f['est_actif']): ?>
                                    <!-- Si ACTIF -> icône toggle-on (ON) -->
                                    <a href="?action=fournisseur_desactiver&id=<?= $f['id_fournisseur'] ?>" 
                                       onclick="return confirm('Désactiver ce fournisseur ?')" 
                                       class="w-7 h-7 inline-flex items-center justify-center text-green-600 hover:bg-green-50 rounded transition" 
                                       title="Désactiver">
                                        <i class="fas fa-toggle-on text-xs"></i>
                                    </a>
                                <?php else: ?>
                                    <!-- Si INACTIF -> icône toggle-off (OFF) -->
                                    <a href="?action=fournisseur_activer&id=<?= $f['id_fournisseur'] ?>" 
                                       onclick="return confirm('Activer ce fournisseur ?')" 
                                       class="w-7 h-7 inline-flex items-center justify-center text-orange-600 hover:bg-orange-50 rounded transition" 
                                       title="Activer">
                                        <i class="fas fa-toggle-off text-xs"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="?action=fournisseur_supprimer&id=<?= $f['id_fournisseur'] ?>" 
                                   onclick="return confirm('Supprimer définitivement ce fournisseur ?')" 
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