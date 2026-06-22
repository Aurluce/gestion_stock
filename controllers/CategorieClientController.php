<?php
require_once __DIR__ . '/../models/CategorieClientModel.php';

class CategorieClientController {
    private CategorieClientModel $model;

    public function __construct(PDO $pdo) {
        $this->model = new CategorieClientModel($pdo);
    }

    public function index(): void {
        checkRight('lister_categories_client');
        $categories = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/categorie_clients.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function create(): void {
        checkRight('creer_categorie_client');
        ob_start();
        require __DIR__ . '/../views/structure/categorie_client_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function store(): void {
        checkRight('creer_categorie_client');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=categorie_clients');
            exit;
        }

        $nom = trim($_POST['nom_categorie'] ?? '');
        $tauxRemise = isset($_POST['taux_remise']) ? (float)$_POST['taux_remise'] : 0.0;
        $description = trim($_POST['description'] ?? '');

        $errors = [];
        if (empty($nom)) {
            $errors['nom_categorie'] = "Le nom de la catégorie est requis.";
        }
        if ($tauxRemise < 0) {
            $errors['taux_remise'] = "Le taux de remise doit être positif ou nul.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?action=categorie_client_creer');
            exit;
        }

        $this->model->create($nom, $tauxRemise, $description !== '' ? $description : null);
        $_SESSION['success'] = "Catégorie client '{$nom}' créée.";
        header('Location: index.php?action=categorie_clients');
        exit;
    }

    public function edit(): void {
        checkRight('modifier_categorie_client');
        $id = (int)($_GET['id'] ?? 0);
        $categorie = $this->model->getById($id);
        if (!$categorie) {
            $_SESSION['error'] = "Catégorie client introuvable.";
            header('Location: index.php?action=categorie_clients');
            exit;
        }

        ob_start();
        require __DIR__ . '/../views/structure/categorie_client_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function update(): void {
        checkRight('modifier_categorie_client');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=categorie_clients');
            exit;
        }

        $id = (int)($_POST['id_categorie_client'] ?? 0);
        $nom = trim($_POST['nom_categorie'] ?? '');
        $tauxRemise = isset($_POST['taux_remise']) ? (float)$_POST['taux_remise'] : 0.0;
        $description = trim($_POST['description'] ?? '');

        $errors = [];
        if (empty($nom)) {
            $errors['nom_categorie'] = "Le nom de la catégorie est requis.";
        }
        if ($tauxRemise < 0) {
            $errors['taux_remise'] = "Le taux de remise doit être positif ou nul.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?action=categorie_client_modifier&id=' . $id);
            exit;
        }

        $this->model->update($id, $nom, $tauxRemise, $description !== '' ? $description : null);
        $_SESSION['success'] = "Catégorie client mise à jour.";
        header('Location: index.php?action=categorie_clients');
        exit;
    }

    public function delete(): void {
        checkRight('supprimer_categorie_client');
        $id = (int)($_GET['id'] ?? 0);
        $categorie = $this->model->getById($id);
        if (!$categorie) {
            $_SESSION['error'] = "Catégorie client introuvable.";
            header('Location: index.php?action=categorie_clients');
            exit;
        }

        $this->model->delete($id);
        $_SESSION['success'] = "Catégorie client supprimée.";
        header('Location: index.php?action=categorie_clients');
        exit;
    }
}
