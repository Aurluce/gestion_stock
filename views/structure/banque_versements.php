<?php
/**
 * @var array $banques
 * @var int $idBanque
 * @var string $dateDebut
 * @var string $dateFin
 * @var array $mouvements
 * @var float $soldeInitial
 * @var float $totalEntrees
 * @var float $totalSorties
 * @var float $soldeFinal
 */
?>

<div class="p-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                État des versements bancaires
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Consultez les mouvements bancaires par période</p>
        </div>
        <div class="flex gap-2">
            <?php if ($idBanque > 0): ?>
                <button type="button" onclick="openModal()" 
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-blue-600 text-sm rounded-lg transition shadow-sm">
                    <i class="fas fa-plus mr-1.5 text-xs"></i>
                    Nouveau mouvement
                </button>
            <?php endif; ?>
            <a href="?action=banques" 
               class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
                <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
                Retour
            </a>
        </div>
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

    <!-- Formulaire de filtrage -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="action" value="banque_versements">
            
            <div class="flex-1 min-w-[180px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Banque</label>
                <select name="id_banque" class="w-full border border-gray-300 rounded-lg px-3 py-2" onchange="this.form.submit()">
                    <option value="">-- Sélectionner une banque --</option>
                    <?php foreach ($banques as $id => $nom): ?>
                        <option value="<?= $id ?>" <?= ($idBanque == $id) ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="date_debut" value="<?= $dateDebut ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            
            <div class="w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="<?= $dateFin ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    <i class="fas fa-search mr-1.5 text-xs"></i> Filtrer
                </button>
                <a href="?action=banque_versements" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
                    <i class="fas fa-sync-alt mr-1.5 text-xs"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <?php if ($idBanque > 0): ?>
        
        <?php if (!empty($mouvements)): ?>
            <!-- Cartes récapitulatives -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Solde initial</p>
                    <p class="text-xl font-bold text-gray-800 mt-1"><?= number_format($soldeInitial, 0) ?> FCFA</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total entrées</p>
                    <p class="text-xl font-bold text-green-600 mt-1"><?= number_format($totalEntrees, 0) ?> FCFA</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total sorties</p>
                    <p class="text-xl font-bold text-red-600 mt-1"><?= number_format($totalSorties, 0) ?> FCFA</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Solde final</p>
                    <p class="text-xl font-bold <?= $soldeFinal >= 0 ? 'text-blue-600' : 'text-red-600' ?> mt-1">
                        <?= number_format($soldeFinal, 0) ?> FCFA
                    </p>
                </div>
            </div>

            <!-- Tableau des mouvements -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h2 class="font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-blue-500"></i>
                        Historique des mouvements
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr class="text-xs font-semibold text-gray-600 uppercase">
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-right">Montant</th>
                                <th class="px-4 py-2 text-left">Référence</th>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-left">Enregistré par</th>
                                <th class="px-4 py-2 text-center w-16">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($mouvements as $index => $m): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-600"><?= date('d/m/Y', strtotime($m['date_mouvement'])) ?></td>
                                <td class="px-4 py-2">
                                    <?php if ($m['type_mouvement'] == 'versement'): ?>
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Versement</span>
                                    <?php else: ?>
                                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Retrait</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-right text-sm font-medium <?= $m['type_mouvement'] == 'versement' ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= number_format($m['montant'], 0) ?> FCFA
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600"><?= htmlspecialchars($m['reference'] ?? '-') ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500"><?= htmlspecialchars($m['description'] ?? '-') ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <?= htmlspecialchars($m['utilisateur_nom'] ?? 'Système') ?>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="?action=banque_mouvement_supprimer&id=<?= $m['id_mouvement_banque'] ?>&id_banque=<?= $idBanque ?>" 
                                       onclick="return confirm('Supprimer ce mouvement ?')" 
                                       class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-6 h-6 rounded hover:bg-red-50 transition" 
                                       title="Supprimer">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg border border-gray-200 text-center py-12">
                <i class="fas fa-receipt text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500 mb-3">Aucun mouvement bancaire pour cette période.</p>
                <button type="button" onclick="openModal()" 
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                    <i class="fas fa-plus mr-1.5 text-xs"></i> Ajouter un premier mouvement
                </button>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="bg-white rounded-lg border border-gray-200 text-center py-12">
            <i class="fas fa-university text-4xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-500">Sélectionnez une banque pour voir ses mouvements.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Ajout Mouvement -->
<div id="addMouvementModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 450px; width: 90%; margin: 20px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1f2937;">
                <i class="fas fa-plus-circle" style="color: #16a34a; margin-right: 8px;"></i>
                Nouveau mouvement bancaire
            </h3>
            <button type="button" onclick="closeModal()" style="color: #9ca3af; background: none; border: none; cursor: pointer; font-size: 20px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="?action=banque_mouvement_enregistrer" style="padding: 20px;">
            <input type="hidden" name="id_banque" value="<?= $idBanque ?>">
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">Type d'opération *</label>
                <select name="type_mouvement" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;" required>
                    <option value="versement">Versement (entrée d'argent)</option>
                    <option value="retrait">Retrait (sortie d'argent)</option>
                </select>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">Date *</label>
                <input type="date" name="date_mouvement" value="<?= date('Y-m-d') ?>" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;" required>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">Montant (FCFA) *</label>
                <input type="number" step="1" name="montant" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;" placeholder="0" required>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">Référence</label>
                <input type="text" name="reference" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;" placeholder="N° chèque, virement, reçu...">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">Description</label>
                <textarea name="description" rows="2" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 14px;" placeholder="Motif de l'opération..."></textarea>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <button type="button" onclick="closeModal()" style="padding: 6px 12px; background: #e5e7eb; border: none; border-radius: 8px; font-size: 14px; cursor: pointer;">Annuler</button>
                <button type="submit" style="padding: 6px 12px; background: #2563eb; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('addMouvementModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('addMouvementModal').style.display = 'none';
    }
    // Fermer en cliquant en dehors
    document.getElementById('addMouvementModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
