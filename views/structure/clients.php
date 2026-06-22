<?php
$title = "Clients";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure'],
    ['label' => 'Clients']
]);
$pageActions = renderButton('Nouveau client', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal']);
echo renderPageHeader('Clients', 'Gérez vos clients', $pageActions);
?>


<?php if (empty($clients)): ?>
    <?= renderEmptyState('fa-users', 'Aucun client', 'Commencez par ajouter votre premier client.', renderButton('Créer un client', 'primary', null, ['icon' => 'fa-plus', 'data-modal-toggle' => 'createModal'])) ?>
<?php else: ?>
    <?php
    $rows = array_map(function($c) {
        $nomComplet = $c['nom'] . ($c['prenom'] ? ' ' . $c['prenom'] : '');
        $typeLabel = match($c['type_client'] ?? 'particulier') {
            'particulier' => 'Particulier',
            'entreprise' => 'Entreprise',
            'administration' => 'Administration',
            default => $c['type_client']
        };
        return [
            '<div class="flex items-center gap-2"><i class="fas fa-user text-brand-500"></i>' . htmlspecialchars($nomComplet) . '</div>',
            htmlspecialchars($c['tel'] ?? '-'),
            htmlspecialchars($c['email'] ?? '-'),
            htmlspecialchars($c['ville'] ?? '-'),
            renderBadge($typeLabel, 'info'),
        ];
    }, $clients);

    $actionsRenderer = function($row, $rowIndex) use ($clients) {
        $c = $clients[$rowIndex] ?? null;
        if (!$c) return '';
        return renderButton('', 'icon', '', ['icon' => 'fa-eye', 'title' => 'Voir', 'data-detail' => '?action=client_detail&id=' . $c['id_client'], 'data-detail-title' => 'Client : ' . $c['nom'] . ' ' . ($c['prenom'] ?? '')]) .
               renderButton('', 'icon', '', ['icon' => 'fa-edit', 'title' => 'Modifier', 'data-modal-toggle' => 'editModal', 'data-edit-client' => $c['id_client']]) .
               renderButton('', 'icon-danger', '?action=clients&delete=' . $c['id_client'], ['icon' => 'fa-trash', 'title' => 'Supprimer', 'data-confirm' => 'Supprimer ce client ?', 'data-confirm-type' => 'danger']);
    };

    echo renderResponsiveTable(
        ['Nom complet', 'Téléphone', 'Email', 'Ville', 'Type'],
        $rows,
        [
            'mobileTitle' => 0,
            'mobileBadge' => 4,
            'mobileHidden' => [1, 2, 3],
            'actions' => $actionsRenderer,
            'emptyMessage' => 'Aucun client trouvé.'
        ]
    );
    ?>
<?php endif; ?>

<?php
$catOptions = '<option value="">-- Sélectionnez une catégorie --</option>';
foreach ($categories as $id => $nomCat) {
    $catOptions .= '<option value="' . $id . '">' . htmlspecialchars($nomCat) . '</option>';
}

$createBody = '
<form method="POST" action="?action=clients" class="space-y-4">
    <input type="hidden" name="action" value="add">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Nom <span class="text-red-500">*</span></label>
            <input type="text" name="nom" class="input" required>
        </div>
        <div>
            <label class="label">Prénom</label>
            <input type="text" name="prenom" class="input">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Téléphone</label>
            <input type="text" name="tel" class="input">
        </div>
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" class="input">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Ville</label>
            <input type="text" name="ville" class="input">
        </div>
        <div>
            <label class="label">Catégorie <span class="text-red-500">*</span></label>
            <select name="id_categorie_client" class="select" required>' . $catOptions . '</select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Type</label>
            <select name="type_client" class="select">
                <option value="particulier">Particulier</option>
                <option value="entreprise">Entreprise</option>
                <option value="administration">Administration</option>
            </select>
        </div>
        <div class="flex items-center pt-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="est_actif" value="1" checked class="checkbox">
                <span class="text-sm">Client actif</span>
            </label>
        </div>
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-plus mr-1.5"></i>Créer</button>
    </div>
</form>';
echo renderModal('createModal', 'Nouveau client', $createBody);

$editBody = '
<form method="POST" action="?action=clients" class="space-y-4">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id_client" id="edit_id_client">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Nom <span class="text-red-500">*</span></label>
            <input type="text" name="nom" id="edit_nom" class="input" required>
        </div>
        <div>
            <label class="label">Prénom</label>
            <input type="text" name="prenom" id="edit_prenom" class="input">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Téléphone</label>
            <input type="text" name="tel" id="edit_tel" class="input">
        </div>
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" id="edit_email" class="input">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Ville</label>
            <input type="text" name="ville" id="edit_ville" class="input">
        </div>
        <div>
            <label class="label">Catégorie <span class="text-red-500">*</span></label>
            <select name="id_categorie_client" id="edit_id_categorie_client" class="select" required>' . $catOptions . '</select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Type</label>
            <select name="type_client" id="edit_type_client" class="select">
                <option value="particulier">Particulier</option>
                <option value="entreprise">Entreprise</option>
                <option value="administration">Administration</option>
            </select>
        </div>
        <div class="flex items-center pt-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="est_actif" value="1" id="edit_est_actif" class="checkbox">
                <span class="text-sm">Client actif</span>
            </label>
        </div>
    </div>
    <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
        <button type="submit" class="btn-primary"><i class="fas fa-save mr-1.5"></i>Mettre à jour</button>
    </div>
</form>';
echo renderModal('editModal', 'Modifier le client', $editBody);
?>

<script>
const CLIENTS = <?= json_encode($clients) ?>;
document.querySelectorAll('[data-edit-client]').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-edit-client');
        const c = CLIENTS.find(c => c.id_client == id);
        if (!c) return;
        document.getElementById('edit_id_client').value = c.id_client;
        document.getElementById('edit_nom').value = c.nom || '';
        document.getElementById('edit_prenom').value = c.prenom || '';
        document.getElementById('edit_tel').value = c.tel || '';
        document.getElementById('edit_email').value = c.email || '';
        document.getElementById('edit_ville').value = c.ville || '';
        document.getElementById('edit_id_categorie_client').value = c.id_categorie_client || '';
        document.getElementById('edit_type_client').value = c.type_client || 'particulier';
        document.getElementById('edit_est_actif').checked = !!c.est_actif;
    });
});
</script>
