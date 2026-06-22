<?php
$title = "Fournisseurs";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Fournisseurs']
]);
$pageActions = renderButton('Nouveau fournisseur', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']);
echo renderPageHeader('Fournisseurs', 'Gérez les fournisseurs de l\'entreprise', $pageActions);
?>


<?php if (empty($fournisseurs)): ?>
    <?= renderEmptyState('fa-truck', 'Aucun fournisseur', 'Commencez par ajouter votre premier fournisseur.', renderButton('Créer un fournisseur', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
<?php else: ?>
    <?php
    $rows = array_map(function($f) {
        $nomHtml = '<div class="flex items-center gap-2"><i class="fas fa-building text-brand-500"></i>' . htmlspecialchars($f['nom']);
        if (!empty($f['nif'])) {
            $nomHtml .= '<span class="text-xs text-neutral-50">(NIF: ' . htmlspecialchars($f['nif']) . ')</span>';
        }
        $nomHtml .= '</div>';
        return [
            $nomHtml,
            htmlspecialchars($f['tel'] ?? '-'),
            htmlspecialchars($f['email'] ?? '-'),
            htmlspecialchars($f['ville'] ?? '-'),
            $f['est_actif'] ? renderBadge('Actif', 'success') : renderBadge('Inactif', 'danger'),
        ];
    }, $fournisseurs);

    $actionsRenderer = function($row, $rowIndex) use ($fournisseurs) {
        $f = $fournisseurs[$rowIndex] ?? null;
        if (!$f) return '';
        $actions = renderButton('', 'icon', '', ['icon' => 'fa-eye', 'title' => 'Voir', 'data-detail' => '?action=fournisseur_detail&id=' . $f['id_fournisseur'], 'data-detail-title' => 'Fournisseur : ' . $f['nom']]);
        $actions .= renderButton('', 'icon', '', ['icon' => 'fa-edit', 'title' => 'Modifier', 'data-modal-toggle' => 'editModal', 'data-edit-four' => $f['id_fournisseur']]);
        if ($f['est_actif']) {
            $actions .= renderButton('', 'icon', '?action=fournisseur_desactiver&id=' . $f['id_fournisseur'], ['icon' => 'fa-toggle-on', 'title' => 'Désactiver', 'data-confirm' => 'Désactiver ce fournisseur ?', 'data-confirm-type' => 'warning']);
        } else {
            $actions .= renderButton('', 'icon', '?action=fournisseur_activer&id=' . $f['id_fournisseur'], ['icon' => 'fa-toggle-off', 'title' => 'Activer', 'data-confirm' => 'Activer ce fournisseur ?', 'data-confirm-type' => 'success']);
        }
        $actions .= renderButton('', 'icon-danger', '?action=fournisseur_supprimer&id=' . $f['id_fournisseur'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce fournisseur ?', 'data-confirm-type' => 'danger']);
        return $actions;
    };

    echo renderResponsiveTable(
        ['Nom', 'Téléphone', 'Email', 'Ville', 'Statut'],
        $rows,
        [
            'mobileTitle' => 0,
            'mobileBadge' => 4,
            'mobileHidden' => [1, 2, 3],
            'actions' => $actionsRenderer,
            'emptyMessage' => 'Aucun fournisseur trouvé.'
        ]
    );
    ?>
<?php endif; ?>

<?php
$createBody = '
<form method="POST" action="?action=fournisseurs" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('nom', 'Nom *', 'text', '', null, ['required' => 'required']) . '
        ' . renderInput('tel', 'Téléphone', 'text', '') . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('email', 'Email', 'email', '') . '
        ' . renderInput('nif', 'NIF', 'text', '') . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('ville', 'Ville', 'text', '') . '
        ' . renderInput('adresse', 'Adresse', 'text', '') . '
    </div>
    <div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="est_actif" value="1" checked class="checkbox">
            <span class="text-sm">Fournisseur actif</span>
        </label>
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-plus mr-1.5"></i>Créer</button>
    </div>
</form>';
echo renderModal('createModal', 'Nouveau fournisseur', $createBody);

$editBody = '
<form method="POST" action="?action=fournisseurs" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_fournisseur" id="edit_id_four">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('nom', 'Nom *', 'text', '', null, ['id' => 'edit_nom', 'required' => 'required']) . '
        ' . renderInput('tel', 'Téléphone', 'text', '', null, ['id' => 'edit_tel']) . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('email', 'Email', 'email', '', null, ['id' => 'edit_email']) . '
        ' . renderInput('nif', 'NIF', 'text', '', null, ['id' => 'edit_nif']) . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('ville', 'Ville', 'text', '', null, ['id' => 'edit_ville']) . '
        ' . renderInput('adresse', 'Adresse', 'text', '', null, ['id' => 'edit_adresse']) . '
    </div>
    <div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="est_actif" value="1" id="edit_est_actif" class="checkbox">
            <span class="text-sm">Fournisseur actif</span>
        </label>
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-save mr-1.5"></i>Mettre à jour</button>
    </div>
</form>';
echo renderModal('editModal', 'Modifier le fournisseur', $editBody);
?>

<script>
const FOURNISSEURS = <?= json_encode($fournisseurs) ?>;
document.querySelectorAll('[data-edit-four]').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-edit-four');
        const f = FOURNISSEURS.find(f => f.id_fournisseur == id);
        if (!f) return;
        document.getElementById('edit_id_four').value = f.id_fournisseur;
        document.getElementById('edit_nom').value = f.nom || '';
        document.getElementById('edit_tel').value = f.tel || '';
        document.getElementById('edit_email').value = f.email || '';
        document.getElementById('edit_nif').value = f.nif || '';
        document.getElementById('edit_ville').value = f.ville || '';
        document.getElementById('edit_adresse').value = f.adresse || '';
        document.getElementById('edit_est_actif').checked = !!f.est_actif;
    });
});
</script>
