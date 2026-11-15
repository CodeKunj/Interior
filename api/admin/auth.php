<?php
/**
 * Admin Authentication & Shared Template Shell
 * Atelier Vérité — Calacatta Marble Luxury CMS
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap.php';

use Atelier\Config\Database;

/**
 * Enforce authentication: redirects to index.php if not signed in
 */
function require_admin_auth(): void {
    if (empty($_SESSION['atelier_admin'])) {
        header("Location: index.php");
        exit();
    }
}

/**
 * Render admin HTML head with luxury fonts & Tailwind configuration
 */
function render_admin_head(string $title, string $activeNav = ''): void {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | AURION Atelier CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        marble: '#FAF9F5',
                        linen: '#F5F3EE',
                        sand: '#EAE6DF',
                        sandDark: '#DCD6CC',
                        charcoal: '#1C1B19',
                        stone: '#6E6A63',
                        accent: '#8C6D53',
                        accentLight: '#F3EDE6'
                    },
                    fontFamily: {
                        serif: ['"Bodoni Moda"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #FAF9F5;
            color: #1C1B19;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-marble text-charcoal antialiased flex flex-col">
<?php
}

/**
 * Render luxury top header navigation bar
 */
function render_admin_nav(string $activeNav = ''): void {
    $username = $_SESSION['atelier_admin'] ?? 'Admin';
?>
    <header class="bg-white/95 backdrop-blur-md border-b border-sand sticky top-0 z-30 shadow-[0_2px_15px_rgba(28,27,25,0.03)]">
        <div class="max-w-7xl mx-auto px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-6 sm:space-x-8">
                <a href="index.php" class="flex items-center space-x-3 group">
                    <span class="font-serif text-2xl tracking-[0.24em] uppercase font-medium text-charcoal group-hover:text-accent transition-colors">
                        AURION
                    </span>
                    <span class="w-[1px] h-4 bg-sand"></span>
                    <span class="text-[9px] tracking-[0.28em] uppercase text-accent font-semibold px-2 py-0.5 rounded-full bg-accentLight border border-accent/20">
                        Atelier CMS
                    </span>
                </a>

                <!-- Nav links -->
                <nav class="hidden md:flex items-center space-x-6 text-xs font-medium tracking-[0.16em] uppercase">
                    <a href="index.php" class="transition-colors pb-1 border-b-2 <?= $activeNav === 'dashboard' ? 'border-accent text-charcoal font-semibold' : 'border-transparent text-stone hover:text-charcoal' ?>">
                        Dashboard
                    </a>
                    <a href="projects-manage.php" class="transition-colors pb-1 border-b-2 <?= $activeNav === 'projects' ? 'border-accent text-charcoal font-semibold' : 'border-transparent text-stone hover:text-charcoal' ?>">
                        Projects & 3D Tours
                    </a>
                    <a href="leads.php" class="transition-colors pb-1 border-b-2 <?= $activeNav === 'leads' ? 'border-accent text-charcoal font-semibold' : 'border-transparent text-stone hover:text-charcoal' ?>">
                        Inquiries & Leads
                    </a>
                </nav>
            </div>

            <!-- Right cluster -->
            <div class="flex items-center space-x-4 sm:space-x-6 text-xs">
                <a href="/" target="_blank" class="hidden sm:inline-flex items-center space-x-1.5 text-stone hover:text-charcoal transition-colors font-medium">
                    <span>Live Studio</span>
                    <span class="text-accent">&nearr;</span>
                </a>

                <span class="w-[1px] h-3.5 bg-sand hidden sm:inline-block"></span>

                <div class="flex items-center space-x-3">
                    <span class="text-stone text-[11px] hidden lg:inline">
                        Signed in as <strong class="text-charcoal font-semibold"><?= htmlspecialchars($username) ?></strong>
                    </span>
                    <a href="index.php?logout=1" class="text-stone hover:text-red-700 font-semibold tracking-wider uppercase text-[10px] sm:text-[11px] px-3 py-1 rounded-full border border-sand hover:border-red-200 transition-colors">
                        Sign Out
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile subnav -->
        <div class="md:hidden flex items-center justify-around border-t border-sand/80 px-4 py-2 bg-linen/50 text-[11px] tracking-wider uppercase font-medium">
            <a href="index.php" class="<?= $activeNav === 'dashboard' ? 'text-accent font-semibold' : 'text-stone' ?>">Dashboard</a>
            <a href="projects-manage.php" class="<?= $activeNav === 'projects' ? 'text-accent font-semibold' : 'text-stone' ?>">Projects</a>
            <a href="leads.php" class="<?= $activeNav === 'leads' ? 'text-accent font-semibold' : 'text-stone' ?>">Inquiries</a>
        </div>
    </header>
<?php
}

/**
 * Render admin footer
 */
function render_admin_footer(): void {
?>
    <footer class="mt-auto border-t border-sand py-8 text-center text-[11px] text-stone bg-white">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                AURION &bull; ATELIER VÉRITÉ ARCHITECTURE & LUXURY INTERIORS
            </div>
            <div class="text-[10px] text-stone/80">
                cPanel PHP 8+ CMS &bull; Matterport / Kuula / Sketchfab 3D Engine &bull; WebP Image Optimizer
            </div>
        </div>
    </footer>
</body>
</html>
<?php
}
