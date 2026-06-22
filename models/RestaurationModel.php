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
                case 'FOURNISSEUR_COMPLET':
                    $this->restoreFournisseur($xmlObj, $idObjet);
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