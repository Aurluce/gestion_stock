<?php
require_once __DIR__ . '/../models/FamilleModel.php';

class FamilleController {
    private $model;
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new FamilleModel($pdo);
    }
    
    public function index() {
        checkRight('lister_familles');
        $familles = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/familles.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function create() {
        checkRight('creer_famille');
        ob_start();
        require __DIR__ . '/../views/structure/famille_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function store() {
        checkRight('creer_famille');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=familles');
            exit;
        }
        
        $nom = trim($_POST['nom_famille'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($nom)) {
            $_SESSION['error'] = "Le nom de la famille est requis.";
            header('Location: index.php?action=famille_creer');
            exit;
        }
        
        try {
            $this->model->create($nom, $description ?: null);
            $_SESSION['success'] = "Famille '{$nom}' créée.";
            header('Location: index.php?action=familles');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            header('Location: index.php?action=famille_creer');
            exit;
        }
    }
    
    public function edit() {
        checkRight('modifier_famille');
        $id = (int)($_GET['id'] ?? 0);
        $famille = $this->model->getById($id);
        if (!$famille) {
            $_SESSION['error'] = "Famille introuvable.";
            header('Location: index.php?action=familles');
            exit;
        }
        ob_start();
        require __DIR__ . '/../views/structure/famille_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function update() {
        checkRight('modifier_famille');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=familles');
            exit;
        }
        
        $id = (int)($_POST['id_famille'] ?? 0);
        $nom = trim($_POST['nom_famille'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($nom)) {
            $_SESSION['error'] = "Le nom est requis.";
            header("Location: index.php?action=famille_modifier&id=$id");
            exit;
        }
        
        $this->model->update($id, $nom, $description ?: null);
        $_SESSION['success'] = "Famille mise à jour.";
        header('Location: index.php?action=familles');
        exit;
    }
    
    public function delete() {
        checkRight('supprimer_famille');
        $id = (int)($_GET['id'] ?? 0);
        $famille = $this->model->getById($id);
        if (!$famille) {
            $_SESSION['error'] = "Famille introuvable.";
            header('Location: index.php?action=familles');
            exit;
        }
        
        $this->model->delete($id);
        $_SESSION['success'] = "Famille supprimée.";
        header('Location: index.php?action=familles');
        exit;
    }
}
