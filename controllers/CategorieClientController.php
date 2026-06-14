<?php
require_once __DIR__ . '/../models/CategorieClientModel.php';

class CategorieClientController {
    private $pdo;
    private $model;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new CategorieClientModel($pdo);
    }

    public function index() {
        checkRight('lister_categories_client');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_categorie_client');
                $data = [
                    'nom_categorie' => trim($_POST['nom_categorie']),
                    'taux_remise' => (float)($_POST['taux_remise'] ?? 0),
                    'description' => trim($_POST['description'] ?? '')
                ];
                
                if (empty($data['nom_categorie'])) {
                    setFlash('Le nom de la catégorie est requis.', 'danger');
                } else {
                    $id = $this->model->create($data);
                    if ($id) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'categorie_client', $id, null, ['nom' => $data['nom_categorie']]);
                        setFlash("Catégorie '{$data['nom_categorie']}' créée.", 'success');
                    } else {
                        setFlash("Erreur lors de la création.", 'danger');
                    }
                }
                header('Location: ?action=categories_client');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_categorie_client');
                $id = (int)$_POST['id_categorie_client'];
                $data = [
                    'nom_categorie' => trim($_POST['nom_categorie']),
                    'taux_remise' => (float)($_POST['taux_remise'] ?? 0),
                    'description' => trim($_POST['description'] ?? '')
                ];
                
                if (empty($data['nom_categorie'])) {
                    setFlash('Le nom de la catégorie est requis.', 'danger');
                } else {
                    $old = $this->model->getById($id);
                    $result = $this->model->update($id, $data);
                    if ($result) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'categorie_client', $id, $old, ['nom' => $data['nom_categorie']]);
                        setFlash("Catégorie mise à jour.", 'success');
                    } else {
                        setFlash("Erreur lors de la mise à jour.", 'danger');
                    }
                }
                header('Location: ?action=categories_client');
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_categorie_client');
            $id = (int)$_GET['delete'];
            $categorie = $this->model->getById($id);
            if (!$categorie) {
                setFlash('Catégorie introuvable.', 'danger');
            } elseif (!$this->model->isDeletable($id)) {
                setFlash('Cette catégorie est utilisée par des clients.', 'danger');
            } elseif ($this->model->delete($id)) {
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'categorie_client', $id, $categorie, null);
                setFlash('Catégorie supprimée.', 'success');
            } else {
                setFlash('Erreur lors de la suppression.', 'danger');
            }
            header('Location: ?action=categories_client');
            exit;
        }

        $categories = $this->model->getAll();
        require __DIR__ . '/../views/structure/categories_client.php';
    }
}