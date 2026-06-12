<?php
require_once __DIR__ . '/../models/ProduitModel.php';
require_once __DIR__ . '/../models/FamilleModel.php';

class ProduitController {
    private ProduitModel $model;
    private FamilleModel $familleModel;
    
    public function __construct(PDO $pdo) {
        $this->model = new ProduitModel($pdo);
        $this->familleModel = new FamilleModel($pdo);
    }
    
    public function index(): void {
        checkRight('lister_produits');
        $famillesSelect = $this->familleModel->getForSelect();
        $mode = $_GET['mode'] ?? 'liste';
        $familleSelectionnee = (int)($_GET['id_famille'] ?? 0);
        $produitsListe = $this->model->getAll();
        $produitsParFamille = [];
        
        if ($mode == 'par_famille') {
            foreach ($this->familleModel->getAll() as $famille) {
                $produits = $this->model->getByFamille($famille['id_famille']);
                if (!empty($produits)) {
                    $produitsParFamille[] = [
                        'nom_famille' => $famille['nom_famille'],
                        'produits' => $produits
                    ];
                }
            }
        } elseif ($familleSelectionnee > 0) {
            $produitsListe = $this->model->getByFamille($familleSelectionnee);
        }
        
        ob_start();
        require __DIR__ . '/../views/structure/produits.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function create(): void {
        checkRight('creer_produit');
        $familles = $this->familleModel->getForSelect();
        ob_start();
        require __DIR__ . '/../views/structure/produit_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function getProduitsPeresAjax(): void {
        $idFamille = (int)($_GET['id_famille'] ?? 0);
        $produitsPeres = $idFamille > 0 ? $this->model->getProduitsPeresByFamille($idFamille) : [];
        header('Content-Type: application/json');
        echo json_encode($produitsPeres);
        exit;
    }
    
    public function store(): void {
        checkRight('creer_produit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=produits');
            exit;
        }
        
        $estActif = isset($_POST['est_actif']) ? 1 : 0;
        $perissable = isset($_POST['perissable']) ? 1 : 0;
        
        $data = [
            'id_famille' => (int)$_POST['id_famille'],
            'id_produit_pere' => !empty($_POST['id_produit_pere']) ? (int)$_POST['id_produit_pere'] : null,
            'code_barre' => $this->model->generateCodeBarre(),
            'nom_produit' => trim($_POST['nom_produit']),
            'description' => trim($_POST['description'] ?? ''),
            'prix_achat' => (float)($_POST['prix_achat'] ?? 0),
            'prix_vente' => (float)($_POST['prix_vente'] ?? 0),
            'stock_actuel' => (float)($_POST['stock_actuel'] ?? 0),
            'seuil_alerte' => (float)($_POST['seuil_alerte'] ?? 0),
            'perissable' => $perissable,
            'date_peremption' => $_POST['date_peremption'] ?? null,
            'unite' => $_POST['unite'] ?? 'pce',
            'est_actif' => $estActif
        ];
        
        if (empty($data['nom_produit']) || empty($data['id_famille'])) {
            $_SESSION['error'] = "Nom et famille sont requis.";
            header('Location: index.php?action=produit_creer');
            exit;
        }
        
        $this->model->create($data);
        $_SESSION['success'] = "Produit créé.";
        header('Location: index.php?action=produits');
        exit;
    }
    
    public function edit(): void {
        checkRight('modifier_produit');
        $id = (int)($_GET['id'] ?? 0);
        $produit = $this->model->getById($id);
        if (!$produit) {
            $_SESSION['error'] = "Produit introuvable.";
            header('Location: index.php?action=produits');
            exit;
        }
        $familles = $this->familleModel->getForSelect();
        $produitsPeres = $this->model->getProduitsPeresByFamille($produit['id_famille']);
        ob_start();
        require __DIR__ . '/../views/structure/produit_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function update(): void {
        checkRight('modifier_produit');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=produits');
            exit;
        }
        
        $estActif = isset($_POST['est_actif']) ? 1 : 0;
        $perissable = isset($_POST['perissable']) ? 1 : 0;
        
        $data = [
            'id_famille' => (int)$_POST['id_famille'],
            'id_produit_pere' => !empty($_POST['id_produit_pere']) ? (int)$_POST['id_produit_pere'] : null,
            'code_barre' => trim($_POST['code_barre'] ?? ''),
            'nom_produit' => trim($_POST['nom_produit']),
            'description' => trim($_POST['description'] ?? ''),
            'prix_achat' => (float)($_POST['prix_achat'] ?? 0),
            'prix_vente' => (float)($_POST['prix_vente'] ?? 0),
            'stock_actuel' => (float)($_POST['stock_actuel'] ?? 0),
            'seuil_alerte' => (float)($_POST['seuil_alerte'] ?? 0),
            'perissable' => $perissable,
            'date_peremption' => $_POST['date_peremption'] ?? null,
            'unite' => $_POST['unite'] ?? 'pce',
            'est_actif' => $estActif
        ];
        
        $this->model->update((int)$_POST['id_produit'], $data);
        $_SESSION['success'] = "Produit mis à jour.";
        header('Location: index.php?action=produits');
        exit;
    }
    
    public function delete(): void {
        checkRight('supprimer_produit');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        $_SESSION['success'] = "Produit supprimé.";
        header('Location: index.php?action=produits');
        exit;
    }
    
    // Désactiver un produit
    public function disable(): void {
        checkRight('modifier_produit');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->disable($id);
        $_SESSION['success'] = "Produit désactivé.";
        header('Location: index.php?action=produits');
        exit;
    }
    
    // Activer un produit
    public function enable(): void {
        checkRight('modifier_produit');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->enable($id);
        $_SESSION['success'] = "Produit activé.";
        header('Location: index.php?action=produits');
        exit;
    }
}