<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CategorieClientModel.php';

class ClientController {
    private ClientModel $model;
    private CategorieClientModel $categorieModel;
    
    public function __construct(PDO $pdo) {
        $this->model = new ClientModel($pdo);
        $this->categorieModel = new CategorieClientModel($pdo);
    }
    
    public function index(): void {
        checkRight('lister_clients');
        $clients = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/clients.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function create(): void {
        checkRight('creer_client');
        $categories = $this->categorieModel->getForSelect();
        ob_start();
        require __DIR__ . '/../views/structure/client_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function store(): void {
        checkRight('creer_client');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=clients');
            exit;
        }
        
        $data = [
            'id_categorie_client' => !empty($_POST['id_categorie_client']) ? (int)$_POST['id_categorie_client'] : null,
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'tel' => trim($_POST['tel'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'ville' => trim($_POST['ville'] ?? ''),
            'type_client' => $_POST['type_client'] ?? 'particulier',
            'est_actif' => isset($_POST['est_actif'])
        ];
        
        if (empty($data['nom'])) {
            $_SESSION['error'] = "Le nom est requis.";
            header('Location: index.php?action=client_creer');
            exit;
        }
        
        $this->model->create($data);
        $_SESSION['success'] = "Client '{$data['nom']}' créé.";
        header('Location: index.php?action=clients');
        exit;
    }
    
    public function edit(): void {
        checkRight('modifier_client');
        $id = (int)($_GET['id'] ?? 0);
        $client = $this->model->getById($id);
        if (!$client) {
            $_SESSION['error'] = "Client introuvable.";
            header('Location: index.php?action=clients');
            exit;
        }
        $categories = $this->categorieModel->getForSelect();
        ob_start();
        require __DIR__ . '/../views/structure/client_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function update(): void {
        checkRight('modifier_client');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=clients');
            exit;
        }
        
        $id = (int)($_POST['id_client'] ?? 0);
        $data = [
            'id_categorie_client' => !empty($_POST['id_categorie_client']) ? (int)$_POST['id_categorie_client'] : null,
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'tel' => trim($_POST['tel'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'ville' => trim($_POST['ville'] ?? ''),
            'type_client' => $_POST['type_client'] ?? 'particulier',
            'est_actif' => isset($_POST['est_actif'])
        ];
        
        $this->model->update($id, $data);
        $_SESSION['success'] = "Client mis à jour.";
        header('Location: index.php?action=clients');
        exit;
    }
    
    public function delete(): void {
        checkRight('supprimer_client');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        $_SESSION['success'] = "Client supprimé.";
        header('Location: index.php?action=clients');
        exit;
    }
    
    public function voirCredit(): void {
        checkRight('voir_credit_client');
        $id = (int)($_GET['id'] ?? 0);
        $client = $this->model->getById($id);
        if (!$client) {
            $_SESSION['error'] = "Client introuvable.";
            header('Location: index.php?action=clients');
            exit;
        }
        ob_start();
        require __DIR__ . '/../views/structure/client_credit.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
