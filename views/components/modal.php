<?php
function renderModal($id, $title, $body, $footer = null, $size = 'default') {
    $sizeClass = $size !== 'default' ? ' modal-' . $size : '';
    $html = '<div id="' . htmlspecialchars($id) . '" class="modal-overlay hidden animate-fade-in"' . $sizeClass . '>'
          . '<div class="modal-content">'
          . '<div class="modal-header">'
          . '<h3 class="modal-title">' . htmlspecialchars($title) . '</h3>'
          . '<button type="button" class="btn-icon" data-modal-close><i class="fas fa-times"></i></button>'
          . '</div>'
          . '<div class="modal-body">' . $body . '</div>';
    if ($footer) {
        $html .= '<div class="modal-footer">' . $footer . '</div>';
    }
    $html .= '</div></div>';
    return $html;
}
