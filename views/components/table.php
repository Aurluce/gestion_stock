<?php
function renderTable($headers, $rows, $actionRenderer = null, $emptyMessage = 'Aucune donnée disponible.') {
    $html = '<div class="table-container"><table class="table"><thead><tr>';
    foreach ($headers as $header) {
        $html .= '<th class="table-th">' . htmlspecialchars($header) . '</th>';
    }
    if ($actionRenderer) {
        $html .= '<th class="table-th text-right">Actions</th>';
    }
    $html .= '</tr></thead><tbody>';

    if (empty($rows)) {
        $html .= '<tr><td colspan="' . (count($headers) + ($actionRenderer ? 1 : 0)) . '">';
        $html .= '<div class="empty-state py-12"><div class="empty-state-icon"><i class="fas fa-inbox"></i></div>';
        $html .= '<div class="empty-state-text">' . htmlspecialchars($emptyMessage) . '</div></div>';
        $html .= '</td></tr>';
    } else {
        foreach ($rows as $row) {
            $html .= '<tr class="table-tr">';
            foreach ($row as $cell) {
                $html .= '<td class="table-td">' . $cell . '</td>';
            }
            if ($actionRenderer) {
                $actions = $actionRenderer($row);
                $html .= '<td class="table-td text-right whitespace-nowrap">' . $actions . '</td>';
            }
            $html .= '</tr>';
        }
    }

    $html .= '</tbody></table></div>';
    return $html;
}
