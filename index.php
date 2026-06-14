<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config/database.php';
require_once 'config/session.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$action = $_GET['action'] ?? 'dashboard';
$publicActions = ['login', 'logout'];

if (!in_array($action, $publicActions) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

switch ($action) {
    case 'login':
        require_once 'controllers/AuthController.php';
        (new AuthController($pdo))->login();
        break;
    case 'logout':
        require_once 'controllers/AuthController.php';
        (new AuthController($pdo))->logout();
        break;
    case 'api_search':
        require_once 'api/search.php';
        break;
    case 'groupes':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->groupes();
        break;
    case 'droits':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->droits();
        break;
    case 'groupes_droits':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->groupesDroits();
        break;
    case 'utilisateurs':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->utilisateurs();
        break;
    case 'profil':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->profil();
        break;
    case 'commande_client':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->commandeClient();
        break;
    case 'bon_livraison':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->bonLivraison();
        break;
    case 'facture_client':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->factureClient();
        break;
    case 'reglement_client':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->reglementClient();
        break;
    case 'sortie_stock':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->sortieStock();
        break;
    case 'vente_comptant':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->venteComptant();
        break;
    case 'etats_ventes':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->etatsVentes();
        break;
    case 'dashboard_ventes':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->dashboardVentes();
        break;

    case 'journal_audit':
        require_once 'controllers/UtilisateurController.php';
        (new UtilisateurController($pdo))->journalAudit();
        break;

    // ==========================================
    // MODULE 4 - APPROVISIONNEMENTS
    // ==========================================

    case 'commande_fourn':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->commandeFourn();
        break;

    case 'reception':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->reception();
        break;

    case 'don':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->don();
        break;

    case 'bon_entree':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->bonEntree();
        break;

    case 'facture_fourn':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->factureFourn();
        break;

    case 'paiement_fourn':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->paiementFourn();
        break;

    case 'etats_achats':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->etatsAchats();
        break;

    // ==========================================
    // FIN MODULE 4
    // ==========================================

       // ==========================================
    // MODULE 3 - STRUCTURE
    // ==========================================
    
    // Familles
    case 'familles':
        require_once 'controllers/FamilleController.php';
        $controller = new FamilleController($pdo);
        $controller->index();
        break;
    
    // Produits
    case 'produits':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->index();
        break;
    case 'produit_enregistrer':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->store();
        break;
    case 'produit_mettre_a_jour':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->update();
        break;
    case 'produit_supprimer':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->delete();
        break;
    case 'ajax_produits_peres':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->getProduitsPeresAjax();
        break;
    
    // Fournisseurs
    case 'fournisseurs':
        require_once 'controllers/FournisseurController.php';
        $controller = new FournisseurController($pdo);
        $controller->index();
        break;
    
    // Clients
    case 'clients':
        require_once 'controllers/ClientController.php';
        $controller = new ClientController($pdo);
        $controller->index();
        break;
    case 'client_credit':
        require_once 'controllers/ClientController.php';
        $controller = new ClientController($pdo);
        $controller->voirCredit();
        break;
    
    // Banques
    case 'banques':
        require_once 'controllers/BanqueController.php';
        $controller = new BanqueController($pdo);
        $controller->index();
        break;
    case 'banque_versements':
        require_once 'controllers/BanqueController.php';
        $controller = new BanqueController($pdo);
        $controller->versements();
        break;
    case 'banque_mouvement_enregistrer':
        require_once 'controllers/BanqueController.php';
        $controller = new BanqueController($pdo);
        $controller->storeMouvement();
        break;
    case 'banque_mouvement_supprimer':
        require_once 'controllers/BanqueController.php';
        $controller = new BanqueController($pdo);
        $controller->deleteMouvement();
        break;
    
    // Restauration
    case 'restauration':
        require_once 'controllers/RestaurationController.php';
        $controller = new RestaurationController($pdo);
        $controller->index();
        break;
    case 'restauration_view':
        require_once 'controllers/RestaurationController.php';
        $controller = new RestaurationController($pdo);
        $controller->view();
        break;
    case 'restauration_restore':
        require_once 'controllers/RestaurationController.php';
        $controller = new RestaurationController($pdo);
        $controller->restore();
        break;
    case 'restauration_delete':
        require_once 'controllers/RestaurationController.php';
        $controller = new RestaurationController($pdo);
        $controller->delete();
        break;
    case 'restauration_clear':
        require_once 'controllers/RestaurationController.php';
        $controller = new RestaurationController($pdo);
        $controller->clear();
        break;

    case 'familles&print':
case 'familles_print':
    // À ajouter
    // ==========================================
    // FIN MODULE 3
    // ==========================================

    case 'dashboard':
    default:
        $title = "Tableau de bord";
        $breadcrumb = renderBreadcrumb([
            ['label' => 'Accueil', 'href' => '?action=dashboard'],
            ['label' => 'Tableau de bord']
        ]);
        ob_start();
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="card-body flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 text-h4">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-caption text-neutral-50">Utilisateurs</p>
                        <p class="text-h4 font-bold text-neutral-14"><?= checkRightIfLogged('creer_utilisateur') ? ($pdo->query("SELECT COUNT(*) FROM utilisateur.utilisateur")->fetchColumn()) : '-' ?></p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-success-50 flex items-center justify-center text-success-500 text-h4">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <p class="text-caption text-neutral-50">Groupes</p>
                        <p class="text-h4 font-bold text-neutral-14"><?= checkRightIfLogged('creer_groupe') ? ($pdo->query("SELECT COUNT(*) FROM utilisateur.groupe")->fetchColumn()) : '-' ?></p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-info-50 flex items-center justify-center text-info-500 text-h4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <p class="text-caption text-neutral-50">Droits</p>
                        <p class="text-h4 font-bold text-neutral-14"><?= checkRightIfLogged('affecter_droits') ? ($pdo->query("SELECT COUNT(*) FROM utilisateur.droit")->fetchColumn()) : '-' ?></p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-warning-50 flex items-center justify-center text-warning-500 text-h4">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <p class="text-caption text-neutral-50">Audits</p>
                        <p class="text-h4 font-bold text-neutral-14"><?= checkRightIfLogged('voir_journal_audit') ? ($pdo->query("SELECT COUNT(*) FROM utilisateur.journal_audit")->fetchColumn()) : '-' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-body-lg font-semibold text-neutral-14">
                    <i class="fas fa-tachometer-alt text-brand-600 mr-2"></i>Bienvenue
                </h2>
            </div>
            <div class="card-body">
                <p class="text-body text-neutral-30">
                    Bonjour <strong><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></strong>, vous êtes connecté à l'application <strong>Gestion de Stock</strong>.
                </p>
                <p class="text-body text-neutral-50 mt-2">
                    Utilisez le menu latéral pour naviguer entre les modules.
                </p>

                <hr class="border-neutral-90 my-4">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="?action=utilisateurs" class="btn-secondary justify-between">
                        <span><i class="fas fa-user mr-2"></i>Gérer les utilisateurs</span>
                        <i class="fas fa-chevron-right text-caption"></i>
                    </a>
                    <a href="?action=groupes" class="btn-secondary justify-between">
                        <span><i class="fas fa-layer-group mr-2"></i>Gérer les groupes</span>
                        <i class="fas fa-chevron-right text-caption"></i>
                    </a>
                    <a href="?action=profil" class="btn-secondary justify-between">
                        <span><i class="fas fa-user-cog mr-2"></i>Mon profil</span>
                        <i class="fas fa-chevron-right text-caption"></i>
                    </a>
                    <a href="?action=journal_audit" class="btn-secondary justify-between">
                        <span><i class="fas fa-history mr-2"></i>Journal d'audit</span>
                        <i class="fas fa-chevron-right text-caption"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        require 'views/layouts/main.php';
        break;
}
?>