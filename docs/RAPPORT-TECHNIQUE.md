# Rapport technique de l'application Gestion Stock

Version du document : 1.0  
Date : 30 juin 2026  
Application : Gestion Stock  
Technologies principales : PHP, PostgreSQL, Tailwind CSS, JavaScript

## Sommaire

- [1. Objet du document](#1-objet-du-document)
- [2. Présentation générale](#2-présentation-générale)
- [3. Périmètre fonctionnel](#3-périmètre-fonctionnel)
- [4. Architecture applicative](#4-architecture-applicative)
- [5. Technologies et dépendances](#5-technologies-et-dépendances)
- [6. Configuration](#6-configuration)
- [7. Base de données](#7-base-de-données)
- [8. Description technique des modules](#8-description-technique-des-modules)
  - [8.1 Module Authentification](#81-module-authentification)
  - [8.2 Module Tableau de bord](#82-module-tableau-de-bord)
  - [8.3 Module Utilisateurs, groupes et droits](#83-module-utilisateurs-groupes-et-droits)
  - [8.4 Module Structure - Familles](#84-module-structure---familles)
  - [8.5 Module Structure - Produits](#85-module-structure---produits)
  - [8.6 Module Structure - Fournisseurs](#86-module-structure---fournisseurs)
  - [8.7 Module Structure - Clients](#87-module-structure---clients)
  - [8.8 Module Structure - Catégories clients](#88-module-structure---catégories-clients)
  - [8.9 Module Structure - Banques et mouvements bancaires](#89-module-structure---banques-et-mouvements-bancaires)
  - [8.10 Module Corbeille et restauration](#810-module-corbeille-et-restauration)
  - [8.11 Module Approvisionnements - Commandes fournisseurs](#811-module-approvisionnements---commandes-fournisseurs)
  - [8.12 Module Approvisionnements - Réceptions](#812-module-approvisionnements---réceptions)
  - [8.13 Module Approvisionnements - Bons d'entrée](#813-module-approvisionnements---bons-dentrée)
  - [8.14 Module Approvisionnements - Dons](#814-module-approvisionnements---dons)
  - [8.15 Module Approvisionnements - Factures fournisseurs](#815-module-approvisionnements---factures-fournisseurs)
  - [8.16 Module Approvisionnements - Paiements fournisseurs](#816-module-approvisionnements---paiements-fournisseurs)
  - [8.17 Module Approvisionnements - États achats](#817-module-approvisionnements---états-achats)
  - [8.18 Module Ventes - Commandes clients](#818-module-ventes---commandes-clients)
  - [8.19 Module Ventes - Bons de livraison](#819-module-ventes---bons-de-livraison)
  - [8.20 Module Ventes - Factures clients](#820-module-ventes---factures-clients)
  - [8.21 Module Ventes - Règlements clients](#821-module-ventes---règlements-clients)
  - [8.22 Module Ventes - Sorties de stock](#822-module-ventes---sorties-de-stock)
  - [8.23 Module Ventes - Vente au comptant](#823-module-ventes---vente-au-comptant)
  - [8.24 Module Ventes - États et tableau de bord ventes](#824-module-ventes---états-et-tableau-de-bord-ventes)
  - [8.25 Module API Recherche globale](#825-module-api-recherche-globale)
- [9. Routage applicatif](#9-routage-applicatif)
- [10. Sécurité applicative](#10-sécurité-applicative)
- [11. Flux métier principaux](#11-flux-métier-principaux)
- [12. Interface utilisateur](#12-interface-utilisateur)
- [13. API interne](#13-api-interne)
- [14. Installation et déploiement](#14-installation-et-déploiement)
- [15. Maintenance](#15-maintenance)
- [16. Qualité technique](#16-qualité-technique)
- [17. Annexes](#17-annexes)

## 1. Objet du document

Ce rapport technique présente l'architecture, les composants, les flux métier, la base de données, les mécanismes de sécurité, les dépendances et les recommandations d'exploitation de l'application Gestion Stock.

Il s'adresse aux développeurs, administrateurs système, intégrateurs, mainteneurs et responsables techniques chargés de déployer, maintenir ou faire évoluer l'application.

## 2. Présentation générale

Gestion Stock est une application web de gestion d'inventaire et de suivi commercial. Elle couvre le cycle complet d'un stock :

- création du référentiel de base : produits, familles, clients, fournisseurs, banques ;
- approvisionnement : commandes fournisseurs, réceptions, bons d'entrée, factures fournisseurs, paiements ;
- vente : commandes clients, livraisons, factures clients, règlements, ventes au comptant ;
- mouvements de stock : entrées, sorties, livraisons, contrôle du stock négatif ;
- sécurité : authentification, groupes, droits, journal d'audit ;
- reporting : tableaux de bord, états journaliers et annuels, impressions.

L'application suit une architecture MVC légère en PHP procédural et orienté objet, sans framework backend externe. Le routage est centralisé dans `index.php`, les contrôleurs orchestrent les traitements, les modèles encapsulent les requêtes SQL, et les vues PHP génèrent l'interface utilisateur.

## 3. Périmètre fonctionnel

### 3.1 Modules couverts

| Module | Description |
|---|---|
| Tableau de bord | KPIs, graphiques ventes/achats, stocks bas, activité récente |
| Utilisateurs | Authentification, utilisateurs, groupes, droits, profil, journal d'audit |
| Structure | Familles, produits, fournisseurs, clients, catégories clients, banques |
| Banque | Mouvements bancaires, versements/retraits, états par période |
| Corbeille | Sauvegarde XML des éléments supprimés, restauration, suppression définitive |
| Approvisionnements | BCF, réceptions, dons, bons d'entrée, factures fournisseurs, paiements |
| Ventes | Commandes clients, livraisons, factures clients, règlements, sorties de stock |
| Vente comptant | Vente complète en un seul flux : commande, livraison, facture, règlement, ticket |
| États | États d'achats et de ventes journaliers/annuels, impressions |

### 3.2 Entités métier principales

- Utilisateur
- Groupe
- Droit
- Famille de produits
- Produit
- Fournisseur
- Client
- Catégorie client
- Banque
- Mouvement bancaire
- Bon de commande fournisseur
- Réception
- Bon d'entrée
- Don
- Facture fournisseur
- Paiement fournisseur
- Commande client
- Bon de livraison
- Facture client
- Règlement client
- Sortie de stock
- Mouvement de stock
- Élément de corbeille XML
- Journal d'audit

## 4. Architecture applicative

### 4.1 Organisation du projet

```text
gestion_stock/
├── api/                         # Endpoints applicatifs légers
├── config/                      # Configuration, session, droits, helpers globaux
├── controllers/                 # Contrôleurs MVC
├── database/                    # Schémas SQL et jeux de données
├── diagram_&_model/             # Diagrammes de conception
├── docs/                        # Documentation technique et UI
├── models/                      # Modèles d'accès aux données
├── public/                      # Assets publics : CSS, JS, vendor
├── src/                         # Classes PSR-4 complémentaires
├── views/                       # Vues PHP, layouts, composants, impressions
├── index.php                    # Front controller et routeur principal
├── composer.json                # Déclaration Composer/autoload PSR-4
├── package.json                 # Dépendances frontend et scripts Tailwind
└── tailwind.config.js           # Configuration Tailwind CSS
```

### 4.2 Front controller

Le fichier `index.php` joue le rôle de point d'entrée unique :

1. initialise l'encodage HTML ;
2. charge la base de données et la session ;
3. lit le paramètre `action` dans l'URL ;
4. vérifie que l'utilisateur est authentifié pour les actions privées ;
5. positionne la variable PostgreSQL `app.user_id` pour les triggers ;
6. instancie le contrôleur correspondant ;
7. appelle la méthode métier appropriée.

Exemple de route :

```php
case 'produits':
    require_once 'controllers/ProduitController.php';
    $controller = new ProduitController($pdo);
    $controller->index();
    break;
```

### 4.3 Contrôleurs

Les contrôleurs assurent :

- le contrôle des droits ;
- la lecture des paramètres `GET` et `POST` ;
- la validation minimale des données ;
- l'appel aux modèles ;
- la gestion des messages flash ;
- les redirections après traitement ;
- le chargement des vues.

Contrôleurs principaux :

| Contrôleur | Responsabilité |
|---|---|
| `AuthController` | Connexion, déconnexion |
| `DashboardController` | Tableau de bord principal |
| `UtilisateurController` | Groupes, droits, utilisateurs, profil, audit |
| `FamilleController` | Gestion des familles |
| `ProduitController` | Gestion du catalogue produits |
| `FournisseurController` | Gestion des fournisseurs |
| `ClientController` | Gestion des clients |
| `CategorieClientController` | Gestion des catégories clients |
| `BanqueController` | Banques et mouvements bancaires |
| `RestaurationController` | Corbeille XML et restauration |
| `ApprovisionnementController` | Achats, entrées, fournisseurs |
| `VenteController` | Ventes, livraisons, factures, règlements |

### 4.4 Modèles

Les modèles encapsulent les accès aux tables PostgreSQL via PDO. Ils utilisent des requêtes préparées pour les opérations paramétrées et gèrent certaines transactions métier.

Exemples :

- `ProduitModel` : produits, recherche, création, modification, activation ;
- `BonCommandeModel` : commandes fournisseurs et lignes ;
- `ReceptionModel` : réceptions, validation et génération de bon d'entrée ;
- `CommandeClientModel` : commandes clients et lignes ;
- `BonLivraisonModel` : livraisons et quantités restantes ;
- `FactureClientModel` : facturation client, statut de paiement ;
- `RestaurationModel` : parsing XML et restauration d'objets supprimés.

### 4.5 Vues et composants

Les vues se trouvent dans `views/`. Elles sont regroupées par domaine :

- `views/auth`
- `views/dashboard`
- `views/structure`
- `views/approvisionnement`
- `views/vente`
- `views/utilisateur`
- `views/components`
- `views/layouts`
- `views/errors`

L'application utilise un layout principal `views/layouts/main.php`, qui contient :

- sidebar ;
- topbar ;
- fil d'Ariane ;
- menu utilisateur ;
- conteneur de contenu ;
- modales globales ;
- chargement des assets CSS et JS.

Les composants UI sont centralisés dans `views/components/`, notamment :

- boutons ;
- alertes ;
- badges ;
- cartes ;
- champs de formulaire ;
- tableaux ;
- tableaux responsives ;
- modales ;
- pagination ;
- états vides ;
- toasts ;
- recherche ;
- filtres ;
- layout d'impression ;
- pages d'erreur.

## 5. Technologies et dépendances

### 5.1 Backend

| Élément | Version/usage |
|---|---|
| PHP | PHP 8.0+ recommandé |
| PDO | Connexion PostgreSQL |
| PostgreSQL | 13+ recommandé |
| Composer | Autoload PSR-4 optionnel |

Le projet déclare l'espace de noms `Aurlucef\GestionStock\` dans `composer.json`, mais la majorité du code charge encore les fichiers par `require_once`.

### 5.2 Frontend

| Élément | Usage |
|---|---|
| Tailwind CSS | Framework CSS et compilation de `main.css` vers `main.min.css` |
| Font Awesome | Icônes |
| Chart.js | Graphiques |
| jQuery | Dépendance disponible côté public |
| JavaScript natif | Interactions UI via `public/js/main.js` |

Scripts npm :

```bash
npm run dev
npm run build
```

`npm run dev` lance Tailwind en mode surveillance.  
`npm run build` produit la feuille minifiée `public/css/main.min.css`.

## 6. Configuration

### 6.1 Base de données

La connexion est définie dans `config/database.php` :

```php
$host = 'localhost';
$port = '5432';
$dbname = 'gestion_stock';
$user = '<utilisateur_postgresql>';
$password = '<mot_de_passe_postgresql>';
```

L'application crée une instance PDO PostgreSQL avec :

- mode erreur en exception ;
- fetch mode par défaut en tableau associatif ;
- encodage UTF-8.

Recommandation : externaliser les identifiants de connexion dans des variables d'environnement ou un fichier non versionné.

### 6.2 Session

La session est initialisée dans `config/session.php`. Ce fichier charge également :

- `database.php` ;
- `fonctions.php` ;
- `autoload.php` ;
- les composants UI.

Il définit :

- `checkRight($rightName)` ;
- `checkRightIfLogged($rightName)` ;
- `$currentUser`.

### 6.3 Autoload

Deux mécanismes coexistent :

- autoload PSR-4 Composer dans `composer.json` ;
- autoload manuel dans `config/autoload.php`.

Le code courant utilise surtout des `require_once` dans les contrôleurs.

## 7. Base de données

### 7.1 Schémas PostgreSQL

Le schéma principal est dans `database/gestion_stock_pg.sql`. Il crée quatre schémas applicatifs :

| Schéma | Rôle |
|---|---|
| `utilisateur` | utilisateurs, groupes, droits, audit, corbeille |
| `structure` | référentiels, produits, clients, fournisseurs, banques, stock |
| `approvisionnement` | achats fournisseurs et entrées |
| `vente` | ventes clients, factures, règlements, sorties |

### 7.2 Tables du module utilisateur

| Table | Description |
|---|---|
| `utilisateur.groupe` | Groupes d'utilisateurs |
| `utilisateur.droit` | Droits applicatifs |
| `utilisateur.utilisateur` | Comptes utilisateurs |
| `utilisateur.groupe_droit` | Association groupes/droits |
| `utilisateur.journal_audit` | Traçabilité des actions |
| `utilisateur.corbeille_xml` | Sauvegarde XML des objets supprimés |

### 7.3 Tables du module structure

| Table | Description |
|---|---|
| `structure.famille` | Familles de produits |
| `structure.categorie_client` | Catégories/remises clients |
| `structure.banque` | Banques |
| `structure.fournisseur` | Fournisseurs |
| `structure.produit` | Produits et stock courant |
| `structure.client` | Clients et solde crédit |
| `structure.mouvement_stock` | Historique des mouvements de stock |
| `structure.mouvement_banque` | Versements/retraits bancaires |

### 7.4 Tables du module approvisionnement

| Table | Description |
|---|---|
| `approvisionnement.don` | Dons reçus |
| `approvisionnement.bon_commande_fourn` | Bons de commande fournisseur |
| `approvisionnement.ligne_commande_fourn` | Lignes de commande fournisseur |
| `approvisionnement.bon_reception` | Bons de réception |
| `approvisionnement.ligne_reception` | Lignes de réception |
| `approvisionnement.bon_entree` | Bons d'entrée en stock |
| `approvisionnement.ligne_bon_entree` | Lignes de bon d'entrée |
| `approvisionnement.facture_fournisseur` | Factures fournisseurs |
| `approvisionnement.ligne_facture_fourn` | Lignes de facture fournisseur |
| `approvisionnement.paiement_fournisseur` | Paiements fournisseurs |

### 7.5 Tables du module vente

| Table | Description |
|---|---|
| `vente.commande_client` | Commandes clients |
| `vente.ligne_commande_client` | Lignes de commande client |
| `vente.bon_livraison` | Bons de livraison |
| `vente.ligne_livraison` | Lignes de livraison |
| `vente.facture_client` | Factures clients |
| `vente.reglement_client` | Règlements clients |
| `vente.sortie_stock` | Sorties de stock hors vente |

### 7.6 Triggers et automatisations

La base de données contient plusieurs triggers essentiels :

| Trigger | Table | Rôle |
|---|---|---|
| `trg_entree_stock` | `approvisionnement.ligne_bon_entree` | Ajoute la quantité au stock produit |
| `trg_sortie_stock` | `vente.sortie_stock` | Retire la quantité du stock produit |
| `trg_livraison_stock` | `vente.ligne_livraison` | Retire du stock lors d'une livraison client |
| `trg_audit_produit` | `structure.produit` | Journalise les changements produit |
| `trg_controle_stock` | `structure.produit` | Interdit le stock négatif |
| `trg_backup_*` | tables métier | Sauvegarde XML avant suppression |
| `trg_fill_supprime_par` | `utilisateur.corbeille_xml` | Renseigne l'utilisateur ayant supprimé |
| `trg_ajuster_solde_credit_facture` | `vente.facture_client` | Ajuste le crédit client |
| `trg_ajuster_solde_credit_reglement` | `vente.reglement_client` | Ajuste le crédit client après règlement |

Ces automatisations imposent que les traitements applicatifs respectent les tables prévues. Par exemple, une entrée de stock doit passer par `ligne_bon_entree` pour déclencher l'augmentation de stock.

## 8. Description technique des modules

Cette section détaille chaque module fonctionnel de l'application sous l'angle technique : responsabilités, routes, contrôleurs, modèles, vues, tables, droits et règles métier.

### 8.1 Module Authentification

#### Rôle

Le module Authentification contrôle l'accès à l'application. Il permet à un utilisateur actif de se connecter, d'ouvrir une session, d'être redirigé vers le tableau de bord et de se déconnecter.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/AuthController.php` |
| Modèle | `models/UtilisateurModel.php` |
| Vue | `views/auth/login.php` |
| Tables | `utilisateur.utilisateur`, `utilisateur.journal_audit` |
| Routes | `?action=login`, `?action=logout` |

#### Fonctionnement

La méthode `login()` récupère le login soumis, recherche l'utilisateur avec `UtilisateurModel::findByLogin()`, vérifie que le compte est actif puis valide le mot de passe avec `password_verify()`.

En cas de succès :

- `$_SESSION['user_id']` reçoit l'identifiant utilisateur ;
- `$_SESSION['user_name']` reçoit le nom complet ;
- la dernière connexion est mise à jour ;
- l'action `LOGIN` est enregistrée dans le journal d'audit ;
- l'utilisateur est redirigé vers `?action=dashboard`.

La méthode `logout()` journalise l'action `LOGOUT`, détruit la session et redirige vers l'écran de connexion.

#### Points d'attention

- Les actions `login` et `logout` sont publiques dans le routeur.
- Toutes les autres actions exigent une session active.
- La politique de mot de passe est principalement gérée lors de la création ou modification utilisateur.

### 8.2 Module Tableau de bord

#### Rôle

Le module Tableau de bord fournit une synthèse de l'activité : indicateurs de stock, achats, ventes, commandes en cours, factures impayées et activité récente.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/DashboardController.php` |
| Vue | `views/dashboard/index.php` |
| Layout | `views/layouts/main.php` |
| Tables consultées | `structure.produit`, `approvisionnement.bon_commande_fourn`, `vente.commande_client`, `vente.facture_client`, `vente.reglement_client`, `approvisionnement.facture_fournisseur`, `approvisionnement.paiement_fournisseur`, `utilisateur.journal_audit` |
| Route | `?action=dashboard` |
| Droit principal | `voir_dashboard` |

#### Données affichées

Le contrôleur calcule les données suivantes selon les droits de l'utilisateur :

- nombre de produits actifs ;
- produits en alerte de stock ;
- commandes fournisseurs en cours ;
- commandes clients en cours ;
- chiffre d'affaires du mois ;
- encaissements du mois ;
- achats du mois ;
- paiements fournisseurs du mois ;
- graphiques ventes/achats annuels ;
- stocks bas ;
- activité récente ;
- mini KPIs clients, fournisseurs, banques et factures impayées.

#### Règles métier

Le tableau de bord est contextuel : les blocs sont affichés uniquement si l'utilisateur possède les droits associés. Cette approche évite d'exposer des données sensibles à des profils non autorisés.

### 8.3 Module Utilisateurs, groupes et droits

#### Rôle

Ce module administre les comptes utilisateurs, les groupes, les droits RBAC, les profils et le journal d'audit.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/UtilisateurController.php` |
| Modèles | `UtilisateurModel`, `GroupeModel`, `DroitModel`, `JournalAuditModel` |
| Vues | `views/utilisateur/groupes.php`, `droits.php`, `groupes_droits.php`, `utilisateurs.php`, `profil.php`, `journal_audit.php` |
| Tables | `utilisateur.utilisateur`, `utilisateur.groupe`, `utilisateur.droit`, `utilisateur.groupe_droit`, `utilisateur.journal_audit` |
| Routes | `?action=groupes`, `?action=droits`, `?action=groupes_droits`, `?action=utilisateurs`, `?action=profil`, `?action=journal_audit` |

#### Sous-fonctions

| Sous-fonction | Description |
|---|---|
| Groupes | Création, modification, suppression de groupes |
| Droits | Consultation des droits disponibles |
| Groupes-droits | Affectation des droits à un groupe |
| Utilisateurs | Création, modification, suppression de comptes |
| Profil | Modification du mot de passe de l'utilisateur connecté |
| Journal audit | Consultation paginée des événements applicatifs |

#### Droits utilisés

- `creer_groupe`
- `affecter_droits`
- `creer_utilisateur`
- `voir_profil`
- `modifier_profil`
- `voir_journal_audit`

Le script SQL contient aussi les droits de modification, suppression et consultation, même si certains écrans utilisent historiquement un droit de création comme droit d'accès principal.

#### Règles métier

- Un groupe contenant des utilisateurs ne doit pas être supprimé.
- Un utilisateur ne peut pas supprimer son propre compte.
- Un utilisateur inactif est déconnecté automatiquement s'il tente d'utiliser l'application.
- La modification du mot de passe exige l'ancien mot de passe et une confirmation.
- Les actions sensibles sont journalisées avec `logAudit()`.

### 8.4 Module Structure - Familles

#### Rôle

Les familles organisent le catalogue produits. Elles servent au classement, au filtrage et à la sélection des produits.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/FamilleController.php` |
| Modèle | `models/FamilleModel.php` |
| Vues | `views/structure/familles.php`, `views/structure/detail_famille.php` |
| Table | `structure.famille` |
| Routes | `?action=familles`, `?action=famille_supprimer`, `?action=famille_detail` |

#### Droits utilisés

- `lister_familles`
- `creer_famille`
- `modifier_famille`
- `supprimer_famille`

#### Règles métier

- Le nom de la famille est obligatoire.
- La suppression doit respecter les dépendances avec les produits.
- Le détail peut être chargé dans une modale AJAX via `famille_detail`.

### 8.5 Module Structure - Produits

#### Rôle

Le module Produits gère le catalogue, les prix, les unités, les seuils d'alerte, les produits périssables et le stock courant.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ProduitController.php` |
| Modèles | `ProduitModel`, `FamilleModel` |
| Vues | `views/structure/produits.php`, `views/structure/detail_produit.php` |
| Table principale | `structure.produit` |
| Tables liées | `structure.famille`, `structure.mouvement_stock` |
| Routes | `?action=produits`, `?action=produit_supprimer`, `?action=produit_desactiver`, `?action=produit_activer`, `?action=produit_detail`, `?action=ajax_produits_peres` |

#### Données gérées

- famille ;
- produit père optionnel ;
- code-barres généré ;
- nom ;
- description ;
- prix d'achat ;
- prix de vente ;
- stock actuel ;
- seuil d'alerte ;
- caractère périssable ;
- date de péremption ;
- unité ;
- état actif/inactif.

#### Droits utilisés

- `lister_produits`
- `creer_produit`
- `modifier_produit`
- `supprimer_produit`
- `voir_produit`

#### Règles métier

- Le nom et la famille sont obligatoires.
- Le code-barres est généré lors de la création.
- Les produits inactifs ne sont pas proposés dans la plupart des opérations.
- Le stock actuel est mis à jour automatiquement par les triggers d'entrée et de sortie.
- Le trigger `trg_controle_stock` interdit le stock négatif.
- Le trigger `trg_audit_produit` journalise les insertions, modifications et suppressions produit.

#### Points d'attention

Modifier directement `stock_actuel` depuis le formulaire produit peut désaligner le stock avec l'historique des mouvements. Les entrées et sorties opérationnelles doivent passer par les flux dédiés.

### 8.6 Module Structure - Fournisseurs

#### Rôle

Le module Fournisseurs maintient le référentiel des partenaires d'achat utilisés par les commandes, factures et paiements fournisseurs.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/FournisseurController.php` |
| Modèle | `models/FournisseurModel.php` |
| Vues | `views/structure/fournisseurs.php`, `views/structure/detail_fournisseur.php` |
| Table | `structure.fournisseur` |
| Routes | `?action=fournisseurs`, `?action=fournisseur_supprimer`, `?action=fournisseur_desactiver`, `?action=fournisseur_activer`, `?action=fournisseur_detail` |

#### Droits utilisés

- `lister_fournisseurs`
- `creer_fournisseur`
- `modifier_fournisseur`
- `supprimer_fournisseur`

#### Règles métier

- Le nom fournisseur est obligatoire.
- Un fournisseur peut être désactivé sans suppression physique.
- Les fournisseurs actifs alimentent les listes de sélection du module Approvisionnements.
- Les suppressions sont journalisées et peuvent être sauvegardées dans la corbeille XML selon les triggers.

### 8.7 Module Structure - Clients

#### Rôle

Le module Clients maintient le référentiel client utilisé par les commandes, livraisons, factures, règlements et ventes au comptant.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ClientController.php` |
| Modèle | `models/ClientModel.php` |
| Vues | `views/structure/clients.php`, `views/structure/detail_client.php` |
| Table | `structure.client` |
| Tables liées | `structure.categorie_client`, `vente.facture_client`, `vente.reglement_client` |
| Routes | `?action=clients`, `?action=client_supprimer`, `?action=client_detail` |

#### Droits utilisés

- `lister_clients`
- `creer_client`
- `modifier_client`
- `supprimer_client`
- `voir_credit_client`

#### Règles métier

- Le nom client est obligatoire.
- Le solde crédit est ajusté automatiquement par les triggers liés aux factures et règlements clients.
- Les clients actifs sont proposés dans les ventes et commandes.

### 8.8 Module Structure - Catégories clients

#### Rôle

Les catégories clients servent à classer les clients et à gérer des paramètres commerciaux comme les remises.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/CategorieClientController.php` |
| Modèle | `models/CategorieClientModel.php` |
| Vue | `views/structure/categorie_clients.php` |
| Table | `structure.categorie_client` |
| Route | `?action=categorie_clients`, `?action=categorie_client_supprimer` |

#### Droits utilisés

- `lister_categories_client`
- `creer_categorie_client`
- `modifier_categorie_client`
- `supprimer_categorie_client`

#### Règles métier

- Le nom de la catégorie est obligatoire.
- La suppression est refusée si des clients sont liés à la catégorie.

### 8.9 Module Structure - Banques et mouvements bancaires

#### Rôle

Ce module gère les banques et permet de suivre les mouvements financiers de type versement ou retrait sur une période.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/BanqueController.php` |
| Modèles | `BanqueModel`, `MouvementBanqueModel` |
| Vues | `views/structure/banques.php`, `views/structure/banque_versements.php` |
| Tables | `structure.banque`, `structure.mouvement_banque` |
| Routes | `?action=banques`, `?action=banque_supprimer`, `?action=banque_versements`, `?action=banque_mouvement_enregistrer`, `?action=banque_mouvement_supprimer` |

#### Droits utilisés

- `lister_banques`
- `creer_banque`
- `modifier_banque`
- `supprimer_banque`
- `etat_versements_periode`
- `creer_mouvement_banque`

#### Règles métier

- Le nom de la banque est obligatoire.
- Un mouvement bancaire exige une banque valide et un montant positif.
- L'état bancaire calcule un solde initial, les entrées, les sorties et un solde final sur la période sélectionnée.
- Les mouvements peuvent être sauvegardés dans la corbeille XML lors des suppressions selon les triggers.

### 8.10 Module Corbeille et restauration

#### Rôle

La corbeille conserve des sauvegardes XML d'objets supprimés. Elle permet de consulter, restaurer ou supprimer définitivement ces sauvegardes.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/RestaurationController.php` |
| Modèle | `models/RestaurationModel.php` |
| Vues | `views/structure/restauration/index.php`, `views/structure/restauration/view.php` |
| Table | `utilisateur.corbeille_xml` |
| Routes | `?action=restauration`, `?action=restauration_view`, `?action=restauration_restore`, `?action=restauration_delete`, `?action=restauration_clear` |

#### Droits utilisés

- `restaurer_corbeille`
- `vider_corbeille`

#### Règles métier

- Les objets supprimés sont sauvegardés au format XML par des triggers PostgreSQL.
- La restauration dépend du type d'objet et des relations nécessaires.
- La suppression définitive retire l'élément de la corbeille.
- Le vidage global est une action administrative sensible.

### 8.11 Module Approvisionnements - Commandes fournisseurs

#### Rôle

Les commandes fournisseurs formalisent les demandes d'achat auprès des fournisseurs.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ApprovisionnementController.php` |
| Méthode | `commandeFourn()` |
| Modèle | `models/BonCommandeModel.php` |
| Vues | `views/approvisionnement/commandes.php`, `detail_commande.php`, `print_commande.php` |
| Tables | `approvisionnement.bon_commande_fourn`, `approvisionnement.ligne_commande_fourn`, `structure.fournisseur`, `structure.produit` |
| Route | `?action=commande_fourn` |

#### Droits utilisés

- `lister_bcf`
- `creer_bcf`
- `modifier_bcf`
- `valider_bcf`
- `annuler_bcf`
- `supprimer_bcf`
- `imprimer_bcf`

#### Règles métier

- Une commande doit contenir au moins une ligne produit.
- La validation passe la commande au statut envoyé.
- Une commande envoyée devient disponible pour réception.
- Les modifications et suppressions sont journalisées.
- La référence est générée avec `generateReference()`.

### 8.12 Module Approvisionnements - Réceptions

#### Rôle

Les réceptions enregistrent les quantités effectivement reçues, éventuellement par rapport à une commande fournisseur.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ApprovisionnementController.php` |
| Méthode | `reception()` |
| Modèle | `models/ReceptionModel.php` |
| Vues | `views/approvisionnement/receptions.php`, `print_reception.php` |
| Tables | `approvisionnement.bon_reception`, `approvisionnement.ligne_reception`, `approvisionnement.bon_commande_fourn`, `approvisionnement.bon_entree`, `approvisionnement.ligne_bon_entree` |
| Route | `?action=reception` |

#### Droits utilisés

- `lister_receptions`
- `creer_reception`
- `valider_reception`
- `imprimer_bon_reception`

#### Règles métier

- Une réception doit contenir au moins une ligne produit.
- Si elle est liée à une commande, la quantité reçue cumulée ne doit pas dépasser la quantité commandée.
- La validation crée un bon d'entrée et ses lignes.
- Les lignes de bon d'entrée déclenchent l'augmentation du stock.
- Si toutes les lignes commandées sont reçues, le BCF passe à `receptionne`.

### 8.13 Module Approvisionnements - Bons d'entrée

#### Rôle

Les bons d'entrée matérialisent les entrées réelles de produits en stock.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ApprovisionnementController.php` |
| Méthode | `bonEntree()` |
| Modèle | `models/BonEntreeModel.php` |
| Vues | `views/approvisionnement/bons_entree.php`, `print_bon_entree.php` |
| Tables | `approvisionnement.bon_entree`, `approvisionnement.ligne_bon_entree`, `structure.produit`, `structure.mouvement_stock` |
| Route | `?action=bon_entree` |

#### Droits utilisés

- `lister_bons_entree`
- `imprimer_bon_entree`

#### Règles métier

- Les bons d'entrée sont générés depuis une réception ou un don.
- L'insertion d'une ligne de bon d'entrée déclenche `trg_entree_stock`.
- Le stock produit augmente et un mouvement de stock est créé.

### 8.14 Module Approvisionnements - Dons

#### Rôle

Le module Dons permet d'enregistrer des entrées gratuites de produits.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ApprovisionnementController.php` |
| Méthodes | `don()`, `detailDon()` |
| Modèle | `models/DonModel.php` |
| Vues | `views/approvisionnement/dons.php`, `detail_don.php` |
| Tables | `approvisionnement.don`, `approvisionnement.bon_entree`, `approvisionnement.ligne_bon_entree` |
| Routes | `?action=don`, `?action=don_detail` |

#### Droits utilisés

- `lister_dons`
- `saisir_don`
- `modifier_don`
- `supprimer_don`

#### Règles métier

- Un don doit contenir au moins une ligne produit.
- La création d'un don génère une entrée en stock.
- Le détail du don est chargé en AJAX dans une modale.

### 8.15 Module Approvisionnements - Factures fournisseurs

#### Rôle

Les factures fournisseurs enregistrent les factures reçues des fournisseurs et les montants dus.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ApprovisionnementController.php` |
| Méthode | `factureFourn()` |
| Modèle | `models/FactureFournisseurModel.php` |
| Vues | `views/approvisionnement/factures.php`, `print_facture.php` |
| Tables | `approvisionnement.facture_fournisseur`, `approvisionnement.ligne_facture_fourn`, `structure.fournisseur`, `approvisionnement.bon_commande_fourn` |
| Route | `?action=facture_fourn` |

#### Droits utilisés

- `lister_factures_fournisseur`
- `creer_facture_fournisseur`
- `modifier_facture_fournisseur`
- `supprimer_facture_fournisseur`
- `imprimer_facture_fournisseur`

#### Règles métier

- Une facture doit contenir au moins une ligne produit.
- La TVA est appliquée uniquement si l'option correspondante est cochée.
- La facture peut être liée à une commande fournisseur.
- La suppression peut être bloquée si des paiements sont liés.

### 8.16 Module Approvisionnements - Paiements fournisseurs

#### Rôle

Ce module enregistre les paiements effectués aux fournisseurs.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ApprovisionnementController.php` |
| Méthode | `paiementFourn()` |
| Modèle | `models/PaiementFournisseurModel.php` |
| Vues | `views/approvisionnement/paiements.php`, `print_recu_paiement.php` |
| Tables | `approvisionnement.paiement_fournisseur`, `approvisionnement.facture_fournisseur`, `structure.fournisseur` |
| Route | `?action=paiement_fourn` |

#### Droits utilisés

- `lister_paiements_fournisseur`
- `payer_fournisseur`
- `supprimer_paiement_fournisseur`
- `imprimer_recu_fournisseur`

#### Règles métier

- Le paiement est lié à une facture fournisseur.
- Le montant, la date et le mode de paiement sont enregistrés.
- Les factures impayées alimentent la liste de sélection.
- Un reçu peut être imprimé après paiement.

### 8.17 Module Approvisionnements - États achats

#### Rôle

Les états achats fournissent une vue analytique des entrées valorisées.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/ApprovisionnementController.php` |
| Méthode | `etatsAchats()` |
| Vues | `views/approvisionnement/etat_achats_jour.php`, `etat_achats_annuel.php`, `print_etat_achats_jour.php`, `print_etat_achats_annuel.php` |
| Tables | `approvisionnement.bon_entree`, `approvisionnement.ligne_bon_entree`, `structure.produit`, `utilisateur.utilisateur` |
| Route | `?action=etats_achats` |

#### Droits utilisés

- `etat_achats_jour`
- `etat_achats_annuel`

#### Règles métier

- L'état journalier affiche les lignes d'entrée pour une date.
- L'état annuel agrège les achats par mois.
- Les impressions utilisent des vues dédiées.

### 8.18 Module Ventes - Commandes clients

#### Rôle

Les commandes clients enregistrent les demandes de produits avant livraison et facturation.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/VenteController.php` |
| Méthodes | `commandeClient()`, `detailCommande()` |
| Modèle | `models/CommandeClientModel.php` |
| Vues | `views/vente/commandes_clients.php`, `detail_commande_client.php`, `print_commande_client.php` |
| Tables | `vente.commande_client`, `vente.ligne_commande_client`, `structure.client`, `structure.produit` |
| Routes | `?action=commande_client`, `?action=commande_client_detail` |

#### Droits utilisés

- `lister_commandes_client`
- `creer_commande_client`
- `modifier_commande_client`
- `annuler_commande_client`
- `supprimer_commande_client`
- `imprimer_bon_commande_client`

#### Règles métier

- Une commande doit contenir au moins une ligne produit.
- Les lignes portent quantité, prix unitaire et remise.
- Une commande peut être annulée ou supprimée selon les droits.
- Le détail est chargé en AJAX.

### 8.19 Module Ventes - Bons de livraison

#### Rôle

Les bons de livraison constatent la sortie physique des produits vers le client.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/VenteController.php` |
| Méthode | `bonLivraison()` |
| Modèle | `models/BonLivraisonModel.php` |
| Vues | `views/vente/bon_livraison.php`, `print_bon_livraison.php` |
| Tables | `vente.bon_livraison`, `vente.ligne_livraison`, `vente.commande_client`, `vente.ligne_commande_client`, `structure.produit` |
| Route | `?action=bon_livraison` |

#### Droits utilisés

- `lister_livraisons`
- `livrer_commande`
- `annuler_livraison`
- `imprimer_bon_livraison`

#### Règles métier

- La livraison est créée depuis une commande livrable.
- Les quantités livrées doivent être positives.
- Le modèle calcule les quantités déjà livrées et restantes.
- L'insertion des lignes de livraison déclenche la sortie de stock.
- Si la livraison est complète, la commande passe à `livree`.

### 8.20 Module Ventes - Factures clients

#### Rôle

Les factures clients sont générées à partir des commandes livrées non encore facturées.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/VenteController.php` |
| Méthode | `factureClient()` |
| Modèle | `models/FactureClientModel.php` |
| Vues | `views/vente/factures_clients.php`, `print_facture_client.php` |
| Tables | `vente.facture_client`, `vente.commande_client`, `vente.ligne_commande_client`, `structure.client` |
| Route | `?action=facture_client` |

#### Droits utilisés

- `lister_factures_client`
- `creer_facture_client`
- `annuler_facture_client`
- `imprimer_facture_client`

#### Règles métier

- Seules les commandes livrées sans facture sont facturables.
- Une facture créée démarre généralement en statut `impayee`.
- L'annulation exclut la facture des états de ventes.
- Les triggers ajustent le solde crédit client.

### 8.21 Module Ventes - Règlements clients

#### Rôle

Les règlements clients enregistrent les encaissements liés aux factures.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/VenteController.php` |
| Méthode | `reglementClient()` |
| Modèle | `models/ReglementClientModel.php` |
| Vues | `views/vente/reglements_clients.php`, `print_recu_client.php` |
| Tables | `vente.reglement_client`, `vente.facture_client`, `structure.client` |
| Route | `?action=reglement_client` |

#### Droits utilisés

- `lister_reglements_client`
- `enregistrer_reglement_client`
- `supprimer_reglement_client`
- `imprimer_recu_client`

#### Règles métier

- Le montant doit être supérieur à zéro.
- La facture doit exister.
- Après règlement, le statut de la facture est recalculé.
- La suppression d'un règlement recalcule également le statut facture.
- Un avertissement est affiché si le montant dépasse le reste à payer.

### 8.22 Module Ventes - Sorties de stock

#### Rôle

Les sorties de stock enregistrent les retraits hors livraison : périmés, casse, non vendu, retour ou autre motif.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/VenteController.php` |
| Méthode | `sortieStock()` |
| Modèle | `models/SortieStockModel.php` |
| Vues | `views/vente/sorties_stock.php`, `print_bon_sortie.php` |
| Tables | `vente.sortie_stock`, `structure.produit`, `structure.client`, `structure.mouvement_stock` |
| Route | `?action=sortie_stock` |

#### Droits utilisés

- `lister_sorties_stock`
- `enregistrer_sortie_stock`
- `imprimer_bon_sortie`

#### Règles métier

- La quantité doit être supérieure à zéro.
- Un produit et un motif sont obligatoires.
- L'insertion déclenche la diminution du stock.
- La base bloque la sortie si le stock est insuffisant.

### 8.23 Module Ventes - Vente au comptant

#### Rôle

La vente au comptant exécute en une seule transaction le cycle complet de vente : commande, livraison, facture, règlement et ticket.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/VenteController.php` |
| Méthode | `venteComptant()` |
| Modèles utilisés | `CommandeClientModel`, `BonLivraisonModel`, `FactureClientModel`, `ReglementClientModel` |
| Vue | `views/vente/vente_comptant.php`, `print_ticket_vente.php` |
| Tables | `vente.commande_client`, `vente.ligne_commande_client`, `vente.bon_livraison`, `vente.ligne_livraison`, `vente.facture_client`, `vente.reglement_client`, `structure.produit`, `structure.client` |
| Route | `?action=vente_comptant` |

#### Droits utilisés

- `effectuer_vente_comptant`
- `imprimer_ticket_vente`

#### Règles métier

- Le client est obligatoire.
- Au moins une ligne produit est obligatoire.
- La transaction crée une commande type `comptant`.
- La livraison est complète.
- Le stock sort via les lignes de livraison.
- La facture est créée avec TVA par défaut.
- Le règlement intégral est créé immédiatement.
- La facture passe à `payee` et la commande à `reglee`.
- En cas d'erreur, toute la transaction est annulée.

### 8.24 Module Ventes - États et tableau de bord ventes

#### Rôle

Ce module restitue les statistiques de vente et les états imprimables.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Contrôleur | `controllers/VenteController.php` |
| Méthodes | `etatsVentes()`, `dashboardVentes()` |
| Vues | `views/vente/etat_ventes_jour.php`, `etat_ventes_annuel.php`, `dashboard_ventes.php`, `print_etat_jour.php`, `print_etat_annuel.php` |
| Tables | `vente.facture_client`, `vente.reglement_client`, `vente.commande_client`, `vente.ligne_commande_client`, `structure.produit`, `structure.client` |
| Routes | `?action=etats_ventes`, `?action=dashboard_ventes` |

#### Droits utilisés

- `tableau_bord_ventes`
- `etat_ventes_jour`
- `etat_ventes_annuel`

#### Données affichées

- factures du jour ;
- nombre de factures ;
- total HT ;
- total TTC ;
- agrégats annuels par mois ;
- ventes des sept derniers jours ;
- top produits ;
- top clients ;
- produits en alerte ;
- montant des factures impayées ou partielles.

#### Règles métier

- Les factures annulées sont exclues des états.
- Les états imprimables disposent de vues spécifiques.

### 8.25 Module API Recherche globale

#### Rôle

L'API de recherche globale fournit des résultats JSON pour la recherche transversale.

#### Composants techniques

| Élément | Fichier ou table |
|---|---|
| Endpoint | `api/search.php` |
| Route | `?action=api_search&q=terme` |
| Tables consultées | `utilisateur.utilisateur`, `utilisateur.groupe`, produits selon implémentation actuelle |
| Format de sortie | JSON |

#### Fonctionnement

L'endpoint :

- exige une session active ;
- ignore les recherches de moins de deux caractères ;
- filtre les résultats selon les droits ;
- retourne une liste d'objets contenant `id`, `title`, `subtitle`, `category`, `icon`, `url`.

#### Point d'attention

La recherche produit vise actuellement `public.produit` si cette table existe, alors que les produits applicatifs sont dans `structure.produit`. Il est recommandé de corriger cette requête pour aligner l'API avec le schéma réel.

## 9. Routage applicatif

Le routage se fait par paramètre `action`.

Exemples :

| URL | Contrôleur | Méthode |
|---|---|---|
| `?action=login` | `AuthController` | `login()` |
| `?action=dashboard` | `DashboardController` | `index()` |
| `?action=produits` | `ProduitController` | `index()` |
| `?action=commande_fourn` | `ApprovisionnementController` | `commandeFourn()` |
| `?action=reception` | `ApprovisionnementController` | `reception()` |
| `?action=commande_client` | `VenteController` | `commandeClient()` |
| `?action=vente_comptant` | `VenteController` | `venteComptant()` |
| `?action=reglement_client` | `VenteController` | `reglementClient()` |
| `?action=restauration` | `RestaurationController` | `index()` |

Les actions publiques sont limitées à :

- `login`
- `logout`

Toutes les autres actions exigent une session utilisateur active.

## 10. Sécurité applicative

### 10.1 Authentification

Le module d'authentification :

1. recherche l'utilisateur par login ;
2. vérifie que le compte est actif ;
3. vérifie le mot de passe avec `password_verify()` ;
4. crée les variables de session ;
5. met à jour la dernière connexion ;
6. journalise l'action `LOGIN`.

Variables de session utilisées :

- `$_SESSION['user_id']`
- `$_SESSION['user_name']`

### 10.2 Autorisation RBAC

L'application utilise un modèle RBAC :

- un utilisateur appartient à un groupe ;
- un groupe possède plusieurs droits ;
- chaque écran ou action sensible appelle `checkRight()`.

`checkRight($rightName)` :

- redirige vers la page de connexion si l'utilisateur n'est pas connecté ;
- vérifie l'existence du droit dans `utilisateur.groupe_droit` ;
- affiche une erreur 403 si le droit est absent.

`checkRightIfLogged($rightName)` :

- retourne `true` ou `false` ;
- sert notamment à masquer les menus ou boutons.

### 10.3 Journalisation

La fonction `logAudit()` insère les événements dans `utilisateur.journal_audit`. Elle conserve :

- utilisateur ;
- action ;
- table cible ;
- identifiant d'enregistrement ;
- ancienne valeur ;
- nouvelle valeur ;
- adresse IP ;
- user agent ;
- date/heure.

Les actions couramment journalisées :

- `LOGIN`
- `LOGOUT`
- `INSERT`
- `UPDATE`
- `DELETE`

### 10.4 Protection du stock

Le stock est protégé par la base :

- la livraison vérifie la disponibilité avant sortie ;
- les sorties de stock vérifient la quantité disponible ;
- un trigger interdit tout stock négatif.

### 10.5 Points d'amélioration sécurité

Recommandations prioritaires :

1. externaliser les identifiants de base de données ;
2. désactiver `display_errors` en production ;
3. ajouter une protection CSRF sur les formulaires ;
4. régénérer l'identifiant de session après connexion ;
5. définir des cookies de session `HttpOnly`, `Secure`, `SameSite`;
6. ajouter une politique de mot de passe plus stricte ;
7. normaliser les contrôles de droits sur certaines actions historiques.

## 11. Flux métier principaux

### 11.1 Flux d'approvisionnement standard

```text
Création BCF
    ↓
Validation / envoi BCF
    ↓
Réception des produits
    ↓
Validation réception
    ↓
Génération bon d'entrée
    ↓
Insertion lignes de bon d'entrée
    ↓
Trigger d'entrée stock
    ↓
Mise à jour stock produit + mouvement_stock
```

Points clés :

- la réception peut être liée à un BCF ;
- la quantité reçue ne doit pas dépasser la quantité commandée ;
- la validation d'une réception génère un bon d'entrée ;
- le stock augmente uniquement quand les lignes de bon d'entrée sont insérées ;
- si toute la commande est reçue, le BCF passe en `receptionne`.

### 11.2 Flux de don

```text
Saisie du donateur
    ↓
Saisie des produits donnés
    ↓
Création don
    ↓
Création bon d'entrée associé
    ↓
Entrée en stock automatique
```

### 11.3 Flux de vente à crédit

```text
Création commande client
    ↓
Livraison partielle ou complète
    ↓
Sortie de stock via lignes de livraison
    ↓
Facturation de la commande livrée
    ↓
Règlements client
    ↓
Mise à jour statut facture
```

Statuts attendus :

- commande : `en_attente`, `en_cours`, `livree`, `facturee`, `reglee`, `annulee` ;
- facture : `impayee`, `partielle`, `payee`, `annulee`.

### 11.4 Flux de vente comptant

La vente comptant est un flux intégré dans `VenteController::venteComptant()` :

```text
Sélection client
    ↓
Sélection produits et quantités
    ↓
Création commande client type comptant
    ↓
Création bon de livraison complet
    ↓
Sortie de stock
    ↓
Création facture TTC
    ↓
Création règlement intégral
    ↓
Passage facture à payée
    ↓
Passage commande à réglée
    ↓
Impression ticket
```

Le flux est exécuté dans une transaction globale. En cas d'erreur, l'ensemble est annulé.

### 11.5 Flux de sortie de stock

Les sorties de stock couvrent les cas hors livraison :

- produit périmé ;
- casse ;
- non vendu ;
- retour ;
- autre motif selon configuration.

À l'enregistrement, un trigger diminue le stock et écrit un mouvement dans `structure.mouvement_stock`.

### 11.6 Flux de restauration

Lorsqu'un objet pris en charge est supprimé, un trigger sauvegarde son contenu XML dans la corbeille.

Flux :

```text
Suppression métier
    ↓
Trigger backup XML
    ↓
Élément visible dans Corbeille
    ↓
Consultation du détail
    ↓
Restauration ou suppression définitive
```

## 12. Interface utilisateur

### 12.1 AppShell

L'application utilise une structure en trois zones :

- sidebar de navigation ;
- topbar avec fil d'Ariane et utilisateur connecté ;
- zone principale de contenu.

La sidebar est organisée en sections :

- Navigation ;
- Structure ;
- Approvisionnements ;
- Ventes ;
- Utilisateurs.

Chaque section est affichée selon les droits de l'utilisateur.

### 12.2 Design system

Le design system repose sur :

- composants PHP réutilisables ;
- classes CSS centralisées dans `public/css/main.css` ;
- Tailwind pour la compilation ;
- Font Awesome pour les icônes ;
- patterns de modales, toasts, badges, cartes, tableaux.

Les vues utilisent des helpers tels que :

- `renderPageHeader()`
- `renderButton()`
- `renderTable()`
- `renderResponsiveTable()`
- `renderInput()`
- `renderSelect()`
- `renderBadge()`
- `renderEmptyState()`
- `renderToastContainer()`

### 12.3 JavaScript applicatif

Le fichier `public/js/main.js` gère :

- ouverture/fermeture de la sidebar mobile ;
- sections collapsibles ;
- action sheets mobiles ;
- swipe cards ;
- segmented controls ;
- correction viewport mobile ;
- modales ;
- toasts ;
- détail AJAX ;
- sélection globale de cases ;
- confirmation d'actions ;
- dropdown utilisateur ;
- filtres mobiles.

Les interactions sont principalement pilotées par des attributs `data-*`.

### 12.4 Impression

Les impressions utilisent des vues dédiées :

- bons de commande ;
- bons de réception ;
- bons d'entrée ;
- factures fournisseurs ;
- reçus fournisseurs ;
- commandes clients ;
- bons de livraison ;
- factures clients ;
- reçus clients ;
- bons de sortie ;
- tickets de vente ;
- états journaliers et annuels.

Le layout d'impression commun est `views/components/print_layout.php`, avec la feuille `public/css/print.css`.

## 13. API interne

### 13.1 Recherche globale

Endpoint :

```text
?action=api_search&q=terme
```

Rôle :

- retourne des résultats JSON ;
- exige une session active ;
- filtre selon les droits de l'utilisateur.

Attention technique : l'endpoint recherche actuellement les produits dans `public.produit` si cette table existe, alors que le schéma principal définit les produits dans `structure.produit`. Il est recommandé d'aligner cette recherche avec le schéma réel.

## 14. Installation et déploiement

### 14.1 Prérequis

- PHP 8.0 ou supérieur ;
- extension `pdo_pgsql` ;
- PostgreSQL 13 ou supérieur ;
- Node.js et npm ;
- serveur web Apache, Nginx ou serveur PHP intégré ;
- Composer optionnel.

### 14.2 Installation des dépendances

```bash
composer install
npm install
```

### 14.3 Création de la base

```bash
createdb gestion_stock
psql -U <utilisateur> -d gestion_stock -f database/gestion_stock_pg.sql
```

Adapter ensuite `config/database.php`.

### 14.4 Compilation des assets

Développement :

```bash
npm run dev
```

Production :

```bash
npm run build
```

### 14.5 Démarrage en développement

```bash
php -S localhost:8000
```

Puis ouvrir :

```text
http://localhost:8000
```

### 14.6 Compte initial

Le script SQL crée un groupe Administrateur avec tous les droits et un utilisateur admin. Vérifier le mot de passe exact dans le script SQL importé et le modifier immédiatement après la première connexion.

## 15. Maintenance

### 15.1 Vérifications syntaxiques PHP

Commande utile :

```bash
find controllers models config api src -name '*.php' -print0 | xargs -0 -n1 php -l
```

### 15.2 Recompilation CSS

```bash
npm run build
```

### 15.3 Sauvegarde de la base

```bash
pg_dump -U <utilisateur> -d gestion_stock -F c -f sauvegarde_gestion_stock.dump
```

### 15.4 Restauration de la base

```bash
createdb gestion_stock_restore
pg_restore -U <utilisateur> -d gestion_stock_restore sauvegarde_gestion_stock.dump
```

### 15.5 Données à surveiller

- `structure.produit.stock_actuel`
- `structure.mouvement_stock`
- `utilisateur.journal_audit`
- `utilisateur.corbeille_xml`
- factures impayées ou partielles ;
- paiements fournisseurs en attente ;
- produits sous seuil d'alerte ;
- erreurs PostgreSQL liées aux triggers de stock.

## 16. Qualité technique

- architecture simple et lisible ;
- séparation contrôleurs/modèles/vues ;
- base PostgreSQL structurée par schémas ;
- triggers pour sécuriser le stock ;
- système RBAC complet ;
- journal d'audit ;
- corbeille XML ;
- interface responsive documentée ;
- nombreux écrans d'impression.

## 17. Annexes

### 17.1 Commandes utiles

```bash
# Lancer le serveur PHP
php -S localhost:8000

# Compiler les assets
npm run build

# Surveiller les assets en développement
npm run dev

# Vérifier la syntaxe PHP
find controllers models config api src -name '*.php' -print0 | xargs -0 -n1 php -l

# Importer le schéma PostgreSQL
psql -U <utilisateur> -d gestion_stock -f database/gestion_stock_pg.sql
```

### 17.2 Fichiers clés

| Fichier | Rôle |
|---|---|
| `index.php` | Routeur principal |
| `config/database.php` | Connexion PostgreSQL |
| `config/session.php` | Session et droits |
| `config/fonctions.php` | Audit et génération de références |
| `views/layouts/main.php` | Layout principal |
| `public/js/main.js` | Interactions UI |
| `public/css/main.css` | Styles source |
| `public/css/main.min.css` | Styles compilés |
| `database/gestion_stock_pg.sql` | Schéma PostgreSQL |
