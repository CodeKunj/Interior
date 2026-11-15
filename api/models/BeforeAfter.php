<?php
/**
 * Before & After Model
 * Manages spatial transformation image pairs for interactive comparison sliders.
 */

declare(strict_types=1);

namespace Atelier\Models;

class BeforeAfter extends BaseModel {
    /**
     * Get all before/after transformation pairs for a project
     */
    public function getByProjectId(int $projectId): array {
        $sql = "SELECT id, project_id, title, before_image, after_image, description 
                FROM before_after 
                WHERE project_id = :pid 
                ORDER BY id ASC";
        $items = $this->fetchAll($sql, [':pid' => $projectId]);

        return array_map(function ($item) {
            $item['id'] = (int)$item['id'];
            $item['project_id'] = (int)$item['project_id'];
            return $item;
        }, $items);
    }

    /**
     * Add single before & after pair
     */
    public function add(int $projectId, string $title, string $beforeImage, string $afterImage, ?string $description = null): int {
        $sql = "INSERT INTO before_after (project_id, title, before_image, after_image, description) 
                VALUES (:pid, :title, :before_image, :after_image, :description)";
        $this->query($sql, [
            ':pid'          => $projectId,
            ':title'        => trim($title),
            ':before_image' => trim($beforeImage),
            ':after_image'  => trim($afterImage),
            ':description'  => $description ? trim($description) : null
        ]);
        return $this->lastInsertId();
    }

    /**
     * Delete before/after pair by ID
     */
    public function delete(int $id): bool {
        $stmt = $this->query("DELETE FROM before_after WHERE id = :id", [':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
