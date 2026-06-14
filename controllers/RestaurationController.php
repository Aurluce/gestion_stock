<?php
require_once __DIR__ . '/../models/RestaurationModel.php';

class RestaurationController {
    private $pdo;
    private $model;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new RestaurationModel($pdo);
    }

    public function index() {
        checkRight('restaurer_corbeille');
        
        $typeFiltre = $_GET['type'] ?? '';
        $search = $_GET['search'] ?? '';
        $elements = $this->model->getAll($typeFiltre, $search);
        $types = $this->model->getTypes();
        
        require __DIR__ . '/../views/structure/restauration/index.php';
    }

    public function view() {
        checkRight('restaurer_corbeille');
        
        $id = (int)($_GET['id'] ?? 0);
        $element = $this->model->getById($id);
        
        if (!$element) {
            setFlash('Élément introuvable dans la corbeille.', 'danger');
            header('Location: ?action=restauration');
            exit;
        }
        
        require __DIR__ . '/../views/structure/restauration/view.php';
    }

    public function restore() {
        checkRight('restaurer_corbeille');
        
        $id = (int)($_GET['id'] ?? 0);
        $result = $this->model->restore($id);
        
        if ($result['success']) {
            setFlash($result['message'], 'success');
        } else {
            setFlash($result['message'], 'danger');
        }
        
        header('Location: ?action=restauration');
        exit;
    }

    public function delete() {
        checkRight('vider_corbeille');
        
        $id = (int)($_GET['id'] ?? 0);
        $this->model->deletePermanently($id);
        setFlash('Élément supprimé définitivement.', 'success');
        
        header('Location: ?action=restauration');
        exit;
    }

    public function clear() {
        checkRight('vider_corbeille');
        
        $type = $_GET['type'] ?? '';
        $count = $this->model->clearAll($type);
        
        if ($type) {
            setFlash("$count élément(s) de type '$type' supprimé(s) de la corbeille.", 'success');
        } else {
            setFlash("$count élément(s) supprimé(s) de la corbeille.", 'success');
        }
        
        header('Location: ?action=restauration');
        exit;
    }

    private function restoreClient($xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_client FROM structure.client WHERE id_client = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.client 
            (id_client, id_categorie_client, nom, prenom, tel, email, ville, type_client, solde_credit, est_actif)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, true)
        ");
        $stmt->execute([
            (int)$xml->id_client,
            isset($xml->id_categorie_client) && (int)$xml->id_categorie_client ? (int)$xml->id_categorie_client : null,
            (string)$xml->nom,
            isset($xml->prenom) ? (string)$xml->prenom : null,
            isset($xml->tel) ? (string)$xml->tel : null,
            isset($xml->email) ? (string)$xml->email : null,
            isset($xml->ville) ? (string)$xml->ville : null,
            isset($xml->type_client) ? (string)$xml->type_client : 'particulier',
            isset($xml->solde_credit) ? (float)$xml->solde_credit : 0
        ]);
        
        $this->pdo->exec("SELECT setval('structure.client_id_client_seq', GREATEST((SELECT MAX(id_client) FROM structure.client), (SELECT nextval('structure.client_id_client_seq'))))");
    }
    
    private function restoreBanque($xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_banque FROM structure.banque WHERE id_banque = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.banque 
            (id_banque, nom_banque, sigle, responsable, tel, email, adresse)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)$xml->id_banque,
            (string)$xml->nom_banque,
            isset($xml->sigle) ? (string)$xml->sigle : null,
            isset($xml->responsable) ? (string)$xml->responsable : null,
            isset($xml->tel) ? (string)$xml->tel : null,
            isset($xml->email) ? (string)$xml->email : null,
            isset($xml->adresse) ? (string)$xml->adresse : null
        ]);
        
        $this->pdo->exec("SELECT setval('structure.banque_id_banque_seq', GREATEST((SELECT MAX(id_banque) FROM structure.banque), (SELECT nextval('structure.banque_id_banque_seq'))))");
    }
    
    private function restoreFamille($xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_famille FROM structure.famille WHERE id_famille = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.famille 
            (id_famille, nom_famille, description)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            (int)$xml->id_famille,
            (string)$xml->nom_famille,
            isset($xml->description) ? (string)$xml->description : null
        ]);
        
        $this->pdo->exec("SELECT setval('structure.famille_id_famille_seq', GREATEST((SELECT MAX(id_famille) FROM structure.famille), (SELECT nextval('structure.famille_id_famille_seq'))))");
    }
    
    private function restoreCategorieClient($xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_categorie_client FROM structure.categorie_client WHERE id_categorie_client = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.categorie_client 
            (id_categorie_client, nom_categorie, taux_remise, description)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)$xml->id_categorie_client,
            (string)$xml->nom_categorie,
            isset($xml->taux_remise) ? (float)$xml->taux_remise : 0,
            isset($xml->description) ? (string)$xml->description : null
        ]);
        
        $this->pdo->exec("SELECT setval('structure.categorie_client_id_categorie_client_seq', GREATEST((SELECT MAX(id_categorie_client) FROM structure.categorie_client), (SELECT nextval('structure.categorie_client_id_categorie_client_seq'))))");
    }
}