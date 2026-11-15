<?php
/**
 * Database Legacy Bridge
 * Maintains backward compatibility while delegating to Atelier\Config\Database singleton.
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Atelier\Config\Database;

if (!function_exists('getDBConnection')) {
    function getDBConnection(): \PDO {
        return Database::getConnection();
    }
}
