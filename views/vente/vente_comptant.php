<?php
$title = "Vente au comptant";
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Ventes'],
    ['label' => 'Vente au comptant']
]);
ob_start();
?>
<?= renderPageHeader(
    'Vente au comptant',
    'Caisse - Vente directe au client',
    null
) ?>

<?php
$produitsJson = json_encode(array_map(function($p) {
    return [
        'id' => $p['id_produit'],
        'nom' => $p['nom_produit'],
        'prix' => $p['prix_vente'],
        'unite' => $p['unite'],
        'stock' => $p['stock_actuel']
    ];
}, $produits));
?>

<div class="card">
    <div class="card-body">
        <form method="post" action="?action=vente_comptant" id="venteForm" class="space-y-4">
            <input type="hidden" name="action" value="add">

            <?= renderSelect('id_client', 'Client', array_combine(
                array_map(fn($c) => $c['id_client'], $clients),
                array_map(fn($c) => $c['nom'] . ' ' . ($c['prenom'] ?? ''), $clients)
            ), null, null, ['required' => 'required'], 'Sélectionner un client') ?>

            <div>
                <label class="form-label">Produits</label>
                <div id="lignesContainer-caisse" class="space-y-2"></div>
                <button type="button" class="btn-secondary mt-2" onclick="ajouterLigne('caisse')"><i class="fas fa-plus mr-2"></i>Ajouter un produit</button>
            </div>

            <div class="text-right">
                <p class="text-body text-neutral-50">Total HT : <span id="totalht-caisse">0</span> FCFA</p>
                <p class="text-body text-neutral-50">TVA (19.25%) : <span id="totaltva-caisse">0</span> FCFA</p>
                <p class="text-h4 font-bold">Total TTC : <span id="totalttc-caisse">0</span> FCFA</p>
            </div>

            <?= renderSelect('mode_paiement', 'Mode de paiement', [
                'espece' => 'Espèce',
                'cheque' => 'Chèque',
                'virement' => 'Virement',
                'mobile_money' => 'Mobile Money',
                'carte' => 'Carte'
            ], 'espece', null, ['required' => 'required']) ?>

            <?= renderTextarea('observations', 'Observations', '') ?>

            <div class="flex justify-end">
                <?= renderButton('Encaisser la vente', 'primary', null, ['icon' => 'fa-cash-register', 'type' => 'submit']) ?>
            </div>
        </form>
    </div>
</div>

<script>
const PRODUITS = <?= $produitsJson ?>;
const TVA = 19.25;

function ligneTemplate(index) {
    let options = '<option value="">-- Produit --</option>';
    PRODUITS.forEach(p => {
        options += `<option value="${p.id}" data-prix="${p.prix}" data-unite="${p.unite}" data-stock="${p.stock}">${p.nom} (Stock: ${parseFloat(p.stock)} ${p.unite})</option>`;
    });
    return `
    <div class="flex flex-wrap gap-2 items-end border border-neutral-90 rounded-lg p-3 ligne-produit">
        <div class="flex-1 min-w-[150px]">
            <select name="id_produit[]" class="form-select" onchange="majPrix(this)" required>${options}</select>
        </div>
        <div class="w-24">
            <input type="number" name="quantite[]" class="form-input" placeholder="Qté" step="0.001" min="0.001" onchange="calculTotal('caisse')" required>
        </div>
        <div class="w-28">
            <input type="number" name="prix_unitaire[]" class="form-input" placeholder="Prix unit." step="0.01" min="0" onchange="calculTotal('caisse')" required>
        </div>
        <div class="w-20">
            <input type="number" name="taux_remise[]" class="form-input" placeholder="Remise %" step="0.01" min="0" max="100" value="0" onchange="calculTotal('caisse')">
        </div>
        <button type="button" class="btn-icon" onclick="this.closest('.ligne-produit').remove(); calculTotal('caisse')"><i class="fas fa-trash"></i></button>
    </div>`;
}

function ajouterLigne(prefix) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    const div = document.createElement('div');
    div.innerHTML = ligneTemplate(container.children.length);
    container.appendChild(div.firstElementChild);
}

function majPrix(select) {
    const option = select.options[select.selectedIndex];
    const prix = option.getAttribute('data-prix');
    const stock = option.getAttribute('data-stock');
    const row = select.closest('.ligne-produit');
    const prixInput = row.querySelector('input[name="prix_unitaire[]"]');
    const qteInput = row.querySelector('input[name="quantite[]"]');
    if (prix) prixInput.value = prix;
    if (stock) qteInput.max = stock;
    calculTotal('caisse');
}

function calculTotal(prefix) {
    const container = document.getElementById(`lignesContainer-${prefix}`);
    let totalHt = 0;
    container.querySelectorAll('.ligne-produit').forEach(row => {
        const qte = parseFloat(row.querySelector('input[name="quantite[]"]').value) || 0;
        const prix = parseFloat(row.querySelector('input[name="prix_unitaire[]"]').value) || 0;
        const remise = parseFloat(row.querySelector('input[name="taux_remise[]"]').value) || 0;
        totalHt += qte * prix * (1 - remise / 100);
    });
    const totalTva = totalHt * (TVA / 100);
    const totalTtc = totalHt + totalTva;
    document.getElementById('totalht-caisse').textContent = totalHt.toLocaleString('fr-FR', {maximumFractionDigits: 0});
    document.getElementById('totaltva-caisse').textContent = totalTva.toLocaleString('fr-FR', {maximumFractionDigits: 0});
    document.getElementById('totalttc-caisse').textContent = totalTtc.toLocaleString('fr-FR', {maximumFractionDigits: 0});
}

document.addEventListener('DOMContentLoaded', function() {
    ajouterLigne('caisse');
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
