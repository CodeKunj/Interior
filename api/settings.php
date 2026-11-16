<?php
/**
 * GET /api/settings.php
 * Public REST endpoint returning non-sensitive dynamic site settings for the React frontend.
 * Provides studio brand logos, contact info, WhatsApp config, social links, press awards, and analytics.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Atelier\Core\Request;
use Atelier\Core\Response;
use Atelier\Models\Setting;
use Atelier\Models\PressAward;

$request = new Request();
$request->requireMethod('GET');

try {
    $settingModel = new Setting();
    $pressModel = new PressAward();

    $settings = $settingModel->getAllPublic();
    $pressAwards = $pressModel->getAll();

    // Package press awards inside the settings response
    $settings['press_awards'] = $pressAwards;

    Response::success($settings, 'Site settings loaded successfully.');
} catch (\Throwable $e) {
    // Graceful fallback defaults if database is offline or not yet imported
    $fallbackSettings = [
        'general' => [
            'studio_name'      => 'AURION • ATELIER VÉRITÉ',
            'tagline'          => 'Architecture & Luxury Interior Sanctuaries',
            'footer_copyright' => '© ' . date('Y') . ' AURION • Atelier Vérité Architecture & Interiors. All Rights Reserved.'
        ],
        'brand' => [
            'logo_dark'  => '/uploads/brand/logo-dark.svg',
            'logo_light' => '/uploads/brand/logo-light.svg',
            'favicon'    => '/favicon.ico'
        ],
        'founder' => [
            'founder_name'        => 'Elena Vance',
            'founder_designation' => 'Principal Architect & Creative Director',
            'founder_bio'         => 'Educated in Milan and Zurich, Elena Vance established Atelier Vérité with an unwavering commitment to architectural restraint, spatial volume, and authentic materiality.',
            'founder_portrait'    => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1200&q=85',
            'founder_signature'   => ''
        ],
        'contact' => [
            'studio_address'         => '74 Mercer Street, Soho, New York, NY 10012',
            'google_maps_embed_url'  => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.633857502697!2d-74.0016489!3d40.7227563!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c2598beea1b585%3A0xbcf0461f36402ec!2s74%20Mercer%20St%2C%20New%20York%2C%20NY%2010012!5e0!3m2!1sen!2sus!4v1700000000000',
            'primary_phone'          => '+1 (212) 555-0198',
            'inquiry_receiver_email' => 'concierge@ateliervérité.com',
            'working_hours'          => 'Monday – Friday: 09:00 – 18:00 EST by Private Appointment'
        ],
        'whatsapp' => [
            'whatsapp_number'          => '+12125550198',
            'whatsapp_default_message' => 'Hello Atelier Vérité, I would like to inquire about an architectural design commission.',
            'enable_whatsapp_float'    => '1'
        ],
        'social' => [
            'instagram_url' => 'https://instagram.com/ateliervérité',
            'pinterest_url' => 'https://pinterest.com/ateliervérité',
            'houzz_url'     => 'https://houzz.com/pro/ateliervérité',
            'linkedin_url'  => 'https://linkedin.com/company/ateliervérité',
            'youtube_url'   => ''
        ],
        'analytics' => [
            'ga4_measurement_id'         => 'G-XXXXXXXXXX',
            'meta_pixel_id'              => '',
            'google_search_console_tag'  => ''
        ],
        'security' => [
            'recaptcha_site_key'   => '',
            'spam_protection_type' => 'disabled'
        ],
        'press_awards' => [
            ['id' => 1, 'title' => 'Architectural Digest - Top 50 Global Sanctuaries 2025', 'logo_url' => 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?auto=format&fit=crop&w=300&q=80', 'display_order' => 1],
            ['id' => 2, 'title' => 'Elle Decor - International Best Penthouse Restoration Award', 'logo_url' => 'https://images.unsplash.com/photo-1516876437184-593fda40c7ce?auto=format&fit=crop&w=300&q=80', 'display_order' => 2],
            ['id' => 3, 'title' => 'Wallpaper* Design Awards - Spatial Harmony Finalist', 'logo_url' => 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?auto=format&fit=crop&w=300&q=80', 'display_order' => 3]
        ]
    ];

    Response::success($fallbackSettings, 'Loaded fallback static settings (database setup required).');
}
