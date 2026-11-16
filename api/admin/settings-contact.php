<?php
/**
 * Module 2: Contact, Studio & WhatsApp Settings
 * Physical atelier address, concierge email, working hours, and floating WhatsApp widget integration.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

use Atelier\Models\Setting;

require_admin_auth();

$settingModel = new Setting();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'CSRF token mismatch. Please try again.');
        header('Location: settings-contact.php');
        exit();
    }

    try {
        $contactSettings = [
            'studio_address'           => trim($_POST['studio_address'] ?? ''),
            'google_maps_embed_url'    => trim($_POST['google_maps_embed_url'] ?? ''),
            'primary_phone'            => trim($_POST['primary_phone'] ?? ''),
            'inquiry_receiver_email'   => trim($_POST['inquiry_receiver_email'] ?? ''),
            'working_hours'            => trim($_POST['working_hours'] ?? ''),
            'whatsapp_number'          => preg_replace('/[^0-9]/', '', $_POST['whatsapp_number'] ?? ''),
            'whatsapp_default_message' => trim($_POST['whatsapp_default_message'] ?? ''),
            'enable_whatsapp_float'    => isset($_POST['enable_whatsapp_float']) ? '1' : '0',
        ];

        $settingModel->setMany($contactSettings, 'contact');
        set_flash('success', 'Studio contact information and WhatsApp concierge saved.');
        header('Location: settings-contact.php');
        exit();

    } catch (\Throwable $e) {
        set_flash('error', 'Failed to save contact settings: ' . $e->getMessage());
    }
}

// Fetch Existing Settings
$current = $settingModel->getByGroup('contact');

render_admin_layout_start(
    'Contact, Studio & WhatsApp',
    'settings',
    'contact',
    [['label' => 'Settings', 'url' => 'settings-general.php'], ['label' => 'Contact & WhatsApp', 'url' => '']]
);
?>

<div class="max-w-4xl space-y-8">

    <form method="POST" class="space-y-8">
        <?= csrf_field() ?>

        <!-- 1. Physical Studio & Concierge Coordinates -->
        <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-sand pb-4">
                <span class="text-[10px] tracking-[0.24em] uppercase text-bronze font-semibold">Physical Presence</span>
                <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">Atelier Coordinates & Concierge</h2>
                <p class="text-xs text-stone mt-1">Client reception details displayed across contact pages, headers, and quotation documents.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Studio Address -->
                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Studio Physical Address</label>
                    <textarea name="studio_address" rows="3" required
                              class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all"><?= htmlspecialchars($current['studio_address'] ?? "742 Evergreen Galleria, Haute Étage\nSouth Extension, New Delhi 110049") ?></textarea>
                </div>

                <!-- Primary Phone -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Studio Concierge Phone</label>
                    <input type="text" name="primary_phone" value="<?= htmlspecialchars($current['primary_phone'] ?? '+91 98765 43210') ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                    <p class="text-[11px] text-stone mt-1">Visible on contact page & header dial shortcuts.</p>
                </div>

                <!-- Inquiry Receiver Email -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Inquiry Receiver Email</label>
                    <input type="email" name="inquiry_receiver_email" value="<?= htmlspecialchars($current['inquiry_receiver_email'] ?? 'concierge@interior.ritiverse.in') ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                    <p class="text-[11px] text-stone mt-1">Receives new client consultation requests.</p>
                </div>

                <!-- Working Hours -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Working Hours / Atelier Salons</label>
                    <input type="text" name="working_hours" value="<?= htmlspecialchars($current['working_hours'] ?? 'Monday – Saturday: 10:00 — 19:00 (By Appointment)') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all">
                </div>

                <!-- Google Maps Embed URL -->
                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Google Maps Embed URL</label>
                    <input type="text" name="google_maps_embed_url" value="<?= htmlspecialchars($current['google_maps_embed_url'] ?? '') ?>"
                           placeholder="https://www.google.com/maps/embed?pb=..."
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono text-xs">
                    <p class="text-[11px] text-stone mt-1">Paste the 'src' link from Google Maps &gt; Share &gt; Embed Map.</p>
                </div>
            </div>
        </div>

        <!-- 2. WhatsApp Floating Concierge Widget -->
        <div class="bg-white rounded-2xl border border-sand p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-sand pb-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] tracking-[0.24em] uppercase text-emerald-700 font-semibold">Instant Dialogue</span>
                    <h2 class="font-serif text-xl sm:text-2xl text-charcoal font-medium mt-1">WhatsApp Concierge Integration</h2>
                    <p class="text-xs text-stone mt-1">Floating luxury button enabling high-net-worth clients to initiate immediate consultations.</p>
                </div>

                <!-- Toggle Switch -->
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-charcoal">Enable Float:</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_whatsapp_float" value="1" <?= ($current['enable_whatsapp_float'] ?? '1') === '1' ? 'checked' : '' ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-sand peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- WhatsApp Phone Number -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">WhatsApp Number (with Country Code)</label>
                    <input type="text" name="whatsapp_number" value="<?= htmlspecialchars($current['whatsapp_number'] ?? '919876543210') ?>"
                           placeholder="919876543210"
                           class="w-full px-4 py-2.5 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all font-mono">
                    <p class="text-[11px] text-stone mt-1">Digits only, including international code (e.g. 919876543210).</p>
                </div>

                <!-- Live Test Button -->
                <div class="flex flex-col justify-end">
                    <?php 
                    $waNum = $current['whatsapp_number'] ?? '919876543210';
                    $waMsg = urlencode($current['whatsapp_default_message'] ?? 'Hello Atelier Vérité, I would like to schedule a private architectural consultation.');
                    ?>
                    <a href="https://wa.me/<?= htmlspecialchars($waNum) ?>?text=<?= $waMsg ?>" target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold hover:bg-emerald-100 transition-colors">
                        <svg class="w-4 h-4 fill-emerald-600" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        <span>Test WhatsApp Direct Link &nearr;</span>
                    </a>
                </div>

                <!-- Default Message -->
                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-charcoal mb-2">Default WhatsApp Prefilled Message</label>
                    <textarea name="whatsapp_default_message" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-sand bg-alabaster/50 text-sm focus:outline-none focus:border-bronze focus:bg-white transition-all"><?= htmlspecialchars($current['whatsapp_default_message'] ?? 'Hello Atelier Vérité, I would like to schedule a private architectural consultation for a new residence.') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button Dock -->
        <div class="flex items-center justify-end gap-4 pt-2">
            <button type="submit" class="px-8 py-3 rounded-xl bg-charcoal text-white hover:bg-bronze transition-colors text-xs uppercase tracking-[0.16em] font-semibold shadow-md">
                Save Contact & WhatsApp Settings
            </button>
        </div>

    </form>

</div>

<?php render_admin_layout_end(); ?>
