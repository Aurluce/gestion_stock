# 🚀 Optimisations UI/UX - Guide d'utilisation

Ce document explique les nouvelles fonctionnalités UI ajoutées à l'application.

---

## ✅ Fonctionnalités Implémentées

### 1. 📍 Breadcrumb Dynamique

**Utilisation dans les controllers :**

```php
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Utilisateurs', 'href' => '?action=utilisateurs'],
    ['label' => 'Modifier'] // Dernier élément sans href
]);
```

Le breadcrumb s'affiche automatiquement dans la topbar (responsive: masqué sur mobile).

---

### 2. 🔖 Badges dans la Sidebar

**Afficher des compteurs sur les items de navigation :**

```php
echo renderSidebarItem(
    'Commandes en attente',
    'fa-shopping-cart',
    '?action=commandes',
    false, // active
    ['count' => 12, 'type' => 'warning'] // badge
);

// Ou via renderCollapsibleSection
echo renderCollapsibleSection('Ventes', [
    [
        'label' => 'Commandes',
        'icon' => 'fa-shopping-cart',
        'href' => '?action=commandes',
        'badge' => ['count' => 7, 'type' => 'danger']
    ]
], 'ventes');
```

**Types de badges disponibles :**
- `success` (vert)
- `danger` (rouge)
- `warning` (orange)
- `info` (bleu)
- `neutral` (gris)

---

### 3. 📁 Sections Collapsibles

**Les sections de la sidebar peuvent maintenant être réduites :**

```php
// Remplacer renderSidebarSection par renderCollapsibleSection
echo renderCollapsibleSection('Structure', [
    ['label' => 'Produits', 'icon' => 'fa-tag', 'href' => '?action=produits'],
    ['label' => 'Familles', 'icon' => 'fa-folder', 'href' => '?action=familles'],
], 'structure', false); // false = ouvert par défaut
```

**Paramètres :**
- `$label` : Titre de la section
- `$items` : Array d'items (même format que renderSidebarSection)
- `$id` : ID unique pour la section (optionnel)
- `$defaultCollapsed` : true pour réduire par défaut (optionnel)

**Comportement :**
- Clic sur l'en-tête pour toggle
- État sauvegardé dans localStorage
- Sur tablette (768-1280px), les sections sont toujours affichées en mode compact

---

### 4. 🔍 Recherche Globale

**Barre de recherche dans la topbar avec raccourci clavier `Ctrl+K`.**

#### Backend - API de recherche

L'endpoint `?action=api_search&q=terme` est déjà configuré et recherche dans :
- Utilisateurs (nom, login)
- Groupes (nom, description)
- Produits (nom, référence) - si la table existe

#### Personnaliser la recherche

Modifier `/api/search.php` pour ajouter d'autres entités :

```php
// Exemple : recherche dans les clients
if (checkRightIfLogged('lister_clients')) {
    $stmt = $pdo->prepare("
        SELECT 
            id_client as id,
            nom as title,
            ville as subtitle,
            'Clients' as category,
            'user-tie' as icon
        FROM public.client
        WHERE nom ILIKE :query
        LIMIT 5
    ");
    $stmt->execute(['query' => $searchTerm]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['url'] = '?action=clients&view=' . $row['id'];
        $results[] = $row;
    }
}
```

#### Navigation clavier
- `Ctrl+K` : Ouvrir la recherche
- `↑` / `↓` : Naviguer dans les résultats
- `Enter` : Sélectionner
- `Escape` : Fermer

---

### 5. 🔄 Loading States

**Nouveaux composants pour le feedback visuel :**

#### Spinner

```php
// Dans vos vues
<?= renderSpinner('md', 'brand') ?>

// Tailles : sm, md, lg
// Couleurs : brand, white, neutral
```

#### Skeleton Screens

```php
// Pendant le chargement d'une liste
<?= renderSkeletonText(5, 'full') ?>

// Pendant le chargement d'une carte
<?= renderSkeletonCard() ?>
```

#### Overlay de chargement

```php
// Afficher un overlay fullscreen
<?= renderLoadingOverlay('Importation en cours...') ?>
```

#### Inline loader

```php
// Petit loader inline
<?= renderInlineLoader('Chargement des données...') ?>
```

