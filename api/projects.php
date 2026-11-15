<?php
/**
 * GET /api/projects.php
 * Fetch portfolio projects with optional filtering by category, featured status, and search query.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Controllers\ProjectController;
use Atelier\Core\Request;

$controller = new ProjectController();
$controller->index(new Request());
