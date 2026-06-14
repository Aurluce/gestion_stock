<?php
$title = "Fournisseurs";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=fournisseurs'],
    ['label' => 'Fournisseurs']
]);
ob_start();
?>

<?= renderPageHeader(
    'Fournisseurs',
    'Gérer les fournisseurs de l\'entreprise',
    renderButton('Nouveau fournisseur', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) .
    renderButton('Imprimer', 'secondary', '?action=fournisseurs&print=1', ['icon' => 'fa-print'])
) ?>

<?php
$actionsRenderer = function($row, $rowIndex) use ($fournisseurs) {
    $f = $fournisseurs[$rowIndex] ?? null;
    if (!$f) return '';
    
    $actions = renderButton('', 'icon', '', [
        'icon' => 'fa-edit',
        'title' => 'Modifier',
        'data-modal-toggle' => 'editModal',
        'data-edit-fournisseur' => json_encode($f)
    ]);
    
    if ($f['est_actif']) {
        $actions .= renderButton('', 'icon', '?action=fournisseurs&disable=' . $f['id_fournisseur'], [
            'icon' => 'fa-toggle-on',
            'title' => 'Désactiver',
            'data-confirm' => 'Désactiver ce fournisseur ?'
        ]);
    } else {
        $actions .= renderButton('', 'icon', '?action=fournisseurs&enable=' . $f['id_fournisseur'], [
            'icon' => 'fa-toggle-off',
            'title' => 'Activer',
            'data-confirm' => 'Activer ce fournisseur ?'
        ]);
    }
    
    $actions .= renderButton('', 'icon-danger', '?action=fournisseurs&delete=' . $f['id_fournisseur'], [
        'icon' => 'fa-trash',
        'title' => 'Supprimer',
        'data-confirm' => 'Supprimer ce fournisseur ?'
    ]);
    return $actions;
};

$tableData = array_map(function($f) {
    $nom = '<div class="flex items-center gap-2"><i class="fas fa-building text-brand-500"></i>' . htmlspecialchars($f['nom']) . '</div>';
    if (!empty($f['nif'])) {
        $nom .= '<div class="text-xs text-neutral-50">NIF: ' . htmlspecialchars($f['nif']) . '</div>';
    }
    return [
        $nom,
        htmlspecialchars($f['tel'] ?? '-'),
        htmlspecialchars($f['email'] ?? '-'),
        htmlspecialchars($f['ville'] ?? '-'),
        $f['est_actif'] ? renderBadge('Actif', 'success') : renderBadge('Inactif', 'danger')
    ];
}, $fournisseurs);

echo renderResponsiveTable(
    ['Nom', 'Téléphone', 'Email', 'Ville', 'Statut'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => 4,
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucun fournisseur trouvé.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=fournisseurs" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('nom', 'Nom *', 'text', '', null, ['required' => 'required']) . '
        ' . renderInput('tel', 'Téléphone', 'tel', '') . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('email', 'Email', 'email', '') . '
        ' . renderInput('nif', 'NIF', 'text', '') . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('ville', 'Ville', 'text', '') . '
        ' . renderInput('adresse', 'Adresse', 'text', '') . '
    </div>
    ' . renderCheckbox('est_actif', 'Fournisseur actif', true) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Ajouter un fournisseur', $createBody);
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=fournisseurs">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_fournisseur" id="edit_id">
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('nom', 'Nom *', 'text', '', null, ['id' => 'edit_nom', 'required' => 'required']) . '
        ' . renderInput('tel', 'Téléphone', 'tel', '', null, ['id' => 'edit_tel']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('email', 'Email', 'email', '', null, ['id' => 'edit_email']) . '
        ' . renderInput('nif', 'NIF', 'text', '', null, ['id' => 'edit_nif']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('ville', 'Ville', 'text', '', null, ['id' => 'edit_ville']) . '
        ' . renderInput('adresse', 'Adresse', 'text', '', null, ['id' => 'edit_adresse']) . '
    </div>
    ' . renderCheckbox('est_actif', 'Fournisseur actif', true, ['id' => 'edit_est_actif']) . '
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier un fournisseur', $editBody);
?>

<script>
document.querySelectorAll('[data-edit-fournisseur]').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-edit-fournisseur'));
        document.getElementById('edit_id').value = data.id_fournisseur;
        document.getElementById('edit_nom').value = data.nom;
        document.getElementById('edit_tel').value = data.tel || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_nif').value = data.nif || '';
        document.getElementById('edit_ville').value = data.ville || '';
        document.getElementById('edit_adresse').value = data.adresse || '';
        document.getElementById('edit_est_actif').checked = data.est_actif;
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>