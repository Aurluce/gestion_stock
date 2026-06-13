-- ==========================================
-- SUPPRESSION DES SCHEMAS
-- ==========================================

DROP SCHEMA IF EXISTS approvisionnement CASCADE;
DROP SCHEMA IF EXISTS vente CASCADE;
DROP SCHEMA IF EXISTS structure CASCADE;
DROP SCHEMA IF EXISTS utilisateur CASCADE;

-- ==========================================
-- CREATION DES SCHEMAS
-- ==========================================

CREATE SCHEMA utilisateur;
CREATE SCHEMA structure;
CREATE SCHEMA approvisionnement;
CREATE SCHEMA vente;

-- ==========================================
-- SUPPRESSION DES TYPES SI EXISTANTS
-- ==========================================

DROP TYPE IF EXISTS type_client_enum CASCADE;
DROP TYPE IF EXISTS statut_fournisseur_enum CASCADE;
DROP TYPE IF EXISTS statut_commande_client_enum CASCADE;
DROP TYPE IF EXISTS type_vente_enum CASCADE;

-- ==========================================
-- ENUMS
-- ==========================================

CREATE TYPE type_client_enum AS ENUM
(
    'particulier',
    'entreprise',
    'administration'
);

CREATE TYPE statut_fournisseur_enum AS ENUM
(
    'actif',
    'inactif',
    'suspendu'
);

CREATE TYPE statut_commande_client_enum AS ENUM
(
    'en_cours',
    'livree',
    'facturee',
    'reglee',
    'annulee',
    'en_attente'
);

CREATE TYPE type_vente_enum AS ENUM
(
    'comptant',
    'credit'
);

-- ==========================================
-- MODULE UTILISATEUR
-- ==========================================

