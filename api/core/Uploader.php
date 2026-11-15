<?php
/**
 * Secure File Uploader for High-Resolution Architectural Photography
 * Enforces MIME-type inspection, extension whitelisting, and script execution prevention.
 */

declare(strict_types=1);

namespace Atelier\Core;

use RuntimeException;

class Uploader {
    private array $config;

    public function __construct(?array $config = null) {
        if ($config === null) {
            $appConfig = require __DIR__ . '/../config/app.php';
            $this->config = $appConfig['uploads'] ?? [];
        } else {
            $this->config = $config;
        }
    }

    /**
     * Process an uploaded file from $_FILES[$inputName]
     *
     * @param string $inputName
     * @return array [ 'success' => bool, 'url' => string, 'path' => string, 'filename' => string ]
     * @throws RuntimeException
     */
    public function upload(string $inputName): array {
        if (!isset($_FILES[$inputName])) {
            throw new RuntimeException("No file was uploaded under input field '{$inputName}'.");
        }

        $file = $_FILES[$inputName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->getErrorMessage($file['error']));
        }

        // Check Max Size
        $maxSize = $this->config['max_file_size'] ?? (10 * 1024 * 1024);
        if ($file['size'] > $maxSize) {
            $sizeMb = round($maxSize / (1024 * 1024));
            throw new RuntimeException("The uploaded file exceeds the maximum permitted size of {$sizeMb}MB.");
        }

        // Validate Extension
        $originalExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = $this->config['allowed_extensions'] ?? ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        if (!in_array($originalExt, $allowedExts, true)) {
            $allowedList = implode(', ', $allowedExts);
            throw new RuntimeException("Invalid file extension '.{$originalExt}'. Allowed: {$allowedList}.");
        }

        // Validate MIME type via FileInfo
        $tmpPath = $file['tmp_name'];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);

            $allowedMimes = $this->config['allowed_mimes'] ?? [
                'image/jpeg', 'image/png', 'image/webp', 'image/avif'
            ];
            if (!in_array($mime, $allowedMimes, true)) {
                throw new RuntimeException("Detected invalid MIME type '{$mime}'. Only authentic image files are permitted.");
            }
        }

        // Ensure Destination Directory Exists & Is Protected
        $targetDir = rtrim($this->config['dir'] ?? (__DIR__ . '/../../uploads'), '/\\');
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new RuntimeException("Failed to initialize target uploads directory '{$targetDir}'. Please check folder permissions.");
            }
        }

        // Secure Uploads Folder with Script Protection .htaccess
        $htaccessPath = $targetDir . '/.htaccess';
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "# Security: Prevent execution of server-side scripts in uploads directory\n"
                             . "<FilesMatch \"\\.(php|phtml|php3|php4|php5|php7|php8|phps|cgi|pl|sh)$\">\n"
                             . "    Require all denied\n"
                             . "</FilesMatch>\n"
                             . "Options -Indexes -ExecCGI\n";
            @file_put_contents($htaccessPath, $htaccessContent);
        }

        // Generate Collision-Free Sanitized Filename
        $safeBase = preg_replace('/[^a-z0-9_-]/i', '', pathinfo($file['name'], PATHINFO_FILENAME));
        if (strlen($safeBase) > 30) {
            $safeBase = substr($safeBase, 0, 30);
        }
        $randomHex = bin2hex(random_bytes(6));
        $newFilename = "atelier_{$safeBase}_{$randomHex}.{$originalExt}";
        $destination = $targetDir . '/' . $newFilename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException("Failed to transfer uploaded file to destination path.");
        }

        $urlPrefix = rtrim($this->config['url_prefix'] ?? '/uploads/', '/') . '/';

        return [
            'success'   => true,
            'filename'  => $newFilename,
            'url'       => $urlPrefix . $newFilename,
            'path'      => $destination,
            'size'      => $file['size'],
            'mime'      => $mime ?? "image/{$originalExt}"
        ];
    }

    private function getErrorMessage(int $errorCode): string {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was selected.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension halted the file upload.',
            default               => 'Unknown upload error.'
        };
    }
}
