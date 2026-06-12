<?php
/**
 * @var array $client
 */
$nomComplet = $client['nom'] . ($client['prenom'] ? ' ' . $client['prenom'] : '');
?>
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Crédit client</h1>
        <p class="text-gray-600">Consultez et gérez le crédit du client</p>
    </div>

    <div class="bg-white rounded shadow p-6">
        <div class="border-b pb-4 mb-4">
            <h2 class="text-xl font-semibold"><?= htmlspecialchars($nomComplet) ?></h2>
            <p class="text-gray-500"><?= htmlspecialchars($client['tel'] ?? 'Pas de téléphone') ?> | <?= htmlspecialchars($client['email'] ?? 'Pas d\'email') ?></p>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded text-center">
                <p class="text-gray-500 text-sm">Crédit actuel</p>
                <p class="text-3xl font-bold <?= $client['solde_credit'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                    <?= number_format($client['solde_credit'], 2) ?> FCFA
                </p>
            </div>
            <div class="bg-gray-50 p-4 rounded text-center">
                <p class="text-gray-500 text-sm">Catégorie / Remise</p>
                <p class="text-xl font-bold"><?= htmlspecialchars($client['nom_categorie'] ?? 'Standard') ?></p>
                <?php if ($client['taux_remise'] > 0): ?>
                    <p class="text-green-600">Remise <?= $client['taux_remise'] ?>%</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6">
            <p class="text-sm text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Le crédit client est automatiquement mis à jour lors des commandes et règlements.
                Un crédit positif signifie que le client doit de l'argent.
            </p>
        </div>
        
        <div class="flex justify-between gap-2">
            <a href="?controller=client&action=index" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Retour à la liste</a>
            <a href="?controller=client&action=edit&id=<?= $client['id_client'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded">Modifier le client</a>
        </div>
    </div>
</div>