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

// Set PostgreSQL session variable for backup triggers to capture who deletes
if (isset($_SESSION['user_id'])) {
    $pdo->exec("SET SESSION \"app.user_id\" = '" . (int)$_SESSION['user_id'] . "'");
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
    // MODULE 3 - GESTION DE LA STRUCTURE
    // ==========================================
    
    case 'familles':
        require_once 'controllers/FamilleController.php';
        $controller = new FamilleController($pdo);
        $controller->index();
        break;

    case 'famille_supprimer':
        require_once 'controllers/FamilleController.php';
        $controller = new FamilleController($pdo);
        $controller->delete();
        break;

    case 'produits':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->index();
        break;

    case 'produit_supprimer':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->delete();
        break;

    case 'produit_desactiver':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->disable();
        break;
        
    case 'produit_activer':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->enable();
        break;

    case 'fournisseurs':
        require_once 'controllers/FournisseurController.php';
        $controller = new FournisseurController($pdo);
        $controller->index();
        break;

    case 'fournisseur_supprimer':
        require_once 'controllers/FournisseurController.php';
        $controller = new FournisseurController($pdo);
        $controller->delete();
        break;

    case 'fournisseur_desactiver':
        require_once 'controllers/FournisseurController.php';
        $controller = new FournisseurController($pdo);
        $controller->disable();
        break;

    case 'fournisseur_activer':
        require_once 'controllers/FournisseurController.php';
        $controller = new FournisseurController($pdo);
        $controller->enable();
        break;

    case 'clients':
        require_once 'controllers/ClientController.php';
        $controller = new ClientController($pdo);
        $controller->index();
        break;

    case 'client_supprimer':
        require_once 'controllers/ClientController.php';
        $controller = new ClientController($pdo);
        $controller->delete();
        break;

    case 'categorie_clients':
        require_once 'controllers/CategorieClientController.php';
        $controller = new CategorieClientController($pdo);
        $controller->index();
        break;

    case 'categorie_client_supprimer':
        require_once 'controllers/CategorieClientController.php';
        $controller = new CategorieClientController($pdo);
        $controller->delete();
        break;
    
    case 'banques':
        require_once 'controllers/BanqueController.php';
        $controller = new BanqueController($pdo);
        $controller->index();
        break;
    case 'banque_supprimer':
        require_once 'controllers/BanqueController.php';
        $controller = new BanqueController($pdo);
        $controller->delete();
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

    case 'ajax_produits_peres':
        require_once 'controllers/ProduitController.php';
        $controller = new ProduitController($pdo);
        $controller->getProduitsPeresAjax();
        break;

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
    
    // ==========================================
    // FIN MODULE 3
    // ==========================================

    case 'error403':
        // handled via renderErrorPage() directly
    case 'error404':
        // handled via renderErrorPage() directly
    case 'error500':
        // handled via renderErrorPage() directly

    // ==========================================
    // DETAIL ROUTES — AJAX modals
    // ==========================================
    case 'famille_detail':
        require_once 'controllers/FamilleController.php';
        (new FamilleController($pdo))->detail();
        break;
    case 'produit_detail':
        require_once 'controllers/ProduitController.php';
        (new ProduitController($pdo))->detail();
        break;
    case 'fournisseur_detail':
        require_once 'controllers/FournisseurController.php';
        (new FournisseurController($pdo))->detail();
        break;
    case 'client_detail':
        require_once 'controllers/ClientController.php';
        (new ClientController($pdo))->detail();
        break;
    case 'commande_client_detail':
        require_once 'controllers/VenteController.php';
        (new VenteController($pdo))->detailCommande();
        break;
    case 'don_detail':
        require_once 'controllers/ApprovisionnementController.php';
        (new ApprovisionnementController($pdo))->detailDon();
        break;

    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        (new DashboardController($pdo))->index();
        break;

    default:
        renderErrorPage(404, "Action '" . htmlspecialchars($action) . "' introuvable.");
}
?>