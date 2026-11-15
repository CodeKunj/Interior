<?php
/**
 * Project Model & Repository
 * Handles CRUD operations, category filters, featured status, 3D tour embeds, and slugs.
 */

declare(strict_types=1);

namespace Atelier\Models;

class Project extends BaseModel {
    /**
     * Retrieve all projects with optional filters
     *
     * @param array $filters [ 'category' => string, 'featured' => bool, 'search' => string, 'limit' => int, 'offset' => int ]
     * @return array
     */
    public function getAll(array $filters = []): array {
        $sql = "SELECT id, slug, title, subtitle, category, location, area_sqft, year,
                       cover_image, hero_image, featured, tour_3d_url, tour_type, created_at
                FROM projects WHERE 1=1";
        $params = [];

        if (!empty($filters['featured'])) {
            $sql .= " AND featured = 1";
        }

        if (!empty($filters['category']) && strtolower($filters['category']) !== 'all') {
            $sql .= " AND LOWER(category) = LOWER(:category)";
            $params[':category'] = trim($filters['category']);
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE :search OR location LIKE :search OR subtitle LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY featured DESC, id ASC";

        if (!empty($filters['limit'])) {
            $limit = intval($filters['limit']);
            $offset = intval($filters['offset'] ?? 0);
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $projects = $this->fetchAll($sql, $params);
        return array_map([$this, 'formatProject'], $projects);
    }

    /**
     * Find single project by URL slug
     */
    public function findBySlug(string $slug): ?array {
        $sql = "SELECT * FROM projects WHERE slug = :slug LIMIT 1";
        $project = $this->fetchOne($sql, [':slug' => $slug]);
        return $project ? $this->formatProject($project) : null;
    }

    /**
     * Find single project by primary ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM projects WHERE id = :id LIMIT 1";
        $project = $this->fetchOne($sql, [':id' => $id]);
        return $project ? $this->formatProject($project) : null;
    }

    /**
     * Get adjacent projects (previous and next) for portfolio navigation
     */
    public function getAdjacent(int $id): array {
        $prevSql = "SELECT slug, title FROM projects WHERE id < :id ORDER BY id DESC LIMIT 1";
        $prev = $this->fetchOne($prevSql, [':id' => $id]);

        $nextSql = "SELECT slug, title FROM projects WHERE id > :id ORDER BY id ASC LIMIT 1";
        $next = $this->fetchOne($nextSql, [':id' => $id]);

        return [
            'previous' => $prev ?: null,
            'next'     => $next ?: null
        ];
    }

    /**
     * Create a new project
     */
    public function create(array $data): int {
        $slug = $this->generateUniqueSlug($data['slug'] ?? $data['title']);

        $sql = "INSERT INTO projects 
                (slug, title, subtitle, category, location, area_sqft, year, 
                 cover_image, hero_image, client_brief, design_solution, featured, tour_3d_url, tour_type)
                VALUES 
                (:slug, :title, :subtitle, :category, :location, :area_sqft, :year, 
                 :cover_image, :hero_image, :client_brief, :design_solution, :featured, :tour_3d_url, :tour_type)";

        $this->query($sql, [
            ':slug'            => $slug,
            ':title'           => trim($data['title']),
            ':subtitle'        => trim($data['subtitle'] ?? ''),
            ':category'        => trim($data['category'] ?? 'Residential'),
            ':location'        => trim($data['location'] ?? ''),
            ':area_sqft'       => intval($data['area_sqft'] ?? 0),
            ':year'            => intval($data['year'] ?? date('Y')),
            ':cover_image'     => trim($data['cover_image'] ?? ''),
            ':hero_image'      => trim($data['hero_image'] ?? $data['cover_image'] ?? ''),
            ':client_brief'    => trim($data['client_brief'] ?? ''),
            ':design_solution' => trim($data['design_solution'] ?? ''),
            ':featured'        => !empty($data['featured']) ? 1 : 0,
            ':tour_3d_url'     => !empty($data['tour_3d_url']) ? trim($data['tour_3d_url']) : null,
            ':tour_type'       => !empty($data['tour_type']) ? trim($data['tour_type']) : 'matterport'
        ]);

        return $this->lastInsertId();
    }

    /**
     * Update an existing project
     */
    public function update(int $id, array $data): bool {
        $slug = isset($data['slug']) 
            ? $this->generateUniqueSlug($data['slug'], $id) 
            : null;

        $sql = "UPDATE projects SET 
                    title = :title,
                    subtitle = :subtitle,
                    category = :category,
                    location = :location,
                    area_sqft = :area_sqft,
                    year = :year,
                    cover_image = :cover_image,
                    hero_image = :hero_image,
                    client_brief = :client_brief,
                    design_solution = :design_solution,
                    featured = :featured,
                    tour_3d_url = :tour_3d_url,
                    tour_type = :tour_type";

        $params = [
            ':id'              => $id,
            ':title'           => trim($data['title']),
            ':subtitle'        => trim($data['subtitle'] ?? ''),
            ':category'        => trim($data['category'] ?? 'Residential'),
            ':location'        => trim($data['location'] ?? ''),
            ':area_sqft'       => intval($data['area_sqft'] ?? 0),
            ':year'            => intval($data['year'] ?? date('Y')),
            ':cover_image'     => trim($data['cover_image'] ?? ''),
            ':hero_image'      => trim($data['hero_image'] ?? $data['cover_image'] ?? ''),
            ':client_brief'    => trim($data['client_brief'] ?? ''),
            ':design_solution' => trim($data['design_solution'] ?? ''),
            ':featured'        => !empty($data['featured']) ? 1 : 0,
            ':tour_3d_url'     => !empty($data['tour_3d_url']) ? trim($data['tour_3d_url']) : null,
            ':tour_type'       => !empty($data['tour_type']) ? trim($data['tour_type']) : 'matterport'
        ];

        if ($slug !== null) {
            $sql .= ", slug = :slug";
            $params[':slug'] = $slug;
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete project by ID (Foreign key cascades delete gallery and before_after automatically)
     */
    public function delete(int $id): bool {
        $stmt = $this->query("DELETE FROM projects WHERE id = :id", [':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Retrieve unique categories with project count
     */
    public function getCategoriesWithCounts(): array {
        $sql = "SELECT category, COUNT(*) as count 
                FROM projects 
                GROUP BY category 
                ORDER BY category ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Generate unique URL slug
     */
    public function generateUniqueSlug(string $title, ?int $excludeId = null): string {
        $base = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
        if ($base === '') {
            $base = 'atelier-project';
        }

        $slug = $base;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM projects WHERE slug = :slug";
            $params = [':slug' => $slug];

            if ($excludeId !== null) {
                $sql .= " AND id != :excludeId";
                $params[':excludeId'] = $excludeId;
            }

            $sql .= " LIMIT 1";

            $existing = $this->fetchOne($sql, $params);
            if (!$existing) {
                return $slug;
            }

            $slug = "{$base}-{$counter}";
            $counter++;
        }
    }

    /**
     * Format raw database record into clean API output
     */
    private function formatProject(array $project): array {
        $project['id'] = (int)$project['id'];
        $project['area_sqft'] = (int)$project['area_sqft'];
        $project['year'] = (int)$project['year'];
        $project['featured'] = (bool)$project['featured'];
        $project['has_3d_tour'] = !empty($project['tour_3d_url']);
        $project['area_sqft_formatted'] = number_format($project['area_sqft']) . ' sq. ft.';
        return $project;
    }
}
