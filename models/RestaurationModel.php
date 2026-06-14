<?php
class RestaurationModel {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(string $type = '', string $search = ''): array {
        $sql = "SELECT c.id_corbeille, c.type_objet, c.id_objet, c.date_suppression, c.donnees_xml,
                       c.supprime_par, u.nom_complet as supprime_par_nom
                FROM utilisateur.corbeille_xml c
                LEFT JOIN utilisateur.utilisateur u ON c.supprime_par = u.id_utilisateur
                WHERE 1=1";
        $params = [];
        
        if (!empty($type)) {
            $sql .= " AND c.type_objet = ?";
            $params[] = $type;
        }
        
        if (!empty($search)) {
            $sql .= " AND (c.type_objet ILIKE ? OR c.id_objet::text ILIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY c.date_suppression DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTypes(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT type_objet FROM utilisateur.corbeille_xml ORDER BY type_objet");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur.corbeille_xml WHERE id_corbeille = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function restore(int $id): array {
        $stmt = $this->pdo->prepare("SELECT type_objet, id_objet, donnees_xml FROM utilisateur.corbeille_xml WHERE id_corbeille = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return ['success' => false, 'message' => 'Élément introuvable'];
        }
        
        $type = $data['type_objet'];
        $idObjet = $data['id_objet'];
        $xml = $data['donnees_xml'];
        $xmlObj = simplexml_load_string($xml);
        
        try {
            $this->pdo->beginTransaction();
            
            switch ($type) {
                case 'PRODUIT_COMPLET':
                    $this->restoreProduit($xmlObj, $idObjet);
                    break;
                case 'FOURNISSEUR_COMPLET':
                    $this->restoreFournisseur($xmlObj, $idObjet);
                    break;
                case 'CLIENT':
                    $this->restoreClient($xmlObj, $idObjet);
                    break;
                case 'BANQUE':
                    $this->restoreBanque($xmlObj, $idObjet);
                    break;
                case 'FAMILLE':
                    $this->restoreFamille($xmlObj, $idObjet);
                    break;
                case 'CATEGORIE_CLIENT':
                    $this->restoreCategorieClient($xmlObj, $idObjet);
                    break;
                case 'MOUVEMENT_BANQUE':
                    $this->restoreMouvementBanque($xmlObj, $idObjet);
                    break;
                default:
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'Type non supporté'];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM utilisateur.corbeille_xml WHERE id_corbeille = ?");
            $stmt->execute([$id]);
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Élément restauré avec succès'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    private function restoreProduit($xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_produit FROM structure.produit WHERE id_produit = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.produit 
            (id_produit, id_famille, id_produit_pere, nom_produit, description, prix_achat, prix_vente, stock_actuel, seuil_alerte, unite, est_actif)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, true)
        ");
        $stmt->execute([
            (int)$xml->id_produit,
            (int)$xml->id_famille,
            isset($xml->id_produit_pere) && (int)$xml->id_produit_pere ? (int)$xml->id_produit_pere : null,
            (string)$xml->nom_produit,
            isset($xml->description) ? (string)$xml->description : null,
            (float)$xml->prix_achat,
            (float)$xml->prix_vente,
            (float)$xml->stock_actuel,
            isset($xml->seuil_alerte) ? (float)$xml->seuil_alerte : 0,
            isset($xml->unite) ? (string)$xml->unite : 'pce'
        ]);
        
        $this->pdo->exec("SELECT setval('structure.produit_id_produit_seq', GREATEST((SELECT MAX(id_produit) FROM structure.produit), (SELECT nextval('structure.produit_id_produit_seq'))))");
    }
    
    private function restoreFournisseur($xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_fournisseur FROM structure.fournisseur WHERE id_fournisseur = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.fournisseur 
            (id_fournisseur, nom, tel, email, adresse, ville, nif, est_actif)
            VALUES (?, ?, ?, ?, ?, ?, ?, true)
        ");
        $stmt->execute([
            (int)$xml->id_fournisseur,
            (string)$xml->nom,
            isset($xml->tel) ? (string)$xml->tel : null,
            isset($xml->email) ? (string)$xml->email : null,
            isset($xml->adresse) ? (string)$xml->adresse : null,
            isset($xml->ville) ? (string)$xml->ville : null,
            isset($xml->nif) ? (string)$xml->nif : null
        ]);
        
        $this->pdo->exec("SELECT setval('structure.fournisseur_id_fournisseur_seq', GREATEST((SELECT MAX(id_fournisseur) FROM structure.fournisseur), (SELECT nextval('structure.fournisseur_id_fournisseur_seq'))))");
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
    
    private function restoreMouvementBanque($xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_mouvement_banque FROM structure.mouvement_banque WHERE id_mouvement_banque = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.mouvement_banque 
            (id_mouvement_banque, id_banque, type_mouvement, montant, date_mouvement, reference, description, id_utilisateur)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([
            (int)$xml->id_mouvement_banque,
            (int)$xml->id_banque,
            (string)$xml->type_mouvement,
            (float)$xml->montant,
            (string)$xml->date_mouvement,
            isset($xml->reference) ? (string)$xml->reference : null,
            isset($xml->description) ? (string)$xml->description : null
        ]);
        
        $this->pdo->exec("SELECT setval('structure.mouvement_banque_id_mouvement_banque_seq', GREATEST((SELECT MAX(id_mouvement_banque) FROM structure.mouvement_banque), (SELECT nextval('structure.mouvement_banque_id_mouvement_banque_seq'))))");
    }
    
    public function deletePermanently(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur.corbeille_xml WHERE id_corbeille = ?");
        return $stmt->execute([$id]);
    }
    
    public function clearAll(string $type = ''): int {
        if (empty($type)) {
            $stmt = $this->pdo->prepare("DELETE FROM utilisateur.corbeille_xml");
        } else {
            $stmt = $this->pdo->prepare("DELETE FROM utilisateur.corbeille_xml WHERE type_objet = ?");
            $stmt->execute([$type]);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }
}