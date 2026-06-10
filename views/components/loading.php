<?php
/**
 * Composants de chargement et feedback visuel
 */

function renderSpinner($size = 'md', $color = 'brand') {
    $sizes = [
        'sm' => 'w-4 h-4 text-caption',
        'md' => 'w-8 h-8 text-body-lg',
        'lg' => 'w-12 h-12 text-h4'
    ];
    $colors = [
        'brand' => 'text-brand-600',
        'white' => 'text-white',
        'neutral' => 'text-neutral-50'
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $colorClass = $colors[$color] ?? $colors['brand'];
    
    return '<div class="spinner ' . $sizeClass . ' ' . $colorClass . '">
        <i class="fas fa-circle-notch fa-spin"></i>
    </div>';
}

function renderSkeletonText($lines = 3, $width = 'full') {
    $widths = [
        'full' => 'w-full',
        'half' => 'w-1/2',
        'third' => 'w-1/3',
        'quarter' => 'w-1/4'
    ];
    $widthClass = $widths[$width] ?? $widths['full'];
    
    $html = '<div class="skeleton-text space-y-3">';
    for ($i = 0; $i < $lines; $i++) {
        $randomWidth = $i === $lines - 1 ? 'w-3/4' : 'w-full';
        $html .= '<div class="skeleton-line ' . $randomWidth . '"></div>';
    }
    $html .= '</div>';
    return $html;
}

function renderSkeletonCard() {
    return '
    <div class="card skeleton-card">
        <div class="card-header">
            <div class="skeleton-line w-1/3 h-6"></div>
        </div>
        <div class="card-body space-y-4">
            <div class="skeleton-line w-full"></div>
            <div class="skeleton-line w-5/6"></div>
            <div class="skeleton-line w-2/3"></div>
        </div>
    </div>';
}

function renderLoadingOverlay($message = 'Chargement en cours...') {
    return '
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            ' . renderSpinner('lg', 'brand') . '
            <p class="text-body text-neutral-30 mt-4">' . htmlspecialchars($message) . '</p>
        </div>
    </div>';
}

function renderInlineLoader($text = 'Chargement...') {
    return '
    <div class="inline-loader">
        ' . renderSpinner('sm', 'brand') . '
        <span class="text-caption text-neutral-50 ml-2">' . htmlspecialchars($text) . '</span>
    </div>';
}
