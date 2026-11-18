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
    'The Tribeca Loft Revival',
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
    'villa-serenita-amalfi',
    'Amalfi Clifftop Pergola & Infinity Sanctuary',
    'Terraced Mediterranean Cliffside Sanctuary',
    'Turnkey',
    'Amalfi Coast, Italy',
    6800,
    2024,
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1600&q=85',
    'Perched 300 meters above the Tyrrhenian Sea, this multi-tiered clifftop villa required structural stabilization and bespoke marine-grade bronze pivot apertures. Designed to maximize coastal breezes and golden afternoon reflection across honed limestone terraces.',
    'We excavated bedrock to establish subterranean wellness suites and aligned the main salon with infinity-edge travertine reflection pools. Every custom piece of furniture was crafted from bleached chestnut and hand-loomed raw linen sourced from local Salerno artisans.',
    1,
    'https://sketchfab.com/models/0d13ad3b66444b76a08990b79dd5f522/embed?autostart=1&internal=1&tracking=0&ui_infos=0',
    'sketchfab'
),
(
    3,
    'kyoto-sanctuary-residence',
    'Kyoto Hinoki & Moss Garden Sanctuary',
    'Meditative Hinoki & Wabi-Sabi Minimalism',
    'Residential',
    'Higashiyama, Kyoto',
    3400,
    2025,
    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=1600&q=85',
    'Created in close collaboration with Kyoto master carpenters (Miyadaiku), balancing traditional Ma negative space with hidden geothermal environmental systems. Hand-carved hinoki timber joinery and raw washi acoustic screens.',
    'A dialogue between century-old smoked cedar pillars and contemporary micro-cement flooring. Slotted cedar screens modulate natural daylight, casting shifting geometric silhouettes across tactile tatami salons and courtyards planted with Japanese weeping maples.',
    1,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
),
(
    4,
    'the-mayfair-heritage-salon',
    'Mayfair Georgian Drawing Room Conservation',
    'Georgian Restoration & Contemporary Restraint',
    'Renovation',
    'Mayfair, London',
    5100,
    2024,
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1600&q=85',
    'A Grade II listed Georgian townhouse suffering from decades of inappropriate partitions. Painstaking restoration of 18th-century plaster friezes and Calacatta Viola fireplaces, seamlessly paired with contemporary concealed kitchen joinery and custom bronze exhibition vitrines.',
    'We meticulously cast ornamental plaster molds from surviving fragments, juxtaposing the restored 1780s detailing with floor-to-ceiling blackened steel glass partitions and custom charcoal silk rugs.',
    1,
    'https://sketchfab.com/models/0d13ad3b66444b76a08990b79dd5f522/embed?autostart=1&internal=1&tracking=0&ui_infos=0',
    'sketchfab'
),
(
    5,
    'lumina-flagship-ateliers',
    'Lumina Flagship Parfumerie, Paris',
    'Sensory Commercial Flagship & VIP Suites',
    'Commercial',
    '1er Arrondissement, Paris',
    2900,
    2025,
    'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=1600&q=85',
    'An experiential fragrance salon featuring a monumental curving counter carved from solid Navona travertine in central Paris. Tuned acoustics and concealed scent diffusion chambers create an ethereal oasis.',
    'Concentric fluted travertine pedestals with concealed micro-diffusers guide visitors through varying temperature and lighting zones. Private consultation salons feature velvet banquettes and acoustic felt ceilings.',
    1,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
),
(
    6,
    'bel-air-promontory-villa',
    'Bel Air Motorized Canyon Glass Pavilion',
    'Cantilevered Warm Brutalism & Skyline Panoramas',
    'Turnkey',
    'Bel Air, California',
    8200,
    2026,
    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85',
    'Blurring the boundary between interior luxury and the dramatic California canyon landscape through 14-foot motorized glass pocket portals, cantilevered infinity reflections, and seamless micro-cement flooring.',
    'Board-formed architectural concrete infused with warm sandstone aggregates was paired with deep overhangs of warm teak and floor-to-ceiling glass pocket doors that fully recess into the walls.',
    1,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
),
(
    7,
    'interactive-3d-digital-twins',
    'Interactive 3D Digital Twin Simulation',
    'Photorealistic Virtual Walkthroughs & Spatial Prototyping',
    'Residential',
    'Global / Virtual Studio',
    10000,
    2026,
    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=85',
    'Every commission includes a fully navigable 3D digital twin so you can test sightlines, natural daylight trajectory, and acoustic isolation before physical build.',
    'Engineered using Matterport Pro3 LiDAR and high-fidelity ray-traced spatial engines. Clients can explore every room, test natural daylight simulation, and review joinery tolerances with full millimeter-level spatial fidelity.',
    1,
    'https://my.matterport.com/show/?m=JGPnGGTgJ7L&play=1',
    'matterport'
)
ON DUPLICATE KEY UPDATE 
    `slug` = VALUES(`slug`),
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `category` = VALUES(`category`),
    `location` = VALUES(`location`),
    `area_sqft` = VALUES(`area_sqft`),
    `year` = VALUES(`year`),
    `cover_image` = VALUES(`cover_image`),
    `hero_image` = VALUES(`hero_image`),
    `client_brief` = VALUES(`client_brief`),
    `design_solution` = VALUES(`design_solution`),
    `featured` = VALUES(`featured`),
    `tour_3d_url` = VALUES(`tour_3d_url`),
    `tour_type` = VALUES(`tour_type`);

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
(6, 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85', 'Cantilevered terrace dining overlooking the Los Angeles canyon skyline', 2),

-- Interactive 3D Digital Twin Simulation
(7, 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=85', 'Virtual twin spatial wireframe and volumetric proportion simulation', 1),
(7, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=85', 'Daylight angle analysis under autumn afternoon sun trajectory', 2),
(7, 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1600&q=85', 'Joinery millwork and acoustic material texture preview in real-time ray-tracing', 3);

-- ------------------------------------------------------------------------------
-- 4. Create Dynamic Content Tables (Journeys, Services, Process, Testimonials, FAQs, Disciplines, Press)
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `journeys` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `tag` VARCHAR(100) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `area` VARCHAR(50) NOT NULL,
    `duration` VARCHAR(100) NOT NULL,
    `hero_image` VARCHAR(512) NOT NULL,
    `overview` TEXT NOT NULL,
    `materials` TEXT NOT NULL,
    `span` VARCHAR(50) DEFAULT 'md:col-span-4',
    `has_3d_tour` TINYINT(1) DEFAULT 0,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_journey_slug` (`slug`),
    INDEX `idx_journey_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `service_key` VARCHAR(100) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `deliverables` TEXT NOT NULL,
    `image_url` VARCHAR(512) NOT NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_service_key` (`service_key`),
    INDEX `idx_service_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `process_steps` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `step_number` VARCHAR(10) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `phase` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_step_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `quote` TEXT NOT NULL,
    `author` VARCHAR(150) NOT NULL,
    `role` VARCHAR(150) NOT NULL,
    `project` VARCHAR(150) NOT NULL,
    `avatar_url` VARCHAR(512) NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_testimonial_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `faqs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `category` VARCHAR(100) DEFAULT 'General',
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_faq_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `disciplines` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `image_url` VARCHAR(512) NOT NULL,
    `grid_span` VARCHAR(50) DEFAULT 'lg:col-span-4',
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_discipline_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `press_mentions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `outlet` VARCHAR(150) NOT NULL,
    `quote` TEXT NOT NULL,
    `year` VARCHAR(50) NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_press_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Seed Architectural Journeys
-- ------------------------------------------------------------------------------
INSERT INTO `journeys` 
(`id`, `slug`, `title`, `tag`, `location`, `area`, `duration`, `hero_image`, `overview`, `materials`, `span`, `has_3d_tour`, `display_order`)
VALUES
(
    1,
    'the-tribeca-loft',
    'The Tribeca Loft Revival',
    'Industrial Heritage',
    'Tribeca, New York',
    '4,200 sq. ft.',
    '14 Months Turnkey',
    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
    'A full-floor conversion within a protected 19th-century cast-iron warehouse. Stripping away decades of ad-hoc drywall to reveal historical timber pillars, softened with monolithic Roman travertine and micro-perforated acoustic linen walls.',
    '["Roman Travertine Islands", "Smoked French Oak", "Acoustic Linen Drapery", "Aged Unlacquered Brass"]',
    'md:col-span-4',
    1,
    1
),
(
    2,
    'villa-serenita-amalfi',
    'Amalfi Clifftop Pergola & Infinity Sanctuary',
    'Turnkey Coastal',
    'Amalfi Coast, Italy',
    '6,800 sq. ft.',
    '18 Months Turnkey',
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    'Perched over the Mediterranean, this multi-tiered clifftop villa required structural stabilization and bespoke marine-grade bronze pivot apertures. Designed to maximize coastal breezes and golden afternoon reflection across honed limestone terraces.',
    '["Honed Vicenza Limestone", "Naval Bronze Portals", "Rough-Hewn Tufa Stone", "Bleached Chestnut"]',
    'md:col-span-8',
    1,
    2
),
(
    3,
    'kyoto-sanctuary-residence',
    'Kyoto Hinoki & Moss Garden Sanctuary',
    'Wabi-Sabi Minimalism',
    'Higashiyama, Kyoto',
    '3,400 sq. ft.',
    '12 Months Turnkey',
    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
    'Created in close collaboration with Kyoto master carpenters (Miyadaiku), balancing traditional Ma negative space with hidden geothermal environmental systems. Hand-carved hinoki timber joinery and raw washi acoustic screens.',
    '["Japanese Hinoki Cedar", "Kurotani Washi Screens", "Charred Yakisugi Oak", "River-Honed Basalt"]',
    'md:col-span-6',
    1,
    3
),
(
    4,
    'the-mayfair-heritage-salon',
    'Mayfair Georgian Drawing Room Conservation',
    'Historic Restoration',
    'Mayfair, London',
    '5,100 sq. ft.',
    '16 Months Turnkey',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    'Painstaking restoration of 18th-century plaster friezes and Calacatta Viola fireplaces, seamlessly paired with contemporary concealed kitchen joinery and custom bronze exhibition vitrines.',
    '["Calacatta Viola Marble", "Cornice Plaster Reliefs", "Burr Walnut Paneling", "Smoked Mirror Glass"]',
    'md:col-span-6',
    0,
    4
),
(
    5,
    'lumina-flagship-ateliers',
    'Lumina Flagship Parfumerie, Paris',
    'Sensory Commercial',
    '1er Arrondissement, Paris',
    '2,900 sq. ft.',
    '8 Months Turnkey',
    'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
    'An experiential fragrance salon featuring a monumental curving counter carved from solid Navona travertine in central Paris. Tuned acoustics and concealed scent diffusion chambers create an ethereal oasis.',
    '["Solid Navona Travertine", "Champagne Anodized Aluminum", "Velvet Acoustic Plaster", "Backlit Alabaster"]',
    'md:col-span-4',
    0,
    5
),
(
    6,
    'bel-air-promontory-villa',
    'Bel Air Motorized Canyon Glass Pavilion',
    'Modernist Turnkey',
    'Bel Air, California',
    '8,200 sq. ft.',
    '22 Months Turnkey',
    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
    'Blurring the boundary between interior luxury and the dramatic California canyon landscape through 14-foot motorized glass pocket portals, cantilevered infinity reflections, and seamless micro-cement flooring.',
    '["Micro-Cement Seamless Floors", "Thermally Broken Glass Portals", "Smoked Eucalyptus Millwork", "Honed Basalt"]',
    'md:col-span-4',
    1,
    6
),
(
    7,
    'interactive-3d-digital-twins',
    'Interactive 3D Digital Twin Simulation',
    'Virtual Twin Technology',
    'Global / Virtual Studio',
    'All Projects',
    'Pre-construction',
    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=85',
    'Every commission includes a fully navigable 3D digital twin so you can test sightlines, natural daylight trajectory, and acoustic isolation before physical build.',
    '["Matterport Pro3 LiDAR", "High-Fidelity Photogrammetry", "Ray-Traced Light Engines", "VR Headset Compatible"]',
    'md:col-span-4',
    1,
    7
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `overview` = VALUES(`overview`);

-- ------------------------------------------------------------------------------
-- Seed Services & Methodology
-- ------------------------------------------------------------------------------
INSERT INTO `services`
(`id`, `service_key`, `title`, `subtitle`, `description`, `deliverables`, `image_url`, `display_order`)
VALUES
(
    1,
    'turnkey-execution',
    'Turnkey Interior Execution',
    'From Bare Shell to Flawless Handover',
    'A holistic, white-glove design-and-build experience. We take absolute stewardship of civil alterations, MEP coordination, acoustic engineering, artisan finishes, and bespoke furniture placement.',
    '["Comprehensive structural & MEP architectural documentation", "Dedicated on-site senior project manager & master contractor team", "Artisan craft curation (custom stonework, millwork, fine metals)", "Turnkey styling, fine art acquisition & final staging"]',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85',
    1
),
(
    2,
    'spatial-planning',
    'Spatial Planning & Architecture',
    'Proportion, Daylight, & Spatial Choreography',
    'We curate human-centric layouts that prioritize intuitive movement, acoustic balance, and maximum daylight exposure, harmonizing volume and void.',
    '["Micro-zoning & flow circulation studies", "Daylight trajectory & sightline mapping", "Acoustic insulation & spatial partition detailing", "Detailed joinery & architectural ceiling schematics"]',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
    2
),
(
    3,
    '3d-visualization',
    '3D Visualization & Virtual Tours',
    'Photorealistic Digital Twins Before Groundbreak',
    'Immerse yourself inside your future sanctuary before a single brick is laid. We engineer cinema-grade CGI renders, interactive 3D digital twins, and walkthrough simulations.',
    '["Ultra-high fidelity photorealistic interior renderings", "Matterport / Kuula 360° interactive digital twin models", "Lighting simulation under seasonal sun angles", "Material sample tactile mood boards"]',
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=85',
    3
),
(
    4,
    'bespoke-furniture',
    'Bespoke Furniture & Curated Objects',
    'Heirloom Furniture & Rare Tactile Finishes',
    'Tailored solely to your home. We commission bespoke dining tables, sculptured seating, and custom lighting fixtures in collaboration with European and Japanese master craftsmen.',
    '["Exclusive bespoke furniture sketches & 1:1 joinery details", "Direct sourcing of rare marbles, French oak, and tactile bouclés", "Commissioned gallery-grade sculptures and art pieces", "White-glove installation and archival care guidelines"]',
    'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=85',
    4
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `description` = VALUES(`description`);

-- ------------------------------------------------------------------------------
-- Seed 4-Step Process Steps
-- ------------------------------------------------------------------------------
INSERT INTO `process_steps`
(`id`, `step_number`, `title`, `phase`, `description`, `display_order`)
VALUES
(1, '01', 'Discovery & Spatial Audit', 'Week 1 – 2', 'We explore your lifestyle, aesthetic sensitivities, functional requirements, and the unique architectural context of the property.', 1),
(2, '02', 'Concept & 3D Digital Twin', 'Week 3 – 6', 'We translate the spatial vision into tactile mood boards, detailed floor plans, and interactive 3D virtual tour simulations.', 2),
(3, '03', 'Material Sourcing & Joinery', 'Week 7 – 10', 'Quarry-direct marble selection, custom timber milling, hand-loomed textiles, and fabrication of custom architectural fixtures.', 3),
(4, '04', 'Execution & White-Glove Handover', 'Week 11 – Completion', 'Rigorous on-site craftsmanship supervision, acoustic testing, deep detail cleaning, art placement, and the ceremonial key handover.', 4)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- ------------------------------------------------------------------------------
-- Seed Testimonials
-- ------------------------------------------------------------------------------
INSERT INTO `testimonials`
(`id`, `quote`, `author`, `role`, `project`, `display_order`)
VALUES
(1, 'Atelier Vérité transformed our Tribeca warehouse into a serene sanctuary that feels simultaneously like a private art gallery and an effortlessly comfortable home. Their restraint and mastery of natural materials is unmatched.', 'Julian & Elena Vance', 'Private Collectors', 'The Tribeca Loft', 1),
(2, 'Being able to walk through our Amalfi villa via their 3D virtual twin before construction commenced saved us months of revisions and gave us absolute clarity. The final result exceeded our wildest aspirations.', 'Conte Marco Rossi', 'Estate Owner', 'Villa Serenità, Amalfi', 2),
(3, 'The level of craftsmanship and architectural rigor they brought to our Paris flagship established our brand identity on an international stage. Every client notes the extraordinary tactile energy of the space.', 'Céline Laurent', 'Creative Director', 'Lumina Haute Parfumerie, Paris', 3)
ON DUPLICATE KEY UPDATE `quote` = VALUES(`quote`);

-- ------------------------------------------------------------------------------
-- Seed FAQs
-- ------------------------------------------------------------------------------
INSERT INTO `faqs`
(`id`, `question`, `answer`, `category`, `display_order`)
VALUES
(1, 'What is your typical project timeline for a full turnkey residence?', 'Turnkey residential projects typically range from 4 to 9 months, depending on carpet area and structural scope. Discovery and 3D digital twin design take 4–6 weeks, followed by concurrent procurement and on-site artisan execution.', 'Turnkey', 1),
(2, 'How do you charge for interior architecture and design services?', 'We operate on a transparent design fee structured per square foot or as a percentage of the total construction cost, paired with direct pass-through wholesale trade pricing for material procurement and bespoke furniture fabrication.', 'Fees', 2),
(3, 'Can you take on international commissions outside your primary studios?', 'Yes. Atelier Vérité frequently designs residences throughout North America, Europe, Japan, and the Middle East. Our remote 3D digital twin methodology and vetted regional contractor networks ensure seamless overseas execution.', 'Locations', 3),
(4, 'How do the interactive 3D virtual tours work during the design phase?', 'Before physical demolition or construction begins, we construct an immersive 3D digital model (accessible via browser or VR headset). You can freely walk through hallways, test sightlines, view material reflections, and experience exact daylight conditions.', 'Technology', 4),
(5, 'Do you manage site contractors and municipal permissions?', 'Yes. In our Turnkey Execution tier, our studio coordinates all municipal permits, heritage board approvals, structural engineer sign-offs, and daily site contractor craftsmanship.', 'Execution', 5)
ON DUPLICATE KEY UPDATE `question` = VALUES(`question`);

-- ------------------------------------------------------------------------------
-- Seed Disciplines
-- ------------------------------------------------------------------------------
INSERT INTO `disciplines`
(`id`, `title`, `description`, `image_url`, `grid_span`, `display_order`)
VALUES
(1, 'Turnkey Architectural Stewardship', 'White-glove design-and-build oversight from bare shell demolition to final linen drapery and art curation.', 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-7', 1),
(2, 'Interactive 3D Virtual Twins', 'Explore your future residence via browser or VR before construction begins with photorealistic daylight simulations.', 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-5', 2),
(3, 'Quarry-Direct Roman Travertine', 'Hand-selected unlacquered stone blocks carved by generational Italian artisans for sculptural monolithic islands.', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-4', 3),
(4, 'Acoustic Insulation & Belgian Linens', 'Micro-perforated acoustic paneling and heavyweight raw linen drapery that absorb reverberation for absolute serenity.', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-4', 4),
(5, 'Bespoke Heirloom Millwork', 'Smoked oak, Japanese hinoki joinery, and custom furniture pieces tailored to the exact proportions of your home.', 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-4', 5)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- ------------------------------------------------------------------------------
-- Seed Press Mentions
-- ------------------------------------------------------------------------------
INSERT INTO `press_mentions`
(`id`, `outlet`, `quote`, `year`, `display_order`)
VALUES
(1, 'Architectural Digest', 'A masterclass in quiet luxury and architectural restraint.', '2025', 1),
(2, 'Wallpaper* Magazine', 'The digital twin methodology that is redefining turnkey interior architecture.', '2025', 2),
(3, 'Elle Décor International', 'Sanctuaries carved from stone, linen, and daylight.', '2024', 3),
(4, 'Dezeen Awards', 'Best Residential Transformation — The Tribeca Loft.', '2024', 4)
ON DUPLICATE KEY UPDATE `outlet` = VALUES(`outlet`);

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

-- ------------------------------------------------------------------------------
-- 4. Create Dynamic Content Tables (Journeys, Services, Process, Testimonials, FAQs, Disciplines, Press)
-- ------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `journeys` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `tag` VARCHAR(100) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `area` VARCHAR(50) NOT NULL,
    `duration` VARCHAR(100) NOT NULL,
    `hero_image` VARCHAR(512) NOT NULL,
    `overview` TEXT NOT NULL,
    `materials` TEXT NOT NULL,
    `span` VARCHAR(50) DEFAULT 'md:col-span-4',
    `has_3d_tour` TINYINT(1) DEFAULT 0,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_journey_slug` (`slug`),
    INDEX `idx_journey_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `service_key` VARCHAR(100) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `deliverables` TEXT NOT NULL,
    `image_url` VARCHAR(512) NOT NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_service_key` (`service_key`),
    INDEX `idx_service_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `process_steps` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `step_number` VARCHAR(10) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `phase` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_step_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `quote` TEXT NOT NULL,
    `author` VARCHAR(150) NOT NULL,
    `role` VARCHAR(150) NOT NULL,
    `project` VARCHAR(150) NOT NULL,
    `avatar_url` VARCHAR(512) NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_testimonial_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `faqs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `category` VARCHAR(100) DEFAULT 'General',
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_faq_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `disciplines` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `image_url` VARCHAR(512) NOT NULL,
    `grid_span` VARCHAR(50) DEFAULT 'lg:col-span-4',
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_discipline_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `press_mentions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `outlet` VARCHAR(150) NOT NULL,
    `quote` TEXT NOT NULL,
    `year` VARCHAR(50) NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_press_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Seed Architectural Journeys
-- ------------------------------------------------------------------------------
INSERT INTO `journeys` 
(`id`, `slug`, `title`, `tag`, `location`, `area`, `duration`, `hero_image`, `overview`, `materials`, `span`, `has_3d_tour`, `display_order`)
VALUES
(
    1,
    'the-tribeca-loft',
    'The Tribeca Loft Revival',
    'Industrial Heritage',
    'Tribeca, New York',
    '4,200 sq. ft.',
    '14 Months Turnkey',
    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
    'A full-floor conversion within a protected 19th-century cast-iron warehouse. Stripping away decades of ad-hoc drywall to reveal historical timber pillars, softened with monolithic Roman travertine and micro-perforated acoustic linen walls.',
    '["Roman Travertine Islands", "Smoked French Oak", "Acoustic Linen Drapery", "Aged Unlacquered Brass"]',
    'md:col-span-4',
    1,
    1
),
(
    2,
    'villa-serenita-amalfi',
    'Amalfi Clifftop Pergola & Infinity Sanctuary',
    'Turnkey Coastal',
    'Amalfi Coast, Italy',
    '6,800 sq. ft.',
    '18 Months Turnkey',
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    'Perched over the Mediterranean, this multi-tiered clifftop villa required structural stabilization and bespoke marine-grade bronze pivot apertures. Designed to maximize coastal breezes and golden afternoon reflection across honed limestone terraces.',
    '["Honed Vicenza Limestone", "Naval Bronze Portals", "Rough-Hewn Tufa Stone", "Bleached Chestnut"]',
    'md:col-span-8',
    1,
    2
),
(
    3,
    'kyoto-sanctuary-residence',
    'Kyoto Hinoki & Moss Garden Sanctuary',
    'Wabi-Sabi Minimalism',
    'Higashiyama, Kyoto',
    '3,400 sq. ft.',
    '12 Months Turnkey',
    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
    'Created in close collaboration with Kyoto master carpenters (Miyadaiku), balancing traditional Ma negative space with hidden geothermal environmental systems. Hand-carved hinoki timber joinery and raw washi acoustic screens.',
    '["Japanese Hinoki Cedar", "Kurotani Washi Screens", "Charred Yakisugi Oak", "River-Honed Basalt"]',
    'md:col-span-6',
    1,
    3
),
(
    4,
    'the-mayfair-heritage-salon',
    'Mayfair Georgian Drawing Room Conservation',
    'Historic Restoration',
    'Mayfair, London',
    '5,100 sq. ft.',
    '16 Months Turnkey',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    'Painstaking restoration of 18th-century plaster friezes and Calacatta Viola fireplaces, seamlessly paired with contemporary concealed kitchen joinery and custom bronze exhibition vitrines.',
    '["Calacatta Viola Marble", "Cornice Plaster Reliefs", "Burr Walnut Paneling", "Smoked Mirror Glass"]',
    'md:col-span-6',
    0,
    4
),
(
    5,
    'lumina-flagship-ateliers',
    'Lumina Flagship Parfumerie, Paris',
    'Sensory Commercial',
    '1er Arrondissement, Paris',
    '2,900 sq. ft.',
    '8 Months Turnkey',
    'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
    'An experiential fragrance salon featuring a monumental curving counter carved from solid Navona travertine in central Paris. Tuned acoustics and concealed scent diffusion chambers create an ethereal oasis.',
    '["Solid Navona Travertine", "Champagne Anodized Aluminum", "Velvet Acoustic Plaster", "Backlit Alabaster"]',
    'md:col-span-4',
    0,
    5
),
(
    6,
    'bel-air-promontory-villa',
    'Bel Air Motorized Canyon Glass Pavilion',
    'Modernist Turnkey',
    'Bel Air, California',
    '8,200 sq. ft.',
    '22 Months Turnkey',
    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
    'Blurring the boundary between interior luxury and the dramatic California canyon landscape through 14-foot motorized glass pocket portals, cantilevered infinity reflections, and seamless micro-cement flooring.',
    '["Micro-Cement Seamless Floors", "Thermally Broken Glass Portals", "Smoked Eucalyptus Millwork", "Honed Basalt"]',
    'md:col-span-4',
    1,
    6
),
(
    7,
    'interactive-3d-digital-twins',
    'Interactive 3D Digital Twin Simulation',
    'Virtual Twin Technology',
    'Global / Virtual Studio',
    'All Projects',
    'Pre-construction',
    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=85',
    'Every commission includes a fully navigable 3D digital twin so you can test sightlines, natural daylight trajectory, and acoustic isolation before physical build.',
    '["Matterport Pro3 LiDAR", "High-Fidelity Photogrammetry", "Ray-Traced Light Engines", "VR Headset Compatible"]',
    'md:col-span-4',
    1,
    7
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `overview` = VALUES(`overview`);

-- ------------------------------------------------------------------------------
-- Seed Services & Methodology
-- ------------------------------------------------------------------------------
INSERT INTO `services`
(`id`, `service_key`, `title`, `subtitle`, `description`, `deliverables`, `image_url`, `display_order`)
VALUES
(
    1,
    'turnkey-execution',
    'Turnkey Interior Execution',
    'From Bare Shell to Flawless Handover',
    'A holistic, white-glove design-and-build experience. We take absolute stewardship of civil alterations, MEP coordination, acoustic engineering, artisan finishes, and bespoke furniture placement.',
    '["Comprehensive structural & MEP architectural documentation", "Dedicated on-site senior project manager & master contractor team", "Artisan craft curation (custom stonework, millwork, fine metals)", "Turnkey styling, fine art acquisition & final staging"]',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85',
    1
),
(
    2,
    'spatial-planning',
    'Spatial Planning & Architecture',
    'Proportion, Daylight, & Spatial Choreography',
    'We curate human-centric layouts that prioritize intuitive movement, acoustic balance, and maximum daylight exposure, harmonizing volume and void.',
    '["Micro-zoning & flow circulation studies", "Daylight trajectory & sightline mapping", "Acoustic insulation & spatial partition detailing", "Detailed joinery & architectural ceiling schematics"]',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
    2
),
(
    3,
    '3d-visualization',
    '3D Visualization & Virtual Tours',
    'Photorealistic Digital Twins Before Groundbreak',
    'Immerse yourself inside your future sanctuary before a single brick is laid. We engineer cinema-grade CGI renders, interactive 3D digital twins, and walkthrough simulations.',
    '["Ultra-high fidelity photorealistic interior renderings", "Matterport / Kuula 360° interactive digital twin models", "Lighting simulation under seasonal sun angles", "Material sample tactile mood boards"]',
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=85',
    3
),
(
    4,
    'bespoke-furniture',
    'Bespoke Furniture & Curated Objects',
    'Heirloom Furniture & Rare Tactile Finishes',
    'Tailored solely to your home. We commission bespoke dining tables, sculptured seating, and custom lighting fixtures in collaboration with European and Japanese master craftsmen.',
    '["Exclusive bespoke furniture sketches & 1:1 joinery details", "Direct sourcing of rare marbles, French oak, and tactile bouclés", "Commissioned gallery-grade sculptures and art pieces", "White-glove installation and archival care guidelines"]',
    'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=85',
    4
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `description` = VALUES(`description`);

-- ------------------------------------------------------------------------------
-- Seed 4-Step Process Steps
-- ------------------------------------------------------------------------------
INSERT INTO `process_steps`
(`id`, `step_number`, `title`, `phase`, `description`, `display_order`)
VALUES
(1, '01', 'Discovery & Spatial Audit', 'Week 1 – 2', 'We explore your lifestyle, aesthetic sensitivities, functional requirements, and the unique architectural context of the property.', 1),
(2, '02', 'Concept & 3D Digital Twin', 'Week 3 – 6', 'We translate the spatial vision into tactile mood boards, detailed floor plans, and interactive 3D virtual tour simulations.', 2),
(3, '03', 'Material Sourcing & Joinery', 'Week 7 – 10', 'Quarry-direct marble selection, custom timber milling, hand-loomed textiles, and fabrication of custom architectural fixtures.', 3),
(4, '04', 'Execution & White-Glove Handover', 'Week 11 – Completion', 'Rigorous on-site craftsmanship supervision, acoustic testing, deep detail cleaning, art placement, and the ceremonial key handover.', 4)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- ------------------------------------------------------------------------------
-- Seed Testimonials
-- ------------------------------------------------------------------------------
INSERT INTO `testimonials`
(`id`, `quote`, `author`, `role`, `project`, `display_order`)
VALUES
(1, 'Atelier Vérité transformed our Tribeca warehouse into a serene sanctuary that feels simultaneously like a private art gallery and an effortlessly comfortable home. Their restraint and mastery of natural materials is unmatched.', 'Julian & Elena Vance', 'Private Collectors', 'The Tribeca Loft', 1),
(2, 'Being able to walk through our Amalfi villa via their 3D virtual twin before construction commenced saved us months of revisions and gave us absolute clarity. The final result exceeded our wildest aspirations.', 'Conte Marco Rossi', 'Estate Owner', 'Villa Serenità, Amalfi', 2),
(3, 'The level of craftsmanship and architectural rigor they brought to our Paris flagship established our brand identity on an international stage. Every client notes the extraordinary tactile energy of the space.', 'Céline Laurent', 'Creative Director', 'Lumina Haute Parfumerie, Paris', 3)
ON DUPLICATE KEY UPDATE `quote` = VALUES(`quote`);

-- ------------------------------------------------------------------------------
-- Seed FAQs
-- ------------------------------------------------------------------------------
INSERT INTO `faqs`
(`id`, `question`, `answer`, `category`, `display_order`)
VALUES
(1, 'What is your typical project timeline for a full turnkey residence?', 'Turnkey residential projects typically range from 4 to 9 months, depending on carpet area and structural scope. Discovery and 3D digital twin design take 4–6 weeks, followed by concurrent procurement and on-site artisan execution.', 'Turnkey', 1),
(2, 'How do you charge for interior architecture and design services?', 'We operate on a transparent design fee structured per square foot or as a percentage of the total construction cost, paired with direct pass-through wholesale trade pricing for material procurement and bespoke furniture fabrication.', 'Fees', 2),
(3, 'Can you take on international commissions outside your primary studios?', 'Yes. Atelier Vérité frequently designs residences throughout North America, Europe, Japan, and the Middle East. Our remote 3D digital twin methodology and vetted regional contractor networks ensure seamless overseas execution.', 'Locations', 3),
(4, 'How do the interactive 3D virtual tours work during the design phase?', 'Before physical demolition or construction begins, we construct an immersive 3D digital model (accessible via browser or VR headset). You can freely walk through hallways, test sightlines, view material reflections, and experience exact daylight conditions.', 'Technology', 4),
(5, 'Do you manage site contractors and municipal permissions?', 'Yes. In our Turnkey Execution tier, our studio coordinates all municipal permits, heritage board approvals, structural engineer sign-offs, and daily site contractor craftsmanship.', 'Execution', 5)
ON DUPLICATE KEY UPDATE `question` = VALUES(`question`);

-- ------------------------------------------------------------------------------
-- Seed Disciplines
-- ------------------------------------------------------------------------------
INSERT INTO `disciplines`
(`id`, `title`, `description`, `image_url`, `grid_span`, `display_order`)
VALUES
(1, 'Turnkey Architectural Stewardship', 'White-glove design-and-build oversight from bare shell demolition to final linen drapery and art curation.', 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-7', 1),
(2, 'Interactive 3D Virtual Twins', 'Explore your future residence via browser or VR before construction begins with photorealistic daylight simulations.', 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-5', 2),
(3, 'Quarry-Direct Roman Travertine', 'Hand-selected unlacquered stone blocks carved by generational Italian artisans for sculptural monolithic islands.', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-4', 3),
(4, 'Acoustic Insulation & Belgian Linens', 'Micro-perforated acoustic paneling and heavyweight raw linen drapery that absorb reverberation for absolute serenity.', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-4', 4),
(5, 'Bespoke Heirloom Millwork', 'Smoked oak, Japanese hinoki joinery, and custom furniture pieces tailored to the exact proportions of your home.', 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=85', 'lg:col-span-4', 5)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- ------------------------------------------------------------------------------
-- Seed Press Mentions
-- ------------------------------------------------------------------------------
INSERT INTO `press_mentions`
(`id`, `outlet`, `quote`, `year`, `display_order`)
VALUES
(1, 'Architectural Digest', 'A masterclass in quiet luxury and architectural restraint.', '2025', 1),
(2, 'Wallpaper* Magazine', 'The digital twin methodology that is redefining turnkey interior architecture.', '2025', 2),
(3, 'Elle Décor International', 'Sanctuaries carved from stone, linen, and daylight.', '2024', 3),
(4, 'Dezeen Awards', 'Best Residential Transformation — The Tribeca Loft.', '2024', 4)
ON DUPLICATE KEY UPDATE `outlet` = VALUES(`outlet`);

SET FOREIGN_KEY_CHECKS = 1;
