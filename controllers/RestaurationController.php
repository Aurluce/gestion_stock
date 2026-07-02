<?php
require_once __DIR__ . '/../models/RestaurationModel.php';

class RestaurationController {
    private RestaurationModel $model;
    
    public function __construct(PDO $pdo) {
        $userId = $_SESSION['user_id'] ?? 1;
        $this->model = new RestaurationModel($pdo, $userId);
    }
    
    public function index(): void {
        checkRight('restaurer_corbeille');
        
        $typeFiltre = $_GET['type'] ?? '';
        $search = $_GET['search'] ?? '';
        $elements = $this->model->getAll($typeFiltre, $search);
        $types = $this->model->getTypes();
        
        // Pre-parse XML to extract display name for each element
        $elements = array_map(function($e) {
            $data = $this->model->parseXml($e['donnees_xml']);
            $e['nom'] = '';
            if (!empty($data)) {
                $e['nom'] = $data['nom'] ?? $data['nom_produit'] ?? $data['reference'] ?? '';
            }
            return $e;
        }, $elements);
        
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
            setFlash('Élément introuvable dans la corbeille.', 'danger');
            header('Location: index.php?action=restauration');
            exit;
        }
        
        $parsedData = $this->model->parseXml($element['donnees_xml']) ?? [];
        
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
            setFlash($result['message'], 'success');
        } else {
            setFlash($result['message'], 'danger');
        }
        
        header('Location: index.php?action=restauration');
        exit;
    }
    
    public function delete(): void {
        checkRight('vider_corbeille');
        
        $id = (int)($_GET['id'] ?? 0);
        $this->model->deletePermanently($id);
        setFlash('Élément supprimé définitivement.', 'success');
        
        header('Location: index.php?action=restauration');
        exit;
    }
    
    public function clear(): void {
        checkRight('vider_corbeille');
        
        $type = $_GET['type'] ?? '';
        $count = $this->model->clearAll($type);
        
        if ($type) {
            setFlash("$count élément(s) de type '$type' supprimé(s) de la corbeille.", 'success');
        } else {
            setFlash("$count élément(s) supprimé(s) de la corbeille.", 'success');
        }
        
        header('Location: index.php?action=restauration');
        exit;
    }
}