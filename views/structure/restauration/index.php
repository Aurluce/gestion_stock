<?php
$title = "Corbeille - Restauration";
$pageActions = renderButton('Vider la corbeille', 'danger', '?action=restauration_clear', ['icon' => 'fa-trash', 'data-confirm' => 'Vider définitivement la corbeille ? Cette action est irréversible.']);
echo renderPageHeader('Corbeille', 'Éléments supprimés et sauvegardés en XML', $pageActions);
?>

<?= renderFlashAlerts() ?>

<!-- Filtres -->
<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="" class="form-grid">
            <input type="hidden" name="action" value="restauration">
            
            <div class="form-group">
                <?= renderSelect('type', 'Type d\'objet', array_combine($types, $types), $typeFiltre ?? '', null, ['onchange' => 'this.form.submit()']) ?>
            </div>
            
            <div class="form-group">
                <?= renderInput('search', 'Rechercher', 'text', $search ?? '', null, ['placeholder' => 'Type ou ID...']) ?>
            </div>
            
            <div class="form-group flex flex-row items-end gap-2">
                <?= renderButton('Filtrer', 'primary', null, ['type' => 'submit', 'icon' => 'fa-search']) ?>
                <?= renderButton('Réinitialiser', 'secondary', '?action=restauration', ['icon' => 'fa-sync-alt']) ?>
            </div>
        </form>
    </div>
</div>

<!-- Liste des éléments supprimés -->
<?php if (empty($elements)): ?>
    <?= renderEmptyState('fa-trash-restore', 'Corbeille vide', 'Aucun élément supprimé pour le moment.') ?>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Type</th>
                            <th class="text-left">Nom / Référence</th>
                            <th class="text-left">ID original</th>
                            <th class="text-left">Date suppression</th>
                            <th class="text-left">Supprimé par</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($elements as $e): ?>
                        <tr>
                            <td class="text-left">
                                <?php
                                $badgeType = match($e['type_objet']) {
                                    'PRODUIT_COMPLET' => 'success',
                                    'FOURNISSEUR_COMPLET' => 'info',
                                    'MOUVEMENT_BANQUE' => 'warning',
                                    default => 'neutral'
                                };
                                echo renderBadge(htmlspecialchars($e['type_objet']), $badgeType);
                                ?>
                            </td>
                            <td class="text-left">
                                <?php
                                $xml = @simplexml_load_string($e['donnees_xml']);
                                $nom = '';
                                if ($xml) {
                                    if (isset($xml->nom)) $nom = (string)$xml->nom;
                                    elseif (isset($xml->nom_produit)) $nom = (string)$xml->nom_produit;
                                    elseif (isset($xml->reference)) $nom = (string)$xml->reference;
                                }
                                echo htmlspecialchars($nom ?: '-');
                                ?>
                            </td>
                            <td class="text-left"><?= $e['id_objet'] ?></td>
                            <td class="text-left"><?= date('d/m/Y H:i:s', strtotime($e['date_suppression'])) ?></td>
                            <td class="text-left"><?= htmlspecialchars($e['supprime_par_nom'] ?? 'Système') ?></td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <?= renderButton('', 'icon', '?action=restauration_view&id=' . $e['id_corbeille'], ['icon' => 'fa-eye', 'title' => 'Voir le détail']) ?>
                                    <?= renderButton('', 'icon', '?action=restauration_restore&id=' . $e['id_corbeille'], ['icon' => 'fa-trash-restore', 'title' => 'Restaurer', 'data-confirm' => 'Restaurer cet élément ?']) ?>
                                    <?= renderButton('', 'icon-danger', '?action=restauration_delete&id=' . $e['id_corbeille'], ['icon' => 'fa-trash', 'title' => 'Supprimer définitivement', 'data-confirm' => 'Supprimer définitivement ?']) ?>
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