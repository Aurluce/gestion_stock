<?php
require_once __DIR__ . '/../models/ProduitModel.php';
require_once __DIR__ . '/../models/FamilleModel.php';

class ProduitController {
    private $pdo;
    private $model;
    private $familleModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new ProduitModel($pdo);
        $this->familleModel = new FamilleModel($pdo);
    }

    public function index() {
        // Vérifier si c'est une demande d'impression
        if (isset($_GET['print'])) {
            $this->printList();
            return;
        }
        
        checkRight('lister_produits');

        $mode = $_GET['mode'] ?? 'liste';
        $familleSelectionnee = (int)($_GET['id_famille'] ?? 0);
        $famillesSelect = $this->familleModel->getForSelect();

        if ($mode == 'par_famille') {
            $allFamilles = $this->familleModel->getAll();
            $produitsParFamille = [];
            foreach ($allFamilles as $famille) {
                $produits = $this->model->getByFamille($famille['id_famille']);
                if (!empty($produits)) {
                    $produitsParFamille[] = [
                        'id_famille' => $famille['id_famille'],
                        'nom_famille' => $famille['nom_famille'],
                        'produits' => $produits
                    ];
                }
            }
        } else {
            $produitsListe = $this->model->getAll();
            if ($familleSelectionnee > 0) {
                $produitsListe = $this->model->getByFamille($familleSelectionnee);
            }
        }

        $familles = $this->familleModel->getForSelect();
        $produitsPeres = $this->model->getProduitsPeres();

        require __DIR__ . '/../views/structure/produits.php';
    }

    public function store() {
        checkRight('creer_produit');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=produits');
            exit;
        }
        
        $codeBarre = $this->model->generateCodeBarre();
        
        $data = [
            'id_famille' => (int)$_POST['id_famille'],
            'id_produit_pere' => !empty($_POST['id_produit_pere']) ? (int)$_POST['id_produit_pere'] : null,
            'code_barre' => $codeBarre,
            'nom_produit' => trim($_POST['nom_produit']),
            'description' => trim($_POST['description'] ?? ''),
            'prix_achat' => (float)($_POST['prix_achat'] ?? 0),
            'prix_vente' => (float)($_POST['prix_vente'] ?? 0),
            'stock_actuel' => (float)($_POST['stock_actuel'] ?? 0),
            'seuil_alerte' => (float)($_POST['seuil_alerte'] ?? 0),
            'perissable' => isset($_POST['perissable']),
            'date_peremption' => $_POST['date_peremption'] ?? null,
            'unite' => $_POST['unite'] ?? 'pce',
            'est_actif' => isset($_POST['est_actif'])
        ];
        
        if (empty($data['nom_produit']) || empty($data['id_famille'])) {
            setFlash('Le nom et la famille sont requis.', 'danger');
            header('Location: ?action=produits');
            exit;
        }
        
        $id = $this->model->create($data);
        if ($id) {
            logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'produit', $id, null, ['nom' => $data['nom_produit']]);
            setFlash("Produit '{$data['nom_produit']}' créé.", 'success');
        } else {
            setFlash("Erreur lors de la création.", 'danger');
        }
        
        header('Location: ?action=produits');
        exit;
    }

    public function update() {
        checkRight('modifier_produit');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=produits');
            exit;
        }
        
        $id = (int)$_POST['id_produit'];
        
        $data = [
            'id_famille' => (int)($_POST['id_famille_hidden'] ?? $_POST['id_famille']),
            'id_produit_pere' => !empty($_POST['id_produit_pere']) ? (int)$_POST['id_produit_pere'] : null,
            'code_barre' => trim($_POST['code_barre'] ?? ''),
            'nom_produit' => trim($_POST['nom_produit']),
            'description' => trim($_POST['description'] ?? ''),
            'prix_achat' => (float)($_POST['prix_achat'] ?? 0),
            'prix_vente' => (float)($_POST['prix_vente'] ?? 0),
            'stock_actuel' => (float)($_POST['stock_actuel'] ?? 0),
            'seuil_alerte' => (float)($_POST['seuil_alerte'] ?? 0),
            'perissable' => isset($_POST['perissable']),
            'date_peremption' => $_POST['date_peremption'] ?? null,
            'unite' => $_POST['unite'] ?? 'pce',
            'est_actif' => isset($_POST['est_actif'])
        ];
        
        $old = $this->model->getById($id);
        $result = $this->model->update($id, $data);
        
        if ($result) {
            logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'produit', $id, $old, ['nom' => $data['nom_produit']]);
            setFlash("Produit mis à jour.", 'success');
        } else {
            setFlash("Erreur lors de la mise à jour.", 'danger');
        }
        
        header('Location: ?action=produits');
        exit;
    }

    public function delete() {
        checkRight('supprimer_produit');
        
        $id = (int)($_GET['id'] ?? 0);
        $produit = $this->model->getById($id);
        
        if (!$produit) {
            setFlash("Produit introuvable.", 'danger');
        } else {
            $result = $this->model->delete($id);
            if ($result) {
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'produit', $id, $produit, null);
                setFlash("Produit supprimé.", 'success');
            } else {
                setFlash("Erreur lors de la suppression.", 'danger');
            }
        }
        
        header('Location: ?action=produits');
        exit;
    }

    public function getProduitsPeresAjax() {
        $idFamille = (int)($_GET['id_famille'] ?? 0);
        $produitsPeres = $idFamille > 0 ? $this->model->getProduitsPeresByFamille($idFamille) : [];
        header('Content-Type: application/json');
        echo json_encode($produitsPeres);
        exit;
    }

    private function printList() {
        checkRight('lister_produits');
        $produits = $this->model->getAll();
        require __DIR__ . '/../views/structure/prints/produits.php';
        exit;
    }
}