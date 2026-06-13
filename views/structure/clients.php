<?php
$title = "Clients";
$pageActions = renderButton('Nouveau client', 'primary', '?action=client_creer', ['icon' => 'fa-plus']);
echo renderPageHeader('Clients', 'Gérez vos clients', $pageActions);
?>

<?= renderFlashAlerts() ?>

<?php if (empty($clients)): ?>
    <?= renderEmptyState('fa-users', 'Aucun client', 'Commencez par ajouter votre premier client.', renderButton('Créer un client', 'primary', '?action=client_creer', ['icon' => 'fa-plus'])) ?>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Nom complet</th>
                            <th class="text-left">Téléphone</th>
                            <th class="text-left">Email</th>
                            <th class="text-left">Ville</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $c): ?>
                        <?php $nomComplet = $c['nom'] . ($c['prenom'] ? ' ' . $c['prenom'] : ''); ?>
                        <tr>
                            <td class="text-left">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-brand-500"></i>
                                    <?= htmlspecialchars($nomComplet) ?>
                                </div>
                            </td>
                            <td class="text-left"><?= htmlspecialchars($c['tel'] ?? '-') ?></td>
                            <td class="text-left"><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                            <td class="text-left"><?= htmlspecialchars($c['ville'] ?? '-') ?></td>
                            <td class="text-center">
                                <?php
                                $typeLabel = match($c['type_client'] ?? 'particulier') {
                                    'particulier' => 'Particulier',
                                    'entreprise' => 'Entreprise',
                                    'administration' => 'Administration',
                                    default => $c['type_client']
                                };
                                ?>
                                <?= renderBadge($typeLabel, 'info') ?>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <?= renderButton('', 'icon', '?action=client_modifier&id=' . $c['id_client'], ['icon' => 'fa-edit', 'title' => 'Modifier']) ?>
                                    <?= renderButton('', 'icon-danger', '?action=client_supprimer&id=' . $c['id_client'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce client ?']) ?>
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