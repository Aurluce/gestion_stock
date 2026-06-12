<?php
class ReglementClientModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllByFacture($idFacture) {
        $stmt = $this->pdo->prepare("SELECT r.*, u.nom_complet AS utilisateur_nom
                FROM vente.reglement_client r
                JOIN utilisateur.utilisateur u ON r.id_utilisateur = u.id_utilisateur
                WHERE r.id_facture = ?
                ORDER BY r.date_creation DESC");
        $stmt->execute([$idFacture]);
        return $stmt->fetchAll();
    }

    public function getAll($filters = []) {
        $sql = "SELECT r.*, f.reference AS facture_reference, c.nom AS client_nom, c.prenom AS client_prenom
                FROM vente.reglement_client r
                JOIN vente.facture_client f ON r.id_facture = f.id_facture
                JOIN structure.client c ON r.id_client = c.id_client
                WHERE 1=1";
        $params = [];

        if (!empty($filters['id_facture'])) {
            $sql .= " AND r.id_facture = ?";
            $params[] = $filters['id_facture'];
        }

        $sql .= " ORDER BY r.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getResteAPayer($idFacture) {
        $stmt = $this->pdo->prepare("
            SELECT f.montant_ttc, COALESCE(SUM(r.montant), 0) AS total_regle,
                   f.montant_ttc - COALESCE(SUM(r.montant), 0) AS reste
            FROM vente.facture_client f
            LEFT JOIN vente.reglement_client r ON r.id_facture = f.id_facture
            WHERE f.id_facture = ?
            GROUP BY f.montant_ttc");
        $stmt->execute([$idFacture]);
        return $stmt->fetch();
    }

    public function create($idFacture, $idClient, $idUtilisateur, $montant, $modePaiement, $reference, $observations) {
        $stmt = $this->pdo->prepare("INSERT INTO vente.reglement_client
            (id_facture, id_client, id_utilisateur, montant, mode_paiement, reference, observations)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            RETURNING id_reglement");
        $stmt->execute([$idFacture, $idClient, $idUtilisateur, $montant, $modePaiement, $reference, $observations]);
        return $stmt->fetchColumn();
    }

    public function delete($idReglement) {
        $stmt = $this->pdo->prepare("DELETE FROM vente.reglement_client WHERE id_reglement = ?");
        return $stmt->execute([$idReglement]);
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM vente.reglement_client WHERE id_reglement = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
