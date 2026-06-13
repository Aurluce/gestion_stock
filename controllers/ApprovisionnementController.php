<?php
require_once __DIR__ . '/../models/BonCommandeModel.php';
require_once __DIR__ . '/../models/ReceptionModel.php';
require_once __DIR__ . '/../models/DonModel.php';
require_once __DIR__ . '/../models/BonEntreeModel.php';
require_once __DIR__ . '/../models/FactureFournisseurModel.php';
require_once __DIR__ . '/../models/PaiementFournisseurModel.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/fonctions.php';

class ApprovisionnementController {
    private $pdo, $commandeModel, $receptionModel, $donModel, $bonEntreeModel, $factureModel, $paiementModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->commandeModel = new BonCommandeModel($pdo);
        $this->receptionModel = new ReceptionModel($pdo);
        $this->donModel = new DonModel($pdo);
        $this->bonEntreeModel = new BonEntreeModel($pdo);
        $this->factureModel = new FactureFournisseurModel($pdo);
        $this->paiementModel = new PaiementFournisseurModel($pdo);
    }

    private function parseLignes($post) {
        $lignes = [];
        if (!isset($post['id_produit']) || !is_array($post['id_produit'])) {
            return $lignes;
        }
        foreach ($post['id_produit'] as $i => $idProduit) {
            if (empty($idProduit)) continue;
            $ligne = ['id_produit' => $idProduit];
            if (isset($post['qte_commandee'][$i])) $ligne['qte_commandee'] = str_replace(',', '.', $post['qte_commandee'][$i]);
            if (isset($post['prix_unitaire'][$i])) $ligne['prix_unitaire'] = str_replace(',', '.', $post['prix_unitaire'][$i]);
            if (isset($post['quantite'][$i])) $ligne['quantite'] = str_replace(',', '.', $post['quantite'][$i]);
            if (isset($post['qte_recue'][$i])) $ligne['qte_recue'] = str_replace(',', '.', $post['qte_recue'][$i]);
            if (isset($post['etat_produit'][$i])) $ligne['etat_produit'] = $post['etat_produit'][$i];
            if (isset($post['ligne_obs'][$i])) $ligne['observations'] = $post['ligne_obs'][$i];
            if (isset($post['taux_remise'][$i])) $ligne['taux_remise'] = str_replace(',', '.', $post['taux_remise'][$i]);
            $lignes[] = $ligne;
        }
        return $lignes;
    }

    public function commandeFourn() {
        checkRight('lister_bcf');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('creer_bcf');
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Ajoutez au moins un produit.', 'danger');
                } else {
                    try {
                        $idBcf = $this->commandeModel->create(
                            $_POST['id_fournisseur'],
                            $_SESSION['user_id'],
                            trim($_POST['observations'] ?? ''),
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'bon_commande_fourn', $idBcf, null, ['lignes' => count($lignes)]);
                        setFlash('Bon de commande créé.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur : ' . $e->getMessage(), 'danger');
                    }
                }
            } elseif ($postAction === 'edit') {
                checkRight('modifier_bcf');
                $idBcf = $_POST['id_bcf'];
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Ajoutez au moins un produit.', 'danger');
                } else {
                    try {
                        $this->commandeModel->update(
                            $idBcf,
                            $_POST['id_fournisseur'],
                            trim($_POST['observations'] ?? ''),
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'bon_commande_fourn', $idBcf, null, ['lignes' => count($lignes)]);
                        setFlash('Commande modifiée.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            header('Location: ?action=commande_fourn');
            exit;
        }

        if (isset($_GET['view'])) {
            checkRight('lister_bcf');
            $idBcf = $_GET['view'];
            $commande = $this->commandeModel->find($idBcf);
            if (!$commande) {
                setFlash('Commande introuvable.', 'danger');
                header('Location: ?action=commande_fourn');
                exit;
            }
            $lignes = $this->commandeModel->getLignes($idBcf);
            require __DIR__ . '/../views/approvisionnement/detail_commande.php';
            exit;
        }

        if (isset($_GET['valider'])) {
            checkRight('valider_bcf');
            $idBcf = $_GET['valider'];
            if ($this->commandeModel->valider($idBcf)) {
                logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'bon_commande_fourn', $idBcf, null, ['statut' => 'envoye']);
                setFlash('Commande validée et envoyée.', 'success');
            } else {
                setFlash('Impossible de valider (déjà traitée ?).', 'danger');
            }
            header('Location: ?action=commande_fourn');
            exit;
        }

        if (isset($_GET['annuler'])) {
            checkRight('annuler_bcf');
            $idBcf = $_GET['annuler'];
            $this->commandeModel->annuler($idBcf);
            logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'bon_commande_fourn', $idBcf, null, ['statut' => 'annule']);
            setFlash('Commande annulée.', 'success');
            header('Location: ?action=commande_fourn');
            exit;
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_bcf');
            $idBcf = $_GET['delete'];
            $this->commandeModel->delete($idBcf);
            logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'bon_commande_fourn', $idBcf);
            setFlash('Commande supprimée.', 'success');
            header('Location: ?action=commande_fourn');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_bcf');
            $idBcf = $_GET['print'];
            $commande = $this->commandeModel->find($idBcf);
            if (!$commande) {
                setFlash('Commande introuvable.', 'danger');
                header('Location: ?action=commande_fourn');
                exit;
            }
            $lignes = $this->commandeModel->getLignes($idBcf);
            $title = 'Bon de commande ' . $commande['reference'];
            require __DIR__ . '/../views/approvisionnement/print_commande.php';
            exit;
        }

        $commandes = $this->commandeModel->getAll($_GET);
        $fournisseurs = $this->pdo->query("SELECT id_fournisseur, nom FROM structure.fournisseur WHERE est_actif = true ORDER BY nom")->fetchAll();
        $produits = $this->pdo->query("SELECT id_produit, nom_produit, prix_achat, unite FROM structure.produit WHERE est_actif = true ORDER BY nom_produit")->fetchAll();

        $lignesParCommande = [];
        $commandesData = [];
        foreach ($commandes as $c) {
            $lignesParCommande[$c['id_bcf']] = $this->commandeModel->getLignes($c['id_bcf']);
            $commandesData[$c['id_bcf']] = [
                'id_fournisseur' => $c['id_fournisseur'],
                'observations' => $c['observations'] ?? ''
            ];
        }

        require __DIR__ . '/../views/approvisionnement/commandes.php';
    }

    public function reception() {
        checkRight('lister_receptions');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('creer_reception');
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Ajoutez au moins un produit.', 'danger');
                } else {
                    try {
                        $idBr = $this->receptionModel->create(
                            $_POST['id_bcf'] ?: null,
                            $_SESSION['user_id'],
                            trim($_POST['observations'] ?? ''),
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'bon_reception', $idBr, null, ['lignes' => count($lignes)]);
                        setFlash('Réception enregistrée.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            header('Location: ?action=reception');
            exit;
        }

        if (isset($_GET['valider'])) {
            checkRight('valider_reception');
            $idBr = $_GET['valider'];
            try {
                $idBe = $this->receptionModel->valider($idBr);
                logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'bon_reception', $idBr, null, ['statut' => 'complet', 'id_bon_entree' => $idBe]);
                setFlash('Réception validée. Bon d\'entrée généré.', 'success');
            } catch (Exception $e) {
                setFlash('Erreur : ' . $e->getMessage(), 'danger');
            }
            header('Location: ?action=reception');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_bon_reception');
            $idBr = $_GET['print'];
            $reception = $this->receptionModel->find($idBr);
            if (!$reception) {
                setFlash('Réception introuvable.', 'danger');
                header('Location: ?action=reception');
                exit;
            }
            $lignes = $this->receptionModel->getLignes($idBr);
            $title = 'Bon de réception ' . $reception['reference'];
            require __DIR__ . '/../views/approvisionnement/print_reception.php';
            exit;
        }

        $receptions = $this->receptionModel->getAll($_GET);
        $commandesDispo = $this->receptionModel->getCommandesDisponibles();
        $produits = $this->pdo->query("SELECT id_produit, nom_produit, prix_achat, unite FROM structure.produit WHERE est_actif = true ORDER BY nom_produit")->fetchAll();
        $lignesBcfDispo = [];
        foreach ($commandesDispo as $c) {
            $lignesBcfDispo[$c['id_bcf']] = $this->commandeModel->getLignes($c['id_bcf']);
        }

        require __DIR__ . '/../views/approvisionnement/receptions.php';
    }

    public function don() {
        checkRight('lister_dons');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('saisir_don');
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Ajoutez au moins un produit.', 'danger');
                } else {
                    try {
                        $idDon = $this->donModel->createWithEntree(
                            trim($_POST['donateur']),
                            trim($_POST['contact_donateur'] ?? ''),
                            $_POST['date_don'] ?: date('Y-m-d'),
                            trim($_POST['description'] ?? ''),
                            $_SESSION['user_id'],
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'don', $idDon);
                        setFlash('Don enregistré avec entrée en stock.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur : ' . $e->getMessage(), 'danger');
                    }
                }
            } elseif ($postAction === 'edit') {
                checkRight('modifier_don');
                try {
                    $this->donModel->update(
                        $_POST['id_don'],
                        trim($_POST['donateur']),
                        trim($_POST['contact_donateur'] ?? ''),
                        $_POST['date_don'] ?: date('Y-m-d'),
                        trim($_POST['description'] ?? ''),
                        0
                    );
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'don', $_POST['id_don']);
                    setFlash('Don modifié.', 'success');
                } catch (Exception $e) {
                    setFlash('Erreur : ' . $e->getMessage(), 'danger');
                }
            }
            header('Location: ?action=don');
            exit;
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_don');
            $idDon = $_GET['delete'];
            $this->donModel->delete($idDon);
            logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'don', $idDon);
            setFlash('Don supprimé.', 'success');
            header('Location: ?action=don');
            exit;
        }

        $dons = $this->donModel->getAll();
        $produits = $this->pdo->query("SELECT id_produit, nom_produit, unite FROM structure.produit WHERE est_actif = true ORDER BY nom_produit")->fetchAll();
        $donsLignes = [];
        foreach ($dons as $d) {
            $donsLignes[$d['id_don']] = $this->donModel->getLignesEntree($d['id_don']);
        }

        require __DIR__ . '/../views/approvisionnement/dons.php';
    }

    public function bonEntree() {
        checkRight('lister_bons_entree');

        if (isset($_GET['print'])) {
            checkRight('imprimer_bon_entree');
            $idBe = $_GET['print'];
            $bonEntree = $this->bonEntreeModel->find($idBe);
            if (!$bonEntree) {
                setFlash('Bon d\'entrée introuvable.', 'danger');
                header('Location: ?action=bon_entree');
                exit;
            }
            $lignes = $this->bonEntreeModel->getLignes($idBe);
            $title = 'Bon d\'entrée ' . $bonEntree['reference'];
            require __DIR__ . '/../views/approvisionnement/print_bon_entree.php';
            exit;
        }

        $bonsEntree = $this->bonEntreeModel->getAll($_GET);
        require __DIR__ . '/../views/approvisionnement/bons_entree.php';
    }

    public function factureFourn() {
        checkRight('lister_factures_fournisseur');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('creer_facture_fournisseur');
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Ajoutez au moins un produit.', 'danger');
                } else {
                    try {
                        $tauxTva = isset($_POST['appliquer_tva']) ? str_replace(',', '.', $_POST['taux_tva'] ?? 0) : 0;
                        $idFactureF = $this->factureModel->create(
                            $_POST['id_fournisseur'],
                            $_POST['id_bcf'] ?: null,
                            trim($_POST['numero_facture']),
                            $_POST['date_facture'] ?: date('Y-m-d'),
                            str_replace(',', '.', $_POST['montant_ht'] ?? 0),
                            $tauxTva,
                            $_POST['date_echeance'] ?: null,
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'facture_fournisseur', $idFactureF, null, ['lignes' => count($lignes)]);
                        setFlash('Facture créée.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur : ' . $e->getMessage(), 'danger');
                    }
                }
            } elseif ($postAction === 'edit') {
                checkRight('modifier_facture_fournisseur');
                $idFactureF = $_POST['id_facture_f'];
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Ajoutez au moins un produit.', 'danger');
                } else {
                    try {
                        $tauxTva = isset($_POST['appliquer_tva']) ? str_replace(',', '.', $_POST['taux_tva'] ?? 0) : 0;
                        $this->factureModel->update(
                            $idFactureF,
                            $_POST['id_fournisseur'],
                            $_POST['id_bcf'] ?: null,
                            trim($_POST['numero_facture']),
                            $_POST['date_facture'] ?: date('Y-m-d'),
                            str_replace(',', '.', $_POST['montant_ht'] ?? 0),
                            $tauxTva,
                            $_POST['date_echeance'] ?: null,
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'facture_fournisseur', $idFactureF);
                        setFlash('Facture modifiée.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            header('Location: ?action=facture_fourn');
            exit;
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_facture_fournisseur');
            $idFactureF = $_GET['delete'];
            try {
                $this->factureModel->delete($idFactureF);
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'facture_fournisseur', $idFactureF);
                setFlash('Facture supprimée.', 'success');
            } catch (Exception $e) {
                setFlash('Impossible de supprimer (paiements liés ?).', 'danger');
            }
            header('Location: ?action=facture_fourn');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_facture_fournisseur');
            $idFactureF = $_GET['print'];
            $facture = $this->factureModel->find($idFactureF);
            if (!$facture) {
                setFlash('Facture introuvable.', 'danger');
                header('Location: ?action=facture_fourn');
                exit;
            }
            $lignes = $this->factureModel->getLignes($idFactureF);
            $title = 'Facture ' . ($facture['numero_facture'] ?? $facture['reference']);
            require __DIR__ . '/../views/approvisionnement/print_facture.php';
            exit;
        }

        $factures = $this->factureModel->getAll($_GET);
        $fournisseurs = $this->pdo->query("SELECT id_fournisseur, nom FROM structure.fournisseur WHERE est_actif = true ORDER BY nom")->fetchAll();
        $commandes = $this->pdo->query("SELECT id_bcf, reference FROM approvisionnement.bon_commande_fourn WHERE statut IN ('envoye', 'receptionne') ORDER BY date_creation DESC")->fetchAll();
        $produits = $this->pdo->query("SELECT id_produit, nom_produit, prix_achat, unite FROM structure.produit WHERE est_actif = true ORDER BY nom_produit")->fetchAll();

        $lignesParFacture = [];
        foreach ($factures as $f) {
            $lignesParFacture[$f['id_facture_f']] = $this->factureModel->getLignes($f['id_facture_f']);
        }

        $lignesBcfDispo = [];
        foreach ($commandes as $c) {
            $lignesBcfDispo[$c['id_bcf']] = $this->commandeModel->getLignes($c['id_bcf']);
        }

        require __DIR__ . '/../views/approvisionnement/factures.php';
    }

    public function paiementFourn() {
        checkRight('lister_paiements_fournisseur');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('payer_fournisseur');
                try {
                    $idPaiement = $this->paiementModel->create(
                        $_POST['id_fournisseur'],
                        $_POST['id_facture_f'],
                        $_SESSION['user_id'],
                        str_replace(',', '.', $_POST['montant']),
                        $_POST['date_paiement'] ?: date('Y-m-d'),
                        $_POST['mode_paiement'],
                        trim($_POST['observations'] ?? '')
                    );
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'paiement_fournisseur', $idPaiement);
                    setFlash('Paiement enregistré.', 'success');
                } catch (Exception $e) {
                    setFlash('Erreur : ' . $e->getMessage(), 'danger');
                }
            }
            header('Location: ?action=paiement_fourn');
            exit;
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_paiement_fournisseur');
            $idPaiement = $_GET['delete'];
            try {
                $this->paiementModel->delete($idPaiement);
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'paiement_fournisseur', $idPaiement);
                setFlash('Paiement supprimé.', 'success');
            } catch (Exception $e) {
                setFlash('Erreur : ' . $e->getMessage(), 'danger');
            }
            header('Location: ?action=paiement_fourn');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_recu_fournisseur');
            $idPaiement = $_GET['print'];
            $paiement = $this->paiementModel->find($idPaiement);
            if (!$paiement) {
                setFlash('Paiement introuvable.', 'danger');
                header('Location: ?action=paiement_fourn');
                exit;
            }
            $title = 'Reçu de paiement';
            require __DIR__ . '/../views/approvisionnement/print_recu_paiement.php';
            exit;
        }

        $paiements = $this->paiementModel->getAll($_GET);
        $facturesImpayees = $this->paiementModel->getFacturesImpayees();
        $fournisseurs = $this->pdo->query("SELECT id_fournisseur, nom FROM structure.fournisseur WHERE est_actif = true ORDER BY nom")->fetchAll();

        require __DIR__ . '/../views/approvisionnement/paiements.php';
    }

    public function etatsAchats() {
        $periode = $_GET['periode'] ?? 'jour';

        if ($periode === 'jour') {
            checkRight('etat_achats_jour');
            $date = $_GET['date'] ?? date('Y-m-d');

            $achats = $this->pdo->prepare("
                SELECT e.reference, e.date_entree, e.type_source, e.observations,
                       l.id_produit, p.nom_produit, l.quantite, l.prix_unitaire,
                       (l.quantite * l.prix_unitaire) AS montant_ligne,
                       u.nom_complet AS utilisateur_nom
                FROM approvisionnement.bon_entree e
                JOIN approvisionnement.ligne_bon_entree l ON e.id_be = l.id_be
                JOIN structure.produit p ON l.id_produit = p.id_produit
                JOIN utilisateur.utilisateur u ON e.id_utilisateur = u.id_utilisateur
                WHERE e.date_entree = ?
                ORDER BY e.date_entree, e.reference
            ");
            $achats->execute([$date]);
            $lignes = $achats->fetchAll();

            $totalAchats = array_sum(array_map(fn($l) => $l['montant_ligne'], $lignes));

            if (isset($_GET['print'])) {
                $title = 'État des achats du ' . date('d/m/Y', strtotime($date));
                require __DIR__ . '/../views/approvisionnement/print_etat_achats_jour.php';
                exit;
            }

            require __DIR__ . '/../views/approvisionnement/etat_achats_jour.php';
        } else {
            checkRight('etat_achats_annuel');
            $annee = $_GET['annee'] ?? date('Y');

            $achats = $this->pdo->prepare("
                SELECT EXTRACT(MONTH FROM e.date_entree) AS mois,
                       COUNT(DISTINCT e.id_be) AS nb_bons,
                       SUM(l.quantite * l.prix_unitaire) AS total_mois
                FROM approvisionnement.bon_entree e
                JOIN approvisionnement.ligne_bon_entree l ON e.id_be = l.id_be
                WHERE EXTRACT(YEAR FROM e.date_entree) = ?
                GROUP BY EXTRACT(MONTH FROM e.date_entree)
                ORDER BY mois
            ");
            $achats->execute([$annee]);
            $moisData = $achats->fetchAll();

            $totalAnnuel = array_sum(array_map(fn($m) => $m['total_mois'], $moisData));
            $moisLabels = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

            if (isset($_GET['print'])) {
                $title = 'État des achats ' . $annee;
                require __DIR__ . '/../views/approvisionnement/print_etat_achats_annuel.php';
                exit;
            }

            require __DIR__ . '/../views/approvisionnement/etat_achats_annuel.php';
        }
    }
}
