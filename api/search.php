<?php
/**
 * API de recherche globale
 * Endpoint: ?action=api_search&q=terme
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Non authentifié', 'results' => []]);
    exit;
}

$query = $_GET['q'] ?? '';
$query = trim($query);

if (strlen($query) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$results = [];
$searchTerm = '%' . $query . '%';

try {
    // Recherche dans les utilisateurs
    if (function_exists('checkRightIfLogged') && checkRightIfLogged('lister_utilisateurs')) {
        $stmt = $pdo->prepare("
            SELECT 
                id_utilisateur as id,
                nom_complet as title,
                login as subtitle,
                'Utilisateurs' as category,
                'user' as icon
            FROM utilisateur.utilisateur
            WHERE (nom_complet ILIKE :query OR login ILIKE :query)
                AND supprime = false
            LIMIT 5
        ");
        $stmt->execute(['query' => $searchTerm]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $row['url'] = '?action=utilisateurs&edit=' . $row['id'];
            $results[] = $row;
        }
    }
    
    // Recherche dans les groupes
    if (function_exists('checkRightIfLogged') && checkRightIfLogged('creer_groupe')) {
        $stmt = $pdo->prepare("
            SELECT 
                id_groupe as id,
                nom_groupe as title,
                description as subtitle,
                'Groupes' as category,
                'layer-group' as icon
            FROM utilisateur.groupe
            WHERE (nom_groupe ILIKE :query OR description ILIKE :query)
                AND supprime = false
            LIMIT 5
        ");
        $stmt->execute(['query' => $searchTerm]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $row['url'] = '?action=groupes&edit=' . $row['id'];
            $results[] = $row;
        }
    }
    
    // Recherche dans les produits (si la table existe)
    if (function_exists('checkRightIfLogged') && checkRightIfLogged('lister_produits')) {
        $tables = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('produit', $tables)) {
            $stmt = $pdo->prepare("
                SELECT 
                    id_produit as id,
                    nom as title,
                    reference as subtitle,
                    'Produits' as category,
                    'tag' as icon
                FROM public.produit
                WHERE (nom ILIKE :query OR reference ILIKE :query)
                    AND actif = true
                LIMIT 5
            ");
            $stmt->execute(['query' => $searchTerm]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $row['url'] = '?action=produits&edit=' . $row['id'];
                $results[] = $row;
            }
        }
    }
    
    echo json_encode([
        'query' => $query,
        'results' => $results,
        'count' => count($results)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Erreur de recherche',
        'results' => []
    ]);
}
