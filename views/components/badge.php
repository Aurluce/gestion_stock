<?php
function renderBadge($label, $type = 'neutral') {
    if (!$label) return '';
    return '<span class="badge-' . $type . '">' . htmlspecialchars($label) . '</span>';
}
