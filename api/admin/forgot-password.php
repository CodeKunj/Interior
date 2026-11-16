<?php
/**
 * Admin Forgot Password Flow
 * Generates a 1-hour secure cryptographic token and sends reset link via Mailer.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap.php';

use Atelier\Models\AdminUser;
use Atelier\Models\Setting;
use Atelier\Core\Mailer;

$userModel = new AdminUser();
$settingModel = new Setting();
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid administrator email address.";
    } else {
        $token = $userModel->createPasswordResetToken($email);
        if ($token) {
            // Determine host URL for reset link
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'interior.ritiverse.in';
            $resetUrl = "{$scheme}://{$host}/admin/reset-password.php?token=" . urlencode($token);

            try {
                $mailer = new Mailer();
                $mailer->sendPasswordReset($email, $resetUrl);
            } catch (\Throwable $e) {
                error_log("Failed to send reset email: " . $e->getMessage());
            }
        }

        // Always show the same confirmation message to prevent email enumeration
        $message = "If an administrator account matches '{$email}', a secure 1-hour recovery link has been dispatched to that inbox.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Credentials | AURION Atelier CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        alabaster: '#F7F5F0',
                        linen: '#EFECE6',
                        charcoal: '#1C1B19',
                        bronze: '#8C6D53',
                        sand: '#E2DDD5'
                    },
                    fontFamily: {
                        serif: ['"Bodoni Moda"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-alabaster text-charcoal flex items-center justify-center p-6 antialiased font-sans">

    <div class="w-full max-w-md bg-white rounded-3xl border border-sand p-8 sm:p-10 shadow-[0_10px_40px_rgba(28,27,25,0.06)] space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="font-serif text-3xl tracking-[0.25em] uppercase font-light text-charcoal block">AURION</span>
            <span class="text-[10px] tracking-[0.3em] uppercase text-bronze font-semibold block">Atelier Vérité &bull; CMS</span>
            <h1 class="text-lg font-serif text-charcoal pt-2">Recover Credentials</h1>
            <p class="text-xs text-stone">Enter your administrator email to receive a secure password reset link.</p>
        </div>

        <?php if ($message): ?>
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs leading-relaxed">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!$message): ?>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Account Email</label>
                    <input type="email" name="email" required placeholder="admin@interior.ritiverse.in"
                           class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/40 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.2em] font-semibold shadow-md">
                    Send Recovery Link
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center pt-2 border-t border-sand">
            <a href="index.php" class="text-xs text-stone hover:text-charcoal font-medium transition-colors">
                &larr; Return to Sign In
            </a>
        </div>

    </div>

</body>
</html>
