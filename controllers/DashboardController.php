<?php
class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        checkRight('voir_dashboard');

        $monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $currentMonth = (int)date('m');
        $currentYear = (int)date('Y');

        $data = [
            'greeting' => [
                'user_name' => $_SESSION['user_name'] ?? 'Utilisateur',
                'user_role' => $this->getUserRole(),
            ],
            'kpi' => $this->getKpis($currentMonth, $currentYear),
            'charts' => [
                'ventes' => $this->getVentesChart($currentYear, $monthNames),
                'achats' => $this->getAchatsChart($currentYear, $monthNames),
            ],
            'stock_bas' => $this->getStockBas(),
            'recent_activity' => $this->getRecentActivity(),
            'mini' => $this->getMiniKpis(),
            'chart_months' => $monthNames,
        ];

        $title = 'Tableau de bord';
        $breadcrumb = renderBreadcrumb([
            ['label' => 'Accueil', 'href' => '?action=dashboard'],
            ['label' => 'Tableau de bord'],
        ]);

        ob_start();
        require __DIR__ . '/../views/dashboard/index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    private function getUserRole(): string {
        $stmt = $this->pdo->prepare("
            SELECT g.nom_groupe FROM utilisateur.utilisateur u
            JOIN utilisateur.groupe g ON u.id_groupe = g.id_groupe
            WHERE u.id_utilisateur = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchColumn() ?: '—';
    }

    private function getKpis(int $currentMonth, int $currentYear): array {
        $kpis = [];

        if (checkRightIfLogged('lister_produits')) {
            $actifs = (int)$this->pdo->query("SELECT COUNT(*) FROM structure.produit WHERE est_actif = true")->fetchColumn();
            $alerte = (int)$this->pdo->query("SELECT COUNT(*) FROM structure.produit WHERE est_actif = true AND stock_actuel <= seuil_alerte AND seuil_alerte > 0")->fetchColumn();
            $kpis['produits'] = [
                'value' => $actifs,
                'sub' => $alerte . ' en alerte',
                'sub_type' => $alerte > 0 ? 'danger' : 'success',
            ];
        }

        if (checkRightIfLogged('lister_bcf')) {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM approvisionnement.bon_commande_fourn WHERE statut IN ('brouillon','envoye')");
            $kpis['bcf_en_cours'] = ['value' => $stmt->fetchColumn()];
        }

        if (checkRightIfLogged('lister_commandes_client')) {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM vente.commande_client WHERE statut IN ('en_cours','en_attente')");
            $kpis['commandes_en_cours'] = ['value' => $stmt->fetchColumn()];
        }

        if (checkRightIfLogged('etat_ventes_mois')) {
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(SUM(montant_ttc), 0) FROM vente.facture_client
                WHERE EXTRACT(MONTH FROM date_facture) = ? AND EXTRACT(YEAR FROM date_facture) = ? AND statut != 'annulee'
            ");
            $stmt->execute([$currentMonth, $currentYear]);
            $kpis['ca_mois'] = ['value' => (float)$stmt->fetchColumn()];

            $stmtReg = $this->pdo->prepare("
                SELECT COALESCE(SUM(montant), 0) FROM vente.reglement_client
                WHERE EXTRACT(MONTH FROM date_reglement) = ? AND EXTRACT(YEAR FROM date_reglement) = ?
            ");
            $stmtReg->execute([$currentMonth, $currentYear]);
            $kpis['encaissements_mois'] = ['value' => (float)$stmtReg->fetchColumn()];
        }

        if (checkRightIfLogged('etat_achats_mois')) {
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(SUM(montant_ttc), 0) FROM approvisionnement.facture_fournisseur
                WHERE EXTRACT(MONTH FROM date_facture) = ? AND EXTRACT(YEAR FROM date_facture) = ? AND statut != 'annulee'
            ");
            $stmt->execute([$currentMonth, $currentYear]);
            $kpis['achats_mois'] = ['value' => (float)$stmt->fetchColumn()];

            $stmtPay = $this->pdo->prepare("
                SELECT COALESCE(SUM(montant), 0) FROM approvisionnement.paiement_fournisseur
                WHERE EXTRACT(MONTH FROM date_paiement) = ? AND EXTRACT(YEAR FROM date_paiement) = ?
            ");
            $stmtPay->execute([$currentMonth, $currentYear]);
            $kpis['paiements_mois'] = ['value' => (float)$stmtPay->fetchColumn()];
        }

        return $kpis;
    }

    private function getVentesChart(int $currentYear, array $monthNames): ?array {
        if (!checkRightIfLogged('etat_ventes_jour')) return null;

        $stmt = $this->pdo->prepare("
            SELECT EXTRACT(MONTH FROM date_facture) AS mois, COALESCE(SUM(montant_ttc), 0) AS total
            FROM vente.facture_client
            WHERE EXTRACT(YEAR FROM date_facture) = ? AND statut != 'annulee'
            GROUP BY mois ORDER BY mois
        ");
        $stmt->execute([$currentYear]);
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = (float)($rows[$m] ?? 0);
        }

        return ['labels' => $monthNames, 'data' => $data, 'label' => 'Ventes (TTC)'];
    }

    private function getAchatsChart(int $currentYear, array $monthNames): ?array {
        if (!checkRightIfLogged('etat_achats_jour')) return null;

        $stmt = $this->pdo->prepare("
            SELECT EXTRACT(MONTH FROM date_facture) AS mois, COALESCE(SUM(montant_ttc), 0) AS total
            FROM approvisionnement.facture_fournisseur
            WHERE EXTRACT(YEAR FROM date_facture) = ? AND statut != 'annulee'
            GROUP BY mois ORDER BY mois
        ");
        $stmt->execute([$currentYear]);
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = (float)($rows[$m] ?? 0);
        }

        return ['labels' => $monthNames, 'data' => $data, 'label' => 'Achats (TTC)'];
    }

    private function getStockBas(): ?array {
        if (!checkRightIfLogged('lister_produits')) return null;

        $stmt = $this->pdo->query("
            SELECT id_produit, nom_produit, stock_actuel, seuil_alerte
            FROM structure.produit
            WHERE est_actif = true AND stock_actuel <= seuil_alerte AND seuil_alerte > 0
            ORDER BY (stock_actuel::float / NULLIF(seuil_alerte, 0)) ASC
            LIMIT 10
        ");
        return $stmt->fetchAll();
    }

    private function getRecentActivity(): ?array {
        if (!checkRightIfLogged('voir_journal_audit')) return null;

        $stmt = $this->pdo->query("
            SELECT j.date_heure, j.action, j.table_cible, j.id_enregistrement, u.nom_complet
            FROM utilisateur.journal_audit j
            LEFT JOIN utilisateur.utilisateur u ON j.id_utilisateur = u.id_utilisateur
            ORDER BY j.date_heure DESC LIMIT 7
        ");
        return $stmt->fetchAll();
    }

    private function getMiniKpis(): array {
        $mini = [];

        if (checkRightIfLogged('lister_clients')) {
            $mini['clients'] = [
                'value' => $this->pdo->query("SELECT COUNT(*) FROM structure.client")->fetchColumn(),
            ];
        }

        if (checkRightIfLogged('lister_fournisseurs')) {
            $mini['fournisseurs'] = [
                'value' => $this->pdo->query("SELECT COUNT(*) FROM structure.fournisseur")->fetchColumn(),
            ];
        }

        if (checkRightIfLogged('lister_factures_client')) {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM vente.facture_client WHERE statut = 'impayee'");
            $mini['factures_impayees'] = [
                'value' => $stmt->fetchColumn(),
            ];
        }

        if (checkRightIfLogged('lister_banques')) {
            $mini['banques'] = [
                'value' => $this->pdo->query("SELECT COUNT(*) FROM structure.banque")->fetchColumn(),
            ];
        }

        return $mini;
    }
}
