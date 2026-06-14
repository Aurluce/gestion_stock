<?php
require_once __DIR__ . '/../models/FamilleModel.php';

class FamilleController {
    private $pdo;
    private $model;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new FamilleModel($pdo);
    }

    public function index() {
        // Vérifier si c'est une demande d'impression
        if (isset($_GET['print'])) {
            $this->printList();
            return;
        }
        
        checkRight('lister_familles');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_famille');
                $nom = trim($_POST['nom_famille'] ?? '');
                $description = trim($_POST['description'] ?? '');
                
                if (empty($nom)) {
                    setFlash('Le nom de la famille est requis.', 'danger');
                } else {
                    $id = $this->model->create($nom, $description ?: null);
                    if ($id) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'famille', $id, null, ['nom' => $nom]);
                        setFlash("Famille '$nom' créée.", 'success');
                    } else {
                        setFlash("Erreur lors de la création.", 'danger');
                    }
                }
                header('Location: ?action=familles');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_famille');
                $id = (int)$_POST['id_famille'];
                $nom = trim($_POST['nom_famille'] ?? '');
                $description = trim($_POST['description'] ?? '');
                
                if (empty($nom)) {
                    setFlash('Le nom de la famille est requis.', 'danger');
                } else {
                    $old = $this->model->getById($id);
                    if ($this->model->update($id, $nom, $description ?: null)) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'famille', $id, $old, ['nom' => $nom]);
                        setFlash("Famille '$nom' mise à jour.", 'success');
                    } else {
                        setFlash("Erreur lors de la mise à jour.", 'danger');
                    }
                }
                header('Location: ?action=familles');
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_famille');
            $id = (int)$_GET['delete'];
            $famille = $this->model->getById($id);
            if (!$famille) {
                setFlash('Famille introuvable.', 'danger');
            } elseif (!$this->model->isDeletable($id)) {
                setFlash('Cette famille contient des produits.', 'danger');
            } elseif ($this->model->delete($id)) {
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'famille', $id, $famille, null);
                setFlash('Famille supprimée.', 'success');
            } else {
                setFlash('Erreur lors de la suppression.', 'danger');
            }
            header('Location: ?action=familles');
            exit;
        }

        $familles = $this->model->getAll();
        require __DIR__ . '/../views/structure/familles.php';
    }

    private function printList() {
        checkRight('lister_familles');
        $familles = $this->model->getAll();
        require __DIR__ . '/../views/structure/prints/familles.php';
        exit;
    }
}