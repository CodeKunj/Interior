<?php
/**
 * Admin Authentication & Master Left Sidebar Layout Shell
 * Atelier Vérité — Calacatta Marble Luxury CMS
 * Native PHP 8+ with Tailwind CSS CDN, Cormorant Garamond, and Plus Jakarta Sans.
 * Independent dual-scroll architecture (Sidebar scrollbar + Right Workspace scrollbar).
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap.php';

use Atelier\Config\Database;
use Atelier\Models\Setting;

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
 * Generate or retrieve CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render hidden CSRF token input
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validate incoming CSRF token
 */
function verify_csrf_token(?string $token = null): bool {
    $token = $token ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!$token || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash alert message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message,
    ];
}

/**
 * Render flash alert banner if present
 */
function render_flash(): void {
    if (empty($_SESSION['flash'])) {
        return;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    $type = $flash['type'] ?? 'info';
    $msg = htmlspecialchars($flash['message'] ?? '');

    $styles = [
        'success' => 'bg-emerald-50/90 border-emerald-200 text-emerald-900',
        'error'   => 'bg-rose-50/90 border-rose-200 text-rose-900',
        'warning' => 'bg-amber-50/90 border-amber-200 text-amber-900',
        'info'    => 'bg-stone-50 border-sand text-charcoal',
    ];

    $badge = [
        'success' => 'SUCCESS',
        'error'   => 'ATTENTION',
        'warning' => 'NOTICE',
        'info'    => 'INFO',
    ];

    $style = $styles[$type] ?? $styles['info'];
    $tag = $badge[$type] ?? 'NOTE';
    ?>
    <div class="mb-6 p-4 rounded-xl border <?= $style ?> backdrop-blur-sm shadow-sm flex items-start justify-between gap-4 transition-all">
        <div class="flex items-center gap-3">
            <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full bg-white/70 border border-current">
                <?= $tag ?>
            </span>
            <span class="text-xs font-medium leading-relaxed"><?= $msg ?></span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-stone/60 hover:text-charcoal text-sm leading-none">&times;</button>
    </div>
    <?php
}

/**
 * Validate and save uploaded media files with strict MIME verification
 */
function handle_image_upload(string $inputName, string $targetSubDir = 'brand', string $prefix = 'img'): ?string {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$inputName];

    // Max 10MB
    if ($file['size'] > 10 * 1024 * 1024) {
        throw new \Exception("Uploaded file exceeds 10MB size limit.");
    }

    // Strict MIME verification using PHP finfo
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'image/x-icon'  => 'ico',
        'image/vnd.microsoft.icon' => 'ico'
    ];

    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!isset($allowedMimes[$mime])) {
        throw new \Exception("Invalid image file format ({$mime}). Allowed: JPG, PNG, WEBP, SVG, ICO.");
    }

    $ext = $allowedMimes[$mime];
    $cleanPrefix = preg_replace('/[^a-zA-Z0-9_\-]/', '', $prefix);
    $filename = $cleanPrefix . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    // Resolve storage directory relative to public document root
    $targetDir = __DIR__ . '/../../uploads/' . trim($targetSubDir, '/');
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new \Exception("Failed to initialize target upload directory.");
        }
    }

    $destination = $targetDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new \Exception("Failed to persist uploaded image file.");
    }

    // Return root-relative URL for public web and frontend API consumption
    return '/uploads/' . trim($targetSubDir, '/') . '/' . $filename;
}

/**
 * Render Master Left Sidebar Layout Start
 */
