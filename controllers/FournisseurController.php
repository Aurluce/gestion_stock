<?php
require_once __DIR__ . '/../models/FournisseurModel.php';

class FournisseurController {
    private $pdo;
    private $model;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new FournisseurModel($pdo);
    }

    public function index() {
        // Vérifier si c'est une demande d'impression
        if (isset($_GET['print'])) {
            $this->printList();
            return;
        }
        
        checkRight('lister_fournisseurs');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_fournisseur');
                $data = [
                    'nom' => trim($_POST['nom']),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'adresse' => trim($_POST['adresse'] ?? ''),
                    'ville' => trim($_POST['ville'] ?? ''),
                    'nif' => trim($_POST['nif'] ?? ''),
                    'est_actif' => isset($_POST['est_actif'])
                ];
                
                if (empty($data['nom'])) {
                    setFlash('Le nom est requis.', 'danger');
                } else {
                    $id = $this->model->create($data);
                    if ($id) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'fournisseur', $id, null, ['nom' => $data['nom']]);
                        setFlash("Fournisseur '{$data['nom']}' créé.", 'success');
                    } else {
                        setFlash("Erreur lors de la création.", 'danger');
                    }
                }
                header('Location: ?action=fournisseurs');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_fournisseur');
                $id = (int)$_POST['id_fournisseur'];
                $data = [
                    'nom' => trim($_POST['nom']),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'adresse' => trim($_POST['adresse'] ?? ''),
                    'ville' => trim($_POST['ville'] ?? ''),
                    'nif' => trim($_POST['nif'] ?? ''),
                    'est_actif' => isset($_POST['est_actif'])
                ];
                
                if (empty($data['nom'])) {
                    setFlash('Le nom est requis.', 'danger');
                } else {
                    $old = $this->model->getById($id);
                    $result = $this->model->update($id, $data);
                    if ($result) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'fournisseur', $id, $old, ['nom' => $data['nom']]);
                        setFlash("Fournisseur mis à jour.", 'success');
                    } else {
                        setFlash("Erreur lors de la mise à jour.", 'danger');
                    }
                }
                header('Location: ?action=fournisseurs');
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_fournisseur');
            $id = (int)$_GET['delete'];
            $fournisseur = $this->model->getById($id);
            if (!$fournisseur) {
                setFlash('Fournisseur introuvable.', 'danger');
            } else {
                $result = $this->model->delete($id);
                if ($result) {
                    logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'fournisseur', $id, $fournisseur, null);
                    setFlash('Fournisseur supprimé.', 'success');
                } else {
                    setFlash('Erreur lors de la suppression.', 'danger');
                }
            }
            header('Location: ?action=fournisseurs');
            exit;
        }

        if (isset($_GET['disable'])) {
            checkRight('modifier_fournisseur');
            $id = (int)$_GET['disable'];
            $this->model->disable($id);
            setFlash('Fournisseur désactivé.', 'success');
            header('Location: ?action=fournisseurs');
            exit;
        }

        if (isset($_GET['enable'])) {
            checkRight('modifier_fournisseur');
            $id = (int)$_GET['enable'];
            $this->model->enable($id);
            setFlash('Fournisseur activé.', 'success');
            header('Location: ?action=fournisseurs');
            exit;
        }

        $fournisseurs = $this->model->getAll();
        require __DIR__ . '/../views/structure/fournisseurs.php';
    }

    private function printList() {
        checkRight('lister_fournisseurs');
        $fournisseurs = $this->model->getAll();
        require __DIR__ . '/../views/structure/prints/fournisseurs.php';
        exit;
    }
}