<?php
class RestaurationModel {
    private PDO $pdo;
    private int $currentUserId;
    
    public function __construct(PDO $pdo, int $userId = 1) {
        $this->pdo = $pdo;
        $this->currentUserId = $userId;
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
        $xmlObj = $this->parseXml($xml);
        
        if (empty($xmlObj) || !is_array($xmlObj)) {
            return ['success' => false, 'message' => 'Données XML invalides ou manquantes'];
        }
        
        try {
            $this->pdo->beginTransaction();
            
            switch ($type) {
                case 'PRODUIT_COMPLET':
                    $this->restoreProduit($xmlObj, $idObjet);
                    break;
                case 'PRODUIT':
                    $this->restoreProduit($xmlObj, $idObjet);
                    break;
                case 'FOURNISSEUR_COMPLET':
                    $this->restoreFournisseur($xmlObj, $idObjet);
                    break;
                case 'FOURNISSEUR':
                    $this->restoreFournisseur($xmlObj, $idObjet);
                    break;
                case 'MOUVEMENT_BANQUE':
                    $this->restoreMouvementBanque($xmlObj, $idObjet);
                    break;
                case 'COMMANDE_FOURN_COMPLETE':
                case 'BON_COMMANDE_FOURN':
                    $this->restoreCommandeFournComplete($xmlObj, $idObjet);
                    break;
                case 'COMMANDE_CLIENT':
                    $this->restoreCommandeClient($xmlObj, $idObjet);
                    break;
                case 'UTILISATEUR':
                    $this->restoreUtilisateur($xmlObj, $idObjet);
                    break;
                case 'GROUPE':
                    $this->restoreGroupe($xmlObj, $idObjet);
                    break;
                default:
                    $this->pdo->rollBack();
                    $supportedTypes = 'PRODUIT_COMPLET, PRODUIT, FOURNISSEUR_COMPLET, FOURNISSEUR, MOUVEMENT_BANQUE, COMMANDE_FOURN_COMPLETE, BON_COMMANDE_FOURN, COMMANDE_CLIENT, UTILISATEUR, GROUPE';
                    return ['success' => false, 'message' => "Type non supporté : {$type}. Types supportés : {$supportedTypes}"];
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
    
    private function restoreProduit(array $xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_produit FROM structure.produit WHERE id_produit = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;

        $familleId = (int)($xml['id_famille'] ?? 0);
        if ($familleId <= 0) {
            $familleId = $this->ensureFamilleExists(null);
        } else {
            $familleId = $this->ensureFamilleExists($familleId);
        }
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.produit 
            (id_produit, id_famille, id_produit_pere, nom_produit, description, prix_achat, prix_vente, stock_actuel, seuil_alerte, unite, est_actif)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, true)
        ");
        $stmt->execute([
            (int)($xml['id_produit'] ?? $idObjet),
            $familleId,
            !empty($xml['id_produit_pere']) ? (int)$xml['id_produit_pere'] : null,
            (string)($xml['nom_produit'] ?? ''),
            $xml['description'] ?? null,
            (float)($xml['prix_achat'] ?? 0),
            (float)($xml['prix_vente'] ?? 0),
            (float)($xml['stock_actuel'] ?? 0),
            isset($xml['seuil_alerte']) ? (float)$xml['seuil_alerte'] : 0,
            $xml['unite'] ?? 'pce'
        ]);
        
        $this->pdo->exec("SELECT setval('structure.produit_id_produit_seq', GREATEST((SELECT MAX(id_produit) FROM structure.produit), (SELECT nextval('structure.produit_id_produit_seq'))))");
    }
    
    private function restoreFournisseur(array $xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_fournisseur FROM structure.fournisseur WHERE id_fournisseur = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.fournisseur 
            (id_fournisseur, nom, tel, email, adresse, ville, nif, est_actif)
            VALUES (?, ?, ?, ?, ?, ?, ?, true)
        ");
        $stmt->execute([
            (int)($xml['id_fournisseur'] ?? $idObjet),
            (string)($xml['nom'] ?? ''),
            $xml['tel'] ?? null,
            $xml['email'] ?? null,
            $xml['adresse'] ?? null,
            $xml['ville'] ?? null,
            $xml['nif'] ?? null
        ]);
        
        $this->pdo->exec("SELECT setval('structure.fournisseur_id_fournisseur_seq', GREATEST((SELECT MAX(id_fournisseur) FROM structure.fournisseur), (SELECT nextval('structure.fournisseur_id_fournisseur_seq'))))");
    }
    
    private function restoreMouvementBanque(array $xml, int $idObjet): void {
        $stmt = $this->pdo->prepare("SELECT id_mouvement_banque FROM structure.mouvement_banque WHERE id_mouvement_banque = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO structure.mouvement_banque 
            (id_mouvement_banque, id_banque, type_mouvement, montant, date_mouvement, reference, description, id_utilisateur)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([
            (int)($xml['id_mouvement_banque'] ?? $idObjet),
            (int)($xml['id_banque'] ?? 0),
            (string)($xml['type_mouvement'] ?? ''),
            (float)($xml['montant'] ?? 0),
            (string)($xml['date_mouvement'] ?? ''),
            $xml['reference'] ?? null,
            $xml['description'] ?? null
        ]);
        
        $this->pdo->exec("SELECT setval('structure.mouvement_banque_id_mouvement_banque_seq', GREATEST((SELECT MAX(id_mouvement_banque) FROM structure.mouvement_banque), (SELECT nextval('structure.mouvement_banque_id_mouvement_banque_seq'))))");
    }

    private function restoreCommandeFournComplete(array $xml, int $idObjet): void {
        // Vérifier si la commande existe déjà
        $stmt = $this->pdo->prepare("SELECT id_bcf FROM approvisionnement.bon_commande_fourn WHERE id_bcf = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;

        // Restaurer l'en-tête de la commande fournisseur
        $entete = $xml['entete'] ?? [];
        $stmt = $this->pdo->prepare("
            INSERT INTO approvisionnement.bon_commande_fourn 
            (id_bcf, id_fournisseur, id_utilisateur, date_commande, statut, reference, montant_total, observations)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)($entete['id_bcf'] ?? $idObjet),
            (int)($entete['id_fournisseur'] ?? 0),
            (int)($entete['id_utilisateur'] ?? 1),
            (string)($entete['date_commande'] ?? date('Y-m-d')),
            (string)($entete['statut'] ?? 'EN_COURS'),
            (string)($entete['reference'] ?? ''),
            (float)($entete['montant_total'] ?? 0),
            $entete['observations'] ?? null
        ]);

        $this->pdo->exec("SELECT setval('approvisionnement.bon_commande_fourn_id_bcf_seq', GREATEST((SELECT MAX(id_bcf) FROM approvisionnement.bon_commande_fourn), (SELECT nextval('approvisionnement.bon_commande_fourn_id_bcf_seq'))))");
    }

    private function restoreCommandeClient(array $xml, int $idObjet): void {
        // Vérifier si la commande existe déjà
        $stmt = $this->pdo->prepare("SELECT id_cc FROM vente.commande_client WHERE id_cc = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;

        // Restaurer la commande client avec id_utilisateur actuel
        $stmt = $this->pdo->prepare("
            INSERT INTO vente.commande_client 
            (id_cc, id_client, id_utilisateur, reference, montant_total, statut, type_vente)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)($xml['id_cc'] ?? $idObjet),
            (int)($xml['id_client'] ?? 0),
            $this->currentUserId,
            (string)($xml['reference'] ?? ''),
            (float)($xml['montant_total'] ?? 0),
            (string)($xml['statut'] ?? 'en_cours'),
            (string)($xml['type_vente'] ?? 'credit')
        ]);

        $this->pdo->exec("SELECT setval('vente.commande_client_id_cc_seq', GREATEST((SELECT MAX(id_cc) FROM vente.commande_client), (SELECT nextval('vente.commande_client_id_cc_seq'))))");
    }

    private function restoreUtilisateur(array $xml, int $idObjet): void {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $this->pdo->prepare("SELECT id_utilisateur FROM utilisateur.utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;

        // Restaurer l'utilisateur
        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur.utilisateur 
            (id_utilisateur, id_groupe, nom_complet, login, password_hash, actif)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)($xml['id_utilisateur'] ?? $idObjet),
            (int)($xml['id_groupe'] ?? 1),
            (string)($xml['nom_complet'] ?? ''),
            (string)($xml['login'] ?? ''),
            (string)($xml['password_hash'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
            true
        ]);

        $this->pdo->exec("SELECT setval('utilisateur.utilisateur_id_utilisateur_seq', GREATEST((SELECT MAX(id_utilisateur) FROM utilisateur.utilisateur), (SELECT nextval('utilisateur.utilisateur_id_utilisateur_seq'))))");
    }

    private function restoreGroupe(array $xml, int $idObjet): void {
        // Vérifier si le groupe existe déjà
        $stmt = $this->pdo->prepare("SELECT id_groupe FROM utilisateur.groupe WHERE id_groupe = ?");
        $stmt->execute([$idObjet]);
        if ($stmt->fetch()) return;

        // Restaurer le groupe
        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur.groupe 
            (id_groupe, nom_groupe, description)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            (int)($xml['id_groupe'] ?? $idObjet),
            (string)($xml['nom_groupe'] ?? ''),
            $xml['description'] ?? null
        ]);

