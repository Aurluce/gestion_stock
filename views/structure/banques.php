<?php
$title = "Banques";
$pageActions = renderButton('Nouvelle banque', 'primary', '?action=banque_creer', ['icon' => 'fa-plus']);
echo renderPageHeader('Banques', 'Gérez les comptes bancaires de l\'entreprise', $pageActions);
?>

<?= renderFlashAlerts() ?>

<?php if (empty($banques)): ?>
    <?= renderEmptyState('fa-university', 'Aucune banque', 'Commencez par ajouter votre première banque.', renderButton('Créer une banque', 'primary', '?action=banque_creer', ['icon' => 'fa-plus'])) ?>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Nom</th>
                            <th class="text-left">Sigle</th>
                            <th class="text-left">Responsable</th>
                            <th class="text-left">Téléphone</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banques as $b): ?>
                        <tr>
                            <td class="text-left">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-building-columns text-brand-500"></i>
                                    <?= htmlspecialchars($b['nom_banque']) ?>
                                </div>
                                <?php if (!empty($b['adresse'])): ?>
                                    <div class="text-xs text-neutral-50"><?= htmlspecialchars($b['adresse']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-left"><?= htmlspecialchars($b['sigle'] ?? '-') ?></td>
                            <td class="text-left"><?= htmlspecialchars($b['responsable'] ?? '-') ?></td>
                            <td class="text-left"><?= htmlspecialchars($b['tel'] ?? '-') ?></td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <?= renderButton('', 'icon', '?action=banque_versements&id_banque=' . $b['id_banque'], ['icon' => 'fa-chart-line', 'title' => 'Voir état des versements']) ?>
                                    <?= renderButton('', 'icon', '?action=banque_modifier&id=' . $b['id_banque'], ['icon' => 'fa-edit', 'title' => 'Modifier']) ?>
                                    <?= renderButton('', 'icon-danger', '?action=banque_supprimer&id=' . $b['id_banque'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer cette banque ?',
            'data-confirm-type' => 'danger']) ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>