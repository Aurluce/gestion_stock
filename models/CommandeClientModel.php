<?php
class CommandeClientModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT cc.*, c.nom AS client_nom, c.prenom AS client_prenom, u.nom_complet AS utilisateur_nom
                FROM vente.commande_client cc
                JOIN structure.client c ON cc.id_client = c.id_client
                JOIN utilisateur.utilisateur u ON cc.id_utilisateur = u.id_utilisateur
                WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND cc.statut = ?";
            $params[] = $filters['statut'];
        }
        if (!empty($filters['id_client'])) {
            $sql .= " AND cc.id_client = ?";
            $params[] = $filters['id_client'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (cc.reference ILIKE ? OR c.nom ILIKE ? OR c.prenom ILIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY cc.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT cc.*, c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.commande_client cc
                JOIN structure.client c ON cc.id_client = c.id_client
                WHERE cc.id_cc = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getLignes($idCc) {
        $stmt = $this->pdo->prepare("SELECT lcc.*, p.nom_produit, p.unite, p.prix_vente
                FROM vente.ligne_commande_client lcc
                JOIN structure.produit p ON lcc.id_produit = p.id_produit
                WHERE lcc.id_cc = ?
                ORDER BY lcc.id_lcc");
        $stmt->execute([$idCc]);
        return $stmt->fetchAll();
    }

    public function create($idClient, $idUtilisateur, $typeVente, $observations, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $reference = generateReference($this->pdo, 'CC', 'vente.commande_client');

            $stmt = $this->pdo->prepare("INSERT INTO vente.commande_client
                (id_client, id_utilisateur, reference, type_vente, observations, statut)
                VALUES (?, ?, ?, ?, ?, 'en_cours')
                RETURNING id_cc");
            $stmt->execute([$idClient, $idUtilisateur, $reference, $typeVente, $observations]);
            $idCc = $stmt->fetchColumn();

            $montantTotal = 0;
            $stmtLigne = $this->pdo->prepare("INSERT INTO vente.ligne_commande_client
                (id_cc, id_produit, quantite, prix_unitaire, taux_remise)
                VALUES (?, ?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $idCc,
                    $ligne['id_produit'],
                    $ligne['quantite'],
                    $ligne['prix_unitaire'],
                    $ligne['taux_remise'] ?? 0
                ]);
                $montant = $ligne['quantite'] * $ligne['prix_unitaire'] * (1 - ($ligne['taux_remise'] ?? 0) / 100);
                $montantTotal += $montant;
            }

            $this->pdo->prepare("UPDATE vente.commande_client SET montant_total = ? WHERE id_cc = ?")
                ->execute([$montantTotal, $idCc]);

            $this->pdo->commit();
            return $idCc;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update($idCc, $idClient, $typeVente, $observations, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("UPDATE vente.commande_client
                SET id_client = ?, type_vente = ?, observations = ?, date_modif = CURRENT_TIMESTAMP
                WHERE id_cc = ?")
                ->execute([$idClient, $typeVente, $observations, $idCc]);

            $this->pdo->prepare("DELETE FROM vente.ligne_commande_client WHERE id_cc = ?")
                ->execute([$idCc]);

            $montantTotal = 0;
            $stmtLigne = $this->pdo->prepare("INSERT INTO vente.ligne_commande_client
                (id_cc, id_produit, quantite, prix_unitaire, taux_remise)
                VALUES (?, ?, ?, ?, ?)");

            foreach ($lignes as $ligne) {
                $stmtLigne->execute([
                    $idCc,
                    $ligne['id_produit'],
                    $ligne['quantite'],
                    $ligne['prix_unitaire'],
                    $ligne['taux_remise'] ?? 0
                ]);
                $montant = $ligne['quantite'] * $ligne['prix_unitaire'] * (1 - ($ligne['taux_remise'] ?? 0) / 100);
                $montantTotal += $montant;
            }

            $this->pdo->prepare("UPDATE vente.commande_client SET montant_total = ? WHERE id_cc = ?")
                ->execute([$montantTotal, $idCc]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function annuler($idCc) {
        $stmt = $this->pdo->prepare("UPDATE vente.commande_client SET statut = 'annulee', date_modif = CURRENT_TIMESTAMP WHERE id_cc = ?");
        return $stmt->execute([$idCc]);
    }

    public function delete($idCc) {
        $stmt = $this->pdo->prepare("DELETE FROM vente.commande_client WHERE id_cc = ?");
        return $stmt->execute([$idCc]);
    }

    public function updateStatut($idCc, $statut) {
        $stmt = $this->pdo->prepare("UPDATE vente.commande_client SET statut = ?, date_modif = CURRENT_TIMESTAMP WHERE id_cc = ?");
        return $stmt->execute([$statut, $idCc]);
    }
}
