-- ==============================================================================
-- DATABASE SCHEMA: Upscale Interior Design Studio (Atelier Vérité)
-- Ready for cPanel MySQL / MariaDB Import via phpMyAdmin
-- Includes Projects, 3D Tours, Dynamic Site Settings, SEO, and Admin Portals
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create Core Projects & Gallery Tables
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(255) NULL,
    `category` VARCHAR(60) NOT NULL, -- 'Residential', 'Commercial', 'Turnkey', 'Renovation'
    `location` VARCHAR(150) NOT NULL,
    `area_sqft` INT NOT NULL,
    `year` INT NOT NULL,
    `cover_image` VARCHAR(512) NOT NULL,
    `hero_image` VARCHAR(512) NOT NULL,
    `client_brief` TEXT NOT NULL,
    `design_solution` TEXT NOT NULL,
    `featured` TINYINT(1) DEFAULT 0,
    `tour_3d_url` VARCHAR(512) NULL,
    `tour_type` ENUM('matterport', 'kuula', 'sketchfab', 'iframe') DEFAULT 'matterport',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category`),
    INDEX `idx_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `image_url` VARCHAR(512) NOT NULL,
    `caption` VARCHAR(255) NULL,
    `display_order` INT DEFAULT 0,
    INDEX `idx_project_id` (`project_id`),
    CONSTRAINT `fk_gallery_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `before_after` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `before_image` VARCHAR(512) NOT NULL,
    `after_image` VARCHAR(512) NOT NULL,
    `description` TEXT NULL,
    INDEX `idx_ba_project_id` (`project_id`),
    CONSTRAINT `fk_ba_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `property_type` VARCHAR(80) NOT NULL,
    `area_sqft` VARCHAR(50) NOT NULL,
    `budget_range` VARCHAR(100) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'contacted', 'scheduled', 'archived') DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(80) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `reset_token` VARCHAR(128) NULL,
    `reset_expires` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create Dynamic Site Settings, SEO & Press Tables
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `site_settings` (
    `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
    `setting_group` VARCHAR(50) NOT NULL, -- 'general', 'brand', 'founder', 'contact', 'whatsapp', 'social', 'smtp', 'security', 'analytics'
    `setting_value` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_setting_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `seo_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `route_key` VARCHAR(50) NOT NULL UNIQUE, -- 'home', 'work', 'services', 'about', 'contact', etc.
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `meta_keywords` TEXT NULL,
    `og_image` VARCHAR(512) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_route_key` (`route_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `press_awards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `logo_url` VARCHAR(512) NOT NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Seed Default Admin User:
-- Username: admin
-- Password: atelier2026Password! (Bcrypt hashed)
-- ------------------------------------------------------------------------------
INSERT INTO `admin_users` (`username`, `password_hash`, `email`)
VALUES (
    'admin',
    '$2y$12$Kj/2.9sO3g78c8M.c1m2te5sCszQ1Wk3mS0q.eSgL46G9t2K6r4iS', -- 'atelier2026Password!'
    'concierge@ateliervérité.com'
) ON DUPLICATE KEY UPDATE `id` = `id`;

-- ------------------------------------------------------------------------------
-- Seed Dynamic Site Settings (General, Brand, Contact, WhatsApp, Social, Founder)
-- ------------------------------------------------------------------------------
INSERT INTO `site_settings` (`setting_key`, `setting_group`, `setting_value`) VALUES
-- Brand & Identity
('studio_name', 'general', 'RiTiVerse • ATELIER VÉRITÉ'),
('tagline', 'general', 'Architecture & Luxury Interior Sanctuaries'),
('footer_copyright', 'general', '© 2026 RiTiVerse • Atelier Vérité Architecture & Interiors. All Rights Reserved.'),
('studio_logo_dark', 'general', '/uploads/brand/logo-dark.svg'),
('studio_logo_light', 'general', '/uploads/brand/logo-light.svg'),
('favicon', 'general', '/favicon.ico'),
('default_og_image', 'general', 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=85'),

-- Founder / About Page Info
('founder_name', 'general', 'Elena Vance'),
('founder_designation', 'general', 'Principal Architect & Creative Director'),
('founder_bio', 'general', 'Educated in Milan and Zurich, Elena Vance established Atelier Vérité with an unwavering commitment to architectural restraint, spatial volume, and authentic materiality. Her practice balances classical proportion with bespoke contemporary restraint.'),
('founder_portrait', 'general', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1200&q=85'),
('founder_signature', 'general', ''),

-- Studio Contact & Physical Location
('studio_address', 'contact', '74 Mercer Street, Soho, New York, NY 10012'),
('google_maps_embed_url', 'contact', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.633857502697!2d-74.0016489!3d40.7227563!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c2598beea1b585%3A0xbcf0461f36402ec!2s74%20Mercer%20St%2C%20New%20York%2C%20NY%2010012!5e0!3m2!1sen!2sus!4v1700000000000'),
('primary_phone', 'contact', '+1 (212) 555-0198'),
('inquiry_receiver_email', 'contact', 'concierge@ateliervérité.com'),
('working_hours', 'contact', 'Monday – Friday: 09:00 – 18:00 EST by Private Appointment'),

-- WhatsApp Direct Desk
('whatsapp_number', 'contact', '+12125550198'),
('whatsapp_default_message', 'contact', 'Hello Atelier Vérité, I would like to inquire about an architectural design commission.'),
('enable_whatsapp_float', 'contact', '1'),

-- Social Media Networks
('instagram_url', 'social', 'https://instagram.com/ateliervérité'),
('pinterest_url', 'social', 'https://pinterest.com/ateliervérité'),
('houzz_url', 'social', 'https://houzz.com/pro/ateliervérité'),
('linkedin_url', 'social', 'https://linkedin.com/company/ateliervérité'),
('youtube_url', 'social', ''),

-- Email & SMTP Settings
('smtp_host', 'smtp', 'localhost'),
('smtp_port', 'smtp', '587'),
('smtp_encryption', 'smtp', 'tls'),
('smtp_username', 'smtp', 'concierge@ateliervérité.com'),
('smtp_password', 'smtp', ''),
('admin_alert_email', 'smtp', 'concierge@ateliervérité.com'),
('smtp_from_name', 'smtp', 'Atelier Vérité Studio Concierge'),
('enable_client_autoreply', 'smtp', '1'),

-- Security & Spam Protection
('recaptcha_site_key', 'security', ''),
('recaptcha_secret_key', 'security', ''),
('spam_protection_type', 'security', 'disabled'),

-- Analytics & Tracking IDs
('ga4_measurement_id', 'seo', 'G-XXXXXXXXXX'),
('meta_pixel_id', 'seo', ''),
('google_search_console_tag', 'seo', '')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;

-- ------------------------------------------------------------------------------
-- Seed Global SEO & OpenGraph Route Metadata
-- ------------------------------------------------------------------------------
INSERT INTO `seo_settings` (`route_key`, `meta_title`, `meta_description`, `meta_keywords`, `og_image`) VALUES
(
    'home',
    'RiTiVerse • ATELIER VÉRITÉ — Architecture & Luxury Interior Sanctuaries',
    'Upscale architecture and interior design studio crafting timeless, marble-toned residential and commercial sanctuaries. Explore interactive 3D virtual tours and bespoke spatial design.',
    'luxury interior design, architectural studio, turnkey renovation, 3D matterport virtual tours, marble interiors, minimalist bespoke furniture',
    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85'
),
(
    'work',
    'Curated Sanctuaries & Works | RiTiVerse Atelier Vérité',
    'Explore our comprehensive portfolio of private residential estates, duplex penthouses, and bespoke commercial sanctuaries with interactive 3D virtual walkthroughs.',
    'interior design portfolio, architectural case studies, residential penthouse design, commercial salon architecture',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=85'
),
(
    'services',
    'Disciplines & Design Methodology | RiTiVerse Atelier Vérité',
    'Comprehensive turnkey architectural execution, 3D spatial planning, materiality curation, and bespoke furniture craft for discerning private clients.',
    'turnkey execution, 3D architectural rendering, spatial planning, custom furniture craft, material procurement',
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85'
),
(
    'about',
    'Studio Heritage & Founder Elena Vance | RiTiVerse Atelier Vérité',
    'Founded by Elena Vance, Atelier Vérité synthesizes European architectural discipline with serene minimalism, honoring unadorned stone, aged bronze, and tailored light.',
    'Elena Vance architect, interior design philosophy, architectural accolades, luxury design heritage',
    'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1200&q=85'
),
(
    'contact',
    'Initiate a Private Commission | RiTiVerse Atelier Vérité',
    'Connect with our senior architectural partners to discuss private residential, turnkey development, or commercial interior commissions worldwide.',
    'interior designer consultation, private commission, architectural booking, interior inquiry Soho New York',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85'
)
ON DUPLICATE KEY UPDATE `route_key` = `route_key`;

-- ------------------------------------------------------------------------------
-- Seed Press & Accolades Logos
-- ------------------------------------------------------------------------------
INSERT INTO `press_awards` (`id`, `title`, `logo_url`, `display_order`) VALUES
(1, 'Architectural Digest - Top 50 Global Sanctuaries 2025', 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?auto=format&fit=crop&w=300&q=80', 1),
(2, 'Elle Decor - International Best Penthouse Restoration Award', 'https://images.unsplash.com/photo-1516876437184-593fda40c7ce?auto=format&fit=crop&w=300&q=80', 2),
(3, 'Wallpaper* Design Awards - Spatial Harmony Finalist', 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?auto=format&fit=crop&w=300&q=80', 3),
(4, 'Dezeen Awards - Master Hospitality Interior Nominee', 'https://images.unsplash.com/photo-1516876437184-593fda40c7ce?auto=format&fit=crop&w=300&q=80', 4)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- ------------------------------------------------------------------------------
-- Seed Sample Projects with Editorial Luxury Imagery & Working 3D Tours
-- ------------------------------------------------------------------------------

INSERT INTO `projects` 
(`id`, `slug`, `title`, `subtitle`, `category`, `location`, `area_sqft`, `year`, `cover_image`, `hero_image`, `client_brief`, `design_solution`, `featured`, `tour_3d_url`, `tour_type`)
VALUES
(
    1,
    'the-tribeca-loft',
    'The Tribeca Loft',
    'Cast-Iron Framing & Honed Roman Travertine',
    'Residential',
    'Tribeca, New York',
    4200,
    2025,
    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=85',
    'The clients sought to transform a cavernous 19th-century cast-iron industrial warehouse into a serene, light-filled family sanctuary. Key requirements included dampening street acoustics, preserving original Douglas fir timber joists, and creating fluid transitions between the formal salon and culinary quarter.',
    'We orchestrated a monolithic intervention: floating partition walls coated in brushed warm alabaster plaster, paired with monolithic slabs of unpolished Roman travertine. Unlacquered brass details and concealed acoustic ceiling baffles create an atmosphere of monastic calm within Manhattan’s urban density.',
    1,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
),
(
    2,
    'villa-serenita',
    'Villa Serenità',
    'Terraced Mediterranean Cliffside Sanctuary',
    'Residential',
    'Amalfi Coast, Italy',
    6800,
    2024,
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1600&q=85',
    'Perched 300 meters above the Tyrrhenian Sea, the estate required a sensitive revival that honored vernacular Italian stone masonry while introducing high-performance climate control and seamless indoor-outdoor living corridors.',
    'We excavated bedrock to establish subterranean wellness suites and aligned the main salon with infinity-edge travertine reflection pools. Every custom piece of furniture was crafted from bleached chestnut and hand-loomed raw linen sourced from local Salerno artisans.',
    1,
    'https://sketchfab.com/models/0d13ad3b66444b76a08990b79dd5f522/embed?autostart=1&internal=1&tracking=0&ui_infos=0',
    'sketchfab'
),
(
    3,
    'kyoto-sanctuary',
    'Kyoto Sanctuary Residence',
    'Meditative Hinoki & Wabi-Sabi Minimalism',
    'Turnkey',
    'Higashiyama, Kyoto',
    3100,
    2025,
    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=1600&q=85',
    'Commissioned by a contemporary art collector, the brief called for a private residential gallery that adhered to traditional Sukiya-zukuri principles while housing large-scale international sculptural works.',
    'A dialogue between century-old smoked cedar pillars and contemporary micro-cement flooring. Slotted cedar screens modulate natural daylight, casting shifting geometric silhouettes across tactile tatami salons and courtyards planted with Japanese weeping maples.',
    1,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
),
(
    4,
    'mayfair-heritage-salon',
    'Mayfair Heritage Salon',
    'Georgian Restoration & Contemporary Restraint',
    'Renovation',
    'Mayfair, London',
    3800,
    2024,
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1600&q=85',
    'A Grade II listed Georgian townhouse suffering from decades of inappropriate partitions. The goal was to restore historic ornamental cornices while crafting an ultra-modern turnkey private residence.',
    'We meticulously cast ornamental plaster molds from surviving fragments, juxtaposing the restored 1780s detailing with floor-to-ceiling blackened steel glass partitions and custom charcoal silk rugs.',
    0,
    'https://sketchfab.com/models/0d13ad3b66444b76a08990b79dd5f522/embed?autostart=1&internal=1&tracking=0&ui_infos=0',
    'sketchfab'
),
(
    5,
    'lumina-haute-parfumerie',
    'Lumina Haute Parfumerie',
    'Bespoke Olfactory Flagship & VIP Suites',
    'Commercial',
    'Rue Saint-Honoré, Paris',
    2400,
    2025,
    'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=1600&q=85',
    'A luxury niche fragrance maison requiring an immersive flagship store where customers experience scent through sensory spatial transitions rather than traditional retail counters.',
    'Concentric fluted travertine pedestals with concealed micro-diffusers guide visitors through varying temperature and lighting zones. Private consultation salons feature velvet banquettes and acoustic felt ceilings.',
    0,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
),
(
    6,
    'bel-air-promontory',
    'Bel Air Promontory Estate',
    'Cantilevered Warm Brutalism & Skyline Panoramas',
    'Residential',
    'Bel Air, California',
    9200,
    2026,
    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85',
    'An ambitious ground-up architectural build commanding 300-degree vistas over the Los Angeles basin. Demanded heroic structural spans without feeling cold or sterile.',
    'Board-formed architectural concrete infused with warm sandstone aggregates was paired with deep overhangs of warm teak and floor-to-ceiling glass pocket doors that fully recess into the walls.',
    1,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
)
ON DUPLICATE KEY UPDATE `slug` = `slug`;

-- ------------------------------------------------------------------------------
-- Seed Project Gallery Images (High Resolution Editorial Shots)
-- ------------------------------------------------------------------------------

INSERT INTO `project_gallery` (`project_id`, `image_url`, `caption`, `display_order`) VALUES
-- The Tribeca Loft
(1, 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85', 'Great room featuring curated linen modular seating and exposed Douglas fir joists', 1),
(1, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=85', 'Monolithic Roman travertine kitchen island with integrated induction burners', 2),
(1, 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=85', 'Primary ensuite with fluted oak vanity and unlacquered aged brass tapware', 3),
(1, 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1600&q=85', 'Intimate library salon with floor-to-ceiling smoked oak joinery', 4),
(1, 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1600&q=85', 'Dining corridor lined with textured limewash plaster and bespoke sconces', 5),

-- Villa Serenità
(2, 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85', 'Main living pavilion framing panoramic Mediterranean clifftop views', 1),
(2, 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1600&q=85', 'Bespoke terracotta outdoor dining pergola with hand-loomed cushions', 2),
(2, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85', 'Guest suite with arched alcove and raw silk drapery', 3),
(2, 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85', 'Honed limestone infinity pool deck reflecting the Amalfi sunset', 4),

-- Kyoto Sanctuary Residence
(3, 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85', 'Tatami salon with custom hinoki woodwork and imported Belgian linen shoji screens', 1),
(3, 'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=1600&q=85', 'Central courtyard zen garden reflecting soft afternoon filtered light', 2),
(3, 'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=1600&q=85', 'Handcrafted granite onsen soaking tub facing a private moss garden', 3),

-- Mayfair Heritage Salon
(4, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85', 'Restored 18th-century ornate plaster ceiling and Calacatta Viola fire surround', 1),
(4, 'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1600&q=85', 'Modern minimalist kitchen intervention within historic Georgian framing', 2),

-- Lumina Haute Parfumerie
(5, 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85', 'Sculptural fluted travertine fragrance bar with hidden diffuse lighting', 1),
(5, 'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=1600&q=85', 'Private VIP consultation alcove upholstered in heavy crushed velvet', 2),

-- Bel Air Promontory Estate
(6, 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85', 'Open-air living pavilion with 14-foot motorized glass pocket facades', 1),
(6, 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85', 'Cantilevered terrace dining overlooking the Los Angeles canyon skyline', 2);

-- ------------------------------------------------------------------------------
-- Seed Before & After Pairs (Transformation Photography)
-- ------------------------------------------------------------------------------

INSERT INTO `before_after` (`project_id`, `title`, `before_image`, `after_image`, `description`) VALUES
(
    1,
    'Great Room & Kitchen Transformation',
    'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
    'Decades of yellowed drywall and low fluorescent troffers were stripped away to reveal soaring 12-foot timber joists and industrial cast-iron colums, paired with warm alabaster finishes.'
),
(
    2,
    'Cliffside Veranda & Pergola Revival',
    'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    'An aged, cracked concrete patio was reconstructed with honed Roman travertine slabs and custom bronze pergola framing that withstands harsh coastal saltwater air.'
),
(
    4,
    'Historic Drawing Room Restoration',
    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    'Flaking paint and damaged cornice friezes were painstakingly restored using heritage lime plaster casts, integrating modern recessed ambient floor uplighting.'
);

SET FOREIGN_KEY_CHECKS = 1;
