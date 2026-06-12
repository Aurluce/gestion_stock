<?php
/**
 * @var array $clients
 */
?>

<div class="p-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-blue-600"></i>
                Clients
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestion des clients de l'entreprise</p>
        </div>
        <a href="?action=client_creer" 
           class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
            <i class="fas fa-plus mr-1.5 text-xs"></i>
            Nouveau client
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

    <!-- Tableau des clients -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($clients)): ?>
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Aucun client enregistré.</p>
                <a href="?action=client_creer" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">Ajouter un client</a>
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-300">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom complet</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Téléphone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ville</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-24">Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-24">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $index => $c): ?>
                    <?php $nomComplet = $c['nom'] . ($c['prenom'] ? ' ' . $c['prenom'] : '') ?>
                    <tr class="hover:bg-gray-50 <?= $index > 0 ? 'border-t border-gray-200' : '' ?>">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-blue-500 text-sm"></i>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($nomComplet) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?= htmlspecialchars($c['tel'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?= htmlspecialchars($c['email'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <?= htmlspecialchars($c['ville'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php
                            $typeLabel = match($c['type_client'] ?? 'particulier') {
                                'particulier' => 'Particulier',
                                'entreprise' => 'Entreprise',
                                'administration' => 'Administration',
                                default => $c['type_client']
                            };
                            ?>
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full"><?= $typeLabel ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="?action=client_modifier&id=<?= $c['id_client'] ?>" 
                                   class="w-7 h-7 inline-flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded transition" 
                                   title="Modifier">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="?action=client_supprimer&id=<?= $c['id_client'] ?>" 
                                   onclick="return confirm('Supprimer définitivement ce client ?')" 
                                   class="w-7 h-7 inline-flex items-center justify-center text-red-600 hover:bg-red-50 rounded transition" 
                                   title="Supprimer">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </a>
                            </div>
                        </td>
                    <tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
