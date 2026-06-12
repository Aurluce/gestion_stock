<?php
require_once __DIR__ . '/../models/BanqueModel.php';
require_once __DIR__ . '/../models/MouvementBanqueModel.php';

class BanqueController {
    private BanqueModel $model;
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->model = new BanqueModel($pdo);
    }
    
    /**
     * Liste des banques
     */
    public function index(): void {
        checkRight('lister_banques');
        $banques = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/banques.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    /**
     * Formulaire de création
     */
    public function create(): void {
        checkRight('creer_banque');
        ob_start();
        require __DIR__ . '/../views/structure/banque_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    /**
     * Enregistrement d'une nouvelle banque
     */
    public function store(): void {
        checkRight('creer_banque');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=banques');
            exit;
        }
        
        $data = [
            'nom_banque' => trim($_POST['nom_banque'] ?? ''),
            'sigle' => trim($_POST['sigle'] ?? ''),
            'responsable' => trim($_POST['responsable'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'tel' => trim($_POST['tel'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];
        
        if (empty($data['nom_banque'])) {
            $_SESSION['error'] = "Le nom de la banque est requis.";
            header('Location: index.php?action=banque_creer');
            exit;
        }
        
        $this->model->create($data);
        $_SESSION['success'] = "Banque '{$data['nom_banque']}' créée.";
        header('Location: index.php?action=banques');
        exit;
    }
    
    /**
     * Formulaire de modification
     */
    public function edit(): void {
        checkRight('modifier_banque');
        $id = (int)($_GET['id'] ?? 0);
        $banque = $this->model->getById($id);
        if (!$banque) {
            $_SESSION['error'] = "Banque introuvable.";
            header('Location: index.php?action=banques');
            exit;
        }
        ob_start();
        require __DIR__ . '/../views/structure/banque_form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    /**
     * Mise à jour d'une banque
     */
    public function update(): void {
        checkRight('modifier_banque');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=banques');
            exit;
        }
        
        $id = (int)($_POST['id_banque'] ?? 0);
        $data = [
            'nom_banque' => trim($_POST['nom_banque'] ?? ''),
            'sigle' => trim($_POST['sigle'] ?? ''),
            'responsable' => trim($_POST['responsable'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'tel' => trim($_POST['tel'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];
        
        $this->model->update($id, $data);
        $_SESSION['success'] = "Banque mise à jour.";
        header('Location: index.php?action=banques');
        exit;
    }
    
    /**
     * Suppression d'une banque
     */
    public function delete(): void {
        checkRight('supprimer_banque');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        $_SESSION['success'] = "Banque supprimée.";
        header('Location: index.php?action=banques');
        exit;
    }
    
    /**
     * État des versements bancaires par période
     */
    public function versements(): void {
        checkRight('etat_versements_periode');
        
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        $banques = $this->model->getForSelect();
        
        $idBanque = (int)($_GET['id_banque'] ?? 0);
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-01');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-t');
        $mouvements = [];
        $resumeParJour = [];
        $soldeInitial = 0;
        $soldeFinal = 0;
        $totalEntrees = 0;
        $totalSorties = 0;
        $nomBanque = '';
        
        if ($idBanque > 0) {
            $banque = $this->model->getById($idBanque);
            $nomBanque = $banque['nom_banque'] ?? '';
            $mouvements = $mouvementModel->getByBanque($idBanque, $dateDebut, $dateFin);
            $resumeParJour = $mouvementModel->getResumeParJour($idBanque, $dateDebut, $dateFin);
            $soldeInitial = $mouvementModel->getSolde($idBanque, date('Y-m-d', strtotime($dateDebut . ' -1 day')));
            
            foreach ($mouvements as $m) {
                if ($m['type_mouvement'] == 'versement') {
                    $totalEntrees += $m['montant'];
                } else {
                    $totalSorties += $m['montant'];
                }
            }
            $soldeFinal = $soldeInitial + $totalEntrees - $totalSorties;
        }
        
        ob_start();
        require __DIR__ . '/../views/structure/banque_versements.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    /**
     * Enregistre un nouveau mouvement bancaire
     */
    public function storeMouvement(): void {
        checkRight('creer_mouvement_banque');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=banques');
            exit;
        }
        
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        
        $data = [
            'id_banque' => (int)($_POST['id_banque'] ?? 0),
            'type_mouvement' => $_POST['type_mouvement'] ?? 'versement',
            'montant' => floatval($_POST['montant'] ?? 0),
            'date_mouvement' => $_POST['date_mouvement'] ?? date('Y-m-d'),
            'reference' => trim($_POST['reference'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        if ($data['id_banque'] <= 0 || $data['montant'] <= 0) {
            $_SESSION['error'] = "Banque et montant valide requis.";
            header('Location: index.php?action=banque_versements');
            exit;
        }
        
        $mouvementModel->create($data);
        $_SESSION['success'] = "Mouvement enregistré avec succès.";
        header('Location: index.php?action=banque_versements&id_banque=' . $data['id_banque']);
        exit;
    }
    
    /**
     * Supprime un mouvement bancaire
     */
    public function deleteMouvement(): void {
        checkRight('supprimer_banque');
        
        $id = (int)($_GET['id'] ?? 0);
        $idBanque = (int)($_GET['id_banque'] ?? 0);
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        $mouvementModel->delete($id);
        
        $_SESSION['success'] = "Mouvement supprimé.";
        header('Location: index.php?action=banque_versements&id_banque=' . $idBanque);
        exit;
    }
}
