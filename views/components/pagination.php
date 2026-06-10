<?php
function renderPagination($currentPage, $totalPages, $baseUrl, $params = []) {
    if ($totalPages <= 1) return '';

    $buildUrl = function($page) use ($baseUrl, $params) {
        $query = http_build_query(array_merge($params, ['page' => $page]));
        $separator = str_contains($baseUrl, '?') ? '&' : '?';
        return $baseUrl . $separator . $query;
    };

    $html = '<div class="pagination">';

    $html .= $currentPage > 1
        ? '<a href="' . $buildUrl(1) . '" class="pagination-item" title="Première page"><i class="fas fa-chevron-left"></i></a>'
        : '<span class="pagination-disabled"><i class="fas fa-chevron-left"></i></span>';

    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    if ($start > 1) {
        $html .= '<a href="' . $buildUrl(1) . '" class="pagination-item">1</a>';
        if ($start > 2) $html .= '<span class="pagination-item">...</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $class = $i == $currentPage ? 'pagination-active' : 'pagination-item';
        $html .= '<a href="' . $buildUrl($i) . '" class="' . $class . '">' . $i . '</a>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<span class="pagination-item">...</span>';
        $html .= '<a href="' . $buildUrl($totalPages) . '" class="pagination-item">' . $totalPages . '</a>';
    }

    $html .= $currentPage < $totalPages
        ? '<a href="' . $buildUrl($totalPages) . '" class="pagination-item" title="Dernière page"><i class="fas fa-chevron-right"></i></a>'
        : '<span class="pagination-disabled"><i class="fas fa-chevron-right"></i></span>';

    $html .= '</div>';
    return $html;
}
