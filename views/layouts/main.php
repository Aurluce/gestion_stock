<?php
require_once __DIR__ . '/../components/autoload.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#0078D4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title><?= htmlspecialchars($title ?? 'Gestion Stock') ?></title>
    <link rel="stylesheet" href="public/css/main.min.css?v=20260630">
    <link rel="stylesheet" href="public/vendor/fontawesome/css/all.min.css?v=20260630">
    <script src="public/vendor/jquery.min.js"></script>
</head>
<body class="appshell">

    <!-- SIDEBAR -->
    <aside class="sidebar" data-sidebar>
        <div class="sidebar-header">
            <span class="sidebar-logo">
                <i class="fas fa-boxes-stacked text-brand-600 text-h4"></i>
                <span class="sidebar-brand">Gestion Stock</span>
            </span>
            <button class="sidebar-close" data-sidebar-close>
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <?php
            $currentAction = $_GET['action'] ?? 'dashboard';
            $currentController = $_GET['controller'] ?? ''; 

            echo renderSidebarSection('Navigation', [
                ['label' => 'Tableau de bord', 'icon' => 'fa-tachometer-alt', 'href' => '?action=dashboard', 'active' => $currentAction === 'dashboard'],
            ]);

           // MODULE 3 - GESTION DE LA STRUCTURE
            if (function_exists('checkRightIfLogged') && checkRightIfLogged('lister_familles')):
            echo renderCollapsibleSection('Structure', [
                ['label' => 'Familles',    'icon' => 'fa-folder', 'href' => '?action=familles', 'active' => $currentAction === 'familles'],
                ['label' => 'Produits',    'icon' => 'fa-tag',    'href' => '?action=produits', 'active' => $currentAction === 'produits'],
                ['label' => 'Fournisseurs','icon' => 'fa-truck',  'href' => '?action=fournisseurs', 'active' => $currentAction === 'fournisseurs'],
                ['label' => 'Clients',     'icon' => 'fa-users',  'href' => '?action=clients', 'active' => $currentAction === 'clients'],
                ['label' => 'Catégories clients', 'icon' => 'fa-tags', 'href' => '?action=categorie_clients', 'active' => $currentAction === 'categorie_clients'],
                ['label' => 'Banques', 'icon' => 'fa-university', 'href' => '?action=banques', 'active' => $currentAction === 'banques'],
                ['label' => 'État banque', 'icon' => 'fa-chart-line', 'href' => '?action=banque_versements', 'active' => $currentAction === 'banque_versements'],
                ['label' => 'Corbeille', 'icon' => 'fa-trash-restore', 'href' => '?action=restauration', 'active' => $currentAction === 'restauration'],
            ], 'structure');
            endif;
            // ==========================================
            // FIN MODULE 3
            // ==========================================

            if (function_exists('checkRightIfLogged') && checkRightIfLogged('lister_bcf')):
            echo renderCollapsibleSection('Approvisionnements', [
                ['label' => 'Commandes fourn.', 'icon' => 'fa-file-invoice',     'href' => '?action=commande_fourn', 'active' => str_starts_with($currentAction, 'commande_fourn')],
                ['label' => 'Réceptions',        'icon' => 'fa-truck-loading',   'href' => '?action=reception',      'active' => $currentAction === 'reception'],
                ['label' => 'Bons d\'entrée',    'icon' => 'fa-arrow-right',     'href' => '?action=bon_entree',     'active' => $currentAction === 'bon_entree'],
                ['label' => 'Dons',              'icon' => 'fa-gift',            'href' => '?action=don',            'active' => $currentAction === 'don'],
                ['label' => 'Factures fourn.',   'icon' => 'fa-receipt',         'href' => '?action=facture_fourn',  'active' => str_starts_with($currentAction, 'facture_fourn')],
                ['label' => 'Paiements fourn.',  'icon' => 'fa-money-bill-wave', 'href' => '?action=paiement_fourn',  'active' => $currentAction === 'paiement_fourn'],
                ['label' => 'États achats',      'icon' => 'fa-chart-line',      'href' => '?action=etats_achats',    'active' => $currentAction === 'etats_achats'],
            ], 'approvisionnements');
            endif;

            if (function_exists('checkRightIfLogged') && checkRightIfLogged('creer_commande_client')):
            echo renderCollapsibleSection('Ventes', [
                ['label' => 'Tableau de bord des ventes','icon' => 'fa-tachometer-alt',      'href' => '?action=dashboard_ventes', 'active' => $currentAction === 'dashboard_ventes'],
                ['label' => 'Commandes clients','icon' => 'fa-shopping-cart',      'href' => '?action=commande_client', 'active' => str_starts_with($currentAction, 'commande_client')],
                ['label' => 'Bons de livraison','icon' => 'fa-truck',      'href' => '?action=bon_livraison', 'active' => str_starts_with($currentAction, 'bon_livraison')],
                ['label' => 'Factures clients',  'icon' => 'fa-file-invoice-dollar','href' => '?action=facture_client',  'active' => str_starts_with($currentAction, 'facture_client')],
                ['label' => 'Règlements clients','icon' => 'fa-money-bill-wave',      'href' => '?action=reglement_client', 'active' => str_starts_with($currentAction, 'reglement_client')],
                ['label' => 'Sorties de stock','icon' => 'fa-dolly-flatbed',      'href' => '?action=sortie_stock', 'active' => str_starts_with($currentAction, 'sortie_stock')],
                ['label' => 'Vente au comptant','icon' => 'fa-cash-register',      'href' => '?action=vente_comptant', 'active' => str_starts_with($currentAction, 'vente_comptant')],
                ['label' => 'États ventes',      'icon' => 'fa-chart-bar',         'href' => '?action=etats_ventes',     'active' => $currentAction === 'etats_ventes'],
            ], 'ventes');
            endif;

            if (function_exists('checkRightIfLogged') && checkRightIfLogged('creer_groupe')):
            echo renderCollapsibleSection('Utilisateurs', [
                ['label' => 'Groupes',       'icon' => 'fa-layer-group', 'href' => '?action=groupes',       'active' => in_array($currentAction, ['groupes', 'groupes_droits'])],
                ['label' => 'Utilisateurs',  'icon' => 'fa-user',        'href' => '?action=utilisateurs',  'active' => $currentAction === 'utilisateurs'],
                ['label' => 'Journal audit', 'icon' => 'fa-history',     'href' => '?action=journal_audit', 'active' => $currentAction === 'journal_audit'],
            ], 'utilisateurs');
            endif;
            ?>
        </nav>
        <div class="sidebar-footer">
            <span class="text-caption text-neutral-60">v1.0.0</span>
        </div>
    </aside>

    <!-- BACKDROP MOBILE -->
    <div class="sidebar-backdrop" data-sidebar-backdrop></div>

    <!-- MAIN -->
    <div class="appshell-main">
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" data-sidebar-toggle>
                    <i class="fas fa-bars"></i>
                </button>
                <?= $breadcrumb ?? '' ?>
            </div>
            <div class="topbar-right">
                <div class="topbar-user-dropdown" data-dropdown-toggle="userDropdown">
                    <div class="topbar-user">
                        <div class="topbar-avatar">
                            <?= strtoupper(substr(htmlspecialchars($_SESSION['user_name'] ?? 'U'), 0, 2)) ?>
                        </div>
                        <div class="topbar-user-info">
                            <span class="topbar-user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                            <span class="topbar-user-role">Connecté</span>
                        </div>
                        <i class="fas fa-chevron-down text-caption text-neutral-60 ml-2"></i>
                    </div>
                    <div id="userDropdown" class="dropdown hidden">
                        <a href="?action=profil" class="dropdown-item">
                            <i class="fas fa-user-cog text-neutral-50"></i>
                            <span>Profil</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item w-full" data-modal-toggle="logoutModal">
                            <i class="fas fa-sign-out-alt text-danger-500"></i>
                            <span class="text-danger-500">Déconnexion</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="appshell-content">
            <?= renderToastContainer() ?>

            <?= $content ?? '' ?>
        </main>
    </div>

    <!-- Modal de confirmation -->
    <div id="confirmModal" class="modal-overlay hidden animate-fade-in modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="confirmTitle">Confirmation</h3>
                <button type="button" class="btn-icon" data-modal-close><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body text-center py-6">
                <div class="text-h2 text-neutral-70 mb-4">
                    <i id="confirmModalIcon" class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="text-body text-neutral-30" id="confirmMessage">Êtes-vous sûr ?</p>
            </div>
            <div class="modal-footer justify-center gap-3">
                <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
                <a href="#" id="confirmLink" class="btn-danger">
                    <i id="confirmLinkIcon" class="fas fa-trash"></i>
                    <span id="confirmLinkLabel">Confirmer</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation déconnexion -->
    <div id="logoutModal" class="modal-overlay hidden animate-fade-in modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Déconnexion</h3>
                <button type="button" class="btn-icon" data-modal-close><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body text-center py-6">
                <div class="text-h2 text-neutral-70 mb-4">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <p class="text-body text-neutral-30">Êtes-vous sûr de vouloir vous déconnecter ?</p>
            </div>
            <div class="modal-footer justify-center gap-3">
                <button type="button" class="btn-secondary" data-modal-close>Annuler</button>
                <a href="?action=logout" class="btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Se déconnecter</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de détail AJAX -->
    <div id="detailModal" class="modal-overlay hidden animate-fade-in modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="detailModalTitle">Détails</h3>
                <button type="button" class="btn-icon" data-modal-close><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="detailBody">
                <div class="flex items-center justify-center py-8">
                    <i class="fas fa-spinner fa-spin text-h3 text-neutral-50"></i>
                </div>
            </div>
        </div>
    </div>

    <script src="public/js/main.js"></script>
</body>
</html>