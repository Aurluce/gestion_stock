<?php
$title = "État des versements bancaires";
$backLink = '?action=banques';

echo renderPageHeader('État des versements bancaires', 'Consultez les mouvements bancaires par période', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>

<?= renderFlashAlerts() ?>

<!-- Formulaire de filtrage -->
<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="action" value="banque_versements">
            
            <div class="flex-1 min-w-[180px]">
                <label class="label">Banque</label>
                <select name="id_banque" class="select" onchange="this.form.submit()">
                    <option value="">-- Sélectionner une banque --</option>
                    <?php foreach ($banques as $id => $nom): ?>
                        <option value="<?= $id ?>" <?= ($idBanque == $id) ? 'selected' : '' ?>><?= htmlspecialchars($nom) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="w-[150px]">
                <label class="label">Date début</label>
                <input type="date" name="date_debut" value="<?= $dateDebut ?>" class="input">
            </div>
            
            <div class="w-[150px]">
                <label class="label">Date fin</label>
                <input type="date" name="date_fin" value="<?= $dateFin ?>" class="input">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Filtrer</button>
                <a href="?action=banque_versements" class="btn-secondary">Réinitialiser</a>
            </div>
            
            <?php if ($idBanque > 0): ?>
                <div class="ml-auto">
                    <button type="button" onclick="openModal()" class="btn-success">
                        <i class="fas fa-plus mr-1"></i> Nouveau mouvement
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($idBanque > 0): ?>
    
    <?php if (!empty($mouvements)): ?>
        <!-- Cartes récapitulatives -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="card">
                <div class="card-body">
                    <p class="text-caption text-neutral-50">Solde initial</p>
                    <p class="text-h4 font-bold text-neutral-14"><?= number_format($soldeInitial, 0) ?> FCFA</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="text-caption text-neutral-50">Total entrées</p>
                    <p class="text-h4 font-bold text-success-500"><?= number_format($totalEntrees, 0) ?> FCFA</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="text-caption text-neutral-50">Total sorties</p>
                    <p class="text-h4 font-bold text-danger-500"><?= number_format($totalSorties, 0) ?> FCFA</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="text-caption text-neutral-50">Solde final</p>
                    <p class="text-h4 font-bold <?= $soldeFinal >= 0 ? 'text-brand-600' : 'text-danger-500' ?>">
                        <?= number_format($soldeFinal, 0) ?> FCFA
                    </p>
                </div>
            </div>
        </div>

        <!-- Tableau des mouvements -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-body-lg font-semibold text-neutral-14">
                    <i class="fas fa-history mr-2"></i> Historique des mouvements
                </h2>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="text-left w-[100px]">Date</th>
                                <th class="text-left w-[100px]">Type</th>
                                <th class="text-right w-[150px]">Montant</th>
                                <th class="text-left w-[120px]">Référence</th>
                                <th class="text-left">Description</th>
                                <th class="text-left w-[130px]">Enregistré par</th>
                                <th class="text-center w-[60px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mouvements as $m): ?>
                            <tr>
                                <td class="text-left"><?= date('d/m/Y', strtotime($m['date_mouvement'])) ?></td>
                                <td class="text-left">
                                    <?php if ($m['type_mouvement'] == 'versement'): ?>
                                        <?= renderBadge('Versement', 'success') ?>
                                    <?php else: ?>
                                        <?= renderBadge('Retrait', 'danger') ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right <?= $m['type_mouvement'] == 'versement' ? 'text-success-500 font-semibold' : 'text-danger-500 font-semibold' ?>">
                                    <?= number_format($m['montant'], 0) ?> FCFA
                                </td>
                                <td class="text-left"><?= htmlspecialchars($m['reference'] ?? '-') ?></td>
                                <td class="text-left"><?= htmlspecialchars($m['description'] ?? '-') ?></td>
                                <td class="text-left text-sm text-neutral-50"><?= htmlspecialchars($m['utilisateur_nom'] ?? 'Système') ?></td>
                                <td class="text-center">
                                    <?= renderButton('', 'icon-danger', '?action=banque_mouvement_supprimer&id=' . $m['id_mouvement_banque'] . '&id_banque=' . $idBanque, ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce mouvement ?']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card text-center py-12">
            <div class="card-body">
                <i class="fas fa-receipt text-5xl text-neutral-70 mb-4 block"></i>
                <p class="text-body text-neutral-50 mb-4">Aucun mouvement bancaire pour cette période.</p>
                <button type="button" onclick="openModal()" class="btn-primary">Ajouter un premier mouvement</button>
            </div>
        </div>
    <?php endif; ?>
    
<?php else: ?>
    <div class="card text-center py-12">
        <div class="card-body">
            <i class="fas fa-university text-5xl text-neutral-70 mb-4 block"></i>
            <p class="text-body text-neutral-50">Sélectionnez une banque pour voir ses mouvements.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Ajout Mouvement -->
<div id="addMouvementModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: 20px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1f2937;">
                <i class="fas fa-plus-circle" style="color: #16a34a; margin-right: 8px;"></i>
                Nouveau mouvement bancaire
            </h3>
            <button type="button" onclick="closeModal()" style="color: #9ca3af; background: none; border: none; cursor: pointer; font-size: 20px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="?action=banque_mouvement_enregistrer">
            <div class="modal-body" style="padding: 20px;">
                <input type="hidden" name="id_banque" value="<?= $idBanque ?>">
                <div class="space-y-4">
                    <div>
                        <label class="label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Type d'opération *</label>
                        <select name="type_mouvement" class="select" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;" required>
                            <option value="versement"> Versement (entrée d'argent)</option>
                            <option value="retrait"> Retrait (sortie d'argent)</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Date *</label>
                        <input type="date" name="date_mouvement" value="<?= date('Y-m-d') ?>" class="input" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;" required>
                    </div>
                    <div>
                        <label class="label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Montant (FCFA) *</label>
                        <input type="number" step="1" name="montant" class="input" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;" placeholder="0" required>
                    </div>
                    <div>
                        <label class="label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Référence</label>
                        <input type="text" name="reference" class="input" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;" placeholder="N° chèque, virement...">
                    </div>
                    <div>
                        <label class="label" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px;">Description</label>
                        <textarea name="description" rows="2" class="textarea" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;" placeholder="Motif de l'opération..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 16px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeModal()" class="btn-secondary" style="padding: 6px 16px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Annuler</button>
                <button type="submit" class="btn-primary" style="padding: 6px 16px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer;">Enregistrer</button>
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
document.getElementById('addMouvementModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>