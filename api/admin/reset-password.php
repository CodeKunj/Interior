<?php
/**
 * Admin Password Reset Completion
 * Validates 1-hour secure reset token and updates password hash.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap.php';

use Atelier\Models\AdminUser;

$userModel = new AdminUser();
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$user = !empty($token) ? $userModel->findByResetToken($token) : null;
$error = null;
$success = false;

if (!$user) {
    $error = "This password reset token is either invalid or has expired. Please request a new link.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (strlen($newPass) < 8) {
        $error = "New password must be at least 8 characters.";
    } elseif ($newPass !== $confirmPass) {
        $error = "Passwords do not match.";
    } else {
        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        if ($userModel->resetPasswordWithToken($token, $newHash)) {
            $success = true;
        } else {
            $error = "Failed to update password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | RiTiVerse Atelier CMS</title>
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
            <span class="font-serif text-3xl tracking-[0.25em] uppercase font-light text-charcoal block">RiTiVerse</span>
            <span class="text-[10px] tracking-[0.3em] uppercase text-bronze font-semibold block">Atelier Vérité &bull; CMS</span>
            <h1 class="text-lg font-serif text-charcoal pt-2">Set New Password</h1>
        </div>

        <?php if ($success): ?>
            <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-3 text-center">
                <p class="font-semibold text-sm">Password Updated Successfully</p>
                <p>Your master administrative credentials have been updated.</p>
                <div class="pt-2">
                    <a href="index.php" class="inline-block px-6 py-2.5 rounded-xl bg-emerald-800 text-white font-semibold uppercase tracking-wider text-[11px]">
                        Sign In Now &rarr;
                    </a>
                </div>
            </div>
        <?php else: ?>

            <?php if ($error): ?>
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($user): ?>
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">New Password (Min 8 chars)</label>
                        <input type="password" name="new_password" required minlength="8"
                               class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/40 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Confirm New Password</label>
                        <input type="password" name="confirm_password" required minlength="8"
                               class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/40 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.2em] font-semibold shadow-md">
                        Save New Password
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center pt-2">
                    <a href="forgot-password.php" class="text-xs text-bronze hover:underline font-semibold">
                        Request a new password reset link &rarr;
                    </a>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <div class="text-center pt-2 border-t border-sand">
            <a href="index.php" class="text-xs text-stone hover:text-charcoal font-medium transition-colors">
                &larr; Return to Sign In
            </a>
        </div>

    </div>

</body>
</html>
