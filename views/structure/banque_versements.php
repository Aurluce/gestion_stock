<?php
$title = "État des versements bancaires";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=banques'],
    ['label' => 'État des versements']
]);
ob_start();
?>

<?= renderPageHeader(
    'État des versements bancaires',
    'Consultez les mouvements bancaires par période',
    renderButton('Retour aux banques', 'secondary', '?action=banques', ['icon' => 'fa-arrow-left'])
) ?>

<!-- Formulaire de filtrage avec renderFilterBar -->
<?= renderFilterBar('banque_versements', [
    ['select', 'id_banque', 'Banque', $banques, $idBanque],
    ['date', 'date_debut', 'Date début', $dateDebut],
    ['date', 'date_fin', 'Date fin', $dateFin],
]) ?>

<?php if ($idBanque > 0): ?>
    <div class="flex justify-end mb-4">
        <?= renderButton('Nouveau mouvement', 'success', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) ?>
    </div>
<?php endif; ?>

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
        <?php
        $actionsRenderer = function($row, $rowIndex) use ($mouvements, $idBanque) {
            $m = $mouvements[$rowIndex] ?? null;
            if (!$m) return '';
            return renderButton('', 'icon-danger', '?action=banque_mouvement_supprimer&id=' . $m['id_mouvement_banque'] . '&id_banque=' . $idBanque, [
                'icon' => 'fa-trash',
                'title' => 'Supprimer',
                'data-confirm' => 'Supprimer ce mouvement ?'
            ]);
        };
        
        $tableData = array_map(function($m) {
            $typeBadge = $m['type_mouvement'] == 'versement' ? renderBadge('Versement', 'success') : renderBadge('Retrait', 'danger');
            $montantClass = $m['type_mouvement'] == 'versement' ? 'text-success-500' : 'text-danger-500';
            return [
                date('d/m/Y', strtotime($m['date_mouvement'])),
                $typeBadge,
                '<span class="' . $montantClass . ' font-medium">' . number_format($m['montant'], 0) . ' FCFA</span>',
                htmlspecialchars($m['reference'] ?? '-'),
                htmlspecialchars($m['description'] ?? '-'),
                htmlspecialchars($m['utilisateur_nom'] ?? 'Système')
            ];
        }, $mouvements);
        
        echo renderResponsiveTable(
            ['Date', 'Type', 'Montant', 'Référence', 'Description', 'Enregistré par'],
            $tableData,
            [
                'mobileTitle' => 0,
                'mobileSubtitle' => 4,
                'mobileBadge' => 1,
                'actions' => $actionsRenderer,
                'emptyMessage' => 'Aucun mouvement trouvé.'
            ]
        );
        ?>
        
    <?php else: ?>
        <?= renderEmptyState('fa-receipt', 'Aucun mouvement bancaire', 'Aucun mouvement pour cette période.', 
            renderButton('Ajouter un premier mouvement', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
    <?php endif; ?>
    
<?php else: ?>
    <?= renderEmptyState('fa-university', 'Sélectionnez une banque', 'Veuillez sélectionner une banque pour voir ses mouvements.') ?>
<?php endif; ?>

<!-- Modal création mouvement -->
<?php
$createBody = '
<form method="post" action="?action=banque_mouvement_enregistrer" class="space-y-4">
    <input type="hidden" name="id_banque" value="' . $idBanque . '">
    ' . renderSelect('type_mouvement', 'Type d\'opération *', ['versement' => ' Versement (entrée)', 'retrait' => ' Retrait (sortie)'], null, null, ['required' => 'required']) . '
    ' . renderInput('date_mouvement', 'Date *', 'date', date('Y-m-d'), null, ['required' => 'required']) . '
    ' . renderInput('montant', 'Montant (FCFA) *', 'number', '', null, ['required' => 'required', 'step' => '1', 'placeholder' => '0']) . '
    ' . renderInput('reference', 'Référence', 'text', '', null, ['placeholder' => 'N° chèque, virement...']) . '
    ' . renderTextarea('description', 'Description', '', null, ['rows' => 2, 'placeholder' => 'Motif de l\'opération...']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save', 'type' => 'submit']) . '
    </div>
</form>';
echo renderModal('createModal', 'Nouveau mouvement bancaire', $createBody);
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>