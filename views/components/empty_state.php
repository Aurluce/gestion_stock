<?php
function renderEmptyState($icon, $title, $description = null, $action = null) {
    $html = '<div class="empty-state">'
          . '<div class="empty-state-icon"><i class="fas ' . $icon . '"></i></div>'
          . '<div class="empty-state-title">' . htmlspecialchars($title) . '</div>';
    if ($description) {
        $html .= '<div class="empty-state-text">' . htmlspecialchars($description) . '</div>';
    }
    if ($action) {
        $html .= '<div class="mt-4">' . $action . '</div>';
    }
    $html .= '</div>';
    return $html;
}
