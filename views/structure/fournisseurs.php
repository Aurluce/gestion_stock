<?php
$title = "Fournisseurs";
$pageActions = renderButton('Nouveau fournisseur', 'primary', '?action=fournisseur_creer', ['icon' => 'fa-plus']);
echo renderPageHeader('Fournisseurs', 'Gérez les fournisseurs de l\'entreprise', $pageActions);
?>

<?= renderFlashAlerts() ?>

<?php if (empty($fournisseurs)): ?>
    <?= renderEmptyState('fa-truck', 'Aucun fournisseur', 'Commencez par ajouter votre premier fournisseur.', renderButton('Créer un fournisseur', 'primary', '?action=fournisseur_creer', ['icon' => 'fa-plus'])) ?>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Nom</th>
                            <th class="text-left">Téléphone</th>
                            <th class="text-left">Email</th>
                            <th class="text-left">Ville</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fournisseurs as $f): ?>
                        <tr>
                            <td class="text-left">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-building text-brand-500"></i>
                                    <?= htmlspecialchars($f['nom']) ?>
                                    <?php if (!empty($f['nif'])): ?>
                                        <span class="text-xs text-neutral-50">(NIF: <?= htmlspecialchars($f['nif']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-left"><?= htmlspecialchars($f['tel'] ?? '-') ?></td>
                            <td class="text-left"><?= htmlspecialchars($f['email'] ?? '-') ?></td>
                            <td class="text-left"><?= htmlspecialchars($f['ville'] ?? '-') ?></td>
                            <td class="text-center">
                                <?php if ($f['est_actif']): ?>
                                    <?= renderBadge('Actif', 'success') ?>
                                <?php else: ?>
                                    <?= renderBadge('Inactif', 'danger') ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <?= renderButton('', 'icon', '?action=fournisseur_modifier&id=' . $f['id_fournisseur'], ['icon' => 'fa-edit', 'title' => 'Modifier']) ?>
                                    <?php if ($f['est_actif']): ?>
                                        <?= renderButton('', 'icon', '?action=fournisseur_desactiver&id=' . $f['id_fournisseur'], ['icon' => 'fa-toggle-on', 'title' => 'Désactiver', 'data-confirm' => 'Désactiver ce fournisseur ?', 'data-confirm-type' => 'warning']) ?>
                                    <?php else: ?>
                                        <?= renderButton('', 'icon', '?action=fournisseur_activer&id=' . $f['id_fournisseur'], ['icon' => 'fa-toggle-off', 'title' => 'Activer', 'data-confirm' => 'Activer ce fournisseur ?', 'data-confirm-type' => 'success']) ?>
                                    <?php endif; ?>
                                    <?= renderButton('', 'icon-danger', '?action=fournisseur_supprimer&id=' . $f['id_fournisseur'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce fournisseur ?', 'data-confirm-type' => 'danger']) ?>
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