<?php
/**
 * Site Setting Model & Repository
 * Handles persistent key-value configuration for studio brand, contact, whatsapp, social, and SMTP.
 */

declare(strict_types=1);

namespace Atelier\Models;

class Setting extends BaseModel {
    /**
     * Keys that must never be exposed to public API endpoints
     */
    private const SENSITIVE_KEYS = [
        'smtp_password',
        'recaptcha_secret_key'
    ];

    /**
     * Retrieve a single setting by key
     */
    public function get(string $key, ?string $default = null): ?string {
        $sql = "SELECT setting_value FROM site_settings WHERE setting_key = :k LIMIT 1";
        $row = $this->fetchOne($sql, [':k' => $key]);
        return $row !== null ? (string)$row['setting_value'] : $default;
    }

    /**
     * Retrieve all settings as an associative key => value array
     */
    public function getAll(): array {
        $sql = "SELECT setting_key, setting_value, setting_group FROM site_settings ORDER BY setting_group ASC, setting_key ASC";
        $rows = $this->fetchAll($sql);
        $result = [];
        foreach ($rows as $r) {
            $result[$r['setting_key']] = $r['setting_value'];
        }
        return $result;
    }

    /**
     * Retrieve all settings grouped by setting_group
     */
    public function getAllGrouped(): array {
        $sql = "SELECT setting_key, setting_value, setting_group FROM site_settings ORDER BY setting_group ASC, setting_key ASC";
        $rows = $this->fetchAll($sql);
        $grouped = [];
        foreach ($rows as $r) {
            $group = $r['setting_group'];
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][$r['setting_key']] = $r['setting_value'];
        }
        return $grouped;
    }

    /**
     * Retrieve all settings for a specific group
     */
    public function getByGroup(string $group): array {
        $sql = "SELECT setting_key, setting_value FROM site_settings WHERE setting_group = :g";
        $rows = $this->fetchAll($sql, [':g' => $group]);
        $result = [];
        foreach ($rows as $r) {
            $result[$r['setting_key']] = $r['setting_value'];
        }
        return $result;
    }

    /**
     * Retrieve public safe settings dictionary for frontend consumption
     */
    public function getAllPublic(): array {
        $sql = "SELECT setting_key, setting_value, setting_group FROM site_settings";
        $rows = $this->fetchAll($sql);
        $grouped = [];

        foreach ($rows as $r) {
            $key = $r['setting_key'];
            if (in_array($key, self::SENSITIVE_KEYS, true)) {
                continue; // Exclude passwords & secret tokens
            }

            $group = $r['setting_group'];
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][$key] = $r['setting_value'];
        }

        return $grouped;
    }

    /**
     * Set or update a single setting
     */
    public function set(string $key, ?string $value, string $group = 'general'): bool {
        $sql = "INSERT INTO site_settings (setting_key, setting_group, setting_value) 
                VALUES (:k, :g, :v)
                ON DUPLICATE KEY UPDATE setting_value = :v2, setting_group = :g2";
        
        $stmt = $this->query($sql, [
            ':k'  => $key,
            ':g'  => $group,
            ':v'  => $value,
            ':v2' => $value,
            ':g2' => $group
        ]);

        return $stmt->rowCount() >= 0;
    }

    /**
     * Bulk update key-value pairs for a specific group
     */
    public function setMany(array $settings, string $group = 'general'): bool {
        foreach ($settings as $k => $v) {
            $this->set((string)$k, $v !== null ? (string)$v : null, $group);
        }
        return true;
    }
}
