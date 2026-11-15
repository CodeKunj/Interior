<?php
/**
 * Image Upload & WebP Conversion Handler
 * Auto-converts high-resolution JPG & PNG images to WebP via PHP GD for superior web performance.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap.php';

use Atelier\Core\Response;

/**
 * Upload and optionally convert an image file to WebP
 *
 * @param array $file Array from $_FILES['input_name']
 * @param string $subfolder Subfolder within /uploads/ ('projects', 'gallery', 'before-after')
 * @param int $quality WebP quality rating (1-100, default 85)
 * @return array ['success' => bool, 'url' => string, 'path' => string, 'filename' => string, 'is_webp' => bool]
 * @throws RuntimeException
 */
function handle_image_upload_to_webp(array $file, string $subfolder = 'projects', int $quality = 85): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid file upload parameters.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'Uploaded file exceeds upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'Uploaded file exceeds MAX_FILE_SIZE specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
        ];
        throw new RuntimeException($errors[$file['error']] ?? 'Unknown upload error.');
    }

    // Size limit: 12MB
    $maxSize = 12 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new RuntimeException('The uploaded photo exceeds the 12MB limit.');
    }

    // MIME verification
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = $finfo ? finfo_file($finfo, $file['tmp_name']) : $file['type'];
    if ($finfo) {
        finfo_close($finfo);
    }

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif'
    ];

    if (!array_key_exists($mimeType, $allowedMimes)) {
        throw new RuntimeException("Invalid image type '{$mimeType}'. Only JPG, PNG, and WebP are supported.");
    }

    // Prepare uploads directory
    $baseUploadDir = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $subfolder;
    if (!is_dir($baseUploadDir)) {
        if (!mkdir($baseUploadDir, 0755, true) && !is_dir($baseUploadDir)) {
            throw new RuntimeException("Failed to create upload destination directory: {$baseUploadDir}");
        }
    }

    // Generate unique base filename
    $token = bin2hex(random_bytes(10));
    $timestamp = time();
    $baseName = "img_{$timestamp}_{$token}";
    $sourcePath = $file['tmp_name'];

    $convertedToWebP = false;
    $finalFilename = '';
    $finalFilePath = '';

    // Convert JPG / PNG to WebP if GD and imagewebp are available
    if (function_exists('imagewebp') && in_array($mimeType, ['image/jpeg', 'image/png'], true)) {
        $imageResource = null;

        if ($mimeType === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
            $imageResource = @imagecreatefromjpeg($sourcePath);
        } elseif ($mimeType === 'image/png' && function_exists('imagecreatefrompng')) {
            $imageResource = @imagecreatefrompng($sourcePath);
            if ($imageResource) {
                // Preserve transparency
                imagepalettetotruecolor($imageResource);
                imagealphablending($imageResource, true);
                imagesavealpha($imageResource, true);
            }
        }

        if ($imageResource) {
            $finalFilename = "{$baseName}.webp";
            $finalFilePath = $baseUploadDir . DIRECTORY_SEPARATOR . $finalFilename;

            if (@imagewebp($imageResource, $finalFilePath, $quality)) {
                $convertedToWebP = true;
            }
            imagedestroy($imageResource);
        }
    }

    // Fallback: save original if WebP conversion wasn't done
    if (!$convertedToWebP) {
        $ext = $allowedMimes[$mimeType];
        $finalFilename = "{$baseName}.{$ext}";
        $finalFilePath = $baseUploadDir . DIRECTORY_SEPARATOR . $finalFilename;

        if (!move_uploaded_file($sourcePath, $finalFilePath)) {
            throw new RuntimeException('Failed to move uploaded file to target directory.');
        }
    }

    // Ensure secure .htaccess in uploads directory
    $uploadsRoot = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'uploads';
    $htaccessPath = $uploadsRoot . DIRECTORY_SEPARATOR . '.htaccess';
    if (!file_exists($htaccessPath)) {
        $htaccessContent = "# Prevent script execution in uploads directory\n<FilesMatch \"\.(php|phtml|php3|php4|php5|php7|phps|pl|py|cgi)$\">\n    Deny from all\n</FilesMatch>\nOptions -ExecCGI\nphp_flag engine off\n";
        @file_put_contents($htaccessPath, $htaccessContent);
    }

    $publicUrl = "/uploads/{$subfolder}/{$finalFilename}";

    return [
        'success'  => true,
        'url'      => $publicUrl,
        'path'     => $finalFilePath,
        'filename' => $finalFilename,
        'is_webp'  => $convertedToWebP || $mimeType === 'image/webp'
    ];
}

// Standalone AJAX / API endpoint handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    if (empty($_SESSION['atelier_admin'])) {
        Response::unauthorized('Authentication required to upload media.');
    }

    $inputKey = isset($_FILES['file']) ? 'file' : (isset($_FILES['image']) ? 'image' : null);
    if (!$inputKey) {
        Response::badRequest('No image file detected in upload request (use field "file" or "image").');
    }

    $subfolder = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['subfolder'] ?? 'projects') ?: 'projects';
    $quality = intval($_POST['quality'] ?? 85);

    try {
        $result = handle_image_upload_to_webp($_FILES[$inputKey], $subfolder, $quality);
        Response::success($result, 'Image successfully uploaded and processed as WebP.');
    } catch (\Throwable $e) {
        Response::error($e->getMessage(), 400);
    }
}
