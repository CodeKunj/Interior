<?php
/**
 * Abstract Base Model
 * Shared database query execution, prepared statements, and transactional helpers.
 */

declare(strict_types=1);

namespace Atelier\Models;

use Atelier\Config\Database;
use PDO;
use PDOStatement;

abstract class BaseModel {
    protected PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Prepare and execute a parameterized SQL query
     */
    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row as an associative array
     */
    protected function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all matching rows as an array of associative arrays
     */
    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Return ID of last inserted row
     */
    protected function lastInsertId(): int {
        return (int)$this->db->lastInsertId();
    }

    /**
     * Execute operations inside a transactional block
     */
    public function transaction(callable $callback): mixed {
        $this->db->beginTransaction();
        try {
            $result = $callback($this);
            $this->db->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
