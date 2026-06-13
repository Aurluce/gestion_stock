<?php
class DonModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM approvisionnement.don ORDER BY date_creation DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM approvisionnement.don WHERE id_don = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($donateur, $contact, $dateDon, $description, $valeurEstimee, $idUtilisateur) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO approvisionnement.don
                (donateur, contact_donateur, date_don, description, valeur_estimee)
                VALUES (?, ?, ?, ?, ?)
                RETURNING id_don");
            $stmt->execute([$donateur, $contact, $dateDon, $description, $valeurEstimee ?: 0]);
            $idDon = $stmt->fetchColumn();

            $this->pdo->commit();
            return $idDon;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update($idDon, $donateur, $contact, $dateDon, $description, $valeurEstimee) {
        $stmt = $this->pdo->prepare("UPDATE approvisionnement.don
            SET donateur = ?, contact_donateur = ?, date_don = ?, description = ?, valeur_estimee = ?
            WHERE id_don = ?");
        return $stmt->execute([$donateur, $contact, $dateDon, $description, $valeurEstimee ?: 0, $idDon]);
    }

    public function getLignesEntree($idDon) {
        $stmt = $this->pdo->prepare("
            SELECT l.id_produit, l.quantite, p.nom_produit, p.unite
            FROM approvisionnement.ligne_bon_entree l
            JOIN approvisionnement.bon_entree e ON l.id_be = e.id_be
            JOIN structure.produit p ON l.id_produit = p.id_produit
            WHERE e.id_don = ?
        ");
        $stmt->execute([$idDon]);
        return $stmt->fetchAll();
    }

    public function delete($idDon) {
        $stmt = $this->pdo->prepare("DELETE FROM approvisionnement.don WHERE id_don = ?");
        return $stmt->execute([$idDon]);
    }

    public function createWithEntree($donateur, $contact, $dateDon, $description, $idUtilisateur, $lignes) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO approvisionnement.don
                (donateur, contact_donateur, date_don, description, valeur_estimee)
                VALUES (?, ?, ?, ?, 0)
                RETURNING id_don");
            $stmt->execute([$donateur, $contact, $dateDon, $description]);
            $idDon = $stmt->fetchColumn();

            $refBe = \generateReference($this->pdo, 'BE', 'approvisionnement.bon_entree');
            $stmtBe = $this->pdo->prepare("INSERT INTO approvisionnement.bon_entree
                (id_don, id_utilisateur, reference, type_source, observations)
                VALUES (?, ?, ?, 'don', 'Don entrée en stock')
                RETURNING id_be");
            $stmtBe->execute([$idDon, $idUtilisateur, $refBe]);
            $idBe = $stmtBe->fetchColumn();

            $stmtLbe = $this->pdo->prepare("INSERT INTO approvisionnement.ligne_bon_entree
                (id_be, id_produit, quantite, prix_unitaire)
                VALUES (?, ?, ?, 0)");
            foreach ($lignes as $ligne) {
                $stmtLbe->execute([
                    $idBe,
                    $ligne['id_produit'],
                    $ligne['quantite']
                ]);
            }

            $this->pdo->commit();
            return $idDon;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
