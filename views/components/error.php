<?php
function renderErrorPage(int $code, string $message, string $details = ''): void {
    http_response_code($code);

    $config = [
        403 => [
            'icon' => 'fa-shield-halved',
            'bg' => 'bg-danger-50',
            'text' => 'text-danger-500',
            'title' => 'Accès refusé',
            'desc' => "On dirait que vous n'avez pas les clés pour entrer ici. Contactez votre administrateur si vous pensez que c'est une erreur.",
            'hint' => 'Vérifiez vos permissions ou connectez-vous avec un compte ayant les droits requis.',
        ],
        404 => [
            'icon' => 'fa-map-signs',
            'bg' => 'bg-warning-50',
            'text' => 'text-warning-500',
            'title' => 'Page introuvable',
            'desc' => "Oups ! On s'est perdus dans le labyrinthe. La page que vous cherchez n'existe pas ou a été déplacée.",
            'hint' => 'Vérifiez le lien ou retournez au tableau de bord.',
        ],
        500 => [
            'icon' => 'fa-gear',
            'bg' => 'bg-danger-50',
            'text' => 'text-danger-500',
            'title' => 'Erreur interne',
            'desc' => "Quelque chose s'est cassé dans le moteur. L'équipe technique a été notifiée.",
            'hint' => 'Réessayez dans quelques instants.',
        ],
    ];

    $cfg = $config[$code] ?? [
        'icon' => 'fa-triangle-exclamation',
        'bg' => 'bg-danger-50',
        'text' => 'text-danger-500',
        'title' => 'Erreur',
        'desc' => $details ?: "Une erreur s'est produite.",
        'hint' => '',
    ];

    $heading = "Erreur $code";

    $breadcrumb = renderBreadcrumb([
        ['label' => 'Accueil', 'href' => '?action=dashboard'],
        ['label' => $heading]
    ]);

    ob_start();
    require __DIR__ . '/../errors/error.php';
    $content = ob_get_clean();
    require __DIR__ . '/../layouts/main.php';
    exit;
}
