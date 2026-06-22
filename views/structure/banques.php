<?php
$title = "Banques";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Banques']
]);
$pageActions = renderButton('Nouvelle banque', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']);
echo renderPageHeader('Banques', 'Gérez les comptes bancaires de l\'entreprise', $pageActions);
?>


<?php if (empty($banques)): ?>
    <?= renderEmptyState('fa-university', 'Aucune banque', 'Commencez par ajouter votre première banque.', renderButton('Créer une banque', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
<?php else: ?>
    <?php
    $rows = array_map(function($b) {
        $nomHtml = '<div class="flex items-center gap-2"><i class="fas fa-building-columns text-brand-500"></i>' . htmlspecialchars($b['nom_banque']);
        if (!empty($b['adresse'])) {
            $nomHtml .= '<div class="text-xs text-neutral-50">' . htmlspecialchars($b['adresse']) . '</div>';
        }
        $nomHtml .= '</div>';
        return [
            $nomHtml,
            htmlspecialchars($b['sigle'] ?? '-'),
            htmlspecialchars($b['responsable'] ?? '-'),
            htmlspecialchars($b['tel'] ?? '-'),
        ];
    }, $banques);

    $actionsRenderer = function($row, $rowIndex) use ($banques) {
        $b = $banques[$rowIndex] ?? null;
        if (!$b) return '';
        return renderButton('', 'icon', '?action=banque_versements&id_banque=' . $b['id_banque'], ['icon' => 'fa-chart-line', 'title' => 'Voir état des versements']) .
               renderButton('', 'icon', '', ['icon' => 'fa-edit', 'title' => 'Modifier', 'data-modal-toggle' => 'editModal', 'data-edit-banque' => $b['id_banque']]) .
               renderButton('', 'icon-danger', '?action=banque_supprimer&id=' . $b['id_banque'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer cette banque ?', 'data-confirm-type' => 'danger']);
    };

    echo renderResponsiveTable(
        ['Nom', 'Sigle', 'Responsable', 'Téléphone'],
        $rows,
        [
            'mobileTitle' => 0,
            'mobileSubtitle' => 1,
            'mobileHidden' => [2, 3],
            'actions' => $actionsRenderer,
            'emptyMessage' => 'Aucune banque trouvée.'
        ]
    );
    ?>
<?php endif; ?>

<?php
$createBody = '
<form method="POST" action="?action=banques" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('nom_banque', 'Nom de la banque *', 'text', '', null, ['required' => 'required']) . '
        ' . renderInput('sigle', 'Sigle', 'text', '') . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('responsable', 'Responsable', 'text', '') . '
        ' . renderInput('tel', 'Téléphone', 'text', '') . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('email', 'Email', 'email', '') . '
        ' . renderInput('adresse', 'Adresse', 'text', '') . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-plus mr-1.5"></i>Créer</button>
    </div>
</form>';
echo renderModal('createModal', 'Nouvelle banque', $createBody);

$editBody = '
<form method="POST" action="?action=banques" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_banque" id="edit_id_banque">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('nom_banque', 'Nom de la banque *', 'text', '', null, ['id' => 'edit_nom_banque', 'required' => 'required']) . '
        ' . renderInput('sigle', 'Sigle', 'text', '', null, ['id' => 'edit_sigle']) . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('responsable', 'Responsable', 'text', '', null, ['id' => 'edit_responsable']) . '
        ' . renderInput('tel', 'Téléphone', 'text', '', null, ['id' => 'edit_tel']) . '
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ' . renderInput('email', 'Email', 'email', '', null, ['id' => 'edit_email']) . '
        ' . renderInput('adresse', 'Adresse', 'text', '', null, ['id' => 'edit_adresse']) . '
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-save mr-1.5"></i>Mettre à jour</button>
    </div>
</form>';
echo renderModal('editModal', 'Modifier la banque', $editBody);
?>

<script>
const BANQUES = <?= json_encode($banques) ?>;
document.querySelectorAll('[data-edit-banque]').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-edit-banque');
        const b = BANQUES.find(b => b.id_banque == id);
        if (!b) return;
        document.getElementById('edit_id_banque').value = b.id_banque;
        document.getElementById('edit_nom_banque').value = b.nom_banque || '';
        document.getElementById('edit_sigle').value = b.sigle || '';
        document.getElementById('edit_responsable').value = b.responsable || '';
        document.getElementById('edit_tel').value = b.tel || '';
        document.getElementById('edit_email').value = b.email || '';
        document.getElementById('edit_adresse').value = b.adresse || '';
    });
});
</script>
