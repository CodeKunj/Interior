<?php
/**
 * GET /api/seo.php
 * Public endpoint for dynamic page SEO & OpenGraph tags.
 * Supports JSON output for React Helmet and HTML output for social crawlers (WhatsApp, Facebook, Twitter).
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Core\Request;
use Atelier\Core\Response;
use Atelier\Models\Seo;
use Atelier\Models\Project;

$request = new Request();
$routeKey = trim(strtolower($request->query('route', 'home')));
$projectSlug = trim($request->query('slug', ''));
$format = strtolower($request->query('format', 'json'));

$seoData = null;

try {
    $seoModel = new Seo();

    // If querying a project case study directly
    if (!empty($projectSlug)) {
        $projectModel = new Project();
        $project = $projectModel->findBySlug($projectSlug);
        if ($project) {
            $seoData = [
                'route_key'        => 'work/' . $project['slug'],
                'meta_title'       => $project['title'] . ' | AURION Atelier Vérité',
                'meta_description' => substr(strip_tags($project['client_brief']), 0, 160) . '...',
                'meta_keywords'    => $project['category'] . ', ' . $project['location'] . ', luxury interior design, 3D matterport tour',
                'og_image'         => $project['cover_image'],
                'canonical_url'    => '/work/' . $project['slug']
            ];
        }
    }

    if ($seoData === null) {
        $seoData = $seoModel->getByRoute($routeKey);
    }
} catch (\Throwable $e) {
    // Database offline fallback
}

// Sensible editorial defaults if route not found
if (!$seoData) {
    $defaults = [
        'home' => [
            'meta_title'       => 'AURION • ATELIER VÉRITÉ — Architecture & Luxury Interior Sanctuaries',
            'meta_description' => 'Upscale architecture and interior design studio crafting timeless, marble-toned residential and commercial sanctuaries. Explore interactive 3D virtual tours.',
            'meta_keywords'    => 'luxury interior design, architectural studio, turnkey renovation, 3D matterport virtual tours',
            'og_image'         => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85'
        ],
        'work' => [
            'meta_title'       => 'Curated Sanctuaries & Works | AURION Atelier Vérité',
            'meta_description' => 'Explore our portfolio of private residential estates, duplex penthouses, and bespoke commercial sanctuaries with 3D virtual walkthroughs.',
            'meta_keywords'    => 'interior design portfolio, architectural case studies, residential penthouse design',
            'og_image'         => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=85'
        ],
        'services' => [
            'meta_title'       => 'Disciplines & Design Methodology | AURION Atelier Vérité',
            'meta_description' => 'Comprehensive turnkey architectural execution, 3D spatial planning, and bespoke furniture craft for discerning private clients.',
            'meta_keywords'    => 'turnkey execution, 3D architectural rendering, spatial planning, custom furniture craft',
            'og_image'         => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85'
        ],
        'about' => [
            'meta_title'       => 'Studio Heritage & Founder Elena Vance | AURION Atelier Vérité',
            'meta_description' => 'Founded by Elena Vance, Atelier Vérité synthesizes European architectural discipline with serene minimalism and authentic stone.',
            'meta_keywords'    => 'Elena Vance architect, interior design philosophy, architectural accolades',
            'og_image'         => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1200&q=85'
        ],
        'contact' => [
            'meta_title'       => 'Initiate a Private Commission | AURION Atelier Vérité',
            'meta_description' => 'Connect with our senior architectural partners to discuss private residential, turnkey development, or commercial interior commissions.',
            'meta_keywords'    => 'interior designer consultation, private commission, architectural booking',
            'og_image'         => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85'
        ]
    ];

    $matched = $defaults[$routeKey] ?? $defaults['home'];
    $seoData = [
        'route_key'        => $routeKey,
        'meta_title'       => $matched['meta_title'],
        'meta_description' => $matched['meta_description'],
        'meta_keywords'    => $matched['meta_keywords'],
        'og_image'         => $matched['og_image'],
        'canonical_url'    => '/' . ($routeKey === 'home' ? '' : $routeKey)
    ];
}

// Check if requester wants HTML format (e.g. crawler preview)
if ($format === 'html') {
    header("Content-Type: text/html; charset=UTF-8");
    $title = htmlspecialchars((string)($seoData['meta_title'] ?? 'AURION'));
    $desc = htmlspecialchars((string)($seoData['meta_description'] ?? ''));
    $ogImg = htmlspecialchars((string)($seoData['og_image'] ?? ''));
    $keywords = htmlspecialchars((string)($seoData['meta_keywords'] ?? ''));

    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <meta name="description" content="{$desc}">
    <meta name="keywords" content="{$keywords}">
    <!-- OpenGraph -->
    <meta property="og:title" content="{$title}">
    <meta property="og:description" content="{$desc}">
    <meta property="og:image" content="{$ogImg}">
    <meta property="og:type" content="website">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{$desc}">
    <meta name="twitter:image" content="{$ogImg}">
</head>
<body>
    <h1>{$title}</h1>
    <p>{$desc}</p>
</body>
</html>
HTML;
    exit();
}

// Otherwise return standard JSON response
Response::success($seoData, 'SEO metadata retrieved.');
