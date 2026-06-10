<?php
function asset($path) {
    return $path;
}

function csrf_field() {
    $token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
}

function activeClass($action, $current = null) {
    $current = $current ?? ($_GET['action'] ?? 'dashboard');
    return $action === $current ? 'active' : '';
}

function startsActive($prefix, $current = null) {
    $current = $current ?? ($_GET['action'] ?? 'dashboard');
    return str_starts_with($current, $prefix) ? 'active' : '';
}

