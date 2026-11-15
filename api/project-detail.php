<?php
/**
 * GET /api/project-detail.php?slug=...
 * Fetch single project details including curated photography gallery, before/after pairs, and 3D virtual tour.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Controllers\ProjectController;
use Atelier\Core\Request;

$controller = new ProjectController();
$controller->detail(new Request());
