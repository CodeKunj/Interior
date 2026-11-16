<?php
/**
 * Admin User Model & Authentication Repository
 * Handles administrator credentials, password hashing, and secure reset tokens.
 */

declare(strict_types=1);

namespace Atelier\Models;

class AdminUser extends BaseModel {

    /**
     * Find admin by username
     */
    public function findByUsername(string $username): ?array {
        $sql = "SELECT * FROM admin_users WHERE username = :u LIMIT 1";
        $row = $this->fetchOne($sql, [':u' => trim($username)]);
        return $row ?: null;
    }

    /**
     * Find admin by email
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM admin_users WHERE email = :e LIMIT 1";
        $row = $this->fetchOne($sql, [':e' => trim($email)]);
        return $row ?: null;
    }

    /**
     * Find admin by primary ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT id, username, email, role, created_at, updated_at FROM admin_users WHERE id = :id LIMIT 1";
        $row = $this->fetchOne($sql, [':id' => $id]);
        return $row ?: null;
    }

    /**
     * Update admin username and email
     */
    public function updateProfile(int $id, string $username, string $email): bool {
        $sql = "UPDATE admin_users SET username = :u, email = :e WHERE id = :id";
        $stmt = $this->query($sql, [
            ':u'  => trim($username),
            ':e'  => trim($email),
            ':id' => $id
        ]);
        return $stmt->rowCount() >= 0;
    }

    /**
     * Update password hash
     */
    public function updatePassword(int $id, string $passwordHash): bool {
        $sql = "UPDATE admin_users SET password_hash = :p WHERE id = :id";
        $stmt = $this->query($sql, [
            ':p'  => $passwordHash,
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Generate and store a 1-hour secure password reset token
     */
    public function createPasswordResetToken(string $email): ?string {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $sql = "UPDATE admin_users 
                SET reset_token = :token, 
                    reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                WHERE email = :email";

        $this->query($sql, [
            ':token' => $token,
            ':email' => trim($email)
        ]);

        return $token;
    }

    /**
     * Locate user by non-expired reset token
     */
    public function findByResetToken(string $token): ?array {
        $sql = "SELECT id, username, email, reset_expires 
                FROM admin_users 
                WHERE reset_token = :t 
                  AND reset_expires > NOW() 
                LIMIT 1";
        $row = $this->fetchOne($sql, [':t' => trim($token)]);
        return $row ?: null;
    }

    /**
     * Set new password and invalidate reset token
     */
    public function resetPasswordWithToken(string $token, string $newPasswordHash): bool {
        $sql = "UPDATE admin_users 
                SET password_hash = :p, 
                    reset_token = NULL, 
                    reset_expires = NULL 
                WHERE reset_token = :t 
                  AND reset_expires > NOW()";
        
        $stmt = $this->query($sql, [
            ':p' => $newPasswordHash,
            ':t' => trim($token)
        ]);

        return $stmt->rowCount() > 0;
    }
}
