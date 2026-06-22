<?php
require_once __DIR__ . '/../models/FournisseurModel.php';

class FournisseurController {
    private FournisseurModel $model;
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->model = new FournisseurModel($pdo);
    }

    public function index(): void {
        checkRight('lister_fournisseurs');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_fournisseur');
                $data = [
                    'nom' => trim($_POST['nom'] ?? ''),
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
                    $this->model->create($data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'fournisseur', null, null, ['nom' => $data['nom']]);
                    setFlash("Fournisseur '{$data['nom']}' créé.", 'success');
                }
                header('Location: ?action=fournisseurs');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_fournisseur');
                $id = (int)($_POST['id_fournisseur'] ?? 0);
                $data = [
                    'nom' => trim($_POST['nom'] ?? ''),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'adresse' => trim($_POST['adresse'] ?? ''),
                    'ville' => trim($_POST['ville'] ?? ''),
                    'nif' => trim($_POST['nif'] ?? ''),
                    'est_actif' => isset($_POST['est_actif'])
                ];
                $old = $this->model->getById($id);
                if (empty($data['nom'])) {
                    setFlash('Le nom est requis.', 'danger');
                } else {
                    $this->model->update($id, $data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'fournisseur', $id, $old, ['nom' => $data['nom']]);
                    setFlash('Fournisseur mis à jour.', 'success');
                }
                header('Location: ?action=fournisseurs');
                exit;
            }
        }

        $fournisseurs = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/fournisseurs.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function detail(): void {
        checkRight('lister_fournisseurs');
        $id = (int)($_GET['id'] ?? 0);
        $fournisseur = $this->model->getById($id);
        if (!$fournisseur) {
            header('Content-Type: application/json');
            echo json_encode(['html' => '<p class="text-danger-500 text-center py-8">Fournisseur introuvable.</p>']);
            exit;
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM approvisionnement.bon_commande_fourn WHERE id_fournisseur = ?");
        $stmt->execute([$id]);
        $nbCommandes = $stmt->fetchColumn();
        ob_start();
        require __DIR__ . '/../views/structure/detail_fournisseur.php';
        $html = ob_get_clean();
        header('Content-Type: application/json');
        echo json_encode(['html' => $html]);
        exit;
    }

    public function delete(): void {
        checkRight('supprimer_fournisseur');
        $id = (int)($_GET['id'] ?? 0);
        $old = $this->model->getById($id);
        $this->model->delete($id);
        logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'fournisseur', $id, $old, null);
        setFlash('Fournisseur supprimé.', 'success');
        header('Location: ?action=fournisseurs');
        exit;
    }

    public function disable(): void {
        checkRight('modifier_fournisseur');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->disable($id);
        setFlash('Fournisseur désactivé.', 'success');
        header('Location: ?action=fournisseurs');
        exit;
    }

    public function enable(): void {
        checkRight('modifier_fournisseur');
        $id = (int)($_GET['id'] ?? 0);
        $this->model->enable($id);
        setFlash('Fournisseur activé.', 'success');
        header('Location: ?action=fournisseurs');
        exit;
    }
}
