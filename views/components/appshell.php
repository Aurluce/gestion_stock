<?php
function renderSidebarItem($label, $icon, $href, $active = false, $badge = null) {
    $activeClass = $active ? ' active' : '';
    $html = '<a href="' . htmlspecialchars($href) . '" class="sidebar-item' . $activeClass . '" data-label="' . htmlspecialchars($label) . '">';
    $html .= '<i class="fas ' . $icon . ' sidebar-item-icon"></i>';
    $html .= '<span class="sidebar-item-label">' . htmlspecialchars($label) . '</span>';
    
    if ($badge && isset($badge['count']) && $badge['count'] > 0) {
        $type = $badge['type'] ?? 'neutral';
        $html .= '<span class="sidebar-badge badge-' . $type . ' ml-auto">' . $badge['count'] . '</span>';
    }
    
    $html .= '</a>';
    return $html;
}

function renderSidebarSection($label, $items) {
    $html = '<div class="sidebar-section">' . htmlspecialchars($label) . '</div>';
    foreach ($items as $item) {
        $html .= renderSidebarItem(
            $item['label'],
            $item['icon'],
            $item['href'],
            $item['active'] ?? false,
            $item['badge'] ?? null
        );
    }
    return $html;
}

function renderCollapsibleSection($label, $items, $id = null, $defaultCollapsed = false) {
    $id = $id ?? 'section-' . preg_replace('/[^a-z0-9]/', '-', strtolower($label));
    $collapsed = $defaultCollapsed ? ' collapsed' : '';
    $dataCollapsed = $defaultCollapsed ? 'true' : 'false';
    
    $html = '<div class="sidebar-section-collapsible" data-section="' . $id . '" data-collapsed="' . $dataCollapsed . '">';
    $html .= '<button class="sidebar-section-header" data-toggle-section type="button">';
    $html .= '<span>' . htmlspecialchars($label) . '</span>';
    $html .= '<i class="fas fa-chevron-down sidebar-section-icon"></i>';
    $html .= '</button>';
    $html .= '<div class="sidebar-section-content' . $collapsed . '">';
    
    foreach ($items as $item) {
        $html .= renderSidebarItem(
            $item['label'],
            $item['icon'],
            $item['href'],
            $item['active'] ?? false,
            $item['badge'] ?? null
        );
    }
    
    $html .= '</div></div>';
    return $html;
}

function renderBreadcrumb($items) {
    $html = '<nav class="topbar-breadcrumb">';
    foreach ($items as $i => $item) {
        if (isset($item['href'])) {
            $html .= '<a href="' . htmlspecialchars($item['href']) . '">' . htmlspecialchars($item['label']) . '</a>';
        } else {
            $html .= '<span>' . htmlspecialchars($item['label']) . '</span>';
        }
        if ($i < count($items) - 1) {
            $html .= '<i class="fas fa-chevron-right text-caption text-neutral-60"></i>';
        }
    }
    return $html . '</nav>';
}

function renderPageHeader($title, $description = null, $actions = null, $breadcrumb = null) {
    $html = '<div class="page-header">'
          . '<div>'
          . '<h1 class="page-title">' . htmlspecialchars($title) . '</h1>';
    if ($description) {
        $html .= '<p class="page-description">' . $description . '</p>';
    }
    $html .= '</div>';
    if ($actions) {
        $html .= '<div class="page-actions">' . $actions . '</div>';
    }
    $html .= '</div>';
    return $html;
}
