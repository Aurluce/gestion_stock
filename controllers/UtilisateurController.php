<?php
require_once __DIR__ . '/../models/UtilisateurModel.php';
require_once __DIR__ . '/../models/GroupeModel.php';
require_once __DIR__ . '/../models/DroitModel.php';
require_once __DIR__ . '/../models/JournalAuditModel.php';
require_once __DIR__ . '/../config/session.php';

class UtilisateurController {
    private $pdo, $userModel, $groupeModel, $droitModel, $journalModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new UtilisateurModel($pdo);
        $this->groupeModel = new GroupeModel($pdo);
        $this->droitModel = new DroitModel($pdo);
        $this->journalModel = new JournalAuditModel($pdo);
    }
    
    public function groupes() {
        checkRight('creer_groupe');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                if ($this->groupeModel->create(trim($_POST['nom_groupe']), trim($_POST['description']))) {
                    logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'groupe', $this->pdo->lastInsertId(), null, ['nom' => $_POST['nom_groupe']]);
                    setFlash('Groupe ajouté.', 'success');
                } else setFlash("Erreur lors de l'ajout.", 'danger');
            } elseif ($_POST['action'] === 'edit') {
                if ($this->groupeModel->update($_POST['id_groupe'], trim($_POST['nom_groupe']), trim($_POST['description']))) {
                    logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'groupe', $_POST['id_groupe'], null, ['nom' => $_POST['nom_groupe']]);
                    setFlash('Groupe modifié.', 'success');
                } else setFlash("Erreur lors de la modification.", 'danger');
            }
        } elseif (isset($_GET['delete'])) {
            if ($this->groupeModel->hasUsers($_GET['delete']))
                setFlash("Impossible de supprimer ce groupe car des utilisateurs y sont rattachés.", 'danger');
            elseif ($this->groupeModel->delete($_GET['delete'])) {
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'groupe', $_GET['delete'], null, null);
                setFlash('Groupe supprimé.', 'success');
            } else setFlash("Erreur lors de la suppression.", 'danger');
        }
        $groupes = $this->groupeModel->getAll();
        require __DIR__ . '/../views/utilisateur/groupes.php';
    }
    
    public function droits() {
        checkRight('affecter_droits');
        $droits = $this->droitModel->getAll();
        $modules = [];
        foreach ($droits as $d) $modules[$d['module']][] = $d;
        require __DIR__ . '/../views/utilisateur/droits.php';
    }
    
    public function groupesDroits() {
        checkRight('affecter_droits');
        $groupeId = $_GET['groupe_id'] ?? 0;
        if (!$groupeId) die("Groupe non spécifié.");
        $groupe = $this->groupeModel->getById($groupeId);
        if (!$groupe) die("Groupe introuvable.");
        $actuelsIds = array_column($this->droitModel->getByGroupe($groupeId), 'id_droit');
        $tousDroits = $this->droitModel->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->droitModel->assignToGroup($groupeId, $_POST['droits'] ?? []);
            logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'groupe_droit', $groupeId, null, ['droits' => $_POST['droits'] ?? []]);
            setFlash('Droits mis à jour.', 'success');
            $actuelsIds = array_column($this->droitModel->getByGroupe($groupeId), 'id_droit');
        }
        require __DIR__ . '/../views/utilisateur/groupes_droits.php';
    }
    
    public function utilisateurs() {
        checkRight('creer_utilisateur');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'add') {
                if (empty($_POST['id_groupe'])) {
                    setFlash('Veuillez sélectionner un groupe.', 'danger');
                } else {
                    $data = [
                        'id_groupe' => $_POST['id_groupe'], 'nom_complet' => trim($_POST['nom_complet']),
                        'login' => trim($_POST['login']), 'password' => $_POST['password'],
                        'actif' => isset($_POST['actif']) ? 1 : 0,
                        'date_expiration_mdp' => !empty($_POST['date_expiration_mdp']) ? $_POST['date_expiration_mdp'] : null
                    ];
                    if ($this->userModel->create($data)) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'INSERT', 'utilisateur', $this->pdo->lastInsertId(), null, ['login' => $data['login']]);
                        setFlash('Utilisateur ajouté.', 'success');
                    } else setFlash("Erreur lors de l'ajout.", 'danger');
                }
            } elseif ($_POST['action'] === 'edit') {
                if (empty($_POST['id_groupe'])) {
                    setFlash('Veuillez sélectionner un groupe.', 'danger');
                } else {
                    $id = $_POST['id_utilisateur'];
                    $data = [
                        'id_groupe' => $_POST['id_groupe'], 'nom_complet' => trim($_POST['nom_complet']),
                        'login' => trim($_POST['login']), 'actif' => isset($_POST['actif']) ? 1 : 0,
                        'date_expiration_mdp' => !empty($_POST['date_expiration_mdp']) ? $_POST['date_expiration_mdp'] : null
                    ];
                    if (!empty($_POST['password'])) $data['password'] = $_POST['password'];
                    if ($this->userModel->update($id, $data)) {
                        logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'utilisateur', $id, null, ['login' => $data['login']]);
                        setFlash('Utilisateur modifié.', 'success');
                    } else {
                        setFlash("Erreur lors de la modification.", 'danger');
                    }
                }
            }
        } elseif (isset($_GET['delete'])) {
            if ($_GET['delete'] == $_SESSION['user_id']) setFlash("Vous ne pouvez pas supprimer votre propre compte.", 'danger');
            elseif ($this->userModel->delete($_GET['delete'])) {
                logAudit($this->pdo, $_SESSION['user_id'], 'DELETE', 'utilisateur', $_GET['delete'], null, null);
                setFlash('Utilisateur supprimé.', 'success');
            } else setFlash("Erreur lors de la suppression.", 'danger');
        }
        $users = $this->userModel->getAll();
        $groupes = $this->groupeModel->getAll();
        require __DIR__ . '/../views/utilisateur/utilisateurs.php';
    }
    
public function profil() {
    $user = $this->userModel->getById($_SESSION['user_id']);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (!password_verify($old, $user['password_hash'])) {
            setFlash("Ancien mot de passe incorrect.", 'danger');
        } elseif ($new !== $confirm) {
            setFlash("Les nouveaux mots de passe ne correspondent pas.", 'danger');
        } elseif (strlen($new) < 4) {
            setFlash("Le mot de passe doit faire au moins 4 caractères.", 'danger');
        } else {
            $this->userModel->update($_SESSION['user_id'], ['password' => $new]);
            logAudit($this->pdo, $_SESSION['user_id'], 'UPDATE', 'utilisateur', $_SESSION['user_id'], null, ['changed_password' => true]);
            setFlash('Mot de passe modifié avec succès.', 'success');
            $user = $this->userModel->getById($_SESSION['user_id']);
        }
    }
    
    $title = "Mon profil";
    ob_start();
    require __DIR__ . '/../views/utilisateur/profil.php';
    $content = ob_get_clean();
    require __DIR__ . '/../views/layouts/main.php';
}
    
    public function journalAudit() {
        checkRight('voir_journal_audit');
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $total = $this->journalModel->count();
        $pages = ceil($total / $limit);
        $logs = $this->journalModel->getAll($limit, $offset);
        require __DIR__ . '/../views/utilisateur/journal_audit.php';
    }
}
?>