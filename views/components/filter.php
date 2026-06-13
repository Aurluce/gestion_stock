<?php
/**
 * Barre de filtre compacte et responsive
 *
 * @param string $action  URL de base (ex: 'commande_fourn')
 * @param array  $fields  [[type, name, label, options?, placeholder?], ...]
 * @param array  $extra   HTML supplémentaire dans la zone boutons
 *
 * Types : 'search', 'select', 'date'
 */
function renderFilterBar($action, $fields = [], $extra = '') {
    $hasActive = false;
    foreach ($fields as $f) {
        $v = $_GET[$f[1] ?? ''] ?? '';
        if ($v !== '' && $v !== null) { $hasActive = true; break; }
    }

    $uid = 'filtre-' . bin2hex(random_bytes(4));
    $show = $hasActive || count($fields) <= 2;
    $mobileToggle = count($fields) > 2
        ? '<button type="button" class="filter-toggle btn-ghost btn-sm" data-filter-toggle="' . $uid . '" title="Filtres"><i class="fas fa-sliders-h"></i></button>'
        : '';

    $html = '<div class="filter-bar' . ($show ? '' : ' filter-bar-collapsed') . '" id="' . $uid . '">';
    $html .= '<form method="get" class="filter-form">';
    $html .= '<input type="hidden" name="action" value="' . htmlspecialchars($action) . '">';

    // Ligne filtres
    $html .= '<div class="filter-fields">';
    foreach ($fields as $f) {
        $type = $f[0] ?? 'search';
        $name = $f[1] ?? '';
        $label = $f[2] ?? '';
        $current = $_GET[$name] ?? '';

        $html .= '<div class="filter-field">';
        if ($type === 'search') {
            $html .= '<div class="filter-search-wrap">';
            $html .= '<i class="fas fa-search filter-search-icon"></i>';
            $html .= '<input type="text" name="' . htmlspecialchars($name) . '" class="filter-input" placeholder="' . htmlspecialchars($label) . '" value="' . htmlspecialchars($current) . '">';
            if ($current !== '') {
                $html .= '<a href="?action=' . htmlspecialchars($action) . '" class="filter-clear" title="Effacer"><i class="fas fa-times"></i></a>';
            }
            $html .= '</div>';
        } elseif ($type === 'select') {
            $options = $f[3] ?? [];
            $placeholder = $f[4] ?? 'Tous';
            $html .= '<select name="' . htmlspecialchars($name) . '" class="filter-select">';
            $html .= '<option value="">' . htmlspecialchars($placeholder) . '</option>';
            foreach ($options as $val => $lbl) {
                $sel = ((string)$current === (string)$val) ? ' selected' : '';
                $html .= '<option value="' . htmlspecialchars($val) . '"' . $sel . '>' . htmlspecialchars($lbl) . '</option>';
            }
            $html .= '</select>';
        } elseif ($type === 'date') {
            $html .= '<input type="date" name="' . htmlspecialchars($name) . '" class="filter-input" value="' . htmlspecialchars($current) . '">';
        }
        $html .= '</div>';
    }
    $html .= '</div>';

    // Boutons
    $html .= '<div class="filter-actions">';
    $html .= $mobileToggle;
    $html .= '<button type="submit" class="btn-primary btn-sm"><i class="fas fa-filter mr-1"></i>Filtrer</button>';
    if ($hasActive) {
        $html .= '<a href="?action=' . htmlspecialchars($action) . '" class="btn-secondary btn-sm">Réinit.</a>';
    }
    $html .= $extra;
    $html .= '</div>';

    $html .= '</form></div>';
    return $html;
}
