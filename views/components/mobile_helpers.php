<?php
/**
 * Composants helpers pour mobile
 */

/**
 * Bouton mobile search toggle (pour afficher la recherche en modal sur mobile)
 */
function renderMobileSearchButton() {
    return '
    <button class="topbar-icon-btn md:hidden" data-mobile-search-toggle>
        <i class="fas fa-search"></i>
    </button>';
}

/**
 * Modal de recherche pour mobile
 */
function renderMobileSearchModal() {
    return '
    <div id="mobileSearchModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Rechercher</h3>
                <button type="button" class="btn-icon" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                ' . renderGlobalSearch() . '
            </div>
        </div>
    </div>';
}

/**
 * Action Sheet - Alternative mobile aux dropdowns
 */
function renderActionSheet($id, $title, $actions, $cancelText = 'Annuler') {
    $html = '<div id="' . $id . '" class="action-sheet hidden">';
    $html .= '<div class="action-sheet-backdrop" data-action-sheet-close></div>';
    $html .= '<div class="action-sheet-content">';
    
    if ($title) {
        $html .= '<div class="action-sheet-header">';
        $html .= '<h4 class="action-sheet-title">' . htmlspecialchars($title) . '</h4>';
        $html .= '</div>';
    }
    
    $html .= '<div class="action-sheet-body">';
    foreach ($actions as $action) {
        $icon = isset($action['icon']) ? '<i class="fas ' . $action['icon'] . ' mr-2"></i>' : '';
        $class = 'action-sheet-item';
        if (isset($action['danger']) && $action['danger']) {
            $class .= ' action-sheet-item-danger';
        }
        
        if (isset($action['href'])) {
            $html .= '<a href="' . htmlspecialchars($action['href']) . '" class="' . $class . '">';
        } else {
            $onclick = isset($action['onclick']) ? ' onclick="' . htmlspecialchars($action['onclick']) . '"' : '';
            $html .= '<button type="button" class="' . $class . '"' . $onclick . '>';
        }
        
        $html .= $icon . htmlspecialchars($action['label']);
        $html .= isset($action['href']) ? '</a>' : '</button>';
    }
    $html .= '</div>';
    
    $html .= '<div class="action-sheet-footer">';
    $html .= '<button type="button" class="action-sheet-cancel" data-action-sheet-close>';
    $html .= htmlspecialchars($cancelText);
    $html .= '</button>';
    $html .= '</div>';
    
    $html .= '</div></div>';
    return $html;
}

/**
 * Swipeable Card - Carte avec actions swipe (pour listes mobiles)
 */
function renderSwipeableCard($content, $swipeActions = []) {
    $html = '<div class="swipeable-card" data-swipeable>';
    
    // Actions gauche (swipe right)
    if (isset($swipeActions['left'])) {
        $html .= '<div class="swipe-actions swipe-actions-left">';
        foreach ($swipeActions['left'] as $action) {
            $html .= '<button class="swipe-action swipe-action-' . ($action['type'] ?? 'default') . '">';
            $html .= '<i class="fas ' . $action['icon'] . '"></i>';
            $html .= '</button>';
        }
        $html .= '</div>';
    }
    
    // Contenu principal
    $html .= '<div class="swipeable-card-content">' . $content . '</div>';
    
    // Actions droite (swipe left)
    if (isset($swipeActions['right'])) {
        $html .= '<div class="swipe-actions swipe-actions-right">';
        foreach ($swipeActions['right'] as $action) {
            $html .= '<button class="swipe-action swipe-action-' . ($action['type'] ?? 'default') . '">';
            $html .= '<i class="fas ' . $action['icon'] . '"></i>';
            $html .= '</button>';
        }
        $html .= '</div>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Bouton Floating Action (FAB) - Pour action principale sur mobile
 */
function renderFloatingActionButton($label, $href = null, $attrs = []) {
    $icon = $attrs['icon'] ?? 'fa-plus';
    $attrsStr = '';
    foreach ($attrs as $key => $value) {
        if ($key !== 'icon') {
            $attrsStr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
    }
    
    if ($href) {
        return '<a href="' . htmlspecialchars($href) . '" class="fab"' . $attrsStr . '>
            <i class="fas ' . $icon . '"></i>
            <span class="fab-label">' . htmlspecialchars($label) . '</span>
        </a>';
    } else {
        return '<button type="button" class="fab"' . $attrsStr . '>
            <i class="fas ' . $icon . '"></i>
            <span class="fab-label">' . htmlspecialchars($label) . '</span>
        </button>';
    }
}

/**
 * Pull to Refresh indicator
 */
function renderPullToRefresh() {
    return '
    <div id="pullToRefresh" class="pull-to-refresh hidden">
        <div class="pull-to-refresh-spinner">
            <i class="fas fa-sync-alt fa-spin"></i>
        </div>
        <span class="pull-to-refresh-text">Actualiser...</span>
    </div>';
}

/**
 * Sticky Action Bar - Barre d'actions sticky en bas sur mobile
 */
function renderStickyActionBar($actions) {
    $html = '<div class="sticky-action-bar">';
    foreach ($actions as $action) {
        $html .= $action;
    }
    $html .= '</div>';
    return $html;
}

/**
 * Segmented Control - Alternative mobile aux tabs
 */
function renderSegmentedControl($id, $segments, $activeIndex = 0) {
    $html = '<div class="segmented-control" id="' . $id . '" role="tablist">';
    foreach ($segments as $index => $segment) {
        $active = $index === $activeIndex ? ' active' : '';
        $html .= '<button type="button" class="segmented-control-item' . $active . '" ';
        $html .= 'role="tab" data-segment-index="' . $index . '">';
        if (isset($segment['icon'])) {
            $html .= '<i class="fas ' . $segment['icon'] . '"></i> ';
        }
        $html .= htmlspecialchars($segment['label']);
        $html .= '</button>';
    }
    $html .= '</div>';
    return $html;
}
