<?php
require_once __DIR__ . '/../models/ProduitModel.php';
require_once __DIR__ . '/../models/FamilleModel.php';

class ProduitController {
    private ProduitModel $model;
    private FamilleModel $familleModel;
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->model = new ProduitModel($pdo);
        $this->familleModel = new FamilleModel($pdo);
    }

    public function index(): void {
        checkRight('lister_produits');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_produit');
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
                    'perissable' => isset($_POST['perissable']) ? 1 : 0,
                    'date_peremption' => $_POST['date_peremption'] ?? null,
                    'unite' => $_POST['unite'] ?? 'pce',
                    'est_actif' => isset($_POST['est_actif']) ? 1 : 0
                ];
                if (empty($data['nom_produit']) || empty($data['id_famille'])) {
                    setFlash('Nom et famille sont requis.', 'danger');
                } else {
                    $id = $this->model->create($data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'produit', $id, null, ['nom' => $data['nom_produit']]);
                    setFlash('Produit créé.', 'success');
                }
                header('Location: ?action=produits');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_produit');
                $id = (int)($_POST['id_produit'] ?? 0);
                $old = $this->model->getById($id);
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
                    'perissable' => isset($_POST['perissable']) ? 1 : 0,
                    'date_peremption' => $_POST['date_peremption'] ?? null,
                    'unite' => $_POST['unite'] ?? 'pce',
                    'est_actif' => isset($_POST['est_actif']) ? 1 : 0
                ];
                if (empty($data['nom_produit']) || empty($data['id_famille'])) {
                    setFlash('Nom et famille sont requis.', 'danger');
                } else {
                    $this->model->update($id, $data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'produit', $id, $old, ['nom' => $data['nom_produit']]);
                    setFlash('Produit mis à jour.', 'success');
                }
                header('Location: ?action=produits');
                exit;
            }
        }

        $famillesSelect = $this->familleModel->getForSelect();
        $mode = $_GET['mode'] ?? 'liste';
        $familleSelectionnee = (int)($_GET['id_famille'] ?? 0);
        $search = trim($_GET['search'] ?? '');
        $produitsListe = [];
        $produitsParFamille = [];

        if ($search !== '') {
            $produitsListe = $this->model->search($search, $familleSelectionnee ?: null);
        } elseif ($mode == 'par_famille') {
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
        } else {
            $produitsListe = $this->model->getAll();
        }

        ob_start();
        require __DIR__ . '/../views/structure/produits.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function detail(): void {
        checkRight('lister_produits');
        $id = (int)($_GET['id'] ?? 0);
        $produit = $this->model->getById($id);
        if (!$produit) {
            header('Content-Type: application/json');
            echo json_encode(['html' => '<p class="text-danger-500 text-center py-8">Produit introuvable.</p>']);
            exit;
        }
        ob_start();
        require __DIR__ . '/../views/structure/detail_produit.php';
        $html = ob_get_clean();
        header('Content-Type: application/json');
        echo json_encode(['html' => $html]);
        exit;
    }

    public function getProduitsPeresAjax(): void {
        $idFamille = (int)($_GET['id_famille'] ?? 0);
        $excludeId = isset($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : null;
        $produitsPeres = $idFamille > 0 ? $this->model->getProduitsPeresByFamille($idFamille, $excludeId) : [];
        header('Content-Type: application/json');
        echo json_encode($produitsPeres);
        exit;
    }

    public function delete(): void {
        checkRight('supprimer_produit');
        $id = (int)($_GET['id'] ?? 0);
        $old = $this->model->getById($id);
        $this->model->delete($id);
        logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'produit', $id, $old, null);
        setFlash('Produit supprimé.', 'success');
        header('Location: ?action=produits');
        exit;
    }

    public function disable(): void {
        checkRight('modifier_produit');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->disable($id);
        setFlash('Produit désactivé.', 'success');
        header('Location: ?action=produits');
        exit;
    }

    public function enable(): void {
        checkRight('modifier_produit');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->enable($id);
        setFlash('Produit activé.', 'success');
        header('Location: ?action=produits');
        exit;
    }
}
