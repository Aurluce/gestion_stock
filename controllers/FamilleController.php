<?php
require_once __DIR__ . '/../models/FamilleModel.php';

class FamilleController {
    private $model;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new FamilleModel($pdo);
    }

    public function index() {
        checkRight('lister_familles');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_famille');
                $nom = trim($_POST['nom_famille'] ?? '');
                $description = trim($_POST['description'] ?? '');
                if (empty($nom)) {
                    setFlash('Le nom de la famille est requis.', 'danger');
                } else {
                    $this->model->create($nom, $description ?: null);
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'famille', null, null, ['nom' => $nom]);
                    setFlash("Famille '{$nom}' créée.", 'success');
                }
                header('Location: ?action=familles');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_famille');
                $id = (int)($_POST['id_famille'] ?? 0);
                $nom = trim($_POST['nom_famille'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $old = $this->model->getById($id);
                if (empty($nom)) {
                    setFlash('Le nom de la famille est requis.', 'danger');
                } else {
                    $this->model->update($id, $nom, $description ?: null);
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'famille', $id, $old, ['nom' => $nom]);
                    setFlash('Famille mise à jour.', 'success');
                }
                header('Location: ?action=familles');
                exit;
            }
        }

        $familles = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/familles.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function detail() {
        checkRight('lister_familles');
        $id = (int)($_GET['id'] ?? 0);
        $famille = $this->model->getById($id);
        if (!$famille) {
            header('Content-Type: application/json');
            echo json_encode(['html' => '<p class="text-danger-500 text-center py-8">Famille introuvable.</p>']);
            exit;
        }
        $nbProduits = $this->pdo->query("SELECT COUNT(*) FROM structure.produit WHERE id_famille = $id")->fetchColumn();
        ob_start();
        require __DIR__ . '/../views/structure/detail_famille.php';
        $html = ob_get_clean();
        header('Content-Type: application/json');
        echo json_encode(['html' => $html]);
        exit;
    }

    public function delete() {
        checkRight('supprimer_famille');
        $id = (int)($_GET['id'] ?? 0);
        $old = $this->model->getById($id);
        $this->model->delete($id);
        logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'famille', $id, $old, null);
        setFlash('Famille supprimée.', 'success');
        header('Location: ?action=familles');
        exit;
    }
}
