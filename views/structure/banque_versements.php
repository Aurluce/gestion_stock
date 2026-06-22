<?php
$title = "État des versements bancaires";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Banques', 'href' => '?action=banques'],
    ['label' => 'Versements']
]);
$backLink = '?action=banques';

echo renderPageHeader('État des versements bancaires', 'Consultez les mouvements bancaires par période', renderButton('Retour', 'secondary', $backLink, ['icon' => 'fa-arrow-left']));
?>


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
                    <button type="button" class="btn-success" data-modal-toggle="addMouvementModal">
                        <i class="fas fa-plus mr-1"></i> Nouveau mouvement
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($idBanque > 0): ?>

    <?php if (!empty($mouvements)): ?>
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

        <?php
        $rows = array_map(function($m) {
            return [
                date('d/m/Y', strtotime($m['date_mouvement'])),
                $m['type_mouvement'] == 'versement' ? renderBadge('Versement', 'success') : renderBadge('Retrait', 'danger'),
                '<span class="' . ($m['type_mouvement'] == 'versement' ? 'text-success-500' : 'text-danger-500') . ' font-semibold">' . number_format($m['montant'], 0) . ' FCFA</span>',
                htmlspecialchars($m['reference'] ?? '-'),
                htmlspecialchars($m['description'] ?? '-'),
                htmlspecialchars($m['utilisateur_nom'] ?? 'Système'),
            ];
        }, $mouvements);

        $actionsRenderer = function($row, $rowIndex) use ($mouvements, $idBanque) {
            $m = $mouvements[$rowIndex] ?? null;
            if (!$m) return '';
            return renderButton('', 'icon-danger', '?action=banque_mouvement_supprimer&id=' . $m['id_mouvement_banque'] . '&id_banque=' . $idBanque, ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce mouvement ?', 'data-confirm-type' => 'danger']);
        };

        echo renderResponsiveTable(
            ['Date', 'Type', 'Montant', 'Référence', 'Description', 'Enregistré par'],
            $rows,
            [
                'mobileTitle' => 0,
                'mobileBadge' => 1,
                'mobileHidden' => [5],
                'actions' => $actionsRenderer,
                'emptyMessage' => 'Aucun mouvement bancaire trouvé.'
            ]
        );
        ?>
    <?php else: ?>
        <?= renderEmptyState('fa-receipt', 'Aucun mouvement', 'Aucun mouvement bancaire pour cette période.', '<button type="button" class="btn-primary" data-modal-toggle="addMouvementModal">Ajouter un premier mouvement</button>') ?>
    <?php endif; ?>

<?php else: ?>
    <div class="card text-center py-12">
        <div class="card-body">
            <i class="fas fa-university text-5xl text-neutral-70 mb-4 block"></i>
            <p class="text-body text-neutral-50">Sélectionnez une banque pour voir ses mouvements.</p>
        </div>
    </div>
<?php endif; ?>

<?php
$modalBody = '
<form method="POST" action="?action=banque_mouvement_enregistrer">
    <input type="hidden" name="id_banque" value="' . $idBanque . '">
    <div class="space-y-4">
        <div>
            <label class="label">Type d\'opération *</label>
            <select name="type_mouvement" class="select w-full" required>
                <option value="versement"> Versement (entrée d\'argent)</option>
                <option value="retrait"> Retrait (sortie d\'argent)</option>
            </select>
        </div>
        <div>
            <label class="label">Date *</label>
            <input type="date" name="date_mouvement" value="' . date('Y-m-d') . '" class="input w-full" required>
        </div>
        <div>
            <label class="label">Montant (FCFA) *</label>
            <input type="number" step="1" name="montant" class="input w-full" placeholder="0" required>
        </div>
        <div>
            <label class="label">Référence</label>
            <input type="text" name="reference" class="input w-full" placeholder="N° chèque, virement...">
        </div>
        <div>
            <label class="label">Description</label>
            <textarea name="description" rows="2" class="textarea w-full" placeholder="Motif de l\'opération..."></textarea>
        </div>
    </div>
</form>';

$modalFooter = '
    <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
    <button type="submit" class="btn-primary" onclick="this.closest(\'.modal-overlay\').querySelector(\'form\').submit()">Enregistrer</button>
';

echo renderModal('addMouvementModal', 'Nouveau mouvement bancaire', $modalBody, $modalFooter);
?>