#### JavaScript - Afficher/masquer dynamiquement

```javascript
// Afficher l'overlay
document.getElementById('loadingOverlay').classList.remove('hidden');

// Masquer l'overlay
document.getElementById('loadingOverlay').classList.add('hidden');
```

---

### 6. 📱 Responsive Tablet Mode

**Sidebar optimisée pour tablette (768px - 1280px) :**

- **Desktop (>1280px)** : Sidebar complète (256px)
- **Tablet (768-1280px)** : Mini sidebar avec icônes seules (64px) + tooltip au survol
- **Mobile (<768px)** : Sidebar cachée avec overlay

**Comportement automatique, aucune configuration nécessaire.**

---

## 🎨 Exemples d'Utilisation

### Page complète avec toutes les optimisations

```php
<?php
// Dans un controller
$title = "Gestion des Produits";

// Breadcrumb
$breadcrumb = renderBreadcrumb([
    ['label' => 'Accueil', 'href' => '?action=dashboard'],
    ['label' => 'Structure', 'href' => '?action=produits'],
    ['label' => 'Produits']
]);

ob_start();
?>

<!-- Loading state initial -->
<?php if ($loading): ?>
    <?= renderSkeletonCard() ?>
<?php else: ?>
    
    <div class="card">
        <div class="card-header">
            <h2>Liste des produits</h2>
        </div>
        <div class="card-body">
            <?php if (empty($produits)): ?>
                <?= renderEmptyState(
                    'fa-box-open',
                    'Aucun produit',
                    'Commencez par ajouter votre premier produit.',
                    renderButton('Créer un produit', 'primary', '?action=produits&add')
                ) ?>
            <?php else: ?>
                <!-- Votre contenu -->
            <?php endif; ?>
        </div>
    </div>
    
<?php endif; ?>

<?php
$content = ob_get_clean();
require 'views/layouts/main.php';
```

---

## 🔧 Configuration

### Désactiver la recherche globale

Dans `views/layouts/main.php`, commenter la ligne :

```php
// <?= renderGlobalSearch() ?>
```

### Modifier les icônes par défaut de la recherche

Dans `public/js/main.js`, fonction `getCategoryIcon()` :

```javascript
const icons = {
    'Utilisateurs': 'user',
    'Groupes': 'layer-group',
    'Produits': 'tag',
    'Vos_Categories': 'votre-icone'
};
```

---

## 📊 Performance

### CSS compilé
Le fichier `main.min.css` est automatiquement regénéré avec toutes les nouvelles classes.

Pour recompiler manuellement :
```bash
npm run build:css
```

### Taille des fichiers
- **CSS** : ~50KB minifié (vs 45KB avant)
- **JS** : ~12KB (vs 8KB avant)
- **Impact** : +9KB total (négligeable)

---

## 🐛 Dépannage

### La recherche ne fonctionne pas
1. Vérifier que l'API est accessible : `?action=api_search&q=test`
2. Ouvrir la console navigateur pour voir les erreurs
3. Vérifier les droits utilisateur dans `checkRightIfLogged()`

### Les sections ne se replient pas
1. Vérifier que jQuery est chargé
2. Vérifier la console pour des erreurs JS
3. Vider le cache navigateur

### Le breadcrumb ne s'affiche pas
1. Vérifier que la variable `$breadcrumb` est définie avant `ob_start()`
2. Vérifier que la fonction `renderBreadcrumb()` est appelée correctement

### Le CSS n'est pas appliqué
1. Recompiler avec `npm run build:css`
2. Vider le cache navigateur (Ctrl+F5)
3. Vérifier que `main.min.css` est bien chargé dans le HTML

---

## 📝 Notes

- **Toutes les fonctionnalités sont rétrocompatibles**
- Les anciennes pages fonctionnent sans modification
- Pour profiter des optimisations, il suffit d'ajouter les nouveaux composants progressivement

---

## 🎯 Prochaines Étapes

Pour aller plus loin, vous pouvez :
1. Ajouter plus d'entités dans la recherche globale
2. Personnaliser les icônes des badges
3. Ajouter des animations personnalisées
4. Intégrer des notifications push (non inclus dans cette version)
