<?php
/**
 * Atelier Vérité CMS — Executive Studio Dashboard & Login
 * Boutique luxury aesthetic with Left Sidebar Navigation & Analytics
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
    $pdo = Database::getConnection();
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
?>

<?php if (!$isLoggedIn): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Sign In | RiTiVerse Atelier CMS</title>
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
    <div class="w-full max-w-md bg-white border border-sand p-8 sm:p-10 rounded-3xl shadow-[0_12px_40px_rgba(28,27,25,0.06)] space-y-8">
        <div class="text-center">
            <span class="text-[10px] uppercase tracking-[0.3em] text-bronze font-semibold block mb-2">Studio Authentication</span>
            <h1 class="font-serif text-3xl text-charcoal font-medium">RiTiVerse &bull; ATELIER VÉRITÉ</h1>
            <p class="text-stone text-xs mt-2 font-light leading-relaxed">Enter studio credentials to manage architectural sanctuaries, 3D digital twins, and consultation dossiers.</p>
        </div>

        <?php if ($loginError): ?>
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
                <?= htmlspecialchars($loginError) ?>
            </div>
        <?php endif; ?>

        <?php if ($dbError): ?>
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                Database Note: <?= htmlspecialchars($dbError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php" class="space-y-5">
            <input type="hidden" name="action" value="login">

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Username or Email</label>
                <input type="text" name="username" value="admin" required
                       class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/40 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal">Master Password</label>
                    <a href="forgot-password.php" class="text-[11px] text-bronze hover:underline font-medium">Forgot?</a>
                </div>
                <input type="password" name="password" value="atelier2026Password!" required
                       class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/40 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.2em] font-semibold shadow-md">
                Unlock Studio CMS
            </button>
        </form>

        <div class="text-center pt-2 border-t border-sand">
            <a href="/" target="_blank" class="text-xs text-stone hover:text-charcoal font-medium transition-colors">
                &larr; View Live Studio Website
            </a>
        </div>
    </div>
</body>
</html>

<?php else: ?>
<?php 
render_admin_layout_start(
    'Executive Studio Dashboard',
    'dashboard',
    '',
    [['label' => 'Dashboard', 'url' => '']]
); 
?>

    <div class="space-y-8">
        <!-- Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Card 1: Total Projects -->
            <div class="bg-white border border-sand rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">Sanctuaries</span>
                    <span class="w-2 h-2 rounded-full bg-bronze"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['total_projects'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span><?= $stats['featured_projects'] ?> Featured</span>
                    <a href="projects-manage.php" class="text-bronze hover:underline font-medium">Manage &rarr;</a>
                </div>
            </div>

            <!-- Card 2: 3D Digital Twins -->
            <div class="bg-white border border-sand rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">3D Virtual Tours</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['projects_with_3d'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span>Matterport / Kuula / 3D</span>
                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full text-[9px] font-semibold border border-emerald-200">Active</span>
                </div>
            </div>

            <!-- Card 3: Total Client Inquiries -->
            <div class="bg-white border border-sand rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">Consultations</span>
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['total_leads'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span><?= $stats['scheduled_leads'] ?> Scheduled</span>
                    <a href="leads.php" class="text-bronze hover:underline font-medium">View &rarr;</a>
                </div>
            </div>

            <!-- Card 4: New Unreviewed Inquiries -->
            <div class="bg-white border border-sand rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between text-stone text-xs mb-3">
                    <span class="uppercase tracking-[0.18em] text-[10px] font-semibold">Pending Follow-ups</span>
                    <span class="w-2 h-2 rounded-full <?= $stats['new_leads'] > 0 ? 'bg-amber-500 animate-pulse' : 'bg-stone' ?>"></span>
                </div>
                <div class="font-serif text-4xl text-charcoal"><?= $stats['new_leads'] ?></div>
                <div class="text-[11px] text-stone mt-2 flex items-center justify-between">
                    <span>Action Needed</span>
                    <?php if ($stats['new_leads'] > 0): ?>
                        <span class="bg-amber-100 text-amber-900 font-semibold px-2 py-0.5 rounded-full text-[9px]">Unread</span>
                    <?php else: ?>
                        <span class="text-emerald-700 font-medium">Up to date</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Two-Column Section: Recent Inquiries (Left) & Projects Spotlight (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Recent Consultation Inquiries -->
            <div class="lg:col-span-2 bg-white border border-sand rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-sand">
                        <div>
                            <h2 class="font-serif text-2xl text-charcoal font-medium">Recent Consultation Leads</h2>
                            <p class="text-stone text-xs font-light mt-0.5">Prospective residential and commercial clients requesting initial design dialogue.</p>
                        </div>
                        <a href="leads.php" class="text-bronze hover:text-charcoal text-xs uppercase tracking-[0.16em] font-semibold transition-colors">
                            All Leads &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-stone text-[10px] uppercase tracking-[0.16em] bg-alabaster/60 rounded-xl">
                                <tr>
                                    <th class="p-3 pl-4">Client</th>
                                    <th class="p-3">Scope &amp; Budget</th>
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
                                        <tr class="hover:bg-alabaster/40 transition-colors">
                                            <td class="p-3 pl-4 font-medium text-charcoal">
                                                <div class="font-semibold text-charcoal"><?= htmlspecialchars($lead['name']) ?></div>
                                                <div class="text-[11px] text-stone font-normal"><?= htmlspecialchars($lead['city'] ?: 'City not specified') ?></div>
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
                                                <a href="leads.php?view=<?= $lead['id'] ?>" class="text-bronze hover:underline font-medium text-[11px]">
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
                    <span>Showing latest <?= count($recentLeads) ?> of <?= $stats['total_leads'] ?> submissions</span>
                    <a href="leads.php" class="text-charcoal hover:text-bronze font-medium">Browse All Leads &rarr;</a>
                </div>
            </div>

            <!-- Right: Studio Projects Quick Glance -->
            <div class="bg-white border border-sand rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-sand">
                        <div>
                            <h2 class="font-serif text-2xl text-charcoal font-medium">Sanctuaries</h2>
                            <p class="text-stone text-xs font-light mt-0.5">Published architectural works.</p>
                        </div>
                        <a href="projects-manage.php" class="text-bronze hover:text-charcoal text-xs uppercase tracking-[0.16em] font-semibold transition-colors">
                            Manage &rarr;
                        </a>
                    </div>

                    <div class="space-y-4">
                        <?php if (empty($recentProjects)): ?>
                            <p class="text-stone text-xs italic py-6 text-center">No projects published yet.</p>
                        <?php else: ?>
                            <?php foreach ($recentProjects as $p): ?>
                                <div class="flex items-center space-x-3.5 p-2.5 rounded-xl hover:bg-alabaster/60 transition-colors border border-transparent hover:border-sand">
                                    <img src="<?= htmlspecialchars($p['cover_image']) ?>" class="w-16 h-12 rounded-lg object-cover border border-sand shadow-sm flex-shrink-0" alt="" onerror="this.src='https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=300&q=80'">
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
                                                <span class="text-[8px] uppercase tracking-wider text-bronze bg-bronzeLight px-2 py-0.5 rounded-full font-semibold">
                                                    FEATURED
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="projects-manage.php?action=edit&id=<?= $p['id'] ?>" class="text-stone hover:text-charcoal text-xs px-2 py-1 rounded-lg hover:bg-sand/40 font-medium">
                                        Edit
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-sand">
                    <a href="projects-manage.php?action=new" class="block w-full text-center bg-alabaster hover:bg-white border border-sand py-2.5 rounded-xl text-xs uppercase tracking-[0.16em] font-semibold text-charcoal transition-colors">
                        + Add Another Sanctuary
                    </a>
                </div>
            </div>
        </div>

        <!-- Technical Integration Status Strip -->
        <div class="bg-linen/60 border border-sand rounded-2xl p-6 text-xs text-stone flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                <div>
                    <strong class="text-charcoal font-semibold block">cPanel Hosting Environment Active</strong>
                    <span>PHP 8+ PDO &bull; UTF-8mb4 Database &bull; Matterport / Kuula / Sketchfab 3D Embed Engine</span>
                </div>
            </div>

            <div class="flex items-center space-x-4 text-[11px]">
                <a href="/work" target="_blank" class="text-charcoal hover:text-bronze font-medium">Portfolio Front &nearr;</a>
                <span>&bull;</span>
                <a href="/contact" target="_blank" class="text-charcoal hover:text-bronze font-medium">Consultation Form &nearr;</a>
                <span>&bull;</span>
                <a href="leads.php?export=csv" class="text-bronze hover:underline font-semibold">Download Leads (.csv)</a>
            </div>
        </div>
    </div>

<?php render_admin_layout_end(); ?>
<?php endif; ?>
