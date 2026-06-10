<?php
function renderAlert($message, $type = 'success', $dismissible = true) {
    if (!$message) return '';
    $iconMap = [
        'success' => 'fa-check-circle',
        'danger'  => 'fa-exclamation-circle',
        'warning' => 'fa-exclamation-triangle',
        'info'    => 'fa-info-circle',
    ];
    $icon = $iconMap[$type] ?? 'fa-info-circle';
    $dismissBtn = $dismissible
        ? '<button class="alert-dismiss" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>'
        : '';
    return '<div class="alert-' . $type . ' animate-fade-in">'
         . '<i class="fas ' . $icon . ' alert-icon"></i>'
         . '<span>' . htmlspecialchars($message) . '</span>'
         . $dismissBtn
         . '</div>';
}

function renderFlashAlerts() {
    $html = '';
    foreach (['success', 'danger', 'warning', 'info'] as $type) {
        if (!empty($_SESSION['flash_' . $type])) {
            $html .= renderAlert($_SESSION['flash_' . $type], $type);
            unset($_SESSION['flash_' . $type]);
        }
    }
    return $html;
}

function setFlash($message, $type = 'success') {
    $_SESSION['flash_' . $type] = $message;
}
