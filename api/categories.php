<?php
/**
 * GET /api/categories.php
 * Retrieve portfolio categories with active project counts.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Controllers\CategoryController;
use Atelier\Core\Request;

$controller = new CategoryController();
$controller->index(new Request());
