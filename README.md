# 📦 Gestion de Stock

> Application web moderne de gestion d'inventaire et de stock avec système de gestion des utilisateurs, groupes et droits d'accès.

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-13%2B-blue.svg)](https://www.postgresql.org/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.4-38B2AC.svg)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-ISC-green.svg)](LICENSE)

## 🌟 Fonctionnalités

### 🔐 Gestion des Utilisateurs & Sécurité
- **Authentification sécurisée** avec gestion des sessions
- **Système de groupes** pour organiser les utilisateurs
- **Gestion fine des droits d'accès** (RBAC - Role-Based Access Control)
- **Journal d'audit complet** avec traçabilité de toutes les actions
- **Expiration des mots de passe** configurable
- **Profils utilisateurs** avec gestion des informations personnelles

### 📊 Gestion de Stock (Architecture prête)
- Structure de base pour la gestion des produits
- Gestion des familles de produits
- Gestion des fournisseurs et clients
- Commandes et factures (fournisseurs & clients)
- États et rapports (achats & ventes)

### 🎨 Interface Utilisateur Moderne
- **Design System complet** basé sur des composants réutilisables
- **Responsive Design** avec adaptation mobile/tablet/desktop
- **Tables adaptatives** se transformant en card list sur mobile
- **Recherche globale** avec raccourci clavier (Ctrl+K)
- **Sidebar collapsible** avec navigation intuitive
- **Modals, toasts, badges** et autres composants UI
- **Dark mode ready** (architecture CSS préparée)

### 🚀 Performance & UX
- **Touch-friendly** (44px minimum pour les zones tactiles)
- **Animations fluides** et transitions CSS
- **Loading states** (spinners, skeleton screens)
- **Breadcrumb dynamique** pour la navigation contextuelle
- **Safe area support** (iPhone X+ notch)
- **Optimisé pour mobile** avec viewport adaptatif

---

## 📋 Table des Matières

- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#%EF%B8%8F-configuration)
- [Structure du Projet](#-structure-du-projet)
- [Utilisation](#-utilisation)
- [Design System](#-design-system)
- [Développement](#-développement)
- [Base de Données](#-base-de-données)
- [Sécurité](#-sécurité)
- [Contribution](#-contribution)
- [Documentation](#-documentation)
- [Licence](#-licence)

---

## 🔧 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP >= 8.0** avec les extensions suivantes :
  - `pdo_pgsql` (PostgreSQL)
  - `session`
  - `json`
  - `mbstring`
  
- **PostgreSQL >= 13**
- **Composer** (optionnel, pour l'autoloading PSR-4)
- **Node.js >= 14** et **npm** (pour compiler les assets CSS)
- **Un serveur web** (Apache, Nginx, ou PHP built-in server pour dev)

---

## 🚀 Installation

### 1. Cloner le repository

```bash
git clone https://github.com/Aurluce/gestion_stock.git
cd gestion_stock
```

### 2. Installer les dépendances

#### Dépendances PHP (optionnel)
```bash
composer install
```

#### Dépendances Node.js
```bash
npm install
```

### 3. Configurer la base de données

#### Créer la base de données PostgreSQL
```bash
# Se connecter à PostgreSQL
sudo -u postgres psql

# Créer la base de données
CREATE DATABASE gestion_stock;

# Créer un utilisateur
CREATE USER gestion_user WITH PASSWORD 'votre_mot_de_passe';

# Donner les privilèges
GRANT ALL PRIVILEGES ON DATABASE gestion_stock TO gestion_user;
```

#### Importer le schéma
```bash
# Avec PostgreSQL
psql -U gestion_user -d gestion_stock -f gestion_stock_pg.sql

# OU avec MySQL (si vous préférez)
mysql -u root -p gestion_stock < gestion_stock.sql
```

### 4. Configurer l'application

Éditez le fichier `config/database.php` :

```php
<?php
$host = 'localhost';
$dbname = 'gestion_stock';
$user = 'gestion_user';
$password = 'votre_mot_de_passe';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
```

### 5. Compiler les assets CSS

```bash
# Mode développement (watch mode)
npm run dev

# Mode production (minifié)
npm run build
```

### 6. Démarrer le serveur

#### Option 1 : PHP Built-in Server (développement)
```bash
php -S localhost:8000
```

#### Option 2 : Apache/Nginx
Configurez un virtual host pointant vers le dossier du projet.

### 7. Accéder à l'application

Ouvrez votre navigateur et accédez à :
```
http://localhost:8000
```

**Identifiants par défaut :**
- Login : `admin`
- Mot de passe : `admin` (à changer immédiatement en production !)

---

## ⚙️ Configuration

### Fichiers de configuration

| Fichier | Description |
|---------|-------------|
| `config/database.php` | Configuration de la connexion à la base de données |
| `config/session.php` | Configuration des sessions PHP |
| `config/fonctions.php` | Fonctions utilitaires (vérification des droits, etc.) |
| `tailwind.config.js` | Configuration TailwindCSS |
| `postcss.config.js` | Configuration PostCSS |

### Variables d'environnement (recommandé pour production)

Créez un fichier `.env` :

```env
DB_HOST=localhost
DB_NAME=gestion_stock
DB_USER=gestion_user
DB_PASS=votre_mot_de_passe
DB_PORT=5432

SESSION_LIFETIME=3600
APP_ENV=production
```

---

## 📁 Structure du Projet

```
gestion_stock/
├── config/                      # Configuration de l'application
│   ├── autoload.php            # Autoload des composants
│   ├── database.php            # Connexion BDD
│   ├── session.php             # Gestion des sessions
│   └── fonctions.php           # Fonctions utilitaires
│
├── controllers/                 # Contrôleurs MVC
│   ├── AuthController.php      # Authentification
│   └── UtilisateurController.php
│
├── models/                      # Modèles de données
│   ├── UtilisateurModel.php
│   ├── GroupeModel.php
│   ├── DroitModel.php
│   └── JournalAuditModel.php
│
├── views/                       # Vues de l'application
│   ├── layouts/                # Layouts principaux
│   │   └── main.php            # Layout principal avec sidebar/topbar
│   ├── components/             # Composants réutilisables
│   │   ├── button.php
│   │   ├── table.php
│   │   ├── responsive_table.php
│   │   ├── modal.php
│   │   ├── form_input.php
│   │   ├── alert.php
│   │   ├── badge.php
│   │   ├── card.php
│   │   ├── pagination.php
│   │   ├── search.php
│   │   ├── loading.php
│   │   └── mobile_helpers.php
│   ├── auth/                   # Vues d'authentification
│   │   └── login.php
│   └── utilisateur/            # Vues gestion utilisateurs
│       ├── utilisateurs.php
│       ├── groupes.php
│       ├── droits.php
│       ├── groupes_droits.php
│       ├── profil.php
│       └── journal_audit.php
│
├── public/                      # Assets publics
│   ├── css/
│   │   ├── main.css            # CSS source (TailwindCSS)
│   │   └── main.min.css        # CSS compilé
│   └── js/
│       └── main.js             # JavaScript principal
│
├── api/                         # Endpoints API
│   └── search.php              # API de recherche globale
│
├── docs/                        # Documentation
│   ├── DESIGN-SYSTEM.md        # Guide du Design System
│   ├── OPTIMISATIONS-UI.md     # Guide des optimisations UI
│   ├── PLAN-RESPONSIVE-MOBILE.md # Plan responsive mobile
│   └── appshell-diagram.svg    # Diagramme de l'architecture UI
│
├── index.php                    # Point d'entrée de l'application
├── logout.php                   # Déconnexion
├── composer.json               # Dépendances PHP
├── package.json                # Dépendances Node.js
├── tailwind.config.js          # Config TailwindCSS
├── gestion_stock_pg.sql        # Schéma PostgreSQL
└── README.md                   # Ce fichier
```

---

## 💻 Utilisation

### Gestion des Utilisateurs

#### Créer un utilisateur
1. Aller dans **Utilisateurs** → **Ajouter un utilisateur**
2. Remplir le formulaire (nom, login, mot de passe, groupe)
3. Définir la date d'expiration du mot de passe (optionnel)
4. Cliquer sur **Créer l'utilisateur**

#### Gérer les groupes
1. Aller dans **Groupes**
2. Créer un nouveau groupe avec nom et description
3. Affecter des droits au groupe via **Gérer les droits**

#### Consulter l'audit
Toutes les actions sont enregistrées dans le **Journal d'audit** avec :
- Date et heure de l'action
- Utilisateur ayant effectué l'action
- Type d'action (INSERT, UPDATE, DELETE, LOGIN, LOGOUT)
- Ancienne et nouvelle valeur (au format JSON)

### Recherche Globale

- **Raccourci clavier** : `Ctrl + K` (desktop) ou bouton 🔍 (mobile)
- Recherche dans : Utilisateurs, Groupes, Produits (si configurés)
- Navigation au clavier : `↑` `↓` pour naviguer, `Enter` pour sélectionner

---

## 🎨 Design System

L'application utilise un **Design System complet** basé sur des composants réutilisables.

### Principes

✅ **Jamais de Tailwind brut dans les vues** - Uniquement des fonctions PHP
✅ **Composants réutilisables** - Un seul endroit pour chaque élément UI
✅ **Tokens de design** - Palette de couleurs cohérente
✅ **Responsive by default** - Mobile-first approach

### Exemples d'utilisation

#### Boutons
```php
<?= renderButton('Ajouter', 'primary', '?action=add', ['icon' => 'fa-plus']) ?>
<?= renderButton('Annuler', 'secondary', null, ['data-modal-close' => 'true']) ?>
<?= renderButton('Supprimer', 'danger', '?action=delete&id=1') ?>
```

#### Tables Responsives
```php
<?= renderResponsiveTable(
    ['ID', 'Nom', 'Email', 'Statut'],
    $rows,
    [
        'mobileTitle' => 1,      // Colonne titre sur mobile
        'mobileSubtitle' => 2,   // Colonne sous-titre
        'mobileBadge' => 3,      // Badge
        'actions' => $actionsCallback
    ]
) ?>
```

#### Formulaires
```php
<?= renderInput('nom', 'Nom complet', 'text', $value, $error) ?>
<?= renderSelect('groupe', 'Groupe', $options, $selected) ?>
<?= renderCheckbox('actif', 'Actif', true) ?>
<?= renderTextarea('description', 'Description', $value) ?>
```

#### Alertes & Toasts
```php
<?= renderAlert('Opération réussie !', 'success', true) ?>
<?= renderToast('Enregistré', 'success') ?>
```

### Documentation complète

Consultez [`docs/DESIGN-SYSTEM.md`](docs/DESIGN-SYSTEM.md) pour la documentation complète du Design System.

---

## 🛠️ Développement

### Workflow de développement

1. **Créer une branche**
   ```bash
   git checkout -b feature/ma-nouvelle-fonctionnalite
   ```

2. **Lancer le mode watch CSS**
   ```bash
   npm run dev
   ```

3. **Développer**
   - Modifier les fichiers PHP
   - Les changements CSS sont compilés automatiquement
   - Rafraîchir le navigateur pour voir les changements

4. **Tester**
   - Tester sur desktop (Chrome, Firefox, Safari)
   - Tester sur mobile (responsive design)
   - Vérifier le journal d'audit

5. **Commiter**
   ```bash
   git add .
   git commit -m "feat: ajout de la fonctionnalité X"
   ```

### Standards de code

#### PHP
- **PSR-12** pour le style de code
- **Échapper toutes les données utilisateur** avec `htmlspecialchars()`
- **Utiliser les composants** du design system (pas de HTML brut)
- **Vérifier les droits** avant chaque action sensible

#### CSS/TailwindCSS
- **Jamais de classes Tailwind dans les vues** (utiliser les composants)
- **Classes custom** dans `@layer components`
- **Mobile-first** approach

#### JavaScript
- **Vanilla JS** (pas de framework)
- **Data attributes** pour les interactions (`data-modal-toggle`, etc.)
- **Event delegation** pour les éléments dynamiques

### Compilation des assets

```bash
# Développement (watch mode)
npm run dev

# Production (minifié)
npm run build
```

---

## 🗄️ Base de Données

### Schéma

L'application utilise PostgreSQL avec le schéma `utilisateur` :

#### Tables principales

**`utilisateur.utilisateur`**
- Gestion des comptes utilisateurs
- Mots de passe hashés (password_hash)
- Date d'expiration des mots de passe
- Soft delete (colonne `supprime`)

**`utilisateur.groupe`**
- Groupes d'utilisateurs (ex: Administrateur, Vendeur, etc.)
- Chaque utilisateur appartient à un groupe

**`utilisateur.droit`**
- Droits d'accès disponibles (ex: `creer_utilisateur`, `supprimer_produit`)
- Organisés par module

**`utilisateur.groupe_droit`**
- Table de liaison entre groupes et droits
- Un groupe peut avoir plusieurs droits

**`utilisateur.journal_audit`**
- Historique de toutes les actions
- Stockage JSON des anciennes/nouvelles valeurs
- Trigger automatique sur INSERT/UPDATE/DELETE

### Migrations

Pour ajouter de nouvelles tables :

1. Créer le fichier SQL dans `migrations/`
2. Exécuter :
   ```bash
   psql -U gestion_user -d gestion_stock -f migrations/001_add_produits.sql
   ```

---

## 🔒 Sécurité

### Bonnes pratiques implémentées

✅ **Mots de passe hashés** avec `password_hash()` (bcrypt)
✅ **Sessions sécurisées** avec régénération d'ID
✅ **Échappement des données** avec `htmlspecialchars()`
✅ **Requêtes préparées** (PDO) pour éviter les injections SQL
✅ **Vérification des droits** avant chaque action sensible
✅ **Journal d'audit** complet pour la traçabilité
✅ **Soft delete** pour éviter la perte de données

### Recommandations pour la production

⚠️ **Changez le mot de passe admin par défaut**
⚠️ **Utilisez HTTPS** (certificat SSL/TLS)
⚠️ **Configurez les permissions** des fichiers (644 pour les fichiers, 755 pour les dossiers)
⚠️ **Activez `display_errors = Off`** dans `php.ini`
⚠️ **Mettez à jour régulièrement** PHP et PostgreSQL
⚠️ **Sauvegardez régulièrement** la base de données

### Gestion des droits

Vérifier les droits dans un contrôleur :

```php
// Vérifier un droit spécifique
checkRight('creer_utilisateur');

// Vérifier sans redirection
if (checkRightIfLogged('supprimer_produit')) {
    // Action autorisée
}
```

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment contribuer :

1. **Fork** le projet
2. **Créer une branche** (`git checkout -b feature/AmazingFeature`)
3. **Commiter** vos changements (`git commit -m 'feat: Add some AmazingFeature'`)
4. **Push** vers la branche (`git push origin feature/AmazingFeature`)
5. **Ouvrir une Pull Request**

### Convention de commits

Utilisez [Conventional Commits](https://www.conventionalcommits.org/) :

```
feat: ajout de la gestion des produits
fix: correction du bug de connexion
docs: mise à jour du README
style: formatage du code
refactor: refactoring du système d'auth
test: ajout de tests unitaires
chore: mise à jour des dépendances
```

---

## 📚 Documentation

- **[Design System](docs/DESIGN-SYSTEM.md)** - Guide complet des composants UI
- **[Optimisations UI](docs/OPTIMISATIONS-UI.md)** - Guide des nouvelles fonctionnalités
- **[Plan Responsive Mobile](docs/PLAN-RESPONSIVE-MOBILE.md)** - Stratégie responsive

### Diagrammes

- **MCD** (Modèle Conceptuel de Données) : `mcd.pdf`
- **MLD** (Modèle Logique de Données) : `mld.pdf`
- **Diagrammes de classes** : `classe.pdf`
- **Diagrammes de séquence** : `sequence*.png`
- **Architecture AppShell** : `docs/appshell-diagram.svg`


## 📊 Statistiques du Projet

- **Version** : 1.0.0
- **Langage principal** : PHP 8.0+
- **Framework CSS** : TailwindCSS 3.4
- **Base de données** : PostgreSQL 13+
- **Composants UI** : 15+ composants réutilisables
- **Responsive** : ✅ Mobile, Tablet, Desktop
