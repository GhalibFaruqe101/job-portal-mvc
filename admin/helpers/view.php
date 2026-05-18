<?php

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function active_class($current, $expected) {
    return $current === $expected ? 'class="active"' : '';
}

function selected_attr($current, $expected) {
    return (string)$current === (string)$expected ? 'selected' : '';
}

function checked_attr($value) {
    return !empty($value) ? 'checked' : '';
}

function fmt_date($value) {
    if (!$value) return '—';
    return date('d M Y', strtotime($value));
}

function fmt_datetime($value) {
    if (!$value) return '—';
    return date('d M Y, h:i A', strtotime($value));
}

function money_bdt($value) {
    if ($value === null || $value === '') return '—';
    return '৳ ' . number_format((float)$value, 0);
}
?>
