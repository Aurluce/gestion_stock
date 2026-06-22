<?php
require_once __DIR__ . '/../models/CategorieClientModel.php';

class CategorieClientController {
    private CategorieClientModel $model;
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->model = new CategorieClientModel($pdo);
    }

    public function index(): void {
        checkRight('lister_categories_client');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_categorie_client');
                $nom = trim($_POST['nom_categorie'] ?? '');
                $taux = isset($_POST['taux_remise']) ? (float)$_POST['taux_remise'] : 0;
                $description = trim($_POST['description'] ?? '');
                if (empty($nom)) {
                    setFlash('Le nom de la catégorie est requis.', 'danger');
                } else {
                    $this->model->create(['nom_categorie' => $nom, 'taux_remise' => $taux, 'description' => $description ?: null]);
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'categorie_client', null, null, ['nom' => $nom]);
                    setFlash("Catégorie client '{$nom}' créée.", 'success');
                }
                header('Location: ?action=categorie_clients');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_categorie_client');
                $id = (int)($_POST['id_categorie_client'] ?? 0);
                $nom = trim($_POST['nom_categorie'] ?? '');
                $taux = isset($_POST['taux_remise']) ? (float)$_POST['taux_remise'] : 0;
                $description = trim($_POST['description'] ?? '');
                $old = $this->model->getById($id);
                if (empty($nom)) {
                    setFlash('Le nom de la catégorie est requis.', 'danger');
                } else {
                    $this->model->update($id, ['nom_categorie' => $nom, 'taux_remise' => $taux, 'description' => $description ?: null]);
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'categorie_client', $id, $old, ['nom' => $nom]);
                    setFlash('Catégorie client mise à jour.', 'success');
                }
                header('Location: ?action=categorie_clients');
                exit;
            }
        }

        $categories = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/categorie_clients.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function delete(): void {
        checkRight('supprimer_categorie_client');
        $id = (int)($_GET['id'] ?? 0);
        $old = $this->model->getById($id);
        if ($this->model->isDeletable($id)) {
            $this->model->delete($id);
            logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'categorie_client', $id, $old, null);
            setFlash('Catégorie client supprimée.', 'success');
        } else {
            setFlash('Impossible de supprimer : des clients sont liés à cette catégorie.', 'danger');
        }
        header('Location: ?action=categorie_clients');
        exit;
    }
}
