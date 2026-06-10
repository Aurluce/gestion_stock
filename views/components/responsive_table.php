<?php
/**
 * Table responsive avec transformation automatique en Card List sur mobile
 */

function renderResponsiveTable($headers, $rows, $config = []) {
    // Configuration par défaut
    $config = array_merge([
        'mobileTitle' => 0,           // Index de la colonne titre (ou clé associative)
        'mobileSubtitle' => 1,        // Index de la colonne sous-titre
        'mobileBadge' => null,        // Index de la colonne badge
        'mobileHidden' => [],         // Colonnes à masquer sur mobile (indices ou clés)
        'actions' => null,            // Fonction de rendu des actions
        'emptyMessage' => 'Aucune donnée disponible.',
        'mobileFields' => null,       // Champs personnalisés pour mobile (override auto)
    ], $config);
    
    $actionsColumn = $config['actions'] !== null;
    $totalColumns = count($headers) + ($actionsColumn ? 1 : 0);
    
    $html = '<div class="responsive-table-wrapper">';
    
    // ============================
    // VERSION DESKTOP/TABLET (Table)
    // ============================
    $html .= '<div class="table-container table-desktop-view">';
    $html .= '<table class="table"><thead><tr>';
    
    foreach ($headers as $header) {
        $html .= '<th class="table-th">' . htmlspecialchars($header) . '</th>';
    }
    if ($actionsColumn) {
        $html .= '<th class="table-th text-right">Actions</th>';
    }
    $html .= '</tr></thead><tbody>';
    
    if (empty($rows)) {
        $html .= '<tr><td colspan="' . $totalColumns . '" class="py-12">';
        $html .= renderEmptyState('fa-inbox', 'Aucune donnée', $config['emptyMessage']);
        $html .= '</td></tr>';
    } else {
        foreach ($rows as $rowIndex => $row) {
            $html .= '<tr class="table-tr">';
            foreach ($row as $cell) {
                $html .= '<td class="table-td">' . $cell . '</td>';
            }
            if ($actionsColumn) {
                $actions = $config['actions']($row, $rowIndex);
                $html .= '<td class="table-td text-right whitespace-nowrap">' . $actions . '</td>';
            }
            $html .= '</tr>';
        }
    }
    
    $html .= '</tbody></table></div>';
    
    // ============================
    // VERSION MOBILE (Card List)
    // ============================
    $html .= '<div class="card-list card-mobile-view">';
    
    if (empty($rows)) {
        $html .= '<div class="py-12">';
        $html .= renderEmptyState('fa-inbox', 'Aucune donnée', $config['emptyMessage']);
        $html .= '</div>';
    } else {
        foreach ($rows as $rowIndex => $row) {
            $html .= renderCardListItem($headers, $row, $config, $rowIndex);
        }
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

function renderCardListItem($headers, $row, $config, $rowIndex) {
    $titleIndex = $config['mobileTitle'];
    $subtitleIndex = $config['mobileSubtitle'] ?? null;
    $badgeIndex = $config['mobileBadge'] ?? null;
    $hiddenIndices = $config['mobileHidden'];
    
    $html = '<div class="card-list-item">';
    
    // Header de la carte (titre + badge)
    $html .= '<div class="card-list-header">';
    
    // Titre - vérifier que ce n'est pas un array
    $titleValue = $row[$titleIndex] ?? '';
    $title = is_array($titleValue) ? '' : strip_tags($titleValue);
    $html .= '<h3 class="card-list-title">' . $title . '</h3>';
    
    // Badge (si configuré)
    if ($badgeIndex !== null && isset($row[$badgeIndex])) {
        $badgeValue = $row[$badgeIndex];
        if (!is_array($badgeValue)) {
            $html .= '<div class="card-list-badge">' . $badgeValue . '</div>';
        }
    }
    
    $html .= '</div>';
    
    // Sous-titre (si configuré)
    if ($subtitleIndex !== null && isset($row[$subtitleIndex])) {
        $subtitleValue = $row[$subtitleIndex] ?? '';
        $subtitle = is_array($subtitleValue) ? '' : strip_tags($subtitleValue);
        if ($subtitle) {
            $html .= '<div class="card-list-subtitle">' . $subtitle . '</div>';
        }
    }
    
    // Body de la carte (autres champs)
    $html .= '<div class="card-list-body">';
    
    if ($config['mobileFields']) {
        // Champs personnalisés
        foreach ($config['mobileFields'] as $fieldIndex => $fieldLabel) {
            if (isset($row[$fieldIndex])) {
                $fieldValue = $row[$fieldIndex];
                if (!is_array($fieldValue)) {
                    $html .= '<div class="card-list-field">';
                    $html .= '<span class="card-list-label">' . htmlspecialchars($fieldLabel) . '</span>';
                    $html .= '<span class="card-list-value">' . $fieldValue . '</span>';
                    $html .= '</div>';
                }
            }
        }
    } else {
        // Affichage automatique (tous les champs sauf titre, subtitle, badge, hidden, et clés commençant par _)
        $excludeIndices = array_merge(
            [$titleIndex],
            $subtitleIndex !== null ? [$subtitleIndex] : [],
            $badgeIndex !== null ? [$badgeIndex] : [],
            $hiddenIndices
        );
        
        foreach ($row as $cellIndex => $cellValue) {
            // Ignorer les clés spéciales (commençant par _) ou les arrays
            if ((is_string($cellIndex) && strpos($cellIndex, '_') === 0) || is_array($cellValue)) {
                continue;
            }
            
            // Ignorer les indices exclus
            if (in_array($cellIndex, $excludeIndices)) {
                continue;
            }
            
            // Ignorer si pas de label correspondant
            $label = $headers[$cellIndex] ?? '';
            if (!$label) continue;
            
            $html .= '<div class="card-list-field">';
            $html .= '<span class="card-list-label">' . htmlspecialchars($label) . '</span>';
            $html .= '<span class="card-list-value">' . $cellValue . '</span>';
            $html .= '</div>';
        }
    }
    
    $html .= '</div>';
    
    // Actions
    if ($config['actions']) {
        $actions = $config['actions']($row, $rowIndex);
        $html .= '<div class="card-list-actions">' . $actions . '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Version simplifiée pour les cas basiques
 */
function renderSimpleResponsiveTable($headers, $rows, $actionRenderer = null, $emptyMessage = 'Aucune donnée disponible.') {
    return renderResponsiveTable($headers, $rows, [
        'mobileTitle' => 0,
        'mobileSubtitle' => 1,
        'mobileBadge' => count($headers) > 4 ? 4 : null,
        'actions' => $actionRenderer,
        'emptyMessage' => $emptyMessage
    ]);
}
