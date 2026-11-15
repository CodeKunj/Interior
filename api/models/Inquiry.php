<?php
/**
 * Inquiry Model
 * Manages client consultation requests and dossier records.
 */

declare(strict_types=1);

namespace Atelier\Models;

class Inquiry extends BaseModel {
    /**
     * Create and record a new consultation inquiry
     */
    public function create(array $data): int {
        $sql = "INSERT INTO inquiries 
                (name, email, phone, property_type, area_sqft, budget_range, city, message, status) 
                VALUES 
                (:name, :email, :phone, :property_type, :area_sqft, :budget_range, :city, :message, 'new')";

        $this->query($sql, [
            ':name'          => trim($data['name']),
            ':email'         => trim($data['email']),
            ':phone'         => trim($data['phone']),
            ':property_type' => trim($data['property_type'] ?? 'Residential'),
            ':area_sqft'     => trim($data['area_sqft'] ?? 'Not Specified'),
            ':budget_range'  => trim($data['budget_range'] ?? 'Flexible'),
            ':city'          => trim($data['city'] ?? ''),
            ':message'       => trim($data['message'])
        ]);

        return $this->lastInsertId();
    }

    /**
     * Retrieve all inquiries with optional status filtering
     */
    public function getAll(string $status = '', int $limit = 50, int $offset = 0): array {
        $sql = "SELECT * FROM inquiries WHERE 1=1";
        $params = [];

        if (!empty($status) && in_array($status, ['new', 'contacted', 'scheduled', 'archived'], true)) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";

        $rows = $this->fetchAll($sql, $params);
        return array_map(function ($row) {
            $row['id'] = (int)$row['id'];
            return $row;
        }, $rows);
    }

    /**
     * Find single inquiry by ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM inquiries WHERE id = :id LIMIT 1";
        $row = $this->fetchOne($sql, [':id' => $id]);
        if ($row) {
            $row['id'] = (int)$row['id'];
        }
        return $row;
    }

    /**
     * Update inquiry status ('new', 'contacted', 'scheduled', 'archived')
     */
    public function updateStatus(int $id, string $status): bool {
        if (!in_array($status, ['new', 'contacted', 'scheduled', 'archived'], true)) {
            return false;
        }

        $stmt = $this->query(
            "UPDATE inquiries SET status = :status WHERE id = :id",
            [':status' => $status, ':id' => $id]
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Get inquiry counts grouped by status
     */
    public function getCountsByStatus(): array {
        $sql = "SELECT status, COUNT(*) as count FROM inquiries GROUP BY status";
        $rows = $this->fetchAll($sql);

        $counts = [
            'new'       => 0,
            'contacted' => 0,
            'scheduled' => 0,
            'archived'  => 0,
            'total'     => 0
        ];

        foreach ($rows as $r) {
            $counts[$r['status']] = (int)$r['count'];
            $counts['total'] += (int)$r['count'];
        }

        return $counts;
    }

    /**
     * Delete inquiry by ID
     */
    public function delete(int $id): bool {
        $stmt = $this->query("DELETE FROM inquiries WHERE id = :id", [':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
