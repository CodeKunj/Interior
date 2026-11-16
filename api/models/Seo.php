<?php
/**
 * SEO Settings Model & Repository
 * Manages route-based meta tags, descriptions, keywords, and OpenGraph social share previews.
 */

declare(strict_types=1);

namespace Atelier\Models;

class Seo extends BaseModel {
    /**
     * Retrieve all SEO route configurations
     */
    public function getAll(): array {
        $sql = "SELECT id, route_key, meta_title, meta_description, meta_keywords, og_image, updated_at 
                FROM seo_settings 
                ORDER BY id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Find SEO tags by route identifier (e.g. 'home', 'work', 'services', 'about', 'contact')
     */
    public function getByRoute(string $routeKey): ?array {
        $sql = "SELECT * FROM seo_settings WHERE route_key = :r LIMIT 1";
        $row = $this->fetchOne($sql, [':r' => trim(strtolower($routeKey))]);
        return $row ?: null;
    }

    /**
     * Save or update SEO metadata for a route
     */
    public function save(string $routeKey, array $data): bool {
        $route = trim(strtolower($routeKey));
        $title = trim($data['meta_title'] ?? '');
        $desc = trim($data['meta_description'] ?? '');
        $keywords = trim($data['meta_keywords'] ?? '');
        $ogImage = trim($data['og_image'] ?? '');

        $sql = "INSERT INTO seo_settings (route_key, meta_title, meta_description, meta_keywords, og_image) 
                VALUES (:r, :t, :d, :k, :og)
                ON DUPLICATE KEY UPDATE 
                    meta_title = :t2,
                    meta_description = :d2,
                    meta_keywords = :k2,
                    og_image = :og2";

        $stmt = $this->query($sql, [
            ':r'   => $route,
            ':t'   => $title,
            ':d'   => $desc,
            ':k'   => $keywords,
            ':og'  => $ogImage,
            ':t2'  => $title,
            ':d2'  => $desc,
            ':k2'  => $keywords,
            ':og2' => $ogImage
        ]);

        return $stmt->rowCount() >= 0;
    }
}
