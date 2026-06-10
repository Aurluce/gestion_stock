<?php
function renderToast($message, $type = 'info', $id = null) {
    if (!$message) return '';
    $id = $id ?? 'toast-' . uniqid();
    $iconMap = [
        'success' => 'fa-check',
        'error'   => 'fa-xmark',
        'warning' => 'fa-triangle-exclamation',
        'info'    => 'fa-circle-info',
    ];
    $titleMap = [
        'success' => 'Succès',
        'error'   => 'Erreur',
        'warning' => 'Attention',
        'info'    => 'Information',
    ];
    $icon = $iconMap[$type] ?? 'fa-circle-info';
    $title = $titleMap[$type] ?? 'Information';
    return '<div id="' . $id . '" class="toast toast-' . $type . '" role="alert">'
         . '<div class="toast-icon"><i class="fas ' . $icon . '"></i></div>'
         . '<div class="toast-body">'
         . '<span class="toast-title">' . $title . '</span>'
         . '<span class="toast-text">' . htmlspecialchars($message) . '</span>'
         . '</div>'
         . '<button class="toast-dismiss" onclick="dismissToast(\'' . $id . '\')"><i class="fas fa-xmark"></i></button>'
         . '</div>';
}

function renderToastContainer() {
    $html = '<div id="toast-container" class="toast-container" aria-live="polite">';
    $map = ['success' => 'success', 'danger' => 'error', 'warning' => 'warning', 'info' => 'info'];
    foreach ($map as $flashType => $toastType) {
        if (!empty($_SESSION['flash_' . $flashType])) {
            $html .= renderToast($_SESSION['flash_' . $flashType], $toastType);
            unset($_SESSION['flash_' . $flashType]);
        }
    }
    $html .= '</div>';
    return $html;
}
