<?php
/**
 * POST /api/upload.php
 * Secure image upload endpoint for admin and studio editors.
 * Stores files safely in /uploads/ with MIME inspection.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Core\Request;
use Atelier\Core\Response;
use Atelier\Core\Uploader;

$request = new Request();
$request->requireMethod('POST');

try {
    $uploader = new Uploader();
    // Default input field name is 'image' or 'file'
    $inputName = isset($_FILES['file']) ? 'file' : 'image';
    $result = $uploader->upload($inputName);

    Response::success($result, 'Image uploaded successfully.', 201);
} catch (\Throwable $e) {
    Response::badRequest($e->getMessage());
}
