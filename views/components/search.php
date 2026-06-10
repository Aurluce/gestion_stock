<?php
/**
 * Composant de recherche globale
 */

function renderGlobalSearch() {
    return '
    <div class="topbar-search-wrapper">
        <div class="topbar-search">
            <i class="fas fa-search topbar-search-icon"></i>
            <input type="text" 
                   id="globalSearch" 
                   class="topbar-search-input" 
                   placeholder="Rechercher... (Ctrl+K)"
                   autocomplete="off"
                   data-search-trigger />
            <kbd class="topbar-search-kbd">Ctrl K</kbd>
        </div>
        
        <!-- Dropdown des résultats -->
        <div id="searchResults" class="search-dropdown hidden">
            <div class="search-loading hidden">
                <i class="fas fa-circle-notch fa-spin text-brand-600"></i>
                <span class="text-caption text-neutral-50 ml-2">Recherche en cours...</span>
            </div>
            
            <div class="search-empty hidden">
                <i class="fas fa-search text-neutral-70 text-h3 mb-2"></i>
                <p class="text-caption text-neutral-50">Aucun résultat trouvé</p>
            </div>
            
            <div class="search-results-container">
                <!-- Résultats injectés dynamiquement par JS -->
            </div>
            
            <div class="search-footer">
                <span class="text-caption text-neutral-60">
                    <kbd>↑</kbd> <kbd>↓</kbd> pour naviguer · 
                    <kbd>↵</kbd> pour sélectionner · 
                    <kbd>Esc</kbd> pour fermer
                </span>
            </div>
        </div>
    </div>';
}

function renderSearchResultSection($title, $results, $icon = 'fa-file') {
    if (empty($results)) return '';
    
    $html = '<div class="search-section">';
    $html .= '<h4 class="search-section-title">';
    $html .= '<i class="fas ' . $icon . ' mr-2"></i>' . htmlspecialchars($title);
    $html .= '</h4>';
    $html .= '<div class="search-section-items">';
    
    foreach ($results as $result) {
        $html .= '<a href="' . htmlspecialchars($result['href']) . '" class="search-result-item">';
        $html .= '<div class="search-result-icon">';
        $html .= '<i class="fas ' . ($result['icon'] ?? $icon) . '"></i>';
        $html .= '</div>';
        $html .= '<div class="search-result-content">';
        $html .= '<div class="search-result-title">' . htmlspecialchars($result['title']) . '</div>';
        if (isset($result['subtitle'])) {
            $html .= '<div class="search-result-subtitle">' . htmlspecialchars($result['subtitle']) . '</div>';
        }
        $html .= '</div>';
        if (isset($result['badge'])) {
            $html .= '<span class="badge badge-' . $result['badge']['type'] . ' ml-auto">';
            $html .= htmlspecialchars($result['badge']['text']);
            $html .= '</span>';
        }
        $html .= '</a>';
    }
    
    $html .= '</div></div>';
    return $html;
}