function render_admin_layout_start(
    string $title,
    string $activeNav = 'dashboard',
    string $activeSubNav = '',
    array $breadcrumbs = []
): void {
    require_admin_auth();
    $username = $_SESSION['atelier_admin'] ?? 'Admin';

    // Retrieve studio name for sidebar branding
    $studioName = 'RiTiVerse';
    try {
        $settingModel = new Setting();
        $studioName = $settingModel->get('studio_name', 'RiTiVerse');
    } catch (\Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | <?= htmlspecialchars($studioName) ?> Studio CMS</title>
    
    <!-- Editorial Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        marble: '#FAF9F5',
                        alabaster: '#F7F5F0',
                        linen: '#EFECE6',
                        linenLight: '#F5F3EE',
                        sand: '#E2DDD5',
                        sandDark: '#CFC8BD',
                        charcoal: '#1C1B19',
                        charcoalLight: '#2D2B28',
                        stone: '#6E6A63',
                        stoneLight: '#8C877E',
                        bronze: '#8C6D53',
                        bronzeLight: '#F3EDE6',
                        bronzeDark: '#6D533D'
                    },
                    fontFamily: {
                        serif: ['"Cormorant Garamond"', '"Bodoni Moda"', 'Georgia', 'serif'],
                        display: ['"Bodoni Moda"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        /* Strict Window Viewport Locking */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevents global outer window scroll */
        }
        body {
            background-color: #F7F5F0;
            color: #1C1B19;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* 1. Dedicated Left Sidebar Dark Custom Scrollbar */
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #383531 transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: #383531;
            border-radius: 9999px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #8C6D53;
        }

        /* 2. Dedicated Right Workspace Light Custom Scrollbar */
        .main-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #D3CDC1 transparent;
        }
        .main-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .main-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .main-scrollbar::-webkit-scrollbar-thumb {
            background: #D3CDC1;
            border-radius: 9999px;
        }
        .main-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #8C6D53;
        }
    </style>
</head>
<body class="h-full w-full bg-alabaster text-charcoal flex flex-col antialiased overflow-hidden">

    <!-- Mobile Top Navigation Header (hidden on desktop lg) -->
    <div class="lg:hidden flex-shrink-0 bg-white/95 backdrop-blur-md border-b border-sand px-5 py-3.5 flex items-center justify-between z-40">
        <div class="flex items-center gap-3">
            <button type="button" id="sidebar-toggle-btn" class="p-2 -ml-2 rounded-lg text-charcoal hover:bg-linen transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <span class="font-serif text-lg tracking-[0.2em] font-medium uppercase text-charcoal"><?= htmlspecialchars($studioName) ?></span>
        </div>
        <div class="flex items-center gap-2">
            <a href="/" target="_blank" class="text-[10px] tracking-wider uppercase font-semibold text-stone hover:text-bronze px-2.5 py-1 rounded-full border border-sand">
                Live Studio &nearr;
            </a>
            <a href="index.php?logout=1" class="text-[10px] tracking-wider uppercase font-semibold text-rose-600 px-2 py-1">
                Out
            </a>
        </div>
    </div>

    <!-- Main Admin Container: Fills 100% of remaining viewport height -->
    <div class="flex-1 flex w-full h-full min-h-0 overflow-hidden relative">

        <!-- ==================================================================== -->
        <!-- LUXURY LEFT SIDEBAR: Pinned height with own independent scrollbar -->
        <!-- ==================================================================== -->
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 h-full flex-shrink-0 bg-[#1C1B19] text-[#FAF9F5] flex flex-col transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-auto shadow-2xl lg:shadow-none border-r border-[#2C2A26]">
            
            <!-- Studio Brand Header (Pinned at top of sidebar) -->
            <div class="flex-shrink-0 p-6 border-b border-white/10 flex items-center justify-between">
                <div>
                    <a href="index.php" class="block group">
                        <span class="font-serif text-2xl tracking-[0.28em] font-light text-white block uppercase group-hover:text-amber-200/90 transition-colors">
                            <?= htmlspecialchars($studioName) ?>
                        </span>
                        <span class="text-[9px] tracking-[0.32em] uppercase text-amber-200/70 font-semibold block mt-0.5">
                            Atelier Vérité &bull; CMS
                        </span>
                    </a>
                </div>
                <!-- Close button for mobile -->
                <button type="button" id="sidebar-close-btn" class="lg:hidden text-white/60 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links Scroll Area: SCROLLS INDEPENDENTLY -->
            <div class="flex-1 overflow-y-auto min-h-0 px-4 py-6 space-y-6 sidebar-scrollbar">

                <!-- Primary Operational Links -->
                <div class="space-y-1">
                    <div class="px-3 pb-2 text-[10px] uppercase tracking-[0.24em] font-semibold text-white/40">
                        Sanctuary Ops
                    </div>

                    <!-- Dashboard -->
                    <a href="index.php" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'dashboard' ? 'bg-[#8C6D53] text-white shadow-lg shadow-black/30' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Projects & 3D Tours -->
                    <a href="projects-manage.php" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'projects' ? 'bg-[#8C6D53] text-white shadow-lg shadow-black/30' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Projects & 3D Tours</span>
                    </a>

                    <!-- Consultation Leads -->
                    <a href="leads.php" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'leads' ? 'bg-[#8C6D53] text-white shadow-lg shadow-black/30' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <div class="flex items-center gap-3.5">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>Consultation Leads</span>
                        </div>
                    </a>

                    <!-- Global SEO & Analytics -->
                    <a href="seo-manage.php" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'seo' ? 'bg-[#8C6D53] text-white shadow-lg shadow-black/30' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        <span>SEO & Analytics</span>
                    </a>
                </div>

                <!-- Studio Settings Hub -->
                <div class="space-y-1">
                    <div class="px-3 pb-2 text-[10px] uppercase tracking-[0.24em] font-semibold text-white/40">
                        Studio Configuration
                    </div>

                    <!-- General & Brand -->
                    <a href="settings-general.php" class="flex items-center gap-3.5 px-3 py-2 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'settings' && $activeSubNav === 'general' ? 'bg-white/10 text-amber-200 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $activeNav === 'settings' && $activeSubNav === 'general' ? 'bg-amber-300' : 'bg-white/30' ?>"></span>
                        <span>General & Identity</span>
                    </a>

                    <!-- Contact & WhatsApp -->
                    <a href="settings-contact.php" class="flex items-center gap-3.5 px-3 py-2 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'settings' && $activeSubNav === 'contact' ? 'bg-white/10 text-amber-200 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $activeNav === 'settings' && $activeSubNav === 'contact' ? 'bg-amber-300' : 'bg-white/30' ?>"></span>
                        <span>Contact & WhatsApp</span>
                    </a>

                    <!-- Social & Press / Awards -->
                    <a href="settings-social.php" class="flex items-center gap-3.5 px-3 py-2 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'settings' && $activeSubNav === 'social' ? 'bg-white/10 text-amber-200 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $activeNav === 'settings' && $activeSubNav === 'social' ? 'bg-amber-300' : 'bg-white/30' ?>"></span>
                        <span>Social & Press Awards</span>
                    </a>

                    <!-- Email & SMTP Alerts -->
                    <a href="settings-smtp.php" class="flex items-center gap-3.5 px-3 py-2 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'settings' && $activeSubNav === 'smtp' ? 'bg-white/10 text-amber-200 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $activeNav === 'settings' && $activeSubNav === 'smtp' ? 'bg-amber-300' : 'bg-white/30' ?>"></span>
                        <span>Email & SMTP Notifications</span>
                    </a>
                </div>

                <!-- Account & Security -->
                <div class="space-y-1">
                    <div class="px-3 pb-2 text-[10px] uppercase tracking-[0.24em] font-semibold text-white/40">
                        Admin Privileges
                    </div>

                    <a href="profile.php" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeNav === 'profile' ? 'bg-[#8C6D53] text-white shadow-lg shadow-black/30' : 'text-white/70 hover:text-white hover:bg-white/5' ?>">
                        <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Account & Security</span>
                    </a>
                </div>

            </div>

            <!-- Sidebar Bottom User Dock (Pinned at bottom of sidebar) -->
            <div class="flex-shrink-0 p-4 border-t border-white/10 bg-black/20 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-[#8C6D53] text-white flex items-center justify-center font-serif text-xs font-bold flex-shrink-0">
                            <?= strtoupper(substr($username, 0, 1)) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($username) ?></p>
                            <p class="text-[10px] text-white/40 truncate">Studio Principal</p>
                        </div>
                    </div>
                    <a href="index.php?logout=1" title="Sign Out" class="text-white/40 hover:text-rose-400 p-1.5 rounded-lg hover:bg-white/5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                </div>

                <a href="/" target="_blank" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white/5 hover:bg-white/10 text-[11px] font-medium text-white/80 transition-colors border border-white/10">
                    <span>View Public Sanctuary</span>
                    <span class="text-amber-300">&nearr;</span>
                </a>
            </div>

        </aside>

        <!-- Overlay backdrop for mobile sidebar -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"></div>

        <!-- ==================================================================== -->
        <!-- RIGHT MAIN WORKSPACE: Pinned height with own independent scrollbar -->
        <!-- ==================================================================== -->
        <main class="flex-1 flex flex-col min-w-0 h-full min-h-0 overflow-hidden bg-alabaster">

            <!-- Desktop Topbar with Breadcrumbs (Pinned at top of right panel) -->
            <header class="flex-shrink-0 bg-white/80 backdrop-blur-md border-b border-sand px-6 sm:px-8 py-4 z-20 flex items-center justify-between">
                <div>
                    <!-- Breadcrumbs -->
                    <div class="flex items-center gap-2 text-[11px] tracking-wider uppercase font-medium text-stone">
                        <a href="index.php" class="hover:text-charcoal transition-colors">CMS</a>
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <span>&rsaquo;</span>
                            <?php if (!empty($crumb['url'])): ?>
                                <a href="<?= htmlspecialchars($crumb['url']) ?>" class="hover:text-charcoal transition-colors"><?= htmlspecialchars($crumb['label']) ?></a>
                            <?php else: ?>
                                <span class="text-charcoal font-semibold"><?= htmlspecialchars($crumb['label']) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <h1 class="font-serif text-2xl sm:text-3xl text-charcoal font-medium mt-0.5 tracking-tight">
                        <?= htmlspecialchars($title) ?>
                    </h1>
                </div>

                <div class="hidden sm:flex items-center gap-3">
                    <span class="text-[11px] text-stone">PHP 8+ Production Core</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
            </header>

            <!-- Page Workspace Wrapper: SCROLLS INDEPENDENTLY WITH RIGHT SCROLLBAR -->
            <div class="flex-1 overflow-y-auto min-h-0 main-scrollbar flex flex-col">
                <div class="p-6 sm:p-8 flex-1 max-w-7xl w-full mx-auto">
                    <?php render_flash(); ?>
<?php
}

/**
 * Render Master Left Sidebar Layout End
 */
function render_admin_layout_end(): void {
?>
                </div><!-- /Max-w container -->

                <!-- Minimal Footer at bottom of scrollable content -->
                <footer class="border-t border-sand py-5 px-8 text-center sm:text-left text-[11px] text-stone bg-white/50 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto">
                    <p>RiTiVerse &bull; ATELIER VÉRITÉ ARCHITECTURAL SANCTUARIES &bull; cPanel Native Engine</p>
                    <p class="text-[10px] text-stone/80">Tailwind CSS &bull; PDO Secure &bull; WebP Optimizer</p>
                </footer>

            </div><!-- /Workspace Scrollable Area -->

        </main>
    </div>

    <!-- Mobile Drawer Interactivity -->
    <script>
        const sidebar = document.getElementById('admin-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const openBtn = document.getElementById('sidebar-toggle-btn');
        const closeBtn = document.getElementById('sidebar-close-btn');

        function openSidebar() {
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }

        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
    </script>
</body>
</html>
<?php
}
