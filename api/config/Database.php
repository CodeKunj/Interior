<?php
/**
 * Database Singleton Connection Manager
 * Secure PDO connection with UTF8mb4, transaction support, and error diagnostics.
 */

declare(strict_types=1);

namespace Atelier\Config;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $instance = null;
    private static ?self $singleton = null;
    private static array $config = [];

    /**
     * Prevent direct instantiation
     */
    private function __construct() {}

    /**
     * Singleton instance accessor
     */
    public static function getInstance(): self {
        if (self::$singleton === null) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    /**
     * Initialize connection settings
     */
    public static function init(array $config): void {
        self::$config = $config;
    }

    /**
     * Get active PDO instance or instantiate a new connection
     *
     * @return PDO
     * @throws RuntimeException
     */
    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        if (empty(self::$config)) {
            $appConfig = require __DIR__ . '/app.php';
            self::$config = $appConfig['db'] ?? [];
        }

        $host     = self::$config['host'] ?? 'localhost';
        $port     = self::$config['port'] ?? 3306;
        $database = self::$config['database'] ?? 'atelier_interiors';
        $username = self::$config['username'] ?? 'root';
        $password = self::$config['password'] ?? '';
        $charset  = self::$config['charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$charset}_unicode_ci"
        ];

        try {
            self::$instance = new PDO($dsn, $username, $password, $options);
            return self::$instance;
        } catch (PDOException $e) {
            throw new RuntimeException(
                "Database Connection Error: " . $e->getMessage() . 
                " [Hint: Check MySQL credentials in /api/config/app.php and ensure the database '{$database}' is imported in phpMyAdmin.]",
                (int)$e->getCode(),
                $e
            );
        }
    }

    /**
     * Check if database connection can be established
     */
    public static function isConnected(): bool {
        try {
            self::getConnection();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Reset connection (useful for testing or reconnecting)
     */
    public static function disconnect(): void {
        self::$instance = null;
    }
}