        $this->pdo->exec("SELECT setval('utilisateur.groupe_id_groupe_seq', GREATEST((SELECT MAX(id_groupe) FROM utilisateur.groupe), (SELECT nextval('utilisateur.groupe_id_groupe_seq'))))");
    }

    private function ensureFamilleExists(?int $familleId): int {
        if ($familleId !== null && $familleId > 0) {
            $stmt = $this->pdo->prepare("SELECT id_famille FROM structure.famille WHERE id_famille = ?");
            $stmt->execute([$familleId]);
            if ($stmt->fetch()) {
                return $familleId;
            }

            $stmt = $this->pdo->prepare("INSERT INTO structure.famille (id_famille, nom_famille, description) VALUES (?, ?, ?)");
            $stmt->execute([$familleId, 'Famille restaurée', 'Famille recréée automatiquement lors de la restauration du produit']);
            $this->pdo->exec("SELECT setval('structure.famille_id_famille_seq', GREATEST((SELECT MAX(id_famille) FROM structure.famille), (SELECT nextval('structure.famille_id_famille_seq'))))");
            return $familleId;
        }

        $stmt = $this->pdo->prepare("INSERT INTO structure.famille (nom_famille, description) VALUES (?, ?)");
        $stmt->execute(['Famille restaurée', 'Famille créée automatiquement durant la restauration']);
        $newId = (int)$this->pdo->lastInsertId('structure.famille_id_famille_seq');
        return $newId;
    }

    public function parseXml(string $xmlString): ?array {
        $xmlString = trim($xmlString);
        if ($xmlString === '') {
            return null;
        }

        if (preg_match('/^<([a-zA-Z0-9_:-]+)>(.*)<\/\1>$/s', $xmlString, $rootMatch)) {
            $xmlString = trim($rootMatch[2]);
        }

        $result = [];
        $pattern = '/<([a-zA-Z0-9_:-]+)>(.*?)<\/\1>/s';
        if (preg_match_all($pattern, $xmlString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $tag = $match[1];
                $content = trim($match[2]);
                if (preg_match('/<([a-zA-Z0-9_:-]+)>/s', $content)) {
                    $value = $this->parseXml($content);
                } else {
                    $value = $content;
                }

                if (array_key_exists($tag, $result)) {
                    if (!is_array($result[$tag]) || array_keys($result[$tag]) === range(0, count($result[$tag]) - 1)) {
                        $result[$tag] = [$result[$tag]];
                    }
                    $result[$tag][] = $value;
                } else {
                    $result[$tag] = $value;
                }
            }
        }

        if (preg_match_all('/<([a-zA-Z0-9_:-]+)\s*\/\>/', $xmlString, $selfClosingMatches)) {
            foreach ($selfClosingMatches[1] as $tag) {
                if (!array_key_exists($tag, $result)) {
                    $result[$tag] = '';
                }
            }
        }

        return $result;
    }
    
    public function deletePermanently(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur.corbeille_xml WHERE id_corbeille = ?");
        return $stmt->execute([$id]);
    }
    
    public function clearAll(string $type = ''): int {
        if (empty($type)) {
            $stmt = $this->pdo->prepare("DELETE FROM utilisateur.corbeille_xml");
            $stmt->execute();
        } else {
            $stmt = $this->pdo->prepare("DELETE FROM utilisateur.corbeille_xml WHERE type_objet = ?");
            $stmt->execute([$type]);
        }

        return $stmt->rowCount();
    }
}