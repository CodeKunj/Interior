<?php
/**
 * Module 6: Account Security & Password Management
 * Update credentials, verify current password, and configure reCAPTCHA/Turnstile spam filters.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Models\AdminUser;
use Atelier\Models\Setting;

require_admin_auth();

$userModel = new AdminUser();
$settingModel = new Setting();

$username = $_SESSION['atelier_admin'] ?? '';
$admin = $userModel->findByUsername($username);

if (!$admin) {
    // If not found by username in session, look up by default ID 1
    $admin = $userModel->findById(1);
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'CSRF token mismatch. Please try again.');
        header('Location: profile.php');
        exit();
    }

    $action = $_POST['action'] ?? '';

    // 1. Update Profile Information (Username & Email)
    if ($action === 'update_profile') {
        try {
            $newUsername = trim($_POST['username'] ?? '');
            $newEmail = trim($_POST['email'] ?? '');

            if (empty($newUsername) || empty($newEmail)) {
                throw new \Exception("Username and email cannot be blank.");
            }

            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Please enter a valid email address.");
            }

            $adminId = (int)($admin['id'] ?? 1);
            $userModel->updateProfile($adminId, $newUsername, $newEmail);
            $_SESSION['atelier_admin'] = $newUsername;

            set_flash('success', 'Profile credentials updated.');
            header('Location: profile.php');
            exit();

        } catch (\Throwable $e) {
            set_flash('error', 'Profile update error: ' . $e->getMessage());
        }
    }

    // 2. Change Password (Requires verifying current password)
    if ($action === 'change_password') {
        try {
            $currentPass = $_POST['current_password'] ?? '';
            $newPass = $_POST['new_password'] ?? '';
            $confirmPass = $_POST['confirm_password'] ?? '';

            if (strlen($newPass) < 8) {
                throw new \Exception("New password must be at least 8 characters long.");
            }

            if ($newPass !== $confirmPass) {
                throw new \Exception("New password and confirmation do not match.");
            }

            // Verify current password against database hash
            $userRecord = $userModel->findByUsername($_SESSION['atelier_admin'] ?? $admin['username']);
            if (!$userRecord || !password_verify($currentPass, $userRecord['password_hash'])) {
                throw new \Exception("The current password entered is incorrect.");
            }

            // Hash new password securely
            $newHash = password_hash($newPass, PASSWORD_DEFAULT);
            $userModel->updatePassword((int)$userRecord['id'], $newHash);

            set_flash('success', 'Master password updated securely.');
            header('Location: profile.php');
            exit();

        } catch (\Throwable $e) {
            set_flash('error', $e->getMessage());
        }
    }

    // 3. Update Spam Protection Keys (reCAPTCHA / Cloudflare Turnstile)
    if ($action === 'save_spam_protection') {
        try {
            $spamSettings = [
                'recaptcha_site_key'   => trim($_POST['recaptcha_site_key'] ?? ''),
                'recaptcha_secret_key' => trim($_POST['recaptcha_secret_key'] ?? ''),
            ];

            $settingModel->setMany($spamSettings, 'security');
            set_flash('success', 'Anti-spam protection keys saved.');
            header('Location: profile.php');
            exit();

        } catch (\Throwable $e) {
            set_flash('error', 'Error saving security keys: ' . $e->getMessage());
        }
    }
}

// Fetch Existing Security Settings
$securityCurrent = $settingModel->getByGroup('security');

render_admin_layout_start(
    'Account & Security',
    'profile',
    '',
    [['label' => 'Account & Security', 'url' => '']]
);
?>

<div class="max-w-4xl space-y-10">

    <!-- 1. Administrator Profile Info -->
    <form method="POST" class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_profile">

        <div class="border-b border-sand pb-4">
            <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Authentication Dossier</span>
            <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Administrator Profile</h2>
            <p class="text-xs text-stone mt-1">Primary login identity for managing the Atelier Vérité sanctuary.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($admin['username'] ?? 'admin') ?>" required
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Administrator Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($admin['email'] ?? 'admin@interior.ritiverse.in') ?>" required
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                <p class="text-[11px] text-stone mt-1">Used for 1-hour secure password reset links.</p>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold">
                Update Identity
            </button>
        </div>
    </form>

    <!-- 2. Change Password Form -->
    <form method="POST" class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">

        <div class="border-b border-sand pb-4">
            <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Cryptographic Vault</span>
            <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Change Administrative Password</h2>
            <p class="text-xs text-stone mt-1">Verify your existing master password before updating to a new 8+ character passphrase.</p>
        </div>

        <div class="space-y-5 max-w-lg">
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">New Password (Min 8 characters)</label>
                <input type="password" name="new_password" required minlength="8"
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Confirm New Password</label>
                <input type="password" name="confirm_password" required minlength="8"
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold">
                Update Master Password
            </button>
        </div>
    </form>

    <!-- 3. Spam Protection Settings (reCAPTCHA / Turnstile) -->
    <form method="POST" class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_spam_protection">

        <div class="border-b border-sand pb-4">
            <span class="text-[10px] tracking-[0.24em] uppercase text-emerald-700 font-semibold">Inquiry Shield</span>
            <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">reCAPTCHA &amp; Spam Protection</h2>
            <p class="text-xs text-stone mt-1">Shield the public consultation request form from bots and automated crawlers.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">reCAPTCHA / Turnstile Site Key</label>
                <input type="text" name="recaptcha_site_key" value="<?= htmlspecialchars($securityCurrent['recaptcha_site_key'] ?? '') ?>"
                       placeholder="6Ld..."
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                <p class="text-[11px] text-stone mt-1">Public key exposed to the contact form.</p>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Secret Key</label>
                <input type="password" name="recaptcha_secret_key" value="<?= htmlspecialchars($securityCurrent['recaptcha_secret_key'] ?? '') ?>"
                       placeholder="••••••••••••••••••••••••••••••••"
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                <p class="text-[11px] text-stone mt-1">Server verification secret (never exposed to frontend).</p>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold">
                Save Anti-Spam Keys
            </button>
        </div>
    </form>

</div>

<?php render_admin_layout_end(); ?>
