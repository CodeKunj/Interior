<?php
/**
 * Module 3: Social Media & Press / Awards Manager
 * Social profile links and architectural publication accolades manager.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Models\Setting;
use Atelier\Models\PressAward;

require_admin_auth();

$settingModel = new Setting();
$pressModel = new PressAward();

// Handle Actions (Save Social Links or Add/Delete Press Logo)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'CSRF verification failed.');
        header('Location: settings-social.php');
        exit();
    }

    $action = $_POST['action'] ?? 'save_social';

    if ($action === 'save_social') {
        try {
            $socialSettings = [
                'instagram_url' => trim($_POST['instagram_url'] ?? ''),
                'pinterest_url' => trim($_POST['pinterest_url'] ?? ''),
                'houzz_url'     => trim($_POST['houzz_url'] ?? ''),
                'linkedin_url'  => trim($_POST['linkedin_url'] ?? ''),
                'youtube_url'   => trim($_POST['youtube_url'] ?? ''),
            ];

            $settingModel->setMany($socialSettings, 'social');
            set_flash('success', 'Social media URLs updated successfully.');
            header('Location: settings-social.php');
            exit();

        } catch (\Throwable $e) {
            set_flash('error', 'Failed to save social links: ' . $e->getMessage());
        }
    }

    if ($action === 'add_press') {
        try {
            $title = trim($_POST['press_title'] ?? '');
            $order = (int)($_POST['display_order'] ?? 0);

            if (empty($title)) {
                throw new \Exception("Publication title is required.");
            }

            $logoUrl = handle_image_upload('press_logo', 'press', 'press');
            if (!$logoUrl) {
                throw new \Exception("Please upload a publication logo image.");
            }

            $pressModel->add($title, $logoUrl, $order);
            set_flash('success', "Accreditation '{$title}' added successfully.");
            header('Location: settings-social.php');
            exit();

        } catch (\Throwable $e) {
            set_flash('error', 'Error adding press badge: ' . $e->getMessage());
        }
    }

    if ($action === 'delete_press') {
        try {
            $id = (int)($_POST['press_id'] ?? 0);
            if ($id > 0) {
                $pressModel->delete($id);
                set_flash('success', 'Publication badge removed.');
            }
            header('Location: settings-social.php');
            exit();
        } catch (\Throwable $e) {
            set_flash('error', 'Error removing badge: ' . $e->getMessage());
        }
    }
}

// Fetch Current Social Settings & Press Awards
$socialCurrent = $settingModel->getByGroup('social');
$pressAwards = $pressModel->getAll();

render_admin_layout_start(
    'Social & Press Awards',
    'settings',
    'social',
    [['label' => 'Settings', 'url' => 'settings-general.php'], ['label' => 'Social & Press Awards', 'url' => '']]
);
?>

<div class="max-w-4xl space-y-10">

    <!-- 1. Social Profiles Form -->
    <form method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_social">

        <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-sand pb-4">
                <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Digital Footprint</span>
                <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Social Channels & Portfolio Profiles</h2>
                <p class="text-xs text-stone mt-1">Leave any field empty to automatically conceal its icon on the public frontend navigation and footer.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Instagram -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Instagram Profile</label>
                    <input type="url" name="instagram_url" value="<?= htmlspecialchars($socialCurrent['instagram_url'] ?? 'https://instagram.com/ateliervérité') ?>"
                           placeholder="https://instagram.com/..."
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                </div>

                <!-- Pinterest -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Pinterest Editorial Boards</label>
                    <input type="url" name="pinterest_url" value="<?= htmlspecialchars($socialCurrent['pinterest_url'] ?? 'https://pinterest.com/ateliervérité') ?>"
                           placeholder="https://pinterest.com/..."
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                </div>

                <!-- Houzz -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Houzz Pro Portfolio</label>
                    <input type="url" name="houzz_url" value="<?= htmlspecialchars($socialCurrent['houzz_url'] ?? 'https://houzz.com/pro/ateliervérité') ?>"
                           placeholder="https://houzz.com/pro/..."
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                </div>

                <!-- LinkedIn -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">LinkedIn Corporate Atelier</label>
                    <input type="url" name="linkedin_url" value="<?= htmlspecialchars($socialCurrent['linkedin_url'] ?? 'https://linkedin.com/company/ateliervérité') ?>"
                           placeholder="https://linkedin.com/company/..."
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                </div>

                <!-- YouTube -->
                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">YouTube Architectural Tours</label>
                    <input type="url" name="youtube_url" value="<?= htmlspecialchars($socialCurrent['youtube_url'] ?? 'https://youtube.com/@ateliervérité') ?>"
                           placeholder="https://youtube.com/@..."
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold">
                    Save Social Links
                </button>
            </div>
        </div>
    </form>

    <!-- 2. Press & Awards Logo Manager -->
    <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
        <div class="border-b border-sand pb-4">
            <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Accreditations</span>
            <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Featured Press & Award Badges</h2>
            <p class="text-xs text-stone mt-1">Logos displayed on the homepage ticker and about page (e.g. Architectural Digest, Elle Decor, Wallpaper*).</p>
        </div>

        <!-- Add New Press Badge Form -->
        <form method="POST" enctype="multipart/form-data" class="p-5 rounded-xl border border-sand bg-alabaster/40 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add_press">

            <div class="text-xs uppercase tracking-wider font-semibold text-charcoal">Add New Publication Logo</div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-[11px] uppercase tracking-wider font-semibold text-stone mb-1.5">Publication Title</label>
                    <input type="text" name="press_title" placeholder="Architectural Digest" required
                           class="w-full px-3 py-2 rounded-lg border border-sand bg-white text-xs focus:outline-none focus:border-bronze">
                </div>

                <div>
                    <label class="block text-[11px] uppercase tracking-wider font-semibold text-stone mb-1.5">Display Order</label>
                    <input type="number" name="display_order" value="0"
                           class="w-full px-3 py-2 rounded-lg border border-sand bg-white text-xs focus:outline-none focus:border-bronze">
                </div>

                <div>
                    <label class="block text-[11px] uppercase tracking-wider font-semibold text-stone mb-1.5">Logo Image (PNG/SVG/WEBP)</label>
                    <input type="file" name="press_logo" accept="image/*" required
                           class="text-xs text-stone file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[10px] file:bg-sand/70 file:text-charcoal hover:file:bg-bronze hover:file:text-white cursor-pointer w-full">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2 rounded-lg bg-bronze text-white hover:bg-bronzeDark transition-colors text-[11px] uppercase tracking-wider font-semibold">
                    + Add Publication Badge
                </button>
            </div>
        </form>

        <!-- Existing Badges List -->
        <div>
            <div class="text-xs uppercase tracking-wider font-semibold text-charcoal mb-4">Current Publications (<?= count($pressAwards) ?>)</div>

            <?php if (empty($pressAwards)): ?>
                <p class="text-xs text-stone italic p-6 text-center border border-dashed border-sand rounded-xl bg-alabaster/20">No press logos added yet. Use the form above to add Architectural Digest, Elle Decor, etc.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($pressAwards as $badge): ?>
                        <div class="p-4 rounded-xl border border-sand bg-alabaster/30 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-16 h-10 bg-white border border-sand rounded-lg flex items-center justify-center p-1.5 flex-shrink-0">
                                    <img src="<?= htmlspecialchars($badge['logo_url']) ?>" alt="<?= htmlspecialchars($badge['title']) ?>" class="max-h-full max-w-full object-contain filter grayscale opacity-80">
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-charcoal truncate"><?= htmlspecialchars($badge['title']) ?></p>
                                    <p class="text-[10px] text-stone">Order: <?= (int)$badge['display_order'] ?></p>
                                </div>
                            </div>

                            <!-- Delete Form -->
                            <form method="POST" onsubmit="return confirm('Remove publication logo <?= htmlspecialchars($badge['title']) ?>?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_press">
                                <input type="hidden" name="press_id" value="<?= (int)$badge['id'] ?>">
                                <button type="submit" class="text-stone hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php render_admin_layout_end(); ?>
