<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CategorieClientModel.php';

class ClientController {
    private $pdo;
    private $model;
    private $categorieModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new ClientModel($pdo);
        $this->categorieModel = new CategorieClientModel($pdo);
    }

    public function index() {
        // Vérifier si c'est une demande d'impression
        if (isset($_GET['print'])) {
            $this->printList();
            return;
        }
        
        checkRight('lister_clients');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_client');
                $data = [
                    'id_categorie_client' => !empty($_POST['id_categorie_client']) ? (int)$_POST['id_categorie_client'] : null,
                    'nom' => trim($_POST['nom']),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'ville' => trim($_POST['ville'] ?? ''),
                    'est_actif' => isset($_POST['est_actif'])
                ];
                
                if (empty($data['nom'])) {
                    setFlash('Le nom est requis.', 'danger');
                } else {
                    $id = $this->model->create($data);
                    if ($id) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'client', $id, null, ['nom' => $data['nom']]);
                        setFlash("Client '{$data['nom']}' créé.", 'success');
                    } else {
                        setFlash("Erreur lors de la création.", 'danger');
                    }
                }
                header('Location: ?action=clients');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_client');
                $id = (int)$_POST['id_client'];
                $data = [
                    'id_categorie_client' => !empty($_POST['id_categorie_client']) ? (int)$_POST['id_categorie_client'] : null,
                    'nom' => trim($_POST['nom']),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'ville' => trim($_POST['ville'] ?? ''),
                    'est_actif' => isset($_POST['est_actif'])
                ];
                
                if (empty($data['nom'])) {
                    setFlash('Le nom est requis.', 'danger');
                } else {
                    $old = $this->model->getById($id);
                    $result = $this->model->update($id, $data);
                    if ($result) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'client', $id, $old, ['nom' => $data['nom']]);
                        setFlash("Client mis à jour.", 'success');
                    } else {
                        setFlash("Erreur lors de la mise à jour.", 'danger');
                    }
                }
                header('Location: ?action=clients');
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            checkRight('supprimer_client');
            $id = (int)$_GET['delete'];
            $client = $this->model->getById($id);
            if (!$client) {
                setFlash('Client introuvable.', 'danger');
            } else {
                $result = $this->model->delete($id);
                if ($result) {
                    logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'client', $id, $client, null);
                    setFlash('Client supprimé.', 'success');
                } else {
                    setFlash('Erreur lors de la suppression.', 'danger');
                }
            }
            header('Location: ?action=clients');
            exit;
        }

        $clients = $this->model->getAll();
        $categories = $this->categorieModel->getForSelect();
        require __DIR__ . '/../views/structure/clients.php';
    }

    private function printList() {
        checkRight('lister_clients');
        $clients = $this->model->getAll();
        require __DIR__ . '/../views/structure/prints/clients.php';
        exit;
    }
}