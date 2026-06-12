<?php
require_once __DIR__ . '/../models/FournisseurModel.php';

class FournisseurController {
    private FournisseurModel $model;
    
    public function __construct(PDO $pdo) {
        $this->model = new FournisseurModel($pdo);
    }
    
    public function index(): void {
        checkRight('lister_fournisseurs');
        $fournisseurs = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/fournisseurs.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function create(): void {
        checkRight('creer_fournisseur');
        ob_start();
        require __DIR__ . '/../views/structure/fournisseur_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function store(): void {
        checkRight('creer_fournisseur');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=fournisseurs');
            exit;
        }
        
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'tel' => trim($_POST['tel'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'ville' => trim($_POST['ville'] ?? ''),
            'nif' => trim($_POST['nif'] ?? ''),
            'est_actif' => isset($_POST['est_actif'])
        ];
        
        if (empty($data['nom'])) {
            $_SESSION['error'] = "Le nom est requis.";
            header('Location: index.php?action=fournisseur_creer');
            exit;
        }
        
        $this->model->create($data);
        $_SESSION['success'] = "Fournisseur '{$data['nom']}' créé.";
        header('Location: index.php?action=fournisseurs');
        exit;
    }
    
    public function edit(): void {
        checkRight('modifier_fournisseur');
        $id = (int)($_GET['id'] ?? 0);
        $fournisseur = $this->model->getById($id);
        if (!$fournisseur) {
            $_SESSION['error'] = "Fournisseur introuvable.";
            header('Location: index.php?action=fournisseurs');
            exit;
        }
        ob_start();
        require __DIR__ . '/../views/structure/fournisseur_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function update(): void {
        checkRight('modifier_fournisseur');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=fournisseurs');
            exit;
        }
        
        $id = (int)($_POST['id_fournisseur'] ?? 0);
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'tel' => trim($_POST['tel'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'ville' => trim($_POST['ville'] ?? ''),
            'nif' => trim($_POST['nif'] ?? ''),
            'est_actif' => isset($_POST['est_actif'])
        ];
        
        $this->model->update($id, $data);
        $_SESSION['success'] = "Fournisseur mis à jour.";
        header('Location: index.php?action=fournisseurs');
        exit;
    }
    
    public function delete(): void {
        checkRight('supprimer_fournisseur');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        $_SESSION['success'] = "Fournisseur supprimé.";
        header('Location: index.php?action=fournisseurs');
        exit;
    }
    
    public function disable(): void {
        checkRight('modifier_fournisseur');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->disable($id);
        $_SESSION['success'] = "Fournisseur désactivé.";
        header('Location: index.php?action=fournisseurs');
        exit;
    }
    
    public function enable(): void {
        checkRight('modifier_fournisseur');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->enable($id);
        $_SESSION['success'] = "Fournisseur activé.";
        header('Location: index.php?action=fournisseurs');
        exit;
    }
}