<?php
/**
 * Atelier Vérité CMS — Executive Studio Dashboard & Login
 * Calacatta Marble Luxury Aesthetic
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Config\Database;
use Atelier\Models\Project;
use Atelier\Models\Inquiry;

$pdo = null;
$dbError = null;
$projectModel = null;
$inquiryModel = null;

try {
    $pdo = Database::getInstance()->getConnection();
    $projectModel = new Project();
    $inquiryModel = new Inquiry();
} catch (\Throwable $e) {
    $dbError = $e->getMessage();
}

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['atelier_admin']);
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle Login
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $authenticated = false;
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                $authenticated = true;
            }
        } catch (\Throwable $e) {}
    }

    // Fallback seed credential from environment configuration
    $appConfig = require __DIR__ . '/../config/app.php';
    $defUser = $appConfig['admin']['default_username'] ?? 'admin';
    $defPass = $appConfig['admin']['default_password'] ?? 'atelier2026Password!';

    if (!$authenticated && $username === $defUser && $password === $defPass) {
        $authenticated = true;
    }

    if ($authenticated) {
        $_SESSION['atelier_admin'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $loginError = 'Invalid credentials. Default: admin / atelier2026Password!';
    }
}

$isLoggedIn = !empty($_SESSION['atelier_admin']);

// If logged in, fetch dashboard statistics & recent records
$stats = [
    'total_projects' => 0,
    'featured_projects' => 0,
    'projects_with_3d' => 0,
    'total_leads' => 0,
    'new_leads' => 0,
    'contacted_leads' => 0,
    'scheduled_leads' => 0
];
$recentProjects = [];
$recentLeads = [];

if ($isLoggedIn && $projectModel && $inquiryModel) {
    try {
        $allProjects = $projectModel->getAll();
        $stats['total_projects'] = count($allProjects);
        foreach ($allProjects as $p) {
            if (!empty($p['featured'])) $stats['featured_projects']++;
            if (!empty($p['tour_3d_url'])) $stats['projects_with_3d']++;
        }
        $recentProjects = array_slice($allProjects, 0, 4);

        $inquiryCounts = $inquiryModel->getCountsByStatus();
        $stats['total_leads'] = $inquiryCounts['total'];
        $stats['new_leads'] = $inquiryCounts['new'];
        $stats['contacted_leads'] = $inquiryCounts['contacted'];
        $stats['scheduled_leads'] = $inquiryCounts['scheduled'];

        $recentLeads = $inquiryModel->getAll('', 6);
    } catch (\Throwable $e) {
        $dbError = $e->getMessage();
    }
}

render_admin_head($isLoggedIn ? 'Executive Dashboard' : 'Studio Login', 'dashboard');
?>

<?php if (!$isLoggedIn): ?>
    <!-- 1. LOGIN SCREEN -->
    <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 py-12">
        <div class="w-full max-w-md bg-white border border-sand p-8 sm:p-10 rounded-3xl shadow-[0_12px_40px_rgba(28,27,25,0.06)]">
            <div class="text-center mb-8">
                <span class="text-[10px] uppercase tracking-[0.3em] text-accent font-semibold block mb-2">Studio Authentication</span>
                <h1 class="font-serif text-3xl text-charcoal">AURION &bull; ATELIER VÉRITÉ</h1>
                <p class="text-stone text-xs mt-2 font-light">Enter studio credentials to manage architectural projects, 3D digital twins, and consultation leads.</p>
            </div>

            <?php if ($dbError): ?>
                <div class="bg-amber-50 border border-amber-200 text-amber-900 p-3.5 text-xs mb-6 rounded-xl leading-relaxed">
                    <strong>Database Offline:</strong> <?= htmlspecialchars($dbError) ?><br>
                    <span class="text-[11px] text-stone mt-1 block">You can still log in using default seed credentials. Import <code class="bg-amber-100 px-1 py-0.5 rounded">api/schema.sql</code> via phpMyAdmin to enable database features.</span>
                </div>
            <?php endif; ?>

            <?php if ($loginError): ?>
                <div class="text-red-700 bg-red-50 p-3.5 text-xs mb-6 rounded-xl border border-red-200">
                    <?= htmlspecialchars($loginError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <input type="hidden" name="action" value="login">
                <div>
                    <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Username</label>
                    <input type="text" name="username" value="admin" required class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent transition-colors">
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Password</label>
                    <input type="password" name="password" value="atelier2026Password!" required class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent transition-colors">
                </div>
                <button type="submit" class="w-full bg-charcoal text-white hover:bg-accent transition-colors py-3.5 rounded-full text-xs uppercase tracking-[0.2em] font-semibold mt-2 shadow-md">
                    Enter Atelier CMS &rarr;
                </button>
                <div class="pt-4 border-t border-sand text-center">
                    <p class="text-[11px] text-stone">
                        Default seed: <code class="bg-marble px-1.5 py-0.5 rounded text-accent font-mono">admin</code> / <code class="bg-marble px-1.5 py-0.5 rounded text-accent font-mono">atelier2026Password!</code>
                    </p>
                </div>
            </form>
        </div>
        <p class="mt-8 text-stone text-[11px]">
            &larr; <a href="/" class="hover:text-charcoal underline">Return to public website</a>
        </p>
    </div>

<?php else: ?>
    <!-- 2. EXECUTIVE DASHBOARD -->
    <?php render_admin_nav('dashboard'); ?>

    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full space-y-10">

        <!-- Welcome Banner & Quick Action Shortcuts -->
        <div class="bg-white border border-sand rounded-3xl p-8 sm:p-10 shadow-[0_4px_25px_rgba(28,27,25,0.03)] flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <span class="text-[10px] uppercase tracking-[0.28em] text-accent font-semibold block mb-1">Executive Studio Suite</span>
                <h1 class="font-serif text-3xl sm:text-4xl text-charcoal">Studio Atelier Vérité</h1>
                <p class="text-stone text-xs sm:text-sm font-light mt-1 max-w-xl">
                    Manage portfolio case studies, configure Matterport / Sketchfab 3D virtual tour players, optimize photography to WebP, and review client consultation dossiers.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="projects-manage.php?action=new" class="inline-flex items-center space-x-2 bg-charcoal text-white hover:bg-accent px-6 py-3 rounded-full text-xs uppercase tracking-[0.18em] font-semibold transition-all shadow-md">
                    <span>+ New Sanctuary</span>
                </a>
                <a href="leads.php?export=csv" class="inline-flex items-center space-x-2 bg-white text-charcoal hover:bg-linen border border-sand px-5 py-3 rounded-full text-xs uppercase tracking-[0.16em] font-medium transition-colors shadow-sm">
                    <span>Export Leads (CSV)</span>
                </a>
            </div>
        </div>

        <?php if ($dbError): ?>
            <div class="bg-amber-50 border border-amber-200 text-amber-900 p-5 rounded-2xl text-xs flex items-start space-x-3">
                <span class="text-amber-600 font-bold text-base">&bull;</span>
                <div>
                    <strong class="font-semibold text-sm block mb-1">Database Setup Notice</strong>
                    <?= htmlspecialchars($dbError) ?><br>
                    <span class="text-stone mt-1 block">Import <code class="bg-amber-100 text-amber-900 px-1.5 py-0.5 rounded font-mono">/api/schema.sql</code> into phpMyAdmin and update credentials in <code class="bg-amber-100 text-amber-900 px-1.5 py-0.5 rounded font-mono">/api/config/app.php</code>.</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- 4 KPI Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Projects -->
            <div class="bg-white border border-sand rounded-3xl p-6 shadow-[0_4px_20px_rgba(28,27,25,0.02)]">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">Sanctuaries</span>
                    <span class="w-2 h-2 rounded-full bg-accent"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['total_projects'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span><?= $stats['featured_projects'] ?> Featured on Home</span>
                    <a href="projects-manage.php" class="text-accent hover:underline font-medium">Manage &rarr;</a>
                </div>
            </div>

            <!-- Card 2: 3D Digital Twins -->
            <div class="bg-white border border-sand rounded-3xl p-6 shadow-[0_4px_20px_rgba(28,27,25,0.02)]">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">3D Virtual Tours</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['projects_with_3d'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span>Matterport / Kuula / Sketchfab</span>
                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full text-[9px] font-semibold border border-emerald-200">Active</span>
                </div>
            </div>

            <!-- Card 3: Total Client Inquiries -->
            <div class="bg-white border border-sand rounded-3xl p-6 shadow-[0_4px_20px_rgba(28,27,25,0.02)]">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">Client Dossiers</span>
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['total_leads'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span><?= $stats['scheduled_leads'] ?> Consultations booked</span>
                    <a href="leads.php" class="text-accent hover:underline font-medium">View &rarr;</a>
                </div>
            </div>

            <!-- Card 4: New Unreviewed Inquiries -->
            <div class="bg-white border border-sand rounded-3xl p-6 shadow-[0_4px_20px_rgba(28,27,25,0.02)]">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">Pending Follow-ups</span>
                    <span class="w-2 h-2 rounded-full <?= $stats['new_leads'] > 0 ? 'bg-amber-500 animate-pulse' : 'bg-stone' ?>"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['new_leads'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span>Requires initial concierge contact</span>
                    <?php if ($stats['new_leads'] > 0): ?>
                        <span class="bg-amber-100 text-amber-900 font-semibold px-2 py-0.5 rounded-full text-[9px]">Action Needed</span>
                    <?php else: ?>
                        <span class="text-emerald-700 font-medium">All caught up</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Two-Column Section: Recent Inquiries (Left) & Projects Spotlight (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Recent Consultation Inquiries -->
            <div class="lg:col-span-2 bg-white border border-sand rounded-3xl p-6 sm:p-8 shadow-[0_4px_25px_rgba(28,27,25,0.03)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-sand">
                        <div>
                            <h2 class="font-serif text-2xl text-charcoal">Recent Consultation Inquiries</h2>
                            <p class="text-stone text-xs font-light mt-0.5">Prospective residential and commercial clients requesting initial design meetings.</p>
                        </div>
                        <a href="leads.php" class="text-accent hover:text-charcoal text-xs uppercase tracking-[0.16em] font-semibold transition-colors">
                            View All Inquiries &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-stone text-[10px] uppercase tracking-[0.16em] bg-linen/60 rounded-xl">
                                <tr>
                                    <th class="p-3 pl-4">Client</th>
                                    <th class="p-3">Scope & Budget</th>
                                    <th class="p-3">Date</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3 pr-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sand/60">
                                <?php if (empty($recentLeads)): ?>
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-stone italic text-xs">
                                            No consultation requests recorded yet. Submissions through the /contact page will appear here.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentLeads as $lead): ?>
                                        <tr class="hover:bg-marble/60 transition-colors">
                                            <td class="p-3 pl-4 font-medium text-charcoal">
                                                <div class="font-semibold text-charcoal"><?= htmlspecialchars($lead['name']) ?></div>
                                                <div class="text-[11px] text-stone font-normal"><?= htmlspecialchars($lead['city'] ?: 'City not provided') ?></div>
                                            </td>
                                            <td class="p-3 text-stone">
                                                <span class="text-charcoal font-medium"><?= htmlspecialchars($lead['property_type']) ?></span><br>
                                                <span class="text-[10px]"><?= htmlspecialchars($lead['budget_range']) ?></span>
                                            </td>
                                            <td class="p-3 text-stone text-[11px] whitespace-nowrap">
                                                <?= date('M d, Y', strtotime($lead['created_at'])) ?>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] uppercase tracking-wider font-semibold 
                                                    <?= $lead['status'] === 'new' ? 'bg-amber-100 text-amber-900 border border-amber-300' : '' ?>
                                                    <?= $lead['status'] === 'contacted' ? 'bg-blue-100 text-blue-900 border border-blue-300' : '' ?>
                                                    <?= $lead['status'] === 'scheduled' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : '' ?>
                                                    <?= $lead['status'] === 'archived' ? 'bg-stone/10 text-stone border border-sand' : '' ?>
                                                ">
                                                    <?= strtoupper(htmlspecialchars($lead['status'])) ?>
                                                </span>
                                            </td>
                                            <td class="p-3 pr-4 text-right">
                                                <a href="leads.php?view=<?= $lead['id'] ?>" class="text-accent hover:underline font-medium text-[11px]">
                                                    Details &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-sand flex items-center justify-between text-xs text-stone">
                    <span>Showing latest <?= count($recentLeads) ?> of <?= $stats['total_leads'] ?> total client submissions</span>
                    <a href="leads.php" class="text-charcoal hover:text-accent font-medium">Browse All Leads &rarr;</a>
                </div>
            </div>

            <!-- Right: Studio Projects Quick Glance -->
            <div class="bg-white border border-sand rounded-3xl p-6 sm:p-8 shadow-[0_4px_25px_rgba(28,27,25,0.03)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-sand">
                        <div>
                            <h2 class="font-serif text-2xl text-charcoal">Curated Sanctuaries</h2>
                            <p class="text-stone text-xs font-light mt-0.5">Top architectural portfolio showcases.</p>
                        </div>
                        <a href="projects-manage.php" class="text-accent hover:text-charcoal text-xs uppercase tracking-[0.16em] font-semibold transition-colors">
                            Manage &rarr;
                        </a>
                    </div>

                    <div class="space-y-4">
                        <?php if (empty($recentProjects)): ?>
                            <p class="text-stone text-xs italic py-6 text-center">No projects published yet.</p>
                        <?php else: ?>
                            <?php foreach ($recentProjects as $p): ?>
                                <div class="flex items-center space-x-3.5 p-2.5 rounded-2xl hover:bg-marble transition-colors border border-transparent hover:border-sand">
                                    <img src="<?= htmlspecialchars($p['cover_image']) ?>" class="w-16 h-12 rounded-xl object-cover border border-sand shadow-sm flex-shrink-0" alt="" onerror="this.src='https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=300&q=80'">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-serif text-sm text-charcoal truncate"><?= htmlspecialchars($p['title']) ?></div>
                                        <div class="text-[10px] text-stone truncate"><?= htmlspecialchars($p['location']) ?> &bull; <?= htmlspecialchars($p['category']) ?></div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <?php if (!empty($p['tour_3d_url'])): ?>
                                                <span class="text-[8px] uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 font-semibold">
                                                    3D TOUR
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($p['featured'])): ?>
                                                <span class="text-[8px] uppercase tracking-wider text-accent bg-accentLight px-2 py-0.5 rounded-full font-semibold">
                                                    FEATURED
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="projects-manage.php?action=edit&id=<?= $p['id'] ?>" class="text-stone hover:text-charcoal text-xs px-2 py-1 rounded-lg hover:bg-linen font-medium">
                                        Edit
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-sand">
                    <a href="projects-manage.php?action=new" class="block w-full text-center bg-linen hover:bg-marble border border-sand py-2.5 rounded-xl text-xs uppercase tracking-[0.16em] font-semibold text-charcoal transition-colors">
                        + Add Another Sanctuary
                    </a>
                </div>
            </div>
        </div>

        <!-- Technical Integration Status Strip -->
        <div class="bg-linen/60 border border-sand rounded-3xl p-6 text-xs text-stone flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                <div>
                    <strong class="text-charcoal font-semibold block">cPanel Hosting Environment Active</strong>
                    <span>PHP 8+ PDO &bull; UTF-8mb4 Database &bull; WebP Image Auto-Conversion Enabled (imagewebp)</span>
                </div>
            </div>

            <div class="flex items-center space-x-4 text-[11px]">
                <a href="/work" target="_blank" class="text-charcoal hover:text-accent font-medium">Portfolio Front &nearr;</a>
                <span>&bull;</span>
                <a href="/contact" target="_blank" class="text-charcoal hover:text-accent font-medium">Consultation Form &nearr;</a>
                <span>&bull;</span>
                <a href="leads.php?export=csv" class="text-accent hover:underline font-semibold">Download Leads (.csv)</a>
            </div>
        </div>

    </main>

    <?php render_admin_footer(); ?>

<?php endif; ?>
