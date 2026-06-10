<?php
function renderInput($name, $label, $type = 'text', $value = '', $error = null, $attrs = []) {
    $inputClass = $error ? 'input-error' : 'input';
    if (isset($attrs['class'])) {
        $inputClass .= ' ' . $attrs['class'];
        unset($attrs['class']);
    }
    $inputId = $attrs['id'] ?? $name;
    unset($attrs['id']);
    $attrsStr = '';
    foreach ($attrs as $key => $valueAttr) {
        $attrsStr .= ' ' . $key . '="' . htmlspecialchars($valueAttr) . '"';
    }
    $errorHtml = $error ? '<p class="error-text">' . htmlspecialchars($error) . '</p>' : '';
    return '<div>'
         . '<label class="label" for="' . htmlspecialchars($inputId) . '">' . htmlspecialchars($label) . '</label>'
         . '<input type="' . $type . '" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($inputId) . '" value="' . htmlspecialchars($value) . '" class="' . $inputClass . '"' . $attrsStr . '>'
         . $errorHtml
         . '</div>';
}

function renderSelect($name, $label, $options, $selected = null, $error = null, $attrs = [], $placeholder = null) {
    $inputClass = $error ? 'input-error' : 'select';
    if (isset($attrs['class'])) {
        $inputClass .= ' ' . $attrs['class'];
        unset($attrs['class']);
    }
    $inputId = $attrs['id'] ?? $name;
    unset($attrs['id']);
    $attrsStr = '';
    foreach ($attrs as $key => $valueAttr) {
        $attrsStr .= ' ' . $key . '="' . htmlspecialchars($valueAttr) . '"';
    }
    $optionsHtml = '';
    if ($placeholder) {
        $optionsHtml .= '<option value="">' . htmlspecialchars($placeholder) . '</option>';
    }
    foreach ($options as $value => $label) {
        $sel = $value == $selected ? ' selected' : '';
        $optionsHtml .= '<option value="' . htmlspecialchars($value) . '"' . $sel . '>' . htmlspecialchars($label) . '</option>';
    }
    $errorHtml = $error ? '<p class="error-text">' . htmlspecialchars($error) . '</p>' : '';
    return '<div>'
         . '<label class="label" for="' . htmlspecialchars($inputId) . '">' . htmlspecialchars($label) . '</label>'
         . '<select name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($inputId) . '" class="' . $inputClass . '"' . $attrsStr . '>' . $optionsHtml . '</select>'
         . $errorHtml
         . '</div>';
}

function renderTextarea($name, $label, $value = '', $error = null, $attrs = []) {
    $inputClass = $error ? 'input-error' : 'textarea';
    if (isset($attrs['class'])) {
        $inputClass .= ' ' . $attrs['class'];
        unset($attrs['class']);
    }
    $inputId = $attrs['id'] ?? $name;
    unset($attrs['id']);
    $attrsStr = '';
    foreach ($attrs as $key => $valueAttr) {
        $attrsStr .= ' ' . $key . '="' . htmlspecialchars($valueAttr) . '"';
    }
    $errorHtml = $error ? '<p class="error-text">' . htmlspecialchars($error) . '</p>' : '';
    return '<div>'
         . '<label class="label" for="' . htmlspecialchars($inputId) . '">' . htmlspecialchars($label) . '</label>'
         . '<textarea name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($inputId) . '" class="' . $inputClass . '"' . $attrsStr . '>' . htmlspecialchars($value) . '</textarea>'
         . $errorHtml
         . '</div>';
}

function renderCheckbox($name, $label, $checked = false, $attrs = []) {
    $inputId = $attrs['id'] ?? $name;
    unset($attrs['id']);
    $attrsStr = '';
    foreach ($attrs as $key => $value) {
        $attrsStr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
    }
    $chk = $checked ? ' checked' : '';
    return '<div class="checkbox-group">'
         . '<input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($inputId) . '" value="1" class="checkbox"' . $chk . $attrsStr . '>'
         . '<label class="text-body text-neutral-30" for="' . htmlspecialchars($inputId) . '">' . htmlspecialchars($label) . '</label>'
         . '</div>';
}
