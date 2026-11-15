<?php
/**
 * Project Gallery Model
 * Manages high-resolution architectural photography items for single project case studies.
 */

declare(strict_types=1);

namespace Atelier\Models;

class ProjectGallery extends BaseModel {
    /**
     * Get all gallery photos for a given project ordered by display sequence
     */
    public function getByProjectId(int $projectId): array {
        $sql = "SELECT id, project_id, image_url, caption, display_order 
                FROM project_gallery 
                WHERE project_id = :pid 
                ORDER BY display_order ASC, id ASC";
        $items = $this->fetchAll($sql, [':pid' => $projectId]);

        return array_map(function ($item) {
            $item['id'] = (int)$item['id'];
            $item['project_id'] = (int)$item['project_id'];
            $item['display_order'] = (int)$item['display_order'];
            return $item;
        }, $items);
    }

    /**
     * Add single gallery photograph
     */
    public function add(int $projectId, string $imageUrl, ?string $caption = null, int $order = 0): int {
        $sql = "INSERT INTO project_gallery (project_id, image_url, caption, display_order) 
                VALUES (:pid, :url, :caption, :order)";
        $this->query($sql, [
            ':pid'     => $projectId,
            ':url'     => trim($imageUrl),
            ':caption' => $caption ? trim($caption) : null,
            ':order'   => $order
        ]);
        return $this->lastInsertId();
    }

    /**
     * Delete gallery photograph by ID
     */
    public function delete(int $id): bool {
        $stmt = $this->query("DELETE FROM project_gallery WHERE id = :id", [':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Clear all gallery images for a project
     */
    public function deleteByProject(int $projectId): bool {
        $stmt = $this->query("DELETE FROM project_gallery WHERE project_id = :pid", [':pid' => $projectId]);
        return $stmt->rowCount() > 0;
    }
}
