<?php
function renderCard($body, $title = null, $footer = null, $attrs = []) {
    $attrsStr = '';
    foreach ($attrs as $key => $value) {
        $attrsStr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
    }
    $html = '<div class="card"' . $attrsStr . '>';
    if ($title) {
        $html .= '<div class="card-header"><h3 class="text-body-lg font-semibold text-neutral-14">' . htmlspecialchars($title) . '</h3></div>';
    }
    $html .= '<div class="card-body">' . $body . '</div>';
    if ($footer) {
        $html .= '<div class="card-footer">' . $footer . '</div>';
    }
    $html .= '</div>';
    return $html;
}
