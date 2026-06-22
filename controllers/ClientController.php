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
                    'type_client' => $_POST['type_client'] ?? 'particulier',
                    'est_actif' => isset($_POST['est_actif'])
                ];
                if (empty($data['nom'])) {
                    setFlash('Le nom est requis.', 'danger');
                } else {
                    $id = $this->model->create($data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'client', $id, null, ['nom' => $data['nom']]);
                    setFlash("Client '{$data['nom']}' créé.", 'success');
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
                    'type_client' => $_POST['type_client'] ?? 'particulier',
                    'est_actif' => isset($_POST['est_actif'])
                ];
                $old = $this->model->getById($id);
                if (empty($data['nom'])) {
                    setFlash('Le nom est requis.', 'danger');
                } else {
                    $this->model->update($id, $data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'client', $id, $old, ['nom' => $data['nom']]);
                    setFlash('Client mis à jour.', 'success');
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
                $this->model->delete($id);
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'client', $id, $client, null);
                setFlash('Client supprimé.', 'success');
            }
            header('Location: ?action=clients');
            exit;
        }

        $clients = $this->model->getAll();
        $categories = $this->categorieModel->getForSelect();
        ob_start();
        require __DIR__ . '/../views/structure/clients.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function detail(): void {
        checkRight('lister_clients');
        $id = (int)($_GET['id'] ?? 0);
        $client = $this->model->getById($id);
        if (!$client) {
            header('Content-Type: application/json');
            echo json_encode(['html' => '<p class="text-danger-500 text-center py-8">Client introuvable.</p>']);
            exit;
        }
        $creditClient = $client['solde_credit'] ?? 0;
        $stmtCmd = $this->pdo->prepare("SELECT COUNT(*) FROM vente.commande_client WHERE id_client = ?");
        $stmtCmd->execute([$id]);
        $nbCommandes = $stmtCmd->fetchColumn();
        ob_start();
        require __DIR__ . '/../views/structure/detail_client.php';
        $html = ob_get_clean();
        header('Content-Type: application/json');
        echo json_encode(['html' => $html]);
        exit;
    }

    public function voirCredit(): void {
        checkRight('voir_credit_client');
        $id = (int)($_GET['id'] ?? 0);
        $client = $this->model->getById($id);
        if (!$client) {
            setFlash('Client introuvable.', 'danger');
            header('Location: ?action=clients');
            exit;
        }
        ob_start();
        require __DIR__ . '/../views/structure/client_credit.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    private function printList() {
        checkRight('lister_clients');
        $clients = $this->model->getAll();
        require __DIR__ . '/../views/structure/prints/clients.php';
        exit;
    }
}
