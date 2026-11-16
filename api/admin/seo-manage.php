<?php
/**
 * Module 4: Global SEO, OpenGraph & Analytics Hub
 * Manage per-route meta titles, descriptions, social share banners, GA4, Meta Pixel, and GSC tags.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Models\Setting;
use Atelier\Models\Seo;

require_admin_auth();

$settingModel = new Setting();
$seoModel = new Seo();

$routes = [
    'home'     => ['name' => 'Home Page', 'slug' => '/'],
    'work'     => ['name' => 'Work & Portfolio', 'slug' => '/work'],
    'services' => ['name' => 'Sanctuary Services', 'slug' => '/services'],
    'about'    => ['name' => 'About Atelier', 'slug' => '/about'],
    'contact'  => ['name' => 'Concierge & Contact', 'slug' => '/contact'],
];

$activeRoute = $_GET['route'] ?? 'home';
if (!isset($routes[$activeRoute])) {
    $activeRoute = 'home';
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'CSRF verification failed.');
        header("Location: seo-manage.php?route={$activeRoute}");
        exit();
    }

    $action = $_POST['action'] ?? 'save_route_seo';

    if ($action === 'save_route_seo') {
        try {
            $routeKey = trim($_POST['route_key'] ?? 'home');
            $metaTitle = trim($_POST['meta_title'] ?? '');
            $metaDesc = trim($_POST['meta_description'] ?? '');
            $metaKeywords = trim($_POST['meta_keywords'] ?? '');
            $ogImage = trim($_POST['og_image_current'] ?? '');

            // Handle optional new OpenGraph banner upload
            if (!empty($_FILES['og_image_file']['name'])) {
                $uploaded = handle_image_upload('og_image_file', 'brand', "og_{$routeKey}");
                if ($uploaded) {
                    $ogImage = $uploaded;
                }
            }

            $seoModel->save($routeKey, [
                'meta_title'       => $metaTitle,
                'meta_description' => $metaDesc,
                'meta_keywords'    => $metaKeywords,
                'og_image'         => $ogImage,
            ]);

            set_flash('success', "SEO metadata and OpenGraph tags for route '{$routeKey}' saved successfully.");
            header("Location: seo-manage.php?route={$routeKey}");
            exit();

        } catch (\Throwable $e) {
            set_flash('error', 'Error updating SEO: ' . $e->getMessage());
        }
    }

    if ($action === 'save_analytics') {
        try {
            $analytics = [
                'ga4_measurement_id'        => trim($_POST['ga4_measurement_id'] ?? ''),
                'meta_pixel_id'             => trim($_POST['meta_pixel_id'] ?? ''),
                'google_search_console_tag' => trim($_POST['google_search_console_tag'] ?? ''),
            ];

            $settingModel->setMany($analytics, 'seo');
            set_flash('success', 'Global analytics and verification tags saved.');
            header("Location: seo-manage.php?route={$activeRoute}");
            exit();

        } catch (\Throwable $e) {
            set_flash('error', 'Error saving analytics: ' . $e->getMessage());
        }
    }
}

// Fetch Current SEO & Analytics Settings
$currentRouteSeo = $seoModel->getByRoute($activeRoute) ?? [];
$analyticsCurrent = $settingModel->getByGroup('seo');

render_admin_layout_start(
    'SEO, OpenGraph & Analytics',
    'seo',
    '',
    [['label' => 'SEO & Analytics', 'url' => '']]
);
?>

<div class="max-w-5xl space-y-10">

    <!-- Route Selector Tabs -->
    <div class="flex items-center gap-2 border-b border-sand pb-3 overflow-x-auto">
        <span class="text-xs uppercase tracking-wider font-semibold text-stone mr-2">Routes:</span>
        <?php foreach ($routes as $k => $r): ?>
            <a href="seo-manage.php?route=<?= $k ?>"
               class="px-4 py-2 rounded-xl text-xs font-medium tracking-wide transition-all <?= $activeRoute === $k ? 'bg-charcoal text-white font-semibold shadow-sm' : 'bg-white border border-sand text-stone hover:text-charcoal hover:bg-alabaster' ?>">
                <?= htmlspecialchars($r['name']) ?> <span class="text-[10px] opacity-70 ml-1"><?= $r['slug'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- 1. Per-Route SEO Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Editor Column (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <form method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save_route_seo">
                <input type="hidden" name="route_key" value="<?= htmlspecialchars($activeRoute) ?>">
                <input type="hidden" name="og_image_current" value="<?= htmlspecialchars($currentRouteSeo['og_image'] ?? '') ?>">

                <div class="border-b border-sand pb-4">
                    <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Metadata Definition</span>
                    <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">SEO for <?= htmlspecialchars($routes[$activeRoute]['name']) ?></h2>
                    <p class="text-xs text-stone mt-1">Target URL: <code class="bg-alabaster px-1.5 py-0.5 rounded text-charcoal font-mono"><?= $routes[$activeRoute]['slug'] ?></code></p>
                </div>

                <!-- Meta Title -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Meta Title Tag</label>
                    <input type="text" name="meta_title" id="seo_title" maxlength="70"
                           value="<?= htmlspecialchars($currentRouteSeo['meta_title'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                    <div class="flex justify-between text-[11px] text-stone mt-1">
                        <span>Recommended: 50 – 60 characters.</span>
                        <span id="seo_title_count">0/70</span>
                    </div>
                </div>

                <!-- Meta Description -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Meta Description</label>
                    <textarea name="meta_description" id="seo_desc" rows="3" maxlength="160" required
                              class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all leading-relaxed"><?= htmlspecialchars($currentRouteSeo['meta_description'] ?? '') ?></textarea>
                    <div class="flex justify-between text-[11px] text-stone mt-1">
                        <span>Recommended: 120 – 160 characters.</span>
                        <span id="seo_desc_count">0/160</span>
                    </div>
                </div>

                <!-- Meta Keywords -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Focus Keywords (Comma-separated)</label>
                    <input type="text" name="meta_keywords"
                           value="<?= htmlspecialchars($currentRouteSeo['meta_keywords'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                    <p class="text-[11px] text-stone mt-1">e.g. luxury interior design, architectural sanctuaries, bespoke marble joinery</p>
                </div>

                <!-- OpenGraph Banner Upload -->
                <div class="border-t border-sand/60 pt-5 space-y-3">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal">OpenGraph Social Banner (1200 &times; 630)</label>
                    
                    <?php if (!empty($currentRouteSeo['og_image'])): ?>
                        <div class="relative rounded-xl border border-sand overflow-hidden max-w-sm aspect-[1.91/1] bg-charcoal">
                            <img src="<?= htmlspecialchars($currentRouteSeo['og_image']) ?>" alt="OG Preview" class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>

                    <input type="file" name="og_image_file" accept="image/*"
                           class="text-xs text-stone file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sand/70 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                    <p class="text-[11px] text-stone">Used for WhatsApp link previews, Twitter cards, and iMessage cards.</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold">
                        Save Route Metadata
                    </button>
                </div>
            </form>
        </div>

        <!-- Live SERP & Social Preview Card (1 col) -->
        <div class="space-y-6">
            
            <!-- Google SERP Preview Card -->
            <div class="bg-white rounded-2xl border border-sand p-5 shadow-sm space-y-3">
                <span class="text-[10px] uppercase tracking-wider font-bold text-stone block">Google SERP Snippet Preview</span>
                <div class="p-4 rounded-xl bg-[#FAFAFA] border border-gray-200 text-left font-sans">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-4 h-4 rounded-full bg-bronze text-white text-[9px] flex items-center justify-center font-bold">V</div>
                        <span class="text-xs text-[#202124] truncate">https://interior.ritiverse.in<?= $routes[$activeRoute]['slug'] ?></span>
                    </div>
                    <div id="preview_title" class="text-base text-[#1a0dab] font-medium leading-snug line-clamp-1 hover:underline cursor-pointer">
                        <?= htmlspecialchars($currentRouteSeo['meta_title'] ?? 'RiTiVerse • Atelier Vérité') ?>
                    </div>
                    <div id="preview_desc" class="text-xs text-[#4d5156] mt-1 line-clamp-2 leading-relaxed">
                        <?= htmlspecialchars($currentRouteSeo['meta_description'] ?? 'Upscale architecture and interior design studio crafting timeless, marble-toned residential and commercial sanctuaries.') ?>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Social Share Card Preview -->
            <div class="bg-white rounded-2xl border border-sand p-5 shadow-sm space-y-3">
                <span class="text-[10px] uppercase tracking-wider font-bold text-stone block">WhatsApp Social Preview Card</span>
                <div class="rounded-xl overflow-hidden border border-gray-200 bg-[#EFECE6]/40 text-left">
                    <div class="aspect-[1.91/1] bg-charcoal relative">
                        <img id="preview_og_img" src="<?= htmlspecialchars($currentRouteSeo['og_image'] ?? 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=1200') ?>" alt="Card" class="w-full h-full object-cover">
                    </div>
                    <div class="p-3 bg-white">
                        <p class="text-[10px] uppercase tracking-wider text-stone font-semibold">interior.ritiverse.in</p>
                        <p id="preview_wa_title" class="text-xs font-semibold text-charcoal mt-0.5 line-clamp-1"><?= htmlspecialchars($currentRouteSeo['meta_title'] ?? 'RiTiVerse • Atelier Vérité') ?></p>
                        <p id="preview_wa_desc" class="text-[11px] text-stone mt-0.5 line-clamp-2 leading-tight"><?= htmlspecialchars($currentRouteSeo['meta_description'] ?? 'Upscale architecture and interior design studio.') ?></p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- 2. Global Analytics & Tracking IDs -->
    <form method="POST" class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_analytics">

        <div class="border-b border-sand pb-4">
            <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Telemetry & Tags</span>
            <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Analytics & Webmaster Verification</h2>
            <p class="text-xs text-stone mt-1">IDs automatically injected into the frontend header for traffic auditing and attribution.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- GA4 ID -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Google Analytics 4 (GA4)</label>
                <input type="text" name="ga4_measurement_id" value="<?= htmlspecialchars($analyticsCurrent['ga4_measurement_id'] ?? '') ?>"
                       placeholder="G-XXXXXXXXXX"
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
                <p class="text-[11px] text-stone mt-1">Measurement ID starting with G-</p>
            </div>

            <!-- Meta Pixel ID -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Meta / Facebook Pixel ID</label>
                <input type="text" name="meta_pixel_id" value="<?= htmlspecialchars($analyticsCurrent['meta_pixel_id'] ?? '') ?>"
                       placeholder="123456789012345"
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
                <p class="text-[11px] text-stone mt-1">Numeric identifier for conversion tracking.</p>
            </div>

            <!-- Google Search Console -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Search Console Verification Tag</label>
                <input type="text" name="google_search_console_tag" value="<?= htmlspecialchars($analyticsCurrent['google_search_console_tag'] ?? '') ?>"
                       placeholder="google-site-verification token"
                       class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
                <p class="text-[11px] text-stone mt-1">HTML meta tag verification string.</p>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold">
                Save Global Analytics Tags
            </button>
        </div>
    </form>

</div>

<script>
    const titleInput = document.getElementById('seo_title');
    const descInput = document.getElementById('seo_desc');
    const titleCount = document.getElementById('seo_title_count');
    const descCount = document.getElementById('seo_desc_count');
    const previewTitle = document.getElementById('preview_title');
    const previewDesc = document.getElementById('preview_desc');
    const previewWaTitle = document.getElementById('preview_wa_title');
    const previewWaDesc = document.getElementById('preview_wa_desc');

    function updateCounters() {
        if (titleInput && titleCount) {
            titleCount.textContent = `${titleInput.value.length}/70`;
            if (previewTitle) previewTitle.textContent = titleInput.value || 'RiTiVerse • Atelier Vérité';
            if (previewWaTitle) previewWaTitle.textContent = titleInput.value || 'RiTiVerse • Atelier Vérité';
        }
        if (descInput && descCount) {
            descCount.textContent = `${descInput.value.length}/160`;
            if (previewDesc) previewDesc.textContent = descInput.value || 'Upscale architecture and interior design studio.';
            if (previewWaDesc) previewWaDesc.textContent = descInput.value || 'Upscale architecture and interior design studio.';
        }
    }

    if (titleInput) titleInput.addEventListener('input', updateCounters);
    if (descInput) descInput.addEventListener('input', updateCounters);
    updateCounters();
</script>

<?php render_admin_layout_end(); ?>