CREATE TABLE utilisateur.groupe
(
    id_groupe SERIAL PRIMARY KEY,
    nom_groupe VARCHAR(100) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE utilisateur.droit
(
    id_droit SERIAL PRIMARY KEY,
    nom_droit VARCHAR(100) NOT NULL,
    module VARCHAR(100) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE utilisateur.utilisateur
(
    id_utilisateur SERIAL PRIMARY KEY,

    id_groupe INTEGER NOT NULL,

    nom_complet VARCHAR(200) NOT NULL,

    login VARCHAR(100) NOT NULL UNIQUE,

    password_hash VARCHAR(255) NOT NULL,

    actif BOOLEAN DEFAULT TRUE,

    derniere_connexion TIMESTAMP,

    nb_tentatives_echouees INTEGER DEFAULT 0,

    date_expiration_mdp DATE,

    ip_derniere_connexion VARCHAR(45),

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE utilisateur.groupe_droit
(
    id_groupe INTEGER NOT NULL,
    id_droit INTEGER NOT NULL,

    PRIMARY KEY(id_groupe,id_droit)
);

CREATE TABLE utilisateur.journal_audit
(
    id_audit BIGSERIAL PRIMARY KEY,

    id_utilisateur INTEGER,

    date_heure TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    action VARCHAR(30) NOT NULL,

    table_cible VARCHAR(100),

    id_enregistrement VARCHAR(50),

    ancienne_valeur JSONB,

    nouvelle_valeur JSONB,

    ip_adresse VARCHAR(45),

    user_agent TEXT
);

CREATE TABLE utilisateur.corbeille_xml
(
    id_corbeille BIGSERIAL PRIMARY KEY,

    type_objet VARCHAR(100) NOT NULL,

    id_objet INTEGER NOT NULL,

    donnees_xml XML NOT NULL,

    date_suppression TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    supprime_par INTEGER
);

-- ==========================================
-- MODULE STRUCTURE
-- ==========================================

CREATE TABLE structure.famille
(
    id_famille SERIAL PRIMARY KEY,

    nom_famille VARCHAR(100) NOT NULL,

    description TEXT,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE structure.categorie_client
(
    id_categorie_client SERIAL PRIMARY KEY,

    nom_categorie VARCHAR(100) NOT NULL,

    taux_remise NUMERIC(5,2) DEFAULT 0,

    description TEXT,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE structure.banque
(
    id_banque SERIAL PRIMARY KEY,

    nom_banque VARCHAR(200) NOT NULL,

    sigle VARCHAR(20),

    responsable VARCHAR(200),

    adresse TEXT,

    tel VARCHAR(30),

    email VARCHAR(150),

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE structure.fournisseur
(
    id_fournisseur SERIAL PRIMARY KEY,

    nom VARCHAR(200) NOT NULL,

    adresse TEXT,

    ville VARCHAR(100),

    code_postal VARCHAR(20),

    pays VARCHAR(100) DEFAULT 'Cameroun',

    tel VARCHAR(30),

    email VARCHAR(150),

    nif VARCHAR(50),

    site_web VARCHAR(200),

    statut statut_fournisseur_enum DEFAULT 'actif',

    est_actif BOOLEAN DEFAULT TRUE,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE structure.produit
(
    id_produit SERIAL PRIMARY KEY,

    id_famille INTEGER NOT NULL,

    id_produit_pere INTEGER,

    code_barre VARCHAR(50),

    nom_produit VARCHAR(200) NOT NULL,

    description TEXT,

    prix_achat NUMERIC(15,2) DEFAULT 0,

    prix_vente NUMERIC(15,2) DEFAULT 0,

    stock_actuel NUMERIC(15,3) DEFAULT 0,

    seuil_alerte NUMERIC(15,3) DEFAULT 0,

    perissable BOOLEAN DEFAULT FALSE,

    date_peremption DATE,

    unite VARCHAR(20) DEFAULT 'pce',

    est_actif BOOLEAN DEFAULT TRUE,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE structure.client
(
    id_client SERIAL PRIMARY KEY,

    id_categorie_client INTEGER NOT NULL,

    nom VARCHAR(150) NOT NULL,

    prenom VARCHAR(150),

    adresse TEXT,

    ville VARCHAR(100),

    code_postal VARCHAR(20),

    pays VARCHAR(100) DEFAULT 'Cameroun',

    tel VARCHAR(30),

    email VARCHAR(150),

    type_client type_client_enum DEFAULT 'particulier',

    solde_credit NUMERIC(15,2) DEFAULT 0,

    est_actif BOOLEAN DEFAULT TRUE,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- CLES ETRANGERES
-- ==========================================

ALTER TABLE utilisateur.utilisateur
ADD CONSTRAINT fk_utilisateur_groupe
FOREIGN KEY (id_groupe)
REFERENCES utilisateur.groupe(id_groupe)
ON DELETE CASCADE;

ALTER TABLE utilisateur.groupe_droit
ADD CONSTRAINT fk_groupe_droit_groupe
FOREIGN KEY (id_groupe)
REFERENCES utilisateur.groupe(id_groupe)
ON DELETE CASCADE;

ALTER TABLE utilisateur.groupe_droit
ADD CONSTRAINT fk_groupe_droit_droit
FOREIGN KEY (id_droit)
REFERENCES utilisateur.droit(id_droit)
ON DELETE CASCADE;

ALTER TABLE utilisateur.journal_audit
ADD CONSTRAINT fk_audit_utilisateur
FOREIGN KEY (id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE SET NULL;

ALTER TABLE utilisateur.corbeille_xml
ADD CONSTRAINT fk_corbeille_utilisateur
FOREIGN KEY (supprime_par)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE SET NULL;

ALTER TABLE structure.client
ADD CONSTRAINT fk_client_categorie
FOREIGN KEY (id_categorie_client)
REFERENCES structure.categorie_client(id_categorie_client)
ON DELETE CASCADE;

ALTER TABLE structure.produit
ADD CONSTRAINT fk_produit_famille
FOREIGN KEY (id_famille)
REFERENCES structure.famille(id_famille)
ON DELETE CASCADE;

ALTER TABLE structure.produit
ADD CONSTRAINT fk_produit_pere
FOREIGN KEY (id_produit_pere)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

-- ==========================================
-- ENUMS APPROVISIONNEMENT
-- ==========================================

DROP TYPE IF EXISTS statut_bcf_enum CASCADE;
DROP TYPE IF EXISTS statut_reception_enum CASCADE;
DROP TYPE IF EXISTS type_source_entree_enum CASCADE;
DROP TYPE IF EXISTS statut_facture_f_enum CASCADE;
DROP TYPE IF EXISTS mode_paiement_enum CASCADE;
DROP TYPE IF EXISTS etat_produit_reception_enum CASCADE;

CREATE TYPE statut_bcf_enum AS ENUM
(
'brouillon',
'envoye',
'receptionne',
'annule'
);

CREATE TYPE statut_reception_enum AS ENUM
(
'partiel',
'complet',
'en_attente'
);

CREATE TYPE type_source_entree_enum AS ENUM
(
'achat',
'don',
'retour',
'autre'
);

CREATE TYPE statut_facture_f_enum AS ENUM
(
'impayee',
'partielle',
'payee',
'annulee'
);

CREATE TYPE mode_paiement_enum AS ENUM
(
'espece',
'cheque',
'virement',
'mobile_money',
'carte'
);

CREATE TYPE etat_produit_reception_enum AS ENUM
(
'bon',
'abime',
'perime',
'a_verifier'
);

-- ==========================================
-- DON
-- ==========================================

CREATE TABLE approvisionnement.don
(
    id_don SERIAL PRIMARY KEY,

    donateur VARCHAR(200) NOT NULL,

    contact_donateur VARCHAR(100),

    date_don DATE DEFAULT CURRENT_DATE,

    description TEXT,

    valeur_estimee NUMERIC(15,2) DEFAULT 0,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- BON COMMANDE FOURNISSEUR
-- ==========================================

CREATE TABLE approvisionnement.bon_commande_fourn
(
    id_bcf SERIAL PRIMARY KEY,

    id_fournisseur INTEGER NOT NULL,

    id_utilisateur INTEGER NOT NULL,

    date_commande DATE DEFAULT CURRENT_DATE,

    statut statut_bcf_enum DEFAULT 'brouillon',

    reference VARCHAR(50) UNIQUE NOT NULL,

    montant_total NUMERIC(15,2) DEFAULT 0,

    observations TEXT,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- LIGNE COMMANDE FOURNISSEUR
-- ==========================================

CREATE TABLE approvisionnement.ligne_commande_fourn
(
    id_lcf SERIAL PRIMARY KEY,

    id_bcf INTEGER NOT NULL,

    id_produit INTEGER NOT NULL,

    qte_commandee NUMERIC(15,3) NOT NULL,

    prix_unitaire NUMERIC(15,2) NOT NULL,

    montant_ligne NUMERIC(15,2)
        GENERATED ALWAYS AS
        (qte_commandee * prix_unitaire) STORED
);

-- ==========================================
-- BON RECEPTION
-- ==========================================

CREATE TABLE approvisionnement.bon_reception
(
    id_br SERIAL PRIMARY KEY,

    id_bcf INTEGER,

    id_utilisateur INTEGER NOT NULL,

    date_reception DATE DEFAULT CURRENT_DATE,

    reference VARCHAR(50) UNIQUE NOT NULL,

    statut statut_reception_enum DEFAULT 'en_attente',

    observations TEXT,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- LIGNE RECEPTION
-- ==========================================

CREATE TABLE approvisionnement.ligne_reception
(
    id_lr SERIAL PRIMARY KEY,

    id_br INTEGER NOT NULL,

    id_produit INTEGER NOT NULL,

    qte_recue NUMERIC(15,3) NOT NULL,

    prix_unitaire NUMERIC(15,2) NOT NULL,

    etat_produit etat_produit_reception_enum DEFAULT 'bon',

    observations TEXT
);

-- ==========================================
-- BON ENTREE
-- ==========================================

CREATE TABLE approvisionnement.bon_entree
(
    id_be SERIAL PRIMARY KEY,

    id_br INTEGER,

    id_don INTEGER,

    id_utilisateur INTEGER NOT NULL,

    date_entree DATE DEFAULT CURRENT_DATE,

    reference VARCHAR(50) UNIQUE NOT NULL,

    type_source type_source_entree_enum NOT NULL,

    observations TEXT,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- LIGNE BON ENTREE
-- ==========================================

CREATE TABLE approvisionnement.ligne_bon_entree
(
    id_lbe SERIAL PRIMARY KEY,

    id_be INTEGER NOT NULL,

    id_produit INTEGER NOT NULL,

    quantite NUMERIC(15,3) NOT NULL,

    prix_unitaire NUMERIC(15,2) DEFAULT 0,

    observations TEXT
);

-- ==========================================
-- FACTURE FOURNISSEUR
-- ==========================================

CREATE TABLE approvisionnement.facture_fournisseur
(
    id_facture_f SERIAL PRIMARY KEY,

    id_fournisseur INTEGER NOT NULL,

    id_bcf INTEGER,

    date_facture DATE NOT NULL,

    numero_facture VARCHAR(100) NOT NULL,

    montant_ht NUMERIC(15,2) DEFAULT 0,

    taux_tva NUMERIC(5,2) DEFAULT 19.25,

    montant_ttc NUMERIC(15,2) DEFAULT 0,

    statut statut_facture_f_enum DEFAULT 'impayee',

    reference VARCHAR(100),

    date_echeance DATE,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- LIGNE FACTURE FOURNISSEUR
-- ==========================================

CREATE TABLE approvisionnement.ligne_facture_fourn
(
    id_ligne_ff SERIAL PRIMARY KEY,

    id_facture_f INTEGER NOT NULL,

    id_produit INTEGER NOT NULL,

    quantite NUMERIC(15,3) NOT NULL,

    prix_unitaire NUMERIC(15,2) NOT NULL,

    montant_ligne NUMERIC(15,2)
        GENERATED ALWAYS AS
        (quantite * prix_unitaire) STORED
);

-- ==========================================
-- PAIEMENT FOURNISSEUR
-- ==========================================

CREATE TABLE approvisionnement.paiement_fournisseur
(
    id_paiement SERIAL PRIMARY KEY,

    id_fournisseur INTEGER NOT NULL,

    id_facture_f INTEGER NOT NULL,

    id_utilisateur INTEGER NOT NULL,

    montant NUMERIC(15,2) NOT NULL,

    date_paiement DATE DEFAULT CURRENT_DATE,

    mode_paiement mode_paiement_enum NOT NULL,

    reference VARCHAR(100),

    observations TEXT,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- FOREIGN KEYS
-- ==========================================

ALTER TABLE approvisionnement.bon_commande_fourn
ADD CONSTRAINT fk_bcf_fournisseur
FOREIGN KEY(id_fournisseur)
REFERENCES structure.fournisseur(id_fournisseur)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.bon_commande_fourn
ADD CONSTRAINT fk_bcf_utilisateur
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_commande_fourn
ADD CONSTRAINT fk_lcf_bcf
FOREIGN KEY(id_bcf)
REFERENCES approvisionnement.bon_commande_fourn(id_bcf)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_commande_fourn
ADD CONSTRAINT fk_lcf_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.bon_reception
ADD CONSTRAINT fk_br_bcf
FOREIGN KEY(id_bcf)
REFERENCES approvisionnement.bon_commande_fourn(id_bcf)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.bon_reception
ADD CONSTRAINT fk_br_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_reception
ADD CONSTRAINT fk_lr_br
FOREIGN KEY(id_br)
REFERENCES approvisionnement.bon_reception(id_br)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_reception
ADD CONSTRAINT fk_lr_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.bon_entree
ADD CONSTRAINT fk_be_br
FOREIGN KEY(id_br)
REFERENCES approvisionnement.bon_reception(id_br)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.bon_entree
ADD CONSTRAINT fk_be_don
FOREIGN KEY(id_don)
REFERENCES approvisionnement.don(id_don)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.bon_entree
ADD CONSTRAINT fk_be_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_bon_entree
ADD CONSTRAINT fk_lbe_be
FOREIGN KEY(id_be)
REFERENCES approvisionnement.bon_entree(id_be)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_bon_entree
ADD CONSTRAINT fk_lbe_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.facture_fournisseur
ADD CONSTRAINT fk_ff_fournisseur
FOREIGN KEY(id_fournisseur)
REFERENCES structure.fournisseur(id_fournisseur)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.facture_fournisseur
ADD CONSTRAINT fk_ff_bcf
FOREIGN KEY(id_bcf)
REFERENCES approvisionnement.bon_commande_fourn(id_bcf)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_facture_fourn
ADD CONSTRAINT fk_lff_facture
FOREIGN KEY(id_facture_f)
REFERENCES approvisionnement.facture_fournisseur(id_facture_f)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.ligne_facture_fourn
ADD CONSTRAINT fk_lff_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.paiement_fournisseur
ADD CONSTRAINT fk_pf_fournisseur
FOREIGN KEY(id_fournisseur)
REFERENCES structure.fournisseur(id_fournisseur)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.paiement_fournisseur
ADD CONSTRAINT fk_pf_facture
FOREIGN KEY(id_facture_f)
REFERENCES approvisionnement.facture_fournisseur(id_facture_f)
ON DELETE CASCADE;

ALTER TABLE approvisionnement.paiement_fournisseur
ADD CONSTRAINT fk_pf_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

-- ==========================================
-- ENUMS VENTE
-- ==========================================

DROP TYPE IF EXISTS statut_bl_enum CASCADE;
DROP TYPE IF EXISTS statut_facture_client_enum CASCADE;
DROP TYPE IF EXISTS motif_sortie_enum CASCADE;

CREATE TYPE statut_bl_enum AS ENUM
(
'en_cours',
'livre',
'partiel',
'annule'
);

CREATE TYPE statut_facture_client_enum AS ENUM
(
'impayee',
'partielle',
'payee',
'annulee'
);

CREATE TYPE motif_sortie_enum AS ENUM
(
'perime',
'non_vendu',
'retour_client',
'casse',
'don',
'autre'
);

-- ==========================================
-- COMMANDE CLIENT
-- ==========================================

CREATE TABLE vente.commande_client
(
    id_cc SERIAL PRIMARY KEY,

    id_client INTEGER NOT NULL,

    id_utilisateur INTEGER NOT NULL,

    date_commande DATE DEFAULT CURRENT_DATE,

    statut statut_commande_client_enum
        DEFAULT 'en_cours',

    montant_total NUMERIC(15,2)
        DEFAULT 0,

    reference VARCHAR(50)
        NOT NULL UNIQUE,

    type_vente type_vente_enum
        DEFAULT 'credit',

    observations TEXT,

    date_creation TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- LIGNE COMMANDE CLIENT
-- ==========================================

CREATE TABLE vente.ligne_commande_client
(
    id_lcc SERIAL PRIMARY KEY,

    id_cc INTEGER NOT NULL,

    id_produit INTEGER NOT NULL,

    quantite NUMERIC(15,3) NOT NULL,

    prix_unitaire NUMERIC(15,2) NOT NULL,

    taux_remise NUMERIC(5,2)
        DEFAULT 0,

    montant_ligne NUMERIC(15,2)
        GENERATED ALWAYS AS
        (
            quantite *
            prix_unitaire *
            (1 - taux_remise / 100)
        ) STORED
);

-- ==========================================
-- BON LIVRAISON
-- ==========================================

CREATE TABLE vente.bon_livraison
(
    id_bl SERIAL PRIMARY KEY,

    id_cc INTEGER NOT NULL,

    id_utilisateur INTEGER NOT NULL,

    date_livraison DATE
        DEFAULT CURRENT_DATE,

    reference VARCHAR(50)
        NOT NULL UNIQUE,

    statut statut_bl_enum
        DEFAULT 'en_cours',

    observations TEXT,

    date_creation TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- LIGNE LIVRAISON
-- ==========================================

CREATE TABLE vente.ligne_livraison
(
    id_ll SERIAL PRIMARY KEY,

    id_bl INTEGER NOT NULL,

    id_produit INTEGER NOT NULL,

    qte_livree NUMERIC(15,3)
        NOT NULL,

    observations TEXT
);

-- ==========================================
-- FACTURE CLIENT
-- ==========================================

CREATE TABLE vente.facture_client
(
    id_facture SERIAL PRIMARY KEY,

    id_cc INTEGER NOT NULL,

    id_utilisateur INTEGER NOT NULL,

    date_facture DATE
        DEFAULT CURRENT_DATE,

    montant_ht NUMERIC(15,2)
        DEFAULT 0,

    taux_tva NUMERIC(5,2)
        DEFAULT 19.25,

    montant_ttc NUMERIC(15,2)
        DEFAULT 0,

    reference VARCHAR(50)
        UNIQUE NOT NULL,

    statut statut_facture_client_enum
        DEFAULT 'impayee',

    date_echeance DATE,

    date_creation TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP,

    date_modif TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- REGLEMENT CLIENT
-- ==========================================

CREATE TABLE vente.reglement_client
(
    id_reglement SERIAL PRIMARY KEY,

    id_facture INTEGER NOT NULL,

    id_client INTEGER NOT NULL,

    id_utilisateur INTEGER NOT NULL,

    montant NUMERIC(15,2)
        NOT NULL,

    date_reglement DATE
        DEFAULT CURRENT_DATE,

    mode_paiement mode_paiement_enum
        NOT NULL,

    reference VARCHAR(100),

    observations TEXT,

    date_creation TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- SORTIE STOCK
-- ==========================================

CREATE TABLE vente.sortie_stock
(
    id_sortie SERIAL PRIMARY KEY,

    id_produit INTEGER NOT NULL,

    id_client INTEGER,

    id_utilisateur INTEGER NOT NULL,

    date_sortie DATE
        DEFAULT CURRENT_DATE,

    quantite NUMERIC(15,3)
        NOT NULL,

    motif_sortie motif_sortie_enum
        NOT NULL,

    reference VARCHAR(50)
        NOT NULL,

    observations TEXT,

    date_creation TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- FOREIGN KEYS
-- ==========================================

ALTER TABLE vente.commande_client
ADD CONSTRAINT fk_cc_client
FOREIGN KEY(id_client)
REFERENCES structure.client(id_client)
ON DELETE CASCADE;

ALTER TABLE vente.commande_client
ADD CONSTRAINT fk_cc_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

ALTER TABLE vente.ligne_commande_client
ADD CONSTRAINT fk_lcc_cc
FOREIGN KEY(id_cc)
REFERENCES vente.commande_client(id_cc)
ON DELETE CASCADE;

ALTER TABLE vente.ligne_commande_client
ADD CONSTRAINT fk_lcc_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE vente.bon_livraison
ADD CONSTRAINT fk_bl_cc
FOREIGN KEY(id_cc)
REFERENCES vente.commande_client(id_cc)
ON DELETE CASCADE;

ALTER TABLE vente.bon_livraison
ADD CONSTRAINT fk_bl_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

ALTER TABLE vente.ligne_livraison
ADD CONSTRAINT fk_ll_bl
FOREIGN KEY(id_bl)
REFERENCES vente.bon_livraison(id_bl)
ON DELETE CASCADE;

ALTER TABLE vente.ligne_livraison
ADD CONSTRAINT fk_ll_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE vente.facture_client
ADD CONSTRAINT fk_facture_cc
FOREIGN KEY(id_cc)
REFERENCES vente.commande_client(id_cc)
ON DELETE CASCADE;

ALTER TABLE vente.facture_client
ADD CONSTRAINT fk_facture_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

ALTER TABLE vente.reglement_client
ADD CONSTRAINT fk_reglement_facture
FOREIGN KEY(id_facture)
REFERENCES vente.facture_client(id_facture)
ON DELETE CASCADE;

ALTER TABLE vente.reglement_client
ADD CONSTRAINT fk_reglement_client
FOREIGN KEY(id_client)
REFERENCES structure.client(id_client)
ON DELETE CASCADE;

ALTER TABLE vente.reglement_client
ADD CONSTRAINT fk_reglement_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

ALTER TABLE vente.sortie_stock
ADD CONSTRAINT fk_sortie_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE vente.sortie_stock
ADD CONSTRAINT fk_sortie_client
FOREIGN KEY(id_client)
REFERENCES structure.client(id_client)
ON DELETE CASCADE;

ALTER TABLE vente.sortie_stock
ADD CONSTRAINT fk_sortie_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;


-- =====================================================
-- TABLE MOUVEMENT STOCK
-- =====================================================

DROP TYPE IF EXISTS type_mouvement_stock_enum CASCADE;

CREATE TYPE type_mouvement_stock_enum AS ENUM
(
'entree_achat',
'entree_don',
'entree_retour',
'sortie_vente',
'sortie_perime',
'sortie_casse',
'ajustement'
);

CREATE TABLE structure.mouvement_stock
(
    id_mouvement BIGSERIAL PRIMARY KEY,

    id_produit INTEGER NOT NULL,

    id_utilisateur INTEGER NOT NULL,

    date_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    type_mouvement type_mouvement_stock_enum NOT NULL,

    quantite NUMERIC(15,3) NOT NULL,

    stock_avant NUMERIC(15,3) NOT NULL,

    stock_apres NUMERIC(15,3) NOT NULL,

    ref_document VARCHAR(100),

    type_document VARCHAR(50),

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- FOREIGN KEYS
-- =====================================================

ALTER TABLE structure.mouvement_stock
ADD CONSTRAINT fk_mv_produit
FOREIGN KEY(id_produit)
REFERENCES structure.produit(id_produit)
ON DELETE CASCADE;

ALTER TABLE structure.mouvement_stock
ADD CONSTRAINT fk_mv_user
FOREIGN KEY(id_utilisateur)
REFERENCES utilisateur.utilisateur(id_utilisateur)
ON DELETE CASCADE;

-- =====================================================
-- FONCTION ENTREE STOCK
-- =====================================================

CREATE OR REPLACE FUNCTION structure.fn_entree_stock()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_stock NUMERIC(15,3);
    v_user INTEGER;
BEGIN

    SELECT stock_actuel
    INTO v_stock
    FROM structure.produit
    WHERE id_produit = NEW.id_produit;

    SELECT id_utilisateur
    INTO v_user
    FROM approvisionnement.bon_entree
    WHERE id_be = NEW.id_be;

    UPDATE structure.produit
    SET stock_actuel = stock_actuel + NEW.quantite
    WHERE id_produit = NEW.id_produit;

    INSERT INTO structure.mouvement_stock
    (
        id_produit,
        id_utilisateur,
        type_mouvement,
        quantite,
        stock_avant,
        stock_apres,
        ref_document,
        type_document
    )
    VALUES
    (
        NEW.id_produit,
        v_user,
        'entree_achat',
        NEW.quantite,
        v_stock,
        v_stock + NEW.quantite,
        NEW.id_be::TEXT,
        'BON_ENTREE'
    );

    RETURN NEW;
END;
$$;

-- =====================================================
-- TRIGGER BON ENTREE
-- =====================================================

CREATE TRIGGER trg_entree_stock
AFTER INSERT OR UPDATE
ON approvisionnement.ligne_bon_entree
FOR EACH ROW
EXECUTE FUNCTION structure.fn_entree_stock();

-- =====================================================
-- FONCTION SORTIE STOCK
-- =====================================================

CREATE OR REPLACE FUNCTION structure.fn_sortie_stock()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_stock NUMERIC(15,3);
BEGIN

    SELECT stock_actuel
    INTO v_stock
    FROM structure.produit
    WHERE id_produit = NEW.id_produit;

    IF v_stock < NEW.quantite THEN
        RAISE EXCEPTION
        'Stock insuffisant pour le produit %',
        NEW.id_produit;
    END IF;

    UPDATE structure.produit
    SET stock_actuel = stock_actuel - NEW.quantite
    WHERE id_produit = NEW.id_produit;

    INSERT INTO structure.mouvement_stock
    (
        id_produit,
        id_utilisateur,
        type_mouvement,
        quantite,
        stock_avant,
        stock_apres,
        ref_document,
        type_document
    )
    VALUES
    (
        NEW.id_produit,
        NEW.id_utilisateur,
        'sortie_perime',
        NEW.quantite,
        v_stock,
        v_stock - NEW.quantite,
        NEW.reference,
        'SORTIE_STOCK'
    );

    RETURN NEW;
END;
$$;

-- =====================================================
-- TRIGGER SORTIE STOCK
-- =====================================================

CREATE TRIGGER trg_sortie_stock
AFTER INSERT OR UPDATE
ON vente.sortie_stock
FOR EACH ROW
EXECUTE FUNCTION structure.fn_sortie_stock();

-- =====================================================
-- LIVRAISON CLIENT
-- =====================================================

CREATE OR REPLACE FUNCTION structure.fn_livraison_client()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_stock NUMERIC(15,3);
    v_user INTEGER;
BEGIN

    SELECT stock_actuel
    INTO v_stock
    FROM structure.produit
    WHERE id_produit = NEW.id_produit;

    SELECT bl.id_utilisateur
    INTO v_user
    FROM vente.bon_livraison bl
    WHERE bl.id_bl = NEW.id_bl;

    IF v_stock < NEW.qte_livree THEN
        RAISE EXCEPTION
        'Stock insuffisant pour livraison';
    END IF;

    UPDATE structure.produit
    SET stock_actuel = stock_actuel - NEW.qte_livree
    WHERE id_produit = NEW.id_produit;

    INSERT INTO structure.mouvement_stock
    (
        id_produit,
        id_utilisateur,
        type_mouvement,
        quantite,
        stock_avant,
        stock_apres,
        ref_document,
        type_document
    )
    VALUES
    (
        NEW.id_produit,
        v_user,
        'sortie_vente',
        NEW.qte_livree,
        v_stock,
        v_stock - NEW.qte_livree,
        NEW.id_bl::TEXT,
        'BON_LIVRAISON'
    );

    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_livraison_stock
AFTER INSERT OR UPDATE
ON vente.ligne_livraison
FOR EACH ROW
EXECUTE FUNCTION structure.fn_livraison_client();

-- =====================================================
-- AUDIT AUTOMATIQUE PRODUIT
-- =====================================================

CREATE OR REPLACE FUNCTION utilisateur.fn_audit_produit()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
BEGIN

    INSERT INTO utilisateur.journal_audit
    (
        action,
        table_cible,
        id_enregistrement,
        ancienne_valeur,
        nouvelle_valeur
    )
    VALUES
    (
        TG_OP,
        'produit',
        COALESCE(NEW.id_produit,OLD.id_produit)::TEXT,
        to_jsonb(OLD),
        to_jsonb(NEW)
    );

    RETURN COALESCE(NEW,OLD);
END;
$$;

CREATE TRIGGER trg_audit_produit
AFTER INSERT OR UPDATE OR DELETE
ON structure.produit
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_audit_produit();

-- =====================================================
-- CONTROLE STOCK NEGATIF
-- =====================================================

CREATE OR REPLACE FUNCTION structure.fn_controle_stock()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
BEGIN

    IF NEW.stock_actuel < 0 THEN
        RAISE EXCEPTION
        'Stock negatif interdit';
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_controle_stock
BEFORE UPDATE
ON structure.produit
FOR EACH ROW
EXECUTE FUNCTION structure.fn_controle_stock();

-- =====================================================
-- PARTIE E
-- CORBEILLE XML ET SAUVEGARDE AVANT SUPPRESSION
-- =====================================================

-- ==========================================
-- PRODUIT
-- ==========================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_produit()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
v_xml XML;
BEGIN

v_xml :=
xmlelement(
    name produit,
    xmlforest(
        OLD.id_produit,
        OLD.id_famille,
        OLD.nom_produit,
        OLD.prix_achat,
        OLD.prix_vente,
        OLD.stock_actuel
    )
);

INSERT INTO utilisateur.corbeille_xml
(
    type_objet,
    id_objet,
    donnees_xml
)
VALUES
(
    'PRODUIT',
    OLD.id_produit,
    v_xml
);

RETURN OLD;

END;
$$;

CREATE TRIGGER trg_backup_produit
BEFORE DELETE
ON structure.produit
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_produit();

-- ==========================================
-- FOURNISSEUR
-- ==========================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_fournisseur()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
v_xml XML;
BEGIN


v_xml :=
xmlelement(
    name fournisseur,
    xmlforest(
        OLD.id_fournisseur,
        OLD.nom,
        OLD.tel,
        OLD.email
    )
);

INSERT INTO utilisateur.corbeille_xml
(
    type_objet,
    id_objet,
    donnees_xml
)
VALUES
(
    'FOURNISSEUR',
    OLD.id_fournisseur,
    v_xml
);

RETURN OLD;


END;
$$;

CREATE TRIGGER trg_backup_fournisseur
BEFORE DELETE
ON structure.fournisseur
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_fournisseur();

-- ==========================================
-- UTILISATEUR
-- ==========================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_utilisateur()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
v_xml XML;
BEGIN


v_xml :=
xmlelement(
    name utilisateur,
    xmlforest(
        OLD.id_utilisateur,
        OLD.id_groupe,
        OLD.nom_complet,
        OLD.login
    )
);

INSERT INTO utilisateur.corbeille_xml
(
    type_objet,
    id_objet,
    donnees_xml
)
VALUES
(
    'UTILISATEUR',
    OLD.id_utilisateur,
    v_xml
);

RETURN OLD;


END;
$$;

CREATE TRIGGER trg_backup_utilisateur
BEFORE DELETE
ON utilisateur.utilisateur
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_utilisateur();

-- ==========================================
-- GROUPE
-- ==========================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_groupe()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
v_xml XML;
BEGIN


v_xml :=
xmlelement(
    name groupe,
    xmlforest(
        OLD.id_groupe,
        OLD.nom_groupe
    )
);

INSERT INTO utilisateur.corbeille_xml
(
    type_objet,
    id_objet,
    donnees_xml
)
VALUES
(
    'GROUPE',
    OLD.id_groupe,
    v_xml
);

RETURN OLD;


END;
$$;

CREATE TRIGGER trg_backup_groupe
BEFORE DELETE
ON utilisateur.groupe
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_groupe();

-- ==========================================
-- COMMANDE FOURNISSEUR
-- ==========================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_bcf()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
v_xml XML;
BEGIN


v_xml :=
xmlelement(
    name commande_fournisseur,
    xmlforest(
        OLD.id_bcf,
        OLD.id_fournisseur,
        OLD.reference,
        OLD.montant_total
    )
);

INSERT INTO utilisateur.corbeille_xml
(
    type_objet,
    id_objet,
    donnees_xml
)
VALUES
(
    'BON_COMMANDE_FOURN',
    OLD.id_bcf,
    v_xml
);

RETURN OLD;


END;
$$;

CREATE TRIGGER trg_backup_bcf
BEFORE DELETE
ON approvisionnement.bon_commande_fourn
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_bcf();

-- ==========================================
-- COMMANDE CLIENT
-- ==========================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_cc()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
v_xml XML;
BEGIN

v_xml :=
xmlelement(
    name commande_client,
    xmlforest(
        OLD.id_cc,
        OLD.id_client,
        OLD.reference,
        OLD.montant_total
    )
);

INSERT INTO utilisateur.corbeille_xml
(
    type_objet,
    id_objet,
    donnees_xml
)
VALUES
(
    'COMMANDE_CLIENT',
    OLD.id_cc,
    v_xml
);

RETURN OLD;


END;
$$;

CREATE TRIGGER trg_backup_cc
BEFORE DELETE
ON vente.commande_client
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_cc();

-- ==========================================
-- INDEX CORBEILLE
-- ==========================================

CREATE INDEX idx_corbeille_type
ON utilisateur.corbeille_xml(type_objet);

CREATE INDEX idx_corbeille_objet
ON utilisateur.corbeille_xml(id_objet);

-- ==========================================
-- VUE CONSULTATION CORBEILLE
-- ==========================================

CREATE OR REPLACE VIEW utilisateur.v_corbeille AS
SELECT
id_corbeille,
type_objet,
id_objet,
date_suppression
FROM utilisateur.corbeille_xml;


CREATE OR REPLACE FUNCTION utilisateur.fn_backup_bcf_complet()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_xml XML;
BEGIN

    SELECT
    xmlelement
    (
        NAME commande_fournisseur,

        xmlelement
        (
            NAME entete,

            xmlforest
            (
                OLD.id_bcf,
                OLD.id_fournisseur,
                OLD.reference,
                OLD.montant_total
            )
        ),

        (
            SELECT xmlagg
            (
                xmlelement
                (
                    NAME ligne,

                    xmlforest
                    (
                        l.id_lcf,
                        l.id_produit,
                        l.qte_commandee,
                        l.prix_unitaire
                    )
                )
            )
            FROM approvisionnement.ligne_commande_fourn l
            WHERE l.id_bcf=OLD.id_bcf
        ),

        (
            SELECT xmlagg
            (
                xmlelement
                (
                    NAME reception,

                    xmlforest
                    (
                        r.id_br,
                        r.reference,
                        r.date_reception
                    )
                )
            )
            FROM approvisionnement.bon_reception r
            WHERE r.id_bcf=OLD.id_bcf
        )

    )
    INTO v_xml;

    INSERT INTO utilisateur.corbeille_xml
    (
        type_objet,
        id_objet,
        donnees_xml
    )
    VALUES
    (
        'COMMANDE_FOURN_COMPLETE',
        OLD.id_bcf,
        v_xml
    );

    RETURN OLD;

END;
$$;


-- ==========================================================
-- PARTIE E V2
-- CORBEILLE XML COMPLETE
-- SAUVEGARDE HIERARCHIQUE
-- ==========================================================

DROP TRIGGER IF EXISTS trg_backup_bcf ON approvisionnement.bon_commande_fourn;
DROP FUNCTION IF EXISTS utilisateur.fn_backup_bcf();

-- ==========================================================
-- COMMANDE FOURNISSEUR COMPLETE
-- ==========================================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_bcf_complet()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_xml XML;
BEGIN

    SELECT
    xmlelement
    (
        NAME commande_fournisseur,

        xmlelement
        (
            NAME entete,

            xmlforest
            (
                OLD.id_bcf,
                OLD.id_fournisseur,
                OLD.id_utilisateur,
                OLD.date_commande,
                OLD.statut,
                OLD.reference,
                OLD.montant_total,
                OLD.observations
            )
        ),

        xmlelement
        (
            NAME lignes_commande,

            (
                SELECT xmlagg
                (
                    xmlelement
                    (
                        NAME ligne,

                        xmlforest
                        (
                            l.id_lcf,
                            l.id_produit,
                            l.qte_commandee,
                            l.prix_unitaire
                        )
                    )
                )
                FROM approvisionnement.ligne_commande_fourn l
                WHERE l.id_bcf = OLD.id_bcf
            )
        ),

        xmlelement
        (
            NAME receptions,

            (
                SELECT xmlagg
                (
                    xmlelement
                    (
                        NAME reception,

                        xmlforest
                        (
                            br.id_br,
                            br.reference,
                            br.date_reception,
                            br.statut
                        ),

                        (
                            SELECT xmlagg
                            (
                                xmlelement
                                (
                                    NAME ligne_reception,

                                    xmlforest
                                    (
                                        lr.id_lr,
                                        lr.id_produit,
                                        lr.qte_recue,
                                        lr.prix_unitaire
                                    )
                                )
                            )
                            FROM approvisionnement.ligne_reception lr
                            WHERE lr.id_br = br.id_br
                        )
                    )
                )
                FROM approvisionnement.bon_reception br
                WHERE br.id_bcf = OLD.id_bcf
            )
        ),

        xmlelement
        (
            NAME factures,

            (
                SELECT xmlagg
                (
                    xmlelement
                    (
                        NAME facture,

                        xmlforest
                        (
                            ff.id_facture_f,
                            ff.numero_facture,
                            ff.montant_ht,
                            ff.montant_ttc,
                            ff.statut
                        ),

                        (
                            SELECT xmlagg
                            (
                                xmlelement
                                (
                                    NAME ligne_facture,

                                    xmlforest
                                    (
                                        lf.id_ligne_ff,
                                        lf.id_produit,
                                        lf.quantite,
                                        lf.prix_unitaire
                                    )
                                )
                            )
                            FROM approvisionnement.ligne_facture_fourn lf
                            WHERE lf.id_facture_f = ff.id_facture_f
                        )
                    )
                )
                FROM approvisionnement.facture_fournisseur ff
                WHERE ff.id_bcf = OLD.id_bcf
            )
        ),

        xmlelement
        (
            NAME paiements,

            (
                SELECT xmlagg
                (
                    xmlelement
                    (
                        NAME paiement,

                        xmlforest
                        (
                            p.id_paiement,
                            p.montant,
                            p.date_paiement,
                            p.mode_paiement,
                            p.reference
                        )
                    )
                )
                FROM approvisionnement.paiement_fournisseur p
                INNER JOIN approvisionnement.facture_fournisseur f
                ON f.id_facture_f = p.id_facture_f
                WHERE f.id_bcf = OLD.id_bcf
            )
        )
    )
    INTO v_xml;

    INSERT INTO utilisateur.corbeille_xml
    (
        type_objet,
        id_objet,
        donnees_xml
    )
    VALUES
    (
        'COMMANDE_FOURN_COMPLETE',
        OLD.id_bcf,
        v_xml
    );

    RETURN OLD;

END;
$$;

CREATE TRIGGER trg_backup_bcf_complet
BEFORE DELETE
ON approvisionnement.bon_commande_fourn
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_bcf_complet();

-- ==========================================================
-- PRODUIT COMPLET
-- ==========================================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_produit_complet()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_xml XML;
BEGIN

    SELECT
    xmlelement
    (
        NAME produit,

        xmlforest
        (
            OLD.id_produit,
            OLD.id_famille,
            OLD.id_produit_pere,
            OLD.nom_produit,
            OLD.prix_achat,
            OLD.prix_vente,
            OLD.stock_actuel
        ),

        xmlelement
        (
            NAME mouvements_stock,

            (
                SELECT xmlagg
                (
                    xmlelement
                    (
                        NAME mouvement,

                        xmlforest
                        (
                            m.id_mouvement,
                            m.type_mouvement,
                            m.quantite,
                            m.stock_avant,
                            m.stock_apres
                        )
                    )
                )
                FROM structure.mouvement_stock m
                WHERE m.id_produit = OLD.id_produit
            )
        )
    )
    INTO v_xml;

    INSERT INTO utilisateur.corbeille_xml
    (
        type_objet,
        id_objet,
        donnees_xml
    )
    VALUES
    (
        'PRODUIT_COMPLET',
        OLD.id_produit,
        v_xml
    );

    RETURN OLD;

END;
$$;

DROP TRIGGER IF EXISTS trg_backup_produit
ON structure.produit;

CREATE TRIGGER trg_backup_produit_complet
BEFORE DELETE
ON structure.produit
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_produit_complet();

-- ==========================================================
-- FOURNISSEUR COMPLET
-- ==========================================================

CREATE OR REPLACE FUNCTION utilisateur.fn_backup_fournisseur_complet()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_xml XML;
BEGIN

    SELECT
    xmlelement
    (
        NAME fournisseur,

        xmlforest
        (
            OLD.id_fournisseur,
            OLD.nom,
            OLD.tel,
            OLD.email,
            OLD.nif
        ),

        xmlelement
        (
            NAME commandes,

            (
                SELECT xmlagg
                (
                    xmlelement
                    (
                        NAME commande,
                        xmlforest
                        (
                            c.id_bcf,
                            c.reference,
                            c.montant_total
                        )
                    )
                )
                FROM approvisionnement.bon_commande_fourn c
                WHERE c.id_fournisseur = OLD.id_fournisseur
            )
        ),

        xmlelement
        (
            NAME factures,

            (
                SELECT xmlagg
                (
                    xmlelement
                    (
                        NAME facture,
                        xmlforest
                        (
                            f.id_facture_f,
                            f.numero_facture,
                            f.montant_ttc
                        )
                    )
                )
                FROM approvisionnement.facture_fournisseur f
                WHERE f.id_fournisseur = OLD.id_fournisseur
            )
        )
    )
    INTO v_xml;

    INSERT INTO utilisateur.corbeille_xml
    (
        type_objet,
        id_objet,
        donnees_xml
    )
    VALUES
    (
        'FOURNISSEUR_COMPLET',
        OLD.id_fournisseur,
        v_xml
    );

    RETURN OLD;

END;
$$;

DROP TRIGGER IF EXISTS trg_backup_fournisseur
ON structure.fournisseur;

CREATE TRIGGER trg_backup_fournisseur_complet
BEFORE DELETE
ON structure.fournisseur
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_fournisseur_complet();

-- ==========================================================
-- INDEX CORBEILLE
-- ==========================================================

CREATE INDEX IF NOT EXISTS idx_corbeille_type
ON utilisateur.corbeille_xml(type_objet);

CREATE INDEX IF NOT EXISTS idx_corbeille_objet
ON utilisateur.corbeille_xml(id_objet);

-- ==========================================================
-- VUE DE CONSULTATION
-- ==========================================================

CREATE OR REPLACE VIEW utilisateur.v_corbeille AS
SELECT
    id_corbeille,
    type_objet,
    id_objet,
    date_suppression
FROM utilisateur.corbeille_xml;


SELECT setval('utilisateur.groupe_id_groupe_seq', (SELECT MAX(id_groupe) FROM utilisateur.groupe));


-- Droits de base
-- ==========================================
-- DROITS COMPLETS POUR GESTION STOCK
-- ==========================================

INSERT INTO utilisateur.droit (nom_droit, module, description) VALUES

-- ==========================================
-- MODULE UTILISATEURS
-- ==========================================
('creer_groupe', 'utilisateurs', 'Créer un groupe'),
('modifier_groupe', 'utilisateurs', 'Modifier un groupe'),
('supprimer_groupe', 'utilisateurs', 'Supprimer un groupe'),
('lister_groupes', 'utilisateurs', 'Consulter la liste des groupes'),
('affecter_droits', 'utilisateurs', 'Affecter des droits à un groupe'),
('creer_utilisateur', 'utilisateurs', 'Créer un utilisateur'),
('modifier_utilisateur', 'utilisateurs', 'Modifier un utilisateur'),
('supprimer_utilisateur', 'utilisateurs', 'Supprimer un utilisateur'),
('lister_utilisateurs', 'utilisateurs', 'Consulter la liste des utilisateurs'),
('voir_profil', 'utilisateurs', 'Voir son propre profil'),
('modifier_profil', 'utilisateurs', 'Modifier son mot de passe / profil'),
('voir_journal_audit', 'utilisateurs', 'Consulter le journal d''audit'),
('restaurer_corbeille', 'utilisateurs', 'Restaurer un objet depuis la corbeille XML'),
('vider_corbeille', 'utilisateurs', 'Vider définitivement la corbeille XML'),

-- ==========================================
-- MODULE STRUCTURE (GESTION GLOBALE)
-- ==========================================
-- Familles
('creer_famille', 'structure', 'Créer une famille de produits'),
('modifier_famille', 'structure', 'Modifier une famille'),
('supprimer_famille', 'structure', 'Supprimer une famille'),
('lister_familles', 'structure', 'Consulter la liste des familles'),

-- Produits
('creer_produit', 'structure', 'Créer un produit'),
('modifier_produit', 'structure', 'Modifier un produit'),
('supprimer_produit', 'structure', 'Supprimer un produit'),
('lister_produits', 'structure', 'Consulter la liste des produits'),
('voir_produit', 'structure', 'Voir le détail d''un produit'),
('fractionner_produit', 'structure', 'Fractionner un produit père en produits fils'),
('creer_produit_fils', 'structure', 'Créer un produit fils directement'),
('exporter_produits', 'structure', 'Exporter la liste des produits (CSV/PDF)'),
('imprimer_etiquette', 'structure', 'Imprimer une étiquette produit'),

-- Fournisseurs
('creer_fournisseur', 'structure', 'Créer un fournisseur'),
('modifier_fournisseur', 'structure', 'Modifier un fournisseur'),
('supprimer_fournisseur', 'structure', 'Supprimer un fournisseur'),
('lister_fournisseurs', 'structure', 'Consulter la liste des fournisseurs'),

-- Clients
('creer_client', 'structure', 'Créer un client'),
('modifier_client', 'structure', 'Modifier un client'),
('supprimer_client', 'structure', 'Supprimer un client'),
('lister_clients', 'structure', 'Consulter la liste des clients'),
('voir_credit_client', 'structure', 'Consulter le crédit d''un client'),

-- Catégories clients
('creer_categorie_client', 'structure', 'Créer une catégorie de client'),
('modifier_categorie_client', 'structure', 'Modifier une catégorie'),
('supprimer_categorie_client', 'structure', 'Supprimer une catégorie'),
('lister_categories_client', 'structure', 'Consulter la liste des catégories'),

-- Banques & comptes bancaires
('creer_banque', 'structure', 'Créer une banque'),
('modifier_banque', 'structure', 'Modifier une banque'),
('supprimer_banque', 'structure', 'Supprimer une banque'),
('lister_banques', 'structure', 'Consulter la liste des banques'),
('creer_compte_bancaire', 'structure', 'Créer un compte bancaire'),
('modifier_compte_bancaire', 'structure', 'Modifier un compte'),
('supprimer_compte_bancaire', 'structure', 'Supprimer un compte'),
('lister_comptes_bancaires', 'structure', 'Consulter la liste des comptes'),
('effectuer_versement', 'structure', 'Effectuer un versement sur un compte'),
('effectuer_retrait', 'structure', 'Effectuer un retrait'),
('consulter_solde', 'structure', 'Consulter le solde d''un compte'),
('etat_versements', 'structure', 'Consulter l''état des versements par période'),

-- Mouvements de stock
('lister_mouvements_stock', 'structure', 'Consulter l''historique des mouvements'),
('exporter_mouvements', 'structure', 'Exporter les mouvements de stock'),

-- ==========================================
-- MODULE APPROVISIONNEMENTS
-- ==========================================
-- Bons de commande fournisseur
('creer_bcf', 'approvisionnement', 'Créer un bon de commande fournisseur'),
('modifier_bcf', 'approvisionnement', 'Modifier un bon de commande (brouillon)'),
('valider_bcf', 'approvisionnement', 'Valider / envoyer un bon de commande'),
('annuler_bcf', 'approvisionnement', 'Annuler un bon de commande'),
('supprimer_bcf', 'approvisionnement', 'Supprimer un bon de commande'),
('lister_bcf', 'approvisionnement', 'Consulter la liste des bons de commande'),
('imprimer_bcf', 'approvisionnement', 'Imprimer un bon de commande'),

-- Réceptions
('creer_reception', 'approvisionnement', 'Enregistrer une réception de commande'),
('modifier_reception', 'approvisionnement', 'Modifier une réception'),
('valider_reception', 'approvisionnement', 'Valider une réception (passer en complet)'),
('lister_receptions', 'approvisionnement', 'Consulter la liste des réceptions'),
('imprimer_bon_reception', 'approvisionnement', 'Imprimer un bon de réception'),

-- Bons d'entrée (achats, dons, retours)
('creer_bon_entree', 'approvisionnement', 'Créer un bon d''entrée'),
('modifier_bon_entree', 'approvisionnement', 'Modifier un bon d''entrée'),
('supprimer_bon_entree', 'approvisionnement', 'Supprimer un bon d''entrée'),
('lister_bons_entree', 'approvisionnement', 'Consulter la liste des bons d''entrée'),
('imprimer_bon_entree', 'approvisionnement', 'Imprimer un bon d''entrée'),

-- Dons
('saisir_don', 'approvisionnement', 'Enregistrer un don (entrée gratuite)'),
('modifier_don', 'approvisionnement', 'Modifier un don'),
('supprimer_don', 'approvisionnement', 'Supprimer un don'),
('lister_dons', 'approvisionnement', 'Consulter la liste des dons'),

-- Factures fournisseurs
('creer_facture_fournisseur', 'approvisionnement', 'Saisir une facture fournisseur'),
('modifier_facture_fournisseur', 'approvisionnement', 'Modifier une facture'),
('supprimer_facture_fournisseur', 'approvisionnement', 'Supprimer une facture'),
('lister_factures_fournisseur', 'approvisionnement', 'Consulter la liste des factures'),
('imprimer_facture_fournisseur', 'approvisionnement', 'Imprimer une facture fournisseur'),

-- Paiements fournisseurs
('payer_fournisseur', 'approvisionnement', 'Enregistrer un paiement à un fournisseur'),
('modifier_paiement_fournisseur', 'approvisionnement', 'Modifier un paiement'),
('supprimer_paiement_fournisseur', 'approvisionnement', 'Supprimer un paiement'),
('lister_paiements_fournisseur', 'approvisionnement', 'Consulter l''historique des paiements'),
('imprimer_recu_fournisseur', 'approvisionnement', 'Imprimer un reçu fournisseur'),

-- États achats
('etat_achats_jour', 'approvisionnement', 'Afficher l''état des achats par jour'),
('etat_achats_mois', 'approvisionnement', 'Afficher l''état des achats par mois'),
('etat_achats_annuel', 'approvisionnement', 'Afficher l''état des achats annuel'),
('exporter_achats', 'approvisionnement', 'Exporter les achats (CSV/PDF)'),

-- ==========================================
-- MODULE VENTES
-- ==========================================
-- Commandes clients
('creer_commande_client', 'vente', 'Enregistrer une commande client'),
('modifier_commande_client', 'vente', 'Modifier une commande client (en cours)'),
('annuler_commande_client', 'vente', 'Annuler une commande client'),
('supprimer_commande_client', 'vente', 'Supprimer une commande client'),
('lister_commandes_client', 'vente', 'Consulter la liste des commandes clients'),
('imprimer_bon_commande_client', 'vente', 'Imprimer un bon de commande client'),

-- Livraisons
('livrer_commande', 'vente', 'Enregistrer une livraison (bon de livraison)'),
('modifier_livraison', 'vente', 'Modifier un bon de livraison'),
('annuler_livraison', 'vente', 'Annuler une livraison'),
('lister_livraisons', 'vente', 'Consulter la liste des livraisons'),
('imprimer_bon_livraison', 'vente', 'Imprimer un bon de livraison'),

-- Factures clients
('creer_facture_client', 'vente', 'Créer une facture client'),
('modifier_facture_client', 'vente', 'Modifier une facture (impayée)'),
('annuler_facture_client', 'vente', 'Annuler une facture'),
('lister_factures_client', 'vente', 'Consulter la liste des factures clients'),
('imprimer_facture_client', 'vente', 'Imprimer une facture client'),

-- Règlements clients (encaissements)
('enregistrer_reglement_client', 'vente', 'Enregistrer un règlement client'),
('modifier_reglement_client', 'vente', 'Modifier un règlement'),
('supprimer_reglement_client', 'vente', 'Supprimer un règlement'),
('lister_reglements_client', 'vente', 'Consulter l''historique des règlements'),
('imprimer_recu_client', 'vente', 'Imprimer un reçu client'),

-- Vente comptant
('effectuer_vente_comptant', 'vente', 'Enregistrer une vente au comptant (sans commande)'),
('annuler_vente_comptant', 'vente', 'Annuler une vente comptant'),
('imprimer_ticket_vente', 'vente', 'Imprimer un ticket de caisse'),

-- Sorties de stock (périmé, non vendu, casse, retour)
('enregistrer_sortie_stock', 'vente', 'Enregistrer une sortie de stock'),
('modifier_sortie_stock', 'vente', 'Modifier une sortie'),
('supprimer_sortie_stock', 'vente', 'Supprimer une sortie'),
('lister_sorties_stock', 'vente', 'Consulter la liste des sorties'),
('imprimer_bon_sortie', 'vente', 'Imprimer un bon de sortie'),

-- États ventes
('etat_ventes_jour', 'vente', 'Afficher l''état des ventes par jour'),
('etat_ventes_mois', 'vente', 'Afficher l''état des ventes par mois'),
('etat_ventes_annuel', 'vente', 'Afficher l''état des ventes annuel'),
('exporter_ventes', 'vente', 'Exporter les ventes (CSV/PDF)'),
('tableau_bord_ventes', 'vente', 'Accéder au tableau de bord des ventes (graphiques)'),

-- ==========================================
-- MODULES COMPLÉMENTAIRES
-- ==========================================
('imprimer_tout', 'global', 'Accès à toutes les impressions (super-droit)'),
('exporter_tout', 'global', 'Accès à tous les exports'),
('acces_parametres', 'global', 'Accéder aux paramètres généraux de l''application'),
('voir_dashboard', 'global', 'Accéder au tableau de bord principal');

-- Groupe administrateur avec tous les droits
INSERT INTO utilisateur.groupe (id_groupe, nom_groupe, description) VALUES (1, 'Administrateur', 'Accès complet');
INSERT INTO utilisateur.groupe_droit (id_groupe, id_droit) SELECT 1, id_droit FROM utilisateur.droit;

-- Utilisateur admin (mot de passe = password)
INSERT INTO utilisateur.utilisateur (id_groupe, nom_complet, login, password_hash, actif) 
VALUES (1, 'Administrateur', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', true);



-- ==========================================
-- AJOUT MODULE 3 - TABLE MOUVEMENT BANQUE
-- ==========================================

-- Table des mouvements bancaires
CREATE TABLE IF NOT EXISTS structure.mouvement_banque
(
    id_mouvement_banque BIGSERIAL PRIMARY KEY,
    id_banque INTEGER NOT NULL,
    id_utilisateur INTEGER NOT NULL,
    date_mouvement DATE DEFAULT CURRENT_DATE,
    type_mouvement VARCHAR(20) NOT NULL,
    montant NUMERIC(15,2) NOT NULL,
    reference VARCHAR(100),
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Clés étrangères
ALTER TABLE structure.mouvement_banque ADD CONSTRAINT fk_mv_banque
FOREIGN KEY (id_banque) REFERENCES structure.banque(id_banque) ON DELETE CASCADE;

ALTER TABLE structure.mouvement_banque ADD CONSTRAINT fk_mv_banque_user
FOREIGN KEY (id_utilisateur) REFERENCES utilisateur.utilisateur(id_utilisateur) ON DELETE CASCADE;

-- Fonction de sauvegarde XML pour mouvement_banque
CREATE OR REPLACE FUNCTION utilisateur.fn_backup_mouvement_banque()
RETURNS TRIGGER
LANGUAGE plpgsql
AS
$$
DECLARE
    v_xml XML;
BEGIN
    v_xml := xmlelement(
        NAME mouvement_banque,
        xmlforest(
            OLD.id_mouvement_banque,
            OLD.id_banque,
            OLD.type_mouvement,
            OLD.montant,
            OLD.date_mouvement
        )
    );
    
    INSERT INTO utilisateur.corbeille_xml (type_objet, id_objet, donnees_xml)
    VALUES ('MOUVEMENT_BANQUE', OLD.id_mouvement_banque, v_xml);
    
    RETURN OLD;
END;
$$;

-- Trigger de sauvegarde XML pour mouvement_banque
CREATE TRIGGER trg_backup_mouvement_banque
BEFORE DELETE ON structure.mouvement_banque
FOR EACH ROW
EXECUTE FUNCTION utilisateur.fn_backup_mouvement_banque();

-- ==========================================
-- DROITS BANQUES
-- ==========================================

INSERT INTO utilisateur.droit (nom_droit, module, description) VALUES
('creer_banque', 'structure', 'Créer une banque'),
('modifier_banque', 'structure', 'Modifier une banque'),
('supprimer_banque', 'structure', 'Supprimer une banque'),
('lister_banques', 'structure', 'Consulter la liste des banques'),
('creer_mouvement_banque', 'structure', 'Créer un mouvement bancaire'),
('etat_versements_periode', 'structure', 'Consulter l''état des versements par période')
ON CONFLICT (nom_droit) DO NOTHING;

-- Attribution des droits banques au groupe administrateur
INSERT INTO utilisateur.groupe_droit (id_groupe, id_droit)
SELECT 1, id_droit FROM utilisateur.droit 
WHERE nom_droit IN ('creer_banque', 'modifier_banque', 'supprimer_banque', 
                    'lister_banques', 'creer_mouvement_banque', 'etat_versements_periode')
ON CONFLICT DO NOTHING;