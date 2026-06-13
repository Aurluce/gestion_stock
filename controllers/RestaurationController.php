<?php
require_once __DIR__ . '/../models/RestaurationModel.php';

class RestaurationController {
    private RestaurationModel $model;
    
    public function __construct(PDO $pdo) {
        $this->model = new RestaurationModel($pdo);
    }
    
    public function index(): void {
        checkRight('restaurer_corbeille');
        
        $typeFiltre = $_GET['type'] ?? '';
        $search = $_GET['search'] ?? '';
        $elements = $this->model->getAll($typeFiltre, $search);
        $types = $this->model->getTypes();
        
        ob_start();
        require __DIR__ . '/../views/structure/restauration/index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function view(): void {
        checkRight('restaurer_corbeille');
        
        $id = (int)($_GET['id'] ?? 0);
        $element = $this->model->getById($id);
        
        if (!$element) {
            $_SESSION['error'] = "Élément introuvable dans la corbeille.";
            header('Location: index.php?action=restauration');
            exit;
        }
        
        ob_start();
        require __DIR__ . '/../views/structure/restauration/view.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    public function restore(): void {
        checkRight('restaurer_corbeille');
        
        $id = (int)($_GET['id'] ?? 0);
        $result = $this->model->restore($id);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: index.php?action=restauration');
        exit;
    }
    
    public function delete(): void {
        checkRight('vider_corbeille');
        
        $id = (int)($_GET['id'] ?? 0);
        $this->model->deletePermanently($id);
        $_SESSION['success'] = "Élément supprimé définitivement.";
        
        header('Location: index.php?action=restauration');
        exit;
    }
    
    public function clear(): void {
        checkRight('vider_corbeille');
        
        $type = $_GET['type'] ?? '';
        $count = $this->model->clearAll($type);
        
        if ($type) {
            $_SESSION['success'] = "$count élément(s) de type '$type' supprimé(s) de la corbeille.";
        } else {
            $_SESSION['success'] = "$count élément(s) supprimé(s) de la corbeille.";
        }
        
        header('Location: index.php?action=restauration');
        exit;
    }
}