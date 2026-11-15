<?php
/**
 * POST /api/inquiry.php
 * Handles client consultation requests, data validation, and email dispatch.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Controllers\InquiryController;
use Atelier\Core\Request;

$controller = new InquiryController();
$controller->submit(new Request());
