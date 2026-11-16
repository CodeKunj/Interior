<?php
/**
 * Module 5: Email & SMTP Notification Settings
 * Configure cPanel mail credentials, client auto-replies, and run live diagnostic test emails.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Models\Setting;
use Atelier\Core\Mailer;

require_admin_auth();

$settingModel = new Setting();
$testResult = null;

// Handle Actions (Save SMTP or Trigger Test Email)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'CSRF verification failed.');
        header('Location: settings-smtp.php');
        exit();
    }

    $action = $_POST['action'] ?? 'save_smtp';

    if ($action === 'save_smtp') {
        try {
            $smtpSettings = [
                'smtp_host'                 => trim($_POST['smtp_host'] ?? ''),
                'smtp_port'                 => trim($_POST['smtp_port'] ?? '465'),
                'smtp_encryption'           => trim($_POST['smtp_encryption'] ?? 'ssl'),
                'smtp_username'             => trim($_POST['smtp_username'] ?? ''),
                'admin_alert_email'         => trim($_POST['admin_alert_email'] ?? ''),
                'enable_client_autoreply'   => isset($_POST['enable_client_autoreply']) ? '1' : '0',
            ];

            // Only update password if provided
            $newPassword = trim($_POST['smtp_password'] ?? '');
            if (!empty($newPassword)) {
                $smtpSettings['smtp_password'] = $newPassword;
            }

            $settingModel->setMany($smtpSettings, 'smtp');
            set_flash('success', 'SMTP server configuration saved successfully.');
            header('Location: settings-smtp.php');
            exit();

        } catch (\Throwable $e) {
            set_flash('error', 'Error saving SMTP credentials: ' . $e->getMessage());
        }
    }

    if ($action === 'send_test_email') {
        $recipient = trim($_POST['test_recipient'] ?? '');
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'Please specify a valid test email address.');
        } else {
            try {
                $mailer = new Mailer();
                $testResult = $mailer->sendTestEmail($recipient);
                if ($testResult['success']) {
                    set_flash('success', "Diagnostic email transmitted to {$recipient} via {$testResult['method']}.");
                } else {
                    set_flash('error', "Diagnostic email failed: " . ($testResult['message'] ?? 'Unknown error'));
                }
            } catch (\Throwable $e) {
                set_flash('error', 'Test email exception: ' . $e->getMessage());
            }
        }
    }
}

// Fetch Current Settings
$current = $settingModel->getByGroup('smtp');

render_admin_layout_start(
    'Email & SMTP Notifications',
    'settings',
    'smtp',
    [['label' => 'Settings', 'url' => 'settings-general.php'], ['label' => 'Email & SMTP', 'url' => '']]
);
?>

<div class="max-w-4xl space-y-10">

    <!-- 1. SMTP Credentials Form -->
    <form method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_smtp">

        <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-sand pb-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Mail Infrastructure</span>
                    <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">cPanel SMTP Gateway</h2>
                    <p class="text-xs text-stone mt-1">Direct SSL/TLS socket credentials for delivering client consultation dossiers.</p>
                </div>

                <!-- Auto-reply Toggle -->
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-charcoal hidden sm:inline">Client Auto-reply:</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_client_autoreply" value="1" <?= ($current['enable_client_autoreply'] ?? '1') === '1' ? 'checked' : '' ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-sand peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-bronze"></div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- SMTP Host -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">SMTP Host / Server</label>
                    <input type="text" name="smtp_host" value="<?= htmlspecialchars($current['smtp_host'] ?? 'mail.interior.ritiverse.in') ?>" required
                           placeholder="mail.interior.ritiverse.in"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                    <p class="text-[11px] text-stone mt-1">e.g. mail.interior.ritiverse.in or smtp.gmail.com</p>
                </div>

                <!-- SMTP Port & Encryption -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Port</label>
                        <select name="smtp_port" class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                            <option value="465" <?= ($current['smtp_port'] ?? '465') === '465' ? 'selected' : '' ?>>465 (SSL)</option>
                            <option value="587" <?= ($current['smtp_port'] ?? '465') === '587' ? 'selected' : '' ?>>587 (TLS)</option>
                            <option value="25" <?= ($current['smtp_port'] ?? '465') === '25' ? 'selected' : '' ?>>25 (Plain)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Encryption</label>
                        <select name="smtp_encryption" class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                            <option value="ssl" <?= ($current['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="tls" <?= ($current['smtp_encryption'] ?? 'ssl') === 'tls' ? 'selected' : '' ?>>STARTTLS</option>
                            <option value="none" <?= ($current['smtp_encryption'] ?? 'ssl') === 'none' ? 'selected' : '' ?>>None</option>
                        </select>
                    </div>
                </div>

                <!-- SMTP Username -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">SMTP Username / Email Account</label>
                    <input type="text" name="smtp_username" value="<?= htmlspecialchars($current['smtp_username'] ?? 'concierge@interior.ritiverse.in') ?>" required
                           placeholder="concierge@interior.ritiverse.in"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                </div>

                <!-- SMTP Password -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">SMTP Password</label>
                    <input type="password" name="smtp_password" placeholder="••••••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                    <p class="text-[11px] text-stone mt-1">Leave empty to keep existing encrypted password.</p>
                </div>

                <!-- Admin Alert Email Recipient -->
                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Studio Concierge Notification Email</label>
                    <input type="email" name="admin_alert_email" value="<?= htmlspecialchars($current['admin_alert_email'] ?? 'concierge@interior.ritiverse.in') ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                    <p class="text-[11px] text-stone mt-1">Destination mailbox where lead inquiries and system alerts are received.</p>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold">
                    Save SMTP Configuration
                </button>
            </div>
        </div>
    </form>

    <!-- 2. Diagnostic Live Test Mailer -->
    <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
        <div class="border-b border-sand pb-4">
            <span class="text-[10px] tracking-[0.24em] uppercase text-emerald-700 font-semibold">Live Diagnostic</span>
            <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Verify SMTP Dispatch</h2>
            <p class="text-xs text-stone mt-1">Dispatch a test email instantly to verify socket handshake, SSL certificates, and cPanel delivery.</p>
        </div>

        <form method="POST" class="flex flex-col sm:flex-row gap-4 items-center">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="send_test_email">

            <div class="w-full sm:flex-1">
                <input type="email" name="test_recipient" placeholder="your-email@gmail.com" required
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
            </div>

            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white transition-colors text-xs uppercase tracking-wider font-semibold flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                <span>Send Test Email</span>
            </button>
        </form>

        <?php if ($testResult): ?>
            <div class="p-4 rounded-xl border <?= $testResult['success'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' ?> text-xs space-y-1">
                <p class="font-semibold uppercase tracking-wider">Diagnostic Result: <?= $testResult['success'] ? 'DELIVERY CONFIRMED' : 'DELIVERY FAILED' ?></p>
                <p><?= htmlspecialchars($testResult['message']) ?></p>
                <?php if (!empty($testResult['error'])): ?>
                    <p class="font-mono text-[11px] opacity-80 mt-1"><?= htmlspecialchars($testResult['error']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php render_admin_layout_end(); ?>
