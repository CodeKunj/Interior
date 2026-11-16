<?php
/**
 * Press & Awards Model & Repository
 * Manages architectural press logos (Architectural Digest, Elle Decor, etc.) and accolades.
 */

declare(strict_types=1);

namespace Atelier\Models;

class PressAward extends BaseModel {
    /**
     * Retrieve all press and award items ordered by display sequence
     */
    public function getAll(): array {
        $sql = "SELECT id, title, logo_url, display_order, created_at 
                FROM press_awards 
                ORDER BY display_order ASC, id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Add a new publication / award badge
     */
    public function add(string $title, string $logoUrl, int $order = 0): int {
        $sql = "INSERT INTO press_awards (title, logo_url, display_order) 
                VALUES (:t, :u, :o)";
        $this->query($sql, [
            ':t' => trim($title),
            ':u' => trim($logoUrl),
            ':o' => $order
        ]);
        return $this->lastInsertId();
    }

    /**
     * Delete an award by ID
     */
    public function delete(int $id): bool {
        $stmt = $this->query("DELETE FROM press_awards WHERE id = :id", [':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
