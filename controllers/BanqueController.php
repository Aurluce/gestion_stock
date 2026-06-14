<?php
require_once __DIR__ . '/../models/BanqueModel.php';
require_once __DIR__ . '/../models/MouvementBanqueModel.php';

class BanqueController {
    private $pdo;
    private $model;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new BanqueModel($pdo);
    }

    public function index() {
        // Vérifier si c'est une demande d'impression
        if (isset($_GET['print']) && empty($_GET['id_banque'])) {
            $this->printList();
            return;
        }
        
        checkRight('lister_banques');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_banque');
                $data = [
                    'nom_banque' => trim($_POST['nom_banque']),
                    'sigle' => trim($_POST['sigle'] ?? ''),
                    'responsable' => trim($_POST['responsable'] ?? ''),
                    'adresse' => trim($_POST['adresse'] ?? ''),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? '')
                ];
                
                if (empty($data['nom_banque'])) {
                    setFlash('Le nom de la banque est requis.', 'danger');
                } else {
                    $id = $this->model->create($data);
                    if ($id) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'banque', $id, null, ['nom' => $data['nom_banque']]);
                        setFlash("Banque '{$data['nom_banque']}' créée.", 'success');
                    } else {
                        setFlash("Erreur lors de la création.", 'danger');
                    }
                }
                header('Location: ?action=banques');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_banque');
                $id = (int)$_POST['id_banque'];
                $data = [
                    'nom_banque' => trim($_POST['nom_banque']),
                    'sigle' => trim($_POST['sigle'] ?? ''),
                    'responsable' => trim($_POST['responsable'] ?? ''),
                    'adresse' => trim($_POST['adresse'] ?? ''),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? '')
                ];
                
                if (empty($data['nom_banque'])) {
                    setFlash('Le nom de la banque est requis.', 'danger');
                } else {
                    $old = $this->model->getById($id);
                    $result = $this->model->update($id, $data);
                    if ($result) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'banque', $id, $old, ['nom' => $data['nom_banque']]);
                        setFlash("Banque mise à jour.", 'success');
                    } else {
                        setFlash("Erreur lors de la mise à jour.", 'danger');
                    }
                }
                header('Location: ?action=banques');
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_banque');
            $id = (int)$_GET['delete'];
            $banque = $this->model->getById($id);
            if (!$banque) {
                setFlash('Banque introuvable.', 'danger');
            } else {
                $result = $this->model->delete($id);
                if ($result) {
                    logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'banque', $id, $banque, null);
                    setFlash('Banque supprimée.', 'success');
                } else {
                    setFlash('Erreur lors de la suppression.', 'danger');
                }
            }
            header('Location: ?action=banques');
            exit;
        }

        $banques = $this->model->getAll();
        require __DIR__ . '/../views/structure/banques.php';
    }

    public function versements() {
        // Vérifier si c'est une demande d'impression
        if (isset($_GET['print'])) {
            $this->printMouvements();
            return;
        }
        
        checkRight('etat_versements_periode');
        
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        $banques = $this->model->getForSelect();
        
        $idBanque = (int)($_GET['id_banque'] ?? 0);
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-01');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-t');
        $mouvements = [];
        $soldeInitial = 0;
        $totalEntrees = 0;
        $totalSorties = 0;
        $soldeFinal = 0;
        
        if ($idBanque > 0) {
            $mouvements = $mouvementModel->getByBanque($idBanque, $dateDebut, $dateFin);
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
        
        require __DIR__ . '/../views/structure/banque_versements.php';
    }

    public function storeMouvement() {
        checkRight('creer_mouvement_banque');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=banque_versements');
            exit;
        }
        
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        
        $idBanque = (int)($_POST['id_banque'] ?? 0);
        $typeMouvement = $_POST['type_mouvement'] ?? 'versement';
        $montant = (float)($_POST['montant'] ?? 0);
        
        if ($idBanque <= 0 || $montant <= 0) {
            setFlash('Banque et montant valide requis.', 'danger');
            header('Location: ?action=banque_versements&id_banque=' . $idBanque);
            exit;
        }
        
        $data = [
            'id_banque' => $idBanque,
            'type_mouvement' => $typeMouvement,
            'montant' => $montant,
            'date_mouvement' => $_POST['date_mouvement'] ?? date('Y-m-d'),
            'reference' => trim($_POST['reference'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        $id = $mouvementModel->create($data);
        if ($id) {
            logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'mouvement_banque', $id, null, ['montant' => $montant, 'type' => $typeMouvement]);
            setFlash('Mouvement enregistré avec succès.', 'success');
        } else {
            setFlash('Erreur lors de l\'enregistrement.', 'danger');
        }
        
        header('Location: ?action=banque_versements&id_banque=' . $idBanque);
        exit;
    }
    
    public function deleteMouvement() {
        checkRight('supprimer_banque');
        
        $id = (int)($_GET['id'] ?? 0);
        $idBanque = (int)($_GET['id_banque'] ?? 0);
        
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        $mouvementModel->delete($id);
        
        logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'mouvement_banque', $id, null, null);
        setFlash('Mouvement supprimé.', 'success');
        
        header('Location: ?action=banque_versements&id_banque=' . $idBanque);
        exit;
    }

    private function printList() {
        checkRight('lister_banques');
        $banques = $this->model->getAll();
        require __DIR__ . '/../views/structure/prints/banques.php';
        exit;
    }

    private function printMouvements() {
        checkRight('etat_versements_periode');
        
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        
        $idBanque = (int)($_GET['id_banque'] ?? 0);
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-01');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-t');
        
        $banque = $this->model->getById($idBanque);
        $nomBanque = $banque['nom_banque'] ?? '';
        $mouvements = $mouvementModel->getByBanque($idBanque, $dateDebut, $dateFin);
        $soldeInitial = $mouvementModel->getSolde($idBanque, date('Y-m-d', strtotime($dateDebut . ' -1 day')));
        
        $totalEntrees = 0;
        $totalSorties = 0;
        foreach ($mouvements as $m) {
            if ($m['type_mouvement'] == 'versement') {
                $totalEntrees += $m['montant'];
            } else {
                $totalSorties += $m['montant'];
            }
        }
        $soldeFinal = $soldeInitial + $totalEntrees - $totalSorties;
        
        require __DIR__ . '/../views/structure/prints/mouvements_banque.php';
        exit;
    }
}