<?php
function renderButton($label, $type = 'primary', $href = null, $attrs = []) {
    $class = "btn-{$type}";
    if (isset($attrs['class'])) {
        $class .= ' ' . $attrs['class'];
        unset($attrs['class']);
    }

    $icon = '';
    if (isset($attrs['icon'])) {
        $icon = '<i class="fas ' . $attrs['icon'] . '"></i>';
        unset($attrs['icon']);
    }

    $attrsStr = '';
    foreach ($attrs as $key => $value) {
        if ($key !== 'title') {
            $attrsStr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
    }

    if (isset($attrs['title'])) {
        $attrsStr .= ' title="' . htmlspecialchars($attrs['title']) . '"';
    }

    $inner = $icon . ($label ? '<span>' . htmlspecialchars($label) . '</span>' : '');

    if ($href) {
        return '<a href="' . htmlspecialchars($href) . '" class="' . $class . '"' . $attrsStr . '>' . $inner . '</a>';
    }
    return '<button class="' . $class . '"' . $attrsStr . '>' . $inner . '</button>';
}
