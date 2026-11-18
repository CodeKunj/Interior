<?php
/**
 * GET /api/content.php
 * Serves dynamic website content: journeys, services, process steps,
 * testimonials, faqs, disciplines, press mentions.
 * Optional query parameter: ?section=journeys|services|process|testimonials|faqs|disciplines|press|all
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Controllers\ContentController;
use Atelier\Core\Request;

$controller = new ContentController();
$controller->index(new Request());
