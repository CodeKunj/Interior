<?php
/**
 * Module 1: General & Brand Identity Settings
 * Studio Name, Tagline, Logos, Favicon, Founder Profile & Signatures
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Models\Setting;

require_admin_auth();

$settingModel = new Setting();
$errors = [];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'CSRF validation failed. Please refresh and try again.');
        header('Location: settings-general.php');
        exit();
    }

    try {
        $settingsToUpdate = [
            'studio_name'         => trim($_POST['studio_name'] ?? 'RiTiVerse'),
            'tagline'             => trim($_POST['tagline'] ?? ''),
            'footer_copyright'    => trim($_POST['footer_copyright'] ?? ''),
            'founder_name'        => trim($_POST['founder_name'] ?? ''),
            'founder_designation' => trim($_POST['founder_designation'] ?? ''),
            'founder_bio'         => trim($_POST['founder_bio'] ?? ''),
        ];

        // Process File Uploads (Dark Logo, Light Logo, Favicon, Founder Portrait, Founder Signature)
        if (!empty($_FILES['studio_logo_dark']['name'])) {
            $uploaded = handle_image_upload('studio_logo_dark', 'brand', 'logo_dark');
            if ($uploaded) {
                $settingsToUpdate['studio_logo_dark'] = $uploaded;
            }
        }

        if (!empty($_FILES['studio_logo_light']['name'])) {
            $uploaded = handle_image_upload('studio_logo_light', 'brand', 'logo_light');
            if ($uploaded) {
                $settingsToUpdate['studio_logo_light'] = $uploaded;
            }
        }

        if (!empty($_FILES['favicon']['name'])) {
            $uploaded = handle_image_upload('favicon', 'brand', 'favicon');
            if ($uploaded) {
                $settingsToUpdate['favicon'] = $uploaded;
            }
        }

        if (!empty($_FILES['default_og_image']['name'])) {
            $uploaded = handle_image_upload('default_og_image', 'brand', 'og_banner');
            if ($uploaded) {
                $settingsToUpdate['default_og_image'] = $uploaded;
            }
        }

        if (!empty($_FILES['founder_portrait']['name'])) {
            $uploaded = handle_image_upload('founder_portrait', 'brand', 'founder_portrait');
            if ($uploaded) {
                $settingsToUpdate['founder_portrait'] = $uploaded;
            }
        }

        if (!empty($_FILES['founder_signature']['name'])) {
            $uploaded = handle_image_upload('founder_signature', 'brand', 'founder_sig');
            if ($uploaded) {
                $settingsToUpdate['founder_signature'] = $uploaded;
            }
        }

        // Save to Database
        $settingModel->setMany($settingsToUpdate, 'general');
        set_flash('success', 'General brand identity and founder profiles saved successfully.');
        header('Location: settings-general.php');
        exit();

    } catch (\Throwable $e) {
        set_flash('error', 'Error updating settings: ' . $e->getMessage());
    }
}

// Fetch Current Settings
$current = $settingModel->getByGroup('general');

render_admin_layout_start(
    'General & Brand Identity',
    'settings',
    'general',
    [['label' => 'Settings', 'url' => 'settings-general.php'], ['label' => 'General & Brand', 'url' => '']]
);
?>

<div class="max-w-4xl space-y-8">

    <form method="POST" enctype="multipart/form-data" class="space-y-8">
        <?= csrf_field() ?>

        <!-- 1. Studio Brand Credentials -->
        <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-sand pb-4">
                <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Visual Identity</span>
                <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Studio Brand & Naming</h2>
                <p class="text-xs text-stone mt-1">Define the visual signature, emblems, and studio trademark displayed on the website.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Studio Name -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Studio Name</label>
                    <input type="text" name="studio_name" value="<?= htmlspecialchars($current['studio_name'] ?? 'RiTiVerse') ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                    <p class="text-[11px] text-stone mt-1">Primary luxury monogram / brand identifier.</p>
                </div>

                <!-- Tagline -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Architectural Tagline</label>
                    <input type="text" name="tagline" value="<?= htmlspecialchars($current['tagline'] ?? 'Atelier Vérité — Architecture & Interior Sanctuaries') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                    <p class="text-[11px] text-stone mt-1">Subtitle displayed under the studio monogram.</p>
                </div>

                <!-- Footer Copyright -->
                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Footer Copyright Notice</label>
                    <input type="text" name="footer_copyright" value="<?= htmlspecialchars($current['footer_copyright'] ?? '© 2026 RiTiVerse • Atelier Vérité Architecture & Luxury Interiors. All Rights Reserved.') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                </div>
            </div>

            <!-- Logo & Brand Assets Upload Grid -->
            <div class="border-t border-sand/60 pt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Dark Logo (For Light Backgrounds) -->
                <div class="p-4 rounded-xl border border-sand bg-alabaster/30 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-charcoal block mb-1">Primary Dark Logo</span>
                        <p class="text-[11px] text-stone mb-3">Shown on light navigation bars & white documents.</p>
                        <?php if (!empty($current['studio_logo_dark'])): ?>
                            <div class="mb-3 p-3 bg-white rounded-lg border border-sand flex items-center justify-center min-h-[64px]">
                                <img src="<?= htmlspecialchars($current['studio_logo_dark']) ?>" alt="Dark Logo" class="max-h-12 object-contain">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="file" name="studio_logo_dark" accept="image/*" class="text-xs text-stone file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sand/60 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                    </div>
                </div>

                <!-- Light Logo (For Dark Overlays) -->
                <div class="p-4 rounded-xl border border-sand bg-alabaster/30 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-charcoal block mb-1">Light / White Logo</span>
                        <p class="text-[11px] text-stone mb-3">Shown over dark hero video overlays & dark backgrounds.</p>
                        <?php if (!empty($current['studio_logo_light'])): ?>
                            <div class="mb-3 p-3 bg-charcoal rounded-lg border border-charcoal flex items-center justify-center min-h-[64px]">
                                <img src="<?= htmlspecialchars($current['studio_logo_light']) ?>" alt="Light Logo" class="max-h-12 object-contain">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="file" name="studio_logo_light" accept="image/*" class="text-xs text-stone file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sand/60 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                    </div>
                </div>

                <!-- Favicon -->
                <div class="p-4 rounded-xl border border-sand bg-alabaster/30 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-charcoal block mb-1">Browser Favicon</span>
                        <p class="text-[11px] text-stone mb-3">Square icon for browser tabs & bookmark bars (PNG/ICO/SVG).</p>
                        <?php if (!empty($current['favicon'])): ?>
                            <div class="mb-3 p-3 bg-white rounded-lg border border-sand flex items-center justify-center min-h-[64px]">
                                <img src="<?= htmlspecialchars($current['favicon']) ?>" alt="Favicon" class="w-8 h-8 object-contain">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="file" name="favicon" accept="image/*" class="text-xs text-stone file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sand/60 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                    </div>
                </div>

                <!-- Global OpenGraph Social Banner -->
                <div class="p-4 rounded-xl border border-sand bg-alabaster/30 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-charcoal block mb-1">Default OG Social Banner</span>
                        <p class="text-[11px] text-stone mb-3">1200 &times; 630 share card for WhatsApp, iMessage, and Twitter previews.</p>
                        <?php if (!empty($current['default_og_image'])): ?>
                            <div class="mb-3 p-1 bg-charcoal rounded-lg border border-sand overflow-hidden aspect-[1.91/1] flex items-center justify-center">
                                <img src="<?= htmlspecialchars($current['default_og_image']) ?>" alt="OG Banner" class="w-full h-full object-cover">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="file" name="default_og_image" accept="image/*" class="text-xs text-stone file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sand/60 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. Founder Profile & Bio -->
        <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-sand pb-4">
                <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Leadership & About</span>
                <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Founder & Principal Architect</h2>
                <p class="text-xs text-stone mt-1">Featured on the studio about page and proposal documents.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Founder Name -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Founder / Principal Name</label>
                    <input type="text" name="founder_name" value="<?= htmlspecialchars($current['founder_name'] ?? 'Éléonore Laurent') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                </div>

                <!-- Founder Designation -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Designation / Architectural Title</label>
                    <input type="text" name="founder_designation" value="<?= htmlspecialchars($current['founder_designation'] ?? 'Founding Principal & Creative Director') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                </div>

                <!-- Founder Bio -->
                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Founder Bio & Philosophy</label>
                    <textarea name="founder_bio" rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all leading-relaxed"><?= htmlspecialchars($current['founder_bio'] ?? "Trained across Milan and Paris, Éléonore founded RiTiVerse Atelier Vérité with an unwavering belief in architectural restraint, honest materiality, and quiet luxury.") ?></textarea>
                </div>
            </div>

            <!-- Founder Portrait & Signature Uploads -->
            <div class="border-t border-sand/60 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Founder Portrait -->
                <div class="p-4 rounded-xl border border-sand bg-alabaster/30 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-charcoal block mb-1">Founder Portrait</span>
                        <p class="text-[11px] text-stone mb-3">High-resolution editorial photography (Vertical portrait recommended).</p>
                        <?php if (!empty($current['founder_portrait'])): ?>
                            <div class="mb-3 p-2 bg-white rounded-lg border border-sand flex items-center justify-center">
                                <img src="<?= htmlspecialchars($current['founder_portrait']) ?>" alt="Founder Portrait" class="max-h-36 rounded-md object-cover">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="file" name="founder_portrait" accept="image/*" class="text-xs text-stone file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sand/60 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                    </div>
                </div>

                <!-- Founder Signature -->
                <div class="p-4 rounded-xl border border-sand bg-alabaster/30 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-charcoal block mb-1">Founder Signature (PNG with transparent bg)</span>
                        <p class="text-[11px] text-stone mb-3">Autograph mark rendered at the conclusion of client proposals.</p>
                        <?php if (!empty($current['founder_signature'])): ?>
                            <div class="mb-3 p-3 bg-white rounded-lg border border-sand flex items-center justify-center min-h-[64px]">
                                <img src="<?= htmlspecialchars($current['founder_signature']) ?>" alt="Signature" class="max-h-16 object-contain">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="file" name="founder_signature" accept="image/*" class="text-xs text-stone file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sand/60 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button Dock -->
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="submit" class="px-8 py-3 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold shadow-md">
                Save Brand & Identity Settings
            </button>
        </div>

    </form>

</div>

<?php render_admin_layout_end(); ?>
