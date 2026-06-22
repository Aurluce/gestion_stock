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

    public function index(): void {
        checkRight('lister_banques');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                checkRight('creer_banque');
                $data = [
                    'nom_banque' => trim($_POST['nom_banque'] ?? ''),
                    'sigle' => trim($_POST['sigle'] ?? ''),
                    'responsable' => trim($_POST['responsable'] ?? ''),
                    'adresse' => trim($_POST['adresse'] ?? ''),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? '')
                ];
                if (empty($data['nom_banque'])) {
                    setFlash('Le nom de la banque est requis.', 'danger');
                } else {
                    $this->model->create($data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'banque', null, null, ['nom' => $data['nom_banque']]);
                    setFlash("Banque '{$data['nom_banque']}' créée.", 'success');
                }
                header('Location: ?action=banques');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                checkRight('modifier_banque');
                $id = (int)($_POST['id_banque'] ?? 0);
                $data = [
                    'nom_banque' => trim($_POST['nom_banque'] ?? ''),
                    'sigle' => trim($_POST['sigle'] ?? ''),
                    'responsable' => trim($_POST['responsable'] ?? ''),
                    'adresse' => trim($_POST['adresse'] ?? ''),
                    'tel' => trim($_POST['tel'] ?? ''),
                    'email' => trim($_POST['email'] ?? '')
                ];
                $old = $this->model->getById($id);
                if (empty($data['nom_banque'])) {
                    setFlash('Le nom de la banque est requis.', 'danger');
                } else {
                    $this->model->update($id, $data);
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'banque', $id, $old, ['nom' => $data['nom_banque']]);
                    setFlash('Banque mise à jour.', 'success');
                }
                header('Location: ?action=banques');
                exit;
            }
        }

        $banques = $this->model->getAll();
        ob_start();
        require __DIR__ . '/../views/structure/banques.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function versements(): void {
        checkRight('etat_versements_periode');

        $mouvementModel = new MouvementBanqueModel($this->pdo);
        $banques = $this->model->getForSelect();
        $idBanque = (int)($_GET['id_banque'] ?? 0);
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-01');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-t');
        $mouvements = [];
        $soldeInitial = 0;
        $soldeFinal = 0;
        $totalEntrees = 0;
        $totalSorties = 0;

        if ($idBanque > 0) {
            $mouvements = $mouvementModel->getByBanque($idBanque, $dateDebut, $dateFin);
            $soldeInitial = $mouvementModel->getSolde($idBanque, date('Y-m-d', strtotime($dateDebut . ' -1 day')));
            foreach ($mouvements as $m) {
                if ($m['type_mouvement'] == 'versement') $totalEntrees += $m['montant'];
                else $totalSorties += $m['montant'];
            }
            $soldeFinal = $soldeInitial + $totalEntrees - $totalSorties;
        }

        ob_start();
        require __DIR__ . '/../views/structure/banque_versements.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }

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
            setFlash('Banque et montant valide requis.', 'danger');
            header('Location: ?action=banque_versements');
            exit;
        }
        $mouvementModel->create($data);
        setFlash('Mouvement enregistré.', 'success');
        header('Location: ?action=banque_versements&id_banque=' . $data['id_banque']);
        exit;
    }

    public function deleteMouvement(): void {
        checkRight('supprimer_banque');
        $id = (int)($_GET['id'] ?? 0);
        $idBanque = (int)($_GET['id_banque'] ?? 0);
        $mouvementModel = new MouvementBanqueModel($this->pdo);
        $mouvementModel->delete($id);
        setFlash('Mouvement supprimé.', 'success');
        header('Location: ?action=banque_versements&id_banque=' . $idBanque);
        exit;
    }

    public function delete(): void {
        checkRight('supprimer_banque');
        $id = (int)($_GET['id'] ?? 0);
        $old = $this->model->getById($id);
        $this->model->delete($id);
        logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'banque', $id, $old, null);
        setFlash('Banque supprimée.', 'success');
        header('Location: ?action=banques');
        exit;
    }
}
