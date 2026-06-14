<?php
$title = "Banques";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=banques'],
    ['label' => 'Banques']
]);
ob_start();
?>

<?= renderPageHeader(
    'Banques',
    'Gérer les comptes bancaires de l\'entreprise',
    renderButton('Nouvelle banque', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']) .
    renderButton('Imprimer', 'secondary', '?action=banques&print=1', ['icon' => 'fa-print'])
) ?>

<?php
$actionsRenderer = function($row, $rowIndex) use ($banques) {
    $b = $banques[$rowIndex] ?? null;
    if (!$b) return '';
    
    $actions = renderButton('', 'icon', '?action=banque_versements&id_banque=' . $b['id_banque'], [
        'icon' => 'fa-chart-line',
        'title' => 'Voir état des versements'
    ]);
    $actions .= renderButton('', 'icon', '', [
        'icon' => 'fa-edit',
        'title' => 'Modifier',
        'data-modal-toggle' => 'editModal',
        'data-edit-banque' => json_encode($b)
    ]);
    $actions .= renderButton('', 'icon-danger', '?action=banques&delete=' . $b['id_banque'], [
        'icon' => 'fa-trash',
        'title' => 'Supprimer',
        'data-confirm' => 'Supprimer cette banque ?'
    ]);
    return $actions;
};

$tableData = array_map(function($b) {
    return [
        '<div class="flex items-center gap-2"><i class="fas fa-building-columns text-brand-500"></i>' . htmlspecialchars($b['nom_banque']) . '</div>',
        htmlspecialchars($b['sigle'] ?? '-'),
        htmlspecialchars($b['responsable'] ?? '-'),
        htmlspecialchars($b['tel'] ?? '-'),
        htmlspecialchars($b['adresse'] ?? '-')
    ];
}, $banques);

echo renderResponsiveTable(
    ['Nom', 'Sigle', 'Responsable', 'Téléphone', 'Adresse'],
    $tableData,
    [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => null,
        'mobileHidden' => [2, 3, 4],
        'actions' => $actionsRenderer,
        'emptyMessage' => 'Aucune banque trouvée.'
    ]
);
?>

<!-- Modal création -->
<?php
$createBody = '
<form method="post" action="?action=banques" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('nom_banque', 'Nom de la banque *', 'text', '', null, ['required' => 'required']) . '
        ' . renderInput('sigle', 'Sigle', 'text', '') . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('responsable', 'Responsable', 'text', '') . '
        ' . renderInput('tel', 'Téléphone', 'tel', '') . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('email', 'Email', 'email', '') . '
        ' . renderInput('adresse', 'Adresse', 'text', '') . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Créer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('createModal', 'Ajouter une banque', $createBody);
?>

<!-- Modal modification -->
<?php
$editBody = '
<form method="post" action="?action=banques">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_banque" id="edit_id">
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('nom_banque', 'Nom de la banque *', 'text', '', null, ['id' => 'edit_nom_banque', 'required' => 'required']) . '
        ' . renderInput('sigle', 'Sigle', 'text', '', null, ['id' => 'edit_sigle']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('responsable', 'Responsable', 'text', '', null, ['id' => 'edit_responsable']) . '
        ' . renderInput('tel', 'Téléphone', 'tel', '', null, ['id' => 'edit_tel']) . '
    </div>
    <div class="grid grid-cols-2 gap-3">
        ' . renderInput('email', 'Email', 'email', '', null, ['id' => 'edit_email']) . '
        ' . renderInput('adresse', 'Adresse', 'text', '', null, ['id' => 'edit_adresse']) . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        ' . renderButton('Enregistrer', 'primary', null, ['icon' => 'fa-save']) . '
    </div>
</form>';
echo renderModal('editModal', 'Modifier une banque', $editBody);
?>

<script>
document.querySelectorAll('[data-edit-banque]').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-edit-banque'));
        document.getElementById('edit_id').value = data.id_banque;
        document.getElementById('edit_nom_banque').value = data.nom_banque;
        document.getElementById('edit_sigle').value = data.sigle || '';
        document.getElementById('edit_responsable').value = data.responsable || '';
        document.getElementById('edit_tel').value = data.tel || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_adresse').value = data.adresse || '';
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>