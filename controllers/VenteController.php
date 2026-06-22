<?php
require_once __DIR__ . '/../models/CommandeClientModel.php';
require_once __DIR__ . '/../models/BonLivraisonModel.php';
require_once __DIR__ . '/../models/FactureClientModel.php';
require_once __DIR__ . '/../models/ReglementClientModel.php';
require_once __DIR__ . '/../models/SortieStockModel.php';
require_once __DIR__ . '/../config/session.php';

class VenteController {
    private $pdo, $commandeModel, $bonLivraisonModel, $factureModel, $reglementModel, $sortieModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->commandeModel = new CommandeClientModel($pdo);
        $this->bonLivraisonModel = new BonLivraisonModel($pdo);
        $this->factureModel = new FactureClientModel($pdo);
        $this->reglementModel = new ReglementClientModel($pdo);
        $this->sortieModel = new SortieStockModel($pdo);
    }

    public function commandeClient() {
        checkRight('lister_commandes_client');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('creer_commande_client');
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Veuillez ajouter au moins une ligne de produit.', 'danger');
                } else {
                    try {
                        $idCc = $this->commandeModel->create(
                            $_POST['id_client'],
                            $_SESSION['user_id'],
                            $_POST['type_vente'] ?? 'credit',
                            trim($_POST['observations'] ?? ''),
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'commande_client', $idCc, null, ['lignes' => count($lignes)]);
                        setFlash('Commande créée avec succès.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur lors de la création : ' . $e->getMessage(), 'danger');
                    }
                }
            } elseif ($postAction === 'edit') {
                checkRight('modifier_commande_client');
                $idCc = $_POST['id_cc'];
                $lignes = $this->parseLignes($_POST);
                if (empty($lignes)) {
                    setFlash('Veuillez ajouter au moins une ligne de produit.', 'danger');
                } else {
                    try {
                        $this->commandeModel->update(
                            $idCc,
                            $_POST['id_client'],
                            $_POST['type_vente'] ?? 'credit',
                            trim($_POST['observations'] ?? ''),
                            $lignes
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'commande_client', $idCc, null, ['lignes' => count($lignes)]);
                        setFlash('Commande modifiée avec succès.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur lors de la modification : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            header('Location: ?action=commande_client');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_bon_commande_client');
            $idCc = $_GET['print'];
            $commande = $this->commandeModel->find($idCc);
            if (!$commande) {
                setFlash('Commande introuvable.', 'danger');
                header('Location: ?action=commande_client');
                exit;
            }
            $lignes = $this->commandeModel->getLignes($idCc);
            require __DIR__ . '/../views/vente/print_commande_client.php';
            exit;
        }

        if (isset($_GET['annuler'])) {
            checkRight('annuler_commande_client');
            $idCc = $_GET['annuler'];
            $this->commandeModel->annuler($idCc);
            logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'commande_client', $idCc, null, ['statut' => 'annulee']);
            setFlash('Commande annulée.', 'success');
            header('Location: ?action=commande_client');
            exit;
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_commande_client');
            $idCc = $_GET['delete'];
            $this->commandeModel->delete($idCc);
            logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'commande_client', $idCc, null, null);
            setFlash('Commande supprimée.', 'success');
            header('Location: ?action=commande_client');
            exit;
        }

        $filtres = [
            "statut" => $_GET["statut"] ?? null,
            "search" => $_GET["search"] ?? null
        ];
        $commandes = $this->commandeModel->getAll(array_filter($filtres));

        // Données pour les modals
        $clients = $this->pdo->query("SELECT id_client, nom, prenom FROM structure.client WHERE est_actif = true ORDER BY nom")->fetchAll();
        $produits = $this->pdo->query("SELECT id_produit, nom_produit, prix_vente, unite FROM structure.produit WHERE est_actif = true ORDER BY nom_produit")->fetchAll();

        // Lignes pour édition (si demandé)
        $lignesParCommande = [];
        foreach ($commandes as $cmd) {
            $lignesParCommande[$cmd['id_cc']] = $this->commandeModel->getLignes($cmd['id_cc']);
        }

        require __DIR__ . '/../views/vente/commandes_clients.php';
    }


    public function detailCommande(): void {
        checkRight('lister_commandes_client');
        $id = (int)($_GET['id'] ?? 0);
        $commande = $this->commandeModel->find($id);
        if (!$commande) {
            header('Content-Type: application/json');
            echo json_encode(['html' => '<p class="text-danger-500 text-center py-8">Commande introuvable.</p>']);
            exit;
        }
        $lignes = $this->commandeModel->getLignes($id);
        ob_start();
        require __DIR__ . '/../views/vente/detail_commande_client.php';
        $html = ob_get_clean();
        header('Content-Type: application/json');
        echo json_encode(['html' => $html]);
        exit;
    }

    public function bonLivraison() {
        checkRight('lister_livraisons');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('livrer_commande');
                $idCc = $_POST['id_cc'];
                $livraisonComplete = isset($_POST['livraison_complete']);
                $lignes = [];
                if (!empty($_POST['id_produit']) && is_array($_POST['id_produit'])) {
                    foreach ($_POST['id_produit'] as $i => $idProduit) {
                        if (empty($_POST['qte_livree'][$i]) || $_POST['qte_livree'][$i] <= 0) continue;
                        $lignes[] = [
                            'id_produit' => $idProduit,
                            'qte_livree' => $_POST['qte_livree'][$i],
                            'observations' => $_POST['ligne_obs'][$i] ?? null
                        ];
                    }
                }
                if (empty($lignes)) {
                    setFlash('Veuillez saisir au moins une quantité à livrer.', 'danger');
                } else {
                    try {
                        $idBl = $this->bonLivraisonModel->create(
                            $idCc,
                            $_SESSION['user_id'],
                            trim($_POST['observations'] ?? ''),
                            $lignes,
                            $livraisonComplete
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'bon_livraison', $idBl, null, ['id_cc' => $idCc]);
                        setFlash('Livraison enregistrée avec succès.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur lors de la livraison : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            header('Location: ?action=bon_livraison');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_bon_livraison');
            $idBl = $_GET['print'];
            $bl = $this->bonLivraisonModel->find($idBl);
            if (!$bl) {
                setFlash('Bon de livraison introuvable.', 'danger');
                header('Location: ?action=bon_livraison');
                exit;
            }
            $lignes = $this->bonLivraisonModel->getLignes($idBl);
            require __DIR__ . '/../views/vente/print_bon_livraison.php';
            exit;
        }

        if (isset($_GET['annuler'])) {
            checkRight('annuler_livraison');
            $idBl = $_GET['annuler'];
            $this->bonLivraisonModel->annuler($idBl);
            logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'bon_livraison', $idBl, null, ['statut' => 'annule']);
            setFlash('Livraison annulée.', 'success');
            header('Location: ?action=bon_livraison');
            exit;
        }

        $livraisons = $this->bonLivraisonModel->getAll();

        // Commandes éligibles à la livraison (en_cours ou partiellement livrées)
        $commandesLivrables = $this->pdo->query("
            SELECT cc.id_cc, cc.reference, c.nom AS client_nom, c.prenom AS client_prenom
            FROM vente.commande_client cc
            JOIN structure.client c ON cc.id_client = c.id_client
            WHERE cc.statut IN ('en_cours', 'en_attente')
            ORDER BY cc.date_creation DESC")->fetchAll();

        $lignesParCommande = [];
        foreach ($commandesLivrables as $cmd) {
            $lignesParCommande[$cmd['id_cc']] = $this->bonLivraisonModel->getLignesAvecRestant($cmd['id_cc']);
        }

        require __DIR__ . '/../views/vente/bon_livraison.php';
    }

    public function factureClient() {
        checkRight('lister_factures_client');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('creer_facture_client');
                try {
                    $idFacture = $this->factureModel->create(
                        $_POST['id_cc'],
                        $_SESSION['user_id'],
                        $_POST['taux_tva'] ?? 19.25,
                        $_POST['date_echeance'] ?? null
                    );
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'facture_client', $idFacture, null, ['id_cc' => $_POST['id_cc']]);
                    setFlash('Facture créée avec succès.', 'success');
                } catch (Exception $e) {
                    setFlash('Erreur lors de la création : ' . $e->getMessage(), 'danger');
                }
            }
            header('Location: ?action=facture_client');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_facture_client');
            $idFacture = $_GET['print'];
            $facture = $this->factureModel->find($idFacture);
            if (!$facture) {
                setFlash('Facture introuvable.', 'danger');
                header('Location: ?action=facture_client');
                exit;
            }
            $lignes = $this->factureModel->getLignes($facture['id_cc']);
            require __DIR__ . '/../views/vente/print_facture_client.php';
            exit;
        }

        if (isset($_GET['annuler'])) {
            checkRight('annuler_facture_client');
            $idFacture = $_GET['annuler'];
            $this->factureModel->annuler($idFacture);
            logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'facture_client', $idFacture, null, ['statut' => 'annulee']);
            setFlash('Facture annulée.', 'success');
            header('Location: ?action=facture_client');
            exit;
        }

        $filtres = ["statut" => $_GET["statut"] ?? null];
        $factures = $this->factureModel->getAll(array_filter($filtres));

        // Commandes livrées sans facture
        $commandesFacturables = $this->pdo->query("
            SELECT cc.id_cc, cc.reference, cc.montant_total, c.nom AS client_nom, c.prenom AS client_prenom
            FROM vente.commande_client cc
            JOIN structure.client c ON cc.id_client = c.id_client
            WHERE cc.statut = 'livree'
            AND NOT EXISTS (SELECT 1 FROM vente.facture_client f WHERE f.id_cc = cc.id_cc)
            ORDER BY cc.date_creation DESC")->fetchAll();

        require __DIR__ . '/../views/vente/factures_clients.php';
    }

    public function reglementClient() {
        checkRight('lister_reglements_client');

        if (isset($_GET['print'])) {
            checkRight('imprimer_recu_client');
            $idReglement = $_GET['print'];
            $reglement = $this->reglementModel->find($idReglement);
            if (!$reglement) {
                setFlash('Règlement introuvable.', 'danger');
                header('Location: ?action=reglement_client');
                exit;
            }
            $stmt = $this->pdo->prepare("SELECT r.*, f.reference AS facture_reference, c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.reglement_client r
                JOIN vente.facture_client f ON r.id_facture = f.id_facture
                JOIN structure.client c ON r.id_client = c.id_client
                WHERE r.id_reglement = ?");
            $stmt->execute([$idReglement]);
            $recu = $stmt->fetch();
            require __DIR__ . '/../views/vente/print_recu_client.php';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('enregistrer_reglement_client');
                $idFacture = $_POST['id_facture'];
                $montant = (float) $_POST['montant'];

                if ($montant <= 0) {
                    setFlash('Le montant doit être supérieur à 0.', 'danger');
                } else {
                    try {
                        $facture = $this->factureModel->find($idFacture);
                        if (!$facture) {
                            throw new Exception('Facture introuvable.');
                        }

                        $resteAvant = $this->reglementModel->getResteAPayer($idFacture);
                        $reste = $resteAvant ? (float) $resteAvant['reste'] : (float) $facture['montant_ttc'];

                        $idReglement = $this->reglementModel->create(
                            $idFacture,
                            $this->getClientIdFromFacture($idFacture),
                            $_SESSION['user_id'],
                            $montant,
                            $_POST['mode_paiement'],
                            trim($_POST['reference'] ?? ''),
                            trim($_POST['observations'] ?? '')
                        );

                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'reglement_client', $idReglement, null, ['montant' => $montant]);

                        $this->factureModel->updateStatutFromReglements($idFacture);

                        if ($montant > $reste) {
                            setFlash('Règlement enregistré. Attention : le montant dépasse le reste à payer (' . number_format($reste, 0, ',', ' ') . ' FCFA).', 'warning');
                        } else {
                            setFlash('Règlement enregistré avec succès.', 'success');
                        }
                    } catch (Exception $e) {
                        setFlash('Erreur lors de l\'enregistrement : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            header('Location: ?action=reglement_client' . (isset($_POST['id_facture']) ? '&id_facture=' . $_POST['id_facture'] : ''));
            exit;
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_reglement_client');
            $idReglement = $_GET['delete'];
            $reglement = $this->reglementModel->find($idReglement);
            if ($reglement) {
                $idFacture = $reglement['id_facture'];
                $this->reglementModel->delete($idReglement);
                $this->factureModel->updateStatutFromReglements($idFacture);
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'reglement_client', $idReglement, null, null);
                setFlash('Règlement supprimé.', 'success');
            }
            header('Location: ?action=reglement_client' . (isset($idFacture) ? '&id_facture=' . $idFacture : ''));
            exit;
        }

        $idFactureFiltre = $_GET['id_facture'] ?? null;
        $reglements = $this->reglementModel->getAll($idFactureFiltre ? ['id_facture' => $idFactureFiltre] : []);

        // Factures avec reste à payer (pour le formulaire)
        $facturesAPayer = $this->pdo->query("
            SELECT f.id_facture, f.reference, f.montant_ttc,
                   c.nom AS client_nom, c.prenom AS client_prenom,
                   f.montant_ttc - COALESCE((SELECT SUM(r.montant) FROM vente.reglement_client r WHERE r.id_facture = f.id_facture), 0) AS reste
            FROM vente.facture_client f
            JOIN vente.commande_client cc ON f.id_cc = cc.id_cc
            JOIN structure.client c ON cc.id_client = c.id_client
            WHERE f.statut IN ('impayee', 'partielle')
            ORDER BY f.date_creation DESC")->fetchAll();

        $factureSelectionnee = null;
        if ($idFactureFiltre) {
            $factureSelectionnee = $this->factureModel->find($idFactureFiltre);
        }

        require __DIR__ . '/../views/vente/reglements_clients.php';
    }

    private function getClientIdFromFacture($idFacture) {
        $stmt = $this->pdo->prepare("SELECT cc.id_client FROM vente.facture_client f
            JOIN vente.commande_client cc ON f.id_cc = cc.id_cc
            WHERE f.id_facture = ?");
        $stmt->execute([$idFacture]);
        return $stmt->fetchColumn();
    }

    public function sortieStock() {
        checkRight('lister_sorties_stock');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                checkRight('enregistrer_sortie_stock');
                $quantite = (float) ($_POST['quantite'] ?? 0);

                if ($quantite <= 0) {
                    setFlash('La quantité doit être supérieure à 0.', 'danger');
                } elseif (empty($_POST['id_produit']) || empty($_POST['motif_sortie'])) {
                    setFlash('Veuillez sélectionner un produit et un motif.', 'danger');
                } else {
                    try {
                        $idSortie = $this->sortieModel->create(
                            $_POST['id_produit'],
                            $_POST['id_client'] ?? null,
                            $_SESSION['user_id'],
                            $quantite,
                            $_POST['motif_sortie'],
                            trim($_POST['observations'] ?? '')
                        );
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'sortie_stock', $idSortie, null, ['quantite' => $quantite, 'motif' => $_POST['motif_sortie']]);
                        setFlash('Sortie de stock enregistrée avec succès.', 'success');
                    } catch (Exception $e) {
                        setFlash('Erreur lors de l\'enregistrement : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            header('Location: ?action=sortie_stock');
            exit;
        }

        if (isset($_GET['print'])) {
            checkRight('imprimer_bon_sortie');
            $idSortie = $_GET['print'];
            $sortie = $this->sortieModel->find($idSortie);
            if (!$sortie) {
                setFlash('Sortie introuvable.', 'danger');
                header('Location: ?action=sortie_stock');
                exit;
            }
            require __DIR__ . '/../views/vente/print_bon_sortie.php';
            exit;
        }

        $filtres = ["motif" => $_GET["motif"] ?? null];
        $sorties = $this->sortieModel->getAll(array_filter($filtres));

        $produits = $this->pdo->query("SELECT id_produit, nom_produit, unite, stock_actuel FROM structure.produit WHERE est_actif = true ORDER BY nom_produit")->fetchAll();
        $clients = $this->pdo->query("SELECT id_client, nom, prenom FROM structure.client WHERE est_actif = true ORDER BY nom")->fetchAll();

        require __DIR__ . '/../views/vente/sorties_stock.php';
    }

    public function venteComptant() {
        checkRight('effectuer_vente_comptant');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'add') {
                $lignes = $this->parseLignes($_POST);
                $idClient = $_POST['id_client'] ?? '';
                $modePaiement = $_POST['mode_paiement'] ?? 'espece';

                if (empty($idClient)) {
                    setFlash('Veuillez sélectionner un client.', 'danger');
                } elseif (empty($lignes)) {
                    setFlash('Veuillez ajouter au moins un produit.', 'danger');
                } else {
                    $this->pdo->beginTransaction();
                    try {
                        // 1. Commande client (type comptant)
                        // On utilise create() du model mais sans transaction imbriquée :
                        // on duplique la logique ici pour rester dans une seule transaction globale.
                        $reference = generateReference($this->pdo, 'CC', 'vente.commande_client');
                        $stmt = $this->pdo->prepare("INSERT INTO vente.commande_client
                            (id_client, id_utilisateur, reference, type_vente, statut, observations)
                            VALUES (?, ?, ?, 'comptant', 'en_cours', ?)
                            RETURNING id_cc");
                        $stmt->execute([$idClient, $_SESSION['user_id'], $reference, trim($_POST['observations'] ?? '')]);
                        $idCc = $stmt->fetchColumn();

                        $montantTotal = 0;
                        $stmtLigne = $this->pdo->prepare("INSERT INTO vente.ligne_commande_client
                            (id_cc, id_produit, quantite, prix_unitaire, taux_remise)
                            VALUES (?, ?, ?, ?, ?)");
                        foreach ($lignes as $l) {
                            $stmtLigne->execute([$idCc, $l['id_produit'], $l['quantite'], $l['prix_unitaire'], $l['taux_remise'] ?? 0]);
                            $montantTotal += $l['quantite'] * $l['prix_unitaire'] * (1 - ($l['taux_remise'] ?? 0) / 100);
                        }
                        $this->pdo->prepare("UPDATE vente.commande_client SET montant_total = ? WHERE id_cc = ?")
                            ->execute([$montantTotal, $idCc]);

                        // 2. Bon de livraison (complet)
                        $refBl = generateReference($this->pdo, 'BL', 'vente.bon_livraison');
                        $stmt = $this->pdo->prepare("INSERT INTO vente.bon_livraison
                            (id_cc, id_utilisateur, reference, statut)
                            VALUES (?, ?, ?, 'livre')
                            RETURNING id_bl");
                        $stmt->execute([$idCc, $_SESSION['user_id'], $refBl]);
                        $idBl = $stmt->fetchColumn();

                        $stmtLl = $this->pdo->prepare("INSERT INTO vente.ligne_livraison
                            (id_bl, id_produit, qte_livree) VALUES (?, ?, ?)");
                        foreach ($lignes as $l) {
                            $stmtLl->execute([$idBl, $l['id_produit'], $l['quantite']]);
                        }

                        $this->pdo->prepare("UPDATE vente.commande_client SET statut = 'livree', date_modif = CURRENT_TIMESTAMP WHERE id_cc = ?")
                            ->execute([$idCc]);

                        // 3. Facture (TVA 19.25%)
                        $tauxTva = 19.25;
                        $montantTtc = round($montantTotal * (1 + $tauxTva / 100), 2);
                        $refFacture = generateReference($this->pdo, 'FACT', 'vente.facture_client');
                        $stmt = $this->pdo->prepare("INSERT INTO vente.facture_client
                            (id_cc, id_utilisateur, reference, montant_ht, taux_tva, montant_ttc, statut)
                            VALUES (?, ?, ?, ?, ?, ?, 'impayee')
                            RETURNING id_facture");
                        $stmt->execute([$idCc, $_SESSION['user_id'], $refFacture, $montantTotal, $tauxTva, $montantTtc]);
                        $idFacture = $stmt->fetchColumn();

                        $this->pdo->prepare("UPDATE vente.commande_client SET statut = 'facturee', date_modif = CURRENT_TIMESTAMP WHERE id_cc = ?")
                            ->execute([$idCc]);

                        // 4. Règlement intégral
                        $stmt = $this->pdo->prepare("INSERT INTO vente.reglement_client
                            (id_facture, id_client, id_utilisateur, montant, mode_paiement)
                            VALUES (?, ?, ?, ?, ?)
                            RETURNING id_reglement");
                        $stmt->execute([$idFacture, $idClient, $_SESSION['user_id'], $montantTtc, $modePaiement]);
                        $idReglement = $stmt->fetchColumn();

                        $this->pdo->prepare("UPDATE vente.facture_client SET statut = 'payee', date_modif = CURRENT_TIMESTAMP WHERE id_facture = ?")
                            ->execute([$idFacture]);
                        $this->pdo->prepare("UPDATE vente.commande_client SET statut = 'reglee', date_modif = CURRENT_TIMESTAMP WHERE id_cc = ?")
                            ->execute([$idCc]);

                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'commande_client', $idCc, null, ['type' => 'vente_comptant', 'montant' => $montantTtc]);

                        $this->pdo->commit();
                        setFlash('Vente au comptant enregistrée avec succès. Ticket : ' . $refFacture, 'success');

                        // Rediriger vers le ticket imprimable
                        header('Location: ?action=vente_comptant&ticket=' . $idFacture);
                        exit;
                    } catch (Exception $e) {
                        $this->pdo->rollBack();
                        setFlash('Erreur lors de la vente : ' . $e->getMessage(), 'danger');
                    }
                }
            }
            if (!isset($idFacture)) {
                header('Location: ?action=vente_comptant');
                exit;
            }
        }

        if (isset($_GET['ticket'])) {
            checkRight('imprimer_ticket_vente');
            $idFacture = $_GET['ticket'];
            $facture = $this->factureModel->find($idFacture);
            if (!$facture) {
                setFlash('Vente introuvable.', 'danger');
                header('Location: ?action=vente_comptant');
                exit;
            }
            $lignes = $this->factureModel->getLignes($facture['id_cc']);
            $reglements = $this->reglementModel->getAllByFacture($idFacture);
            require __DIR__ . '/../views/vente/print_ticket_vente.php';
            exit;
        }

        $clients = $this->pdo->query("SELECT id_client, nom, prenom FROM structure.client WHERE est_actif = true ORDER BY nom")->fetchAll();
        $produits = $this->pdo->query("SELECT id_produit, nom_produit, prix_vente, unite, stock_actuel FROM structure.produit WHERE est_actif = true AND stock_actuel > 0 ORDER BY nom_produit")->fetchAll();

        require __DIR__ . '/../views/vente/vente_comptant.php';
    }

    public function etatsVentes() {
        checkRight('tableau_bord_ventes');

        $type = $_GET['type'] ?? 'jour';

        if ($type === 'jour') {
            checkRight('etat_ventes_jour');

            $factures = $this->pdo->query("
                SELECT f.*, cc.reference AS cc_reference, c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.facture_client f
                JOIN vente.commande_client cc ON f.id_cc = cc.id_cc
                JOIN structure.client c ON cc.id_client = c.id_client
                WHERE f.date_facture = CURRENT_DATE
                AND f.statut != 'annulee'
                ORDER BY f.date_creation DESC")->fetchAll();

            $stats = $this->pdo->query("
                SELECT COUNT(*) AS nb_factures,
                       COALESCE(SUM(montant_ht), 0) AS total_ht,
                       COALESCE(SUM(montant_ttc), 0) AS total_ttc
                FROM vente.facture_client
                WHERE date_facture = CURRENT_DATE
                AND statut != 'annulee'")->fetch();

            if (isset($_GET['print'])) {
                checkRight('imprimer_facture_client');
                require __DIR__ . '/../views/vente/print_etat_jour.php';
                exit;
            }

            require __DIR__ . '/../views/vente/etat_ventes_jour.php';
        } else {
            checkRight('etat_ventes_annuel');

            $annee = $_GET['annee'] ?? date('Y');

            $parMois = $this->pdo->prepare("
                SELECT EXTRACT(MONTH FROM date_facture)::INT AS mois,
                       COUNT(*) AS nb_factures,
                       COALESCE(SUM(montant_ht), 0) AS total_ht,
                       COALESCE(SUM(montant_ttc), 0) AS total_ttc
                FROM vente.facture_client
                WHERE EXTRACT(YEAR FROM date_facture) = ?
                AND statut != 'annulee'
                GROUP BY EXTRACT(MONTH FROM date_facture)
                ORDER BY mois");
            $parMois->execute([$annee]);
            $statsParMois = $parMois->fetchAll();

            $stmtTotal = $this->pdo->prepare("
                SELECT COUNT(*) AS nb_factures,
                       COALESCE(SUM(montant_ht), 0) AS total_ht,
                       COALESCE(SUM(montant_ttc), 0) AS total_ttc
                FROM vente.facture_client
                WHERE EXTRACT(YEAR FROM date_facture) = ?
                AND statut != 'annulee'");
            $stmtTotal->execute([$annee]);
            $totalAnnee = $stmtTotal->fetch();

            // Années disponibles
            $anneesDisponibles = $this->pdo->query("
                SELECT DISTINCT EXTRACT(YEAR FROM date_facture)::INT AS annee
                FROM vente.facture_client
                ORDER BY annee DESC")->fetchAll(PDO::FETCH_COLUMN);
            if (empty($anneesDisponibles)) {
                $anneesDisponibles = [date('Y')];
            }

            if (isset($_GET['print'])) {
                checkRight('imprimer_facture_client');
                require __DIR__ . '/../views/vente/print_etat_annuel.php';
                exit;
            }

            require __DIR__ . '/../views/vente/etat_ventes_annuel.php';
        }
    }

    public function dashboardVentes() {
        checkRight('tableau_bord_ventes');

        // Ventes des 7 derniers jours
        $ventes7j = $this->pdo->query("
            SELECT date_facture::DATE AS jour,
                   COUNT(*) AS nb_factures,
                   COALESCE(SUM(montant_ttc), 0) AS total_ttc
            FROM vente.facture_client
            WHERE date_facture >= CURRENT_DATE - INTERVAL '6 days'
            AND statut != 'annulee'
            GROUP BY date_facture
            ORDER BY date_facture")->fetchAll();

        // Compléter les jours manquants avec 0
        $ventesParJour = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $ventesParJour[$date] = ['jour' => $date, 'nb_factures' => 0, 'total_ttc' => 0];
        }
        foreach ($ventes7j as $v) {
            $ventesParJour[$v['jour']] = $v;
        }

        // Top 5 produits vendus (par quantité, sur les commandes)
        $topProduits = $this->pdo->query("
            SELECT p.nom_produit, p.unite,
                   SUM(lcc.quantite) AS qte_totale,
                   SUM(lcc.montant_ligne) AS montant_total
            FROM vente.ligne_commande_client lcc
            JOIN structure.produit p ON lcc.id_produit = p.id_produit
            JOIN vente.commande_client cc ON lcc.id_cc = cc.id_cc
            WHERE cc.statut != 'annulee'
            GROUP BY p.id_produit, p.nom_produit, p.unite
            ORDER BY qte_totale DESC
            LIMIT 5")->fetchAll();

        // Top 5 clients (par montant facturé)
        $topClients = $this->pdo->query("
            SELECT c.nom, c.prenom,
                   COUNT(f.id_facture) AS nb_factures,
                   SUM(f.montant_ttc) AS montant_total
            FROM vente.facture_client f
            JOIN vente.commande_client cc ON f.id_cc = cc.id_cc
            JOIN structure.client c ON cc.id_client = c.id_client
            WHERE f.statut != 'annulee'
            GROUP BY c.id_client, c.nom, c.prenom
            ORDER BY montant_total DESC
            LIMIT 5")->fetchAll();

        // Produits en alerte de stock (vendus récemment, stock <= seuil)
        $produitsAlerte = $this->pdo->query("
            SELECT p.id_produit, p.nom_produit, p.unite, p.stock_actuel, p.seuil_alerte
            FROM structure.produit p
            WHERE p.stock_actuel <= p.seuil_alerte
            AND p.est_actif = true
            ORDER BY (p.stock_actuel - p.seuil_alerte) ASC
            LIMIT 10")->fetchAll();

        // Factures en attente de règlement
        $facturesImpayees = $this->pdo->query("
            SELECT COUNT(*) AS nb, COALESCE(SUM(montant_ttc - COALESCE((
                SELECT SUM(r.montant) FROM vente.reglement_client r WHERE r.id_facture = f.id_facture
            ), 0)), 0) AS montant_du
            FROM vente.facture_client f
            WHERE f.statut IN ('impayee', 'partielle')")->fetch();

        require __DIR__ . '/../views/vente/dashboard_ventes.php';
    }
    private function parseLignes($post) {
        $lignes = [];
        if (!empty($post['id_produit']) && is_array($post['id_produit'])) {
            foreach ($post['id_produit'] as $i => $idProduit) {
                if (empty($idProduit) || empty($post['quantite'][$i])) continue;
                $lignes[] = [
                    'id_produit' => $idProduit,
                    'quantite' => $post['quantite'][$i],
                    'prix_unitaire' => $post['prix_unitaire'][$i],
                    'taux_remise' => $post['taux_remise'][$i] ?? 0
                ];
            }
        }
        return $lignes;
    }
}
