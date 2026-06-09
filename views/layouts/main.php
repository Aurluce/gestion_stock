<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Gestion Stock' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- FontAwesome 6 (CDN gratuit) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-blue-800 text-white w-64 flex-shrink-0 overflow-y-auto sidebar-transition -translate-x-full md:translate-x-0">
            <div class="p-4 border-b border-blue-700">
                <h1 class="text-xl font-bold">Gestion Stock</h1>
            </div>
            <nav class="mt-4">
                <div class="px-4 py-2 text-xs uppercase tracking-wider text-blue-300">Navigation</div>
                <a href="index.php?action=dashboard" class="flex items-center px-4 py-2 hover:bg-blue-700 <?= ($_GET['action'] ?? 'dashboard') == 'dashboard' ? 'bg-blue-700' : '' ?>">
                    <i class="fas fa-tachometer-alt w-5 mr-2"></i> Dashboard
                </a>

                <!-- Structure -->
                <?php if (checkRightIfLogged('lister_produits')): ?>
                <div class="px-4 py-2 mt-2 text-xs uppercase tracking-wider text-blue-300">Structure</div>
                <a href="index.php?action=produits" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-tag w-5 mr-2"></i> Produits
                </a>
                <a href="index.php?action=familles" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-folder w-5 mr-2"></i> Familles
                </a>
                <a href="index.php?action=fournisseurs" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-truck w-5 mr-2"></i> Fournisseurs
                </a>
                <a href="index.php?action=clients" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-users w-5 mr-2"></i> Clients
                </a>
                <?php endif; ?>

                <!-- Approvisionnements -->
                <?php if (checkRightIfLogged('creer_bcf')): ?>
                <div class="px-4 py-2 mt-2 text-xs uppercase tracking-wider text-blue-300">Approvisionnements</div>
                <a href="index.php?action=commandes_fourn" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-file-invoice w-5 mr-2"></i> Commandes fourn.
                </a>
                <a href="index.php?action=factures_fourn" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-receipt w-5 mr-2"></i> Factures fourn.
                </a>
                <a href="index.php?action=etats_achats" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-chart-line w-5 mr-2"></i> États achats
                </a>
                <?php endif; ?>

                <!-- Ventes -->
                <?php if (checkRightIfLogged('creer_commande_client')): ?>
                <div class="px-4 py-2 mt-2 text-xs uppercase tracking-wider text-blue-300">Ventes</div>
                <a href="index.php?action=commandes_client" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-shopping-cart w-5 mr-2"></i> Commandes clients
                </a>
                <a href="index.php?action=factures_client" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-file-invoice-dollar w-5 mr-2"></i> Factures clients
                </a>
                <a href="index.php?action=etats_ventes" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-chart-simple w-5 mr-2"></i> États ventes
                </a>
                <?php endif; ?>

                <!-- Utilisateurs -->
                <?php if (checkRightIfLogged('creer_groupe')): ?>
                <div class="px-4 py-2 mt-2 text-xs uppercase tracking-wider text-blue-300">Utilisateurs</div>
                <a href="index.php?action=groupes" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-layer-group w-5 mr-2"></i> Groupes
                </a>
                <a href="index.php?action=utilisateurs" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-user w-5 mr-2"></i> Utilisateurs
                </a>
                <a href="index.php?action=journal_audit" class="flex items-center px-4 py-2 hover:bg-blue-700">
                    <i class="fas fa-history w-5 mr-2"></i> Journal audit
                </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-md p-4 flex justify-between items-center">
                <button id="sidebarToggle" class="md:hidden text-gray-600 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        <i class="fas fa-user-circle mr-1"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
                    </span>
                    <a href="index.php?action=profil" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-user-edit mr-1"></i> Profil
                    </a>
                    <a href="index.php?action=logout" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                        <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                    </a>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }
    </script>
</body>
</html>