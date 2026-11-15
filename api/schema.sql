-- ==============================================================================
-- DATABASE SCHEMA: Upscale Interior Design Studio (Atelier Vérité)
-- Ready for cPanel MySQL / MariaDB Import via phpMyAdmin
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create Tables
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
-- Seed Sample Projects with Editorial Luxury Imagery & Working 3D Tours
-- ------------------------------------------------------------------------------

INSERT INTO `projects` 
(`id`, `slug`, `title`, `subtitle`, `category`, `location`, `area_sqft`, `year`, `cover_image`, `hero_image`, `client_brief`, `design_solution`, `featured`, `tour_3d_url`, `tour_type`)
VALUES
(
    1,
    'the-tribeca-loft',
    'The Tribeca Loft',
    'Curated Industrial Minimalism & Warm Alabaster Patina',
    'Residential',
    'Tribeca, New York',
    4200,
    2025,
    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=2200&q=85',
    'The client sought a complete spatial reimagining of a historic cast-iron warehouse floor. Key mandates included soft architectural acoustic dampening, continuous gallery-scale walls for private art exhibitions, and an effortless transition between serene private suites and high-capacity entertaining salons.',
    'We stripped back decades of disjointed drywall partitions to honor the original timber beams and structural cast-iron pillars. An expansive open-plan layout was organized into sculptural micro-zones using monolithic travertine islands, custom smoked-oak millwork, and full-height linen acoustic drapery. Flush architectural recessed lighting accentuates rich textural contrasts.',
    1,
    'https://my.matterport.com/show/?m=JGPnGQLBTJb&play=1&qs=1',
    'matterport'
),
(
    2,
    'villa-serenita-amalfi',
    'Villa Serenità',
    'Sun-Drenched Terracotta & Mediterranean Modernism',
    'Turnkey',
    'Amalfi Coast, Italy',
    6800,
    2025,
    'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=2200&q=85',
    'Perched high on the cliffs of Ravello, this private clifftop estate required a turnkey transformation that celebrated boundless azure sea vistas without succumbing to rustic clichés. The owners requested seamless indoor-outdoor living, bespoke climate-resilient furnishings, and a subterranean wine sanctuary.',
    'A soft, low-reflectance palette of hand-raked lime plaster and honed Roman travertine forms a quiet backdrop to the dramatic Mediterranean light. We designed custom bronze-framed pivot portals opening onto sprawling pergolas, paired with sculpted Bouclé lounge pieces, unlacquered brass hardware, and hand-loomed Tuscan textiles.',
    1,
    'https://sketchfab.com/models/b27f6cfb04ea4f9588eb7d65ff9e6fc9/embed?autostart=0&ui_controls=1&ui_infos=0&ui_inspector=0&ui_watermark=0',
    'sketchfab'
),
(
    3,
    'kyoto-sanctuary-residence',
    'Kyoto Sanctuary Residence',
    'Contemporary Wabi-Sabi & Hinoki Timber Craftsmanship',
    'Residential',
    'Higashiyama, Kyoto',
    3100,
    2024,
    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=2200&q=85',
    'Commissioned by a contemporary art patron, the brief called for an introspective residence immersed in traditional Japanese spatial philosophies of Ma (negative space) and Shakkei (borrowed scenery), equipped with discreet state-of-the-art smart environmental controls.',
    'Constructed in close collaboration with Kyoto master carpenters (Miyadaiku), the home features aromatic Japanese Hinoki framing, hand-troweled Juraku earth plaster walls, and custom Shoji screens reimagined with Belgian linen. A sunken granite soaking bath overlooks an intimate, enclosed moss and water garden.',
    1,
    'https://kuula.co/share/collection/7lZl5?logo=0&info=0&fs=1&vr=1&sd=1&thumbs=1',
    'kuula'
),
(
    4,
    'the-mayfair-heritage-salon',
    'The Mayfair Heritage Salon',
    'Georgian Classical Grandeur Meets Bespoke Savile Row Detailing',
    'Renovation',
    'Mayfair, London',
    5400,
    2024,
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=2200&q=85',
    'A sensitive architectural renovation of a Grade II listed Georgian townhouse. The client requested the preservation of delicate 18th-century plaster cornices and marble hearths alongside a cutting-edge contemporary kitchen and subterranean wellness suite.',
    'We executed painstaking conservation of historic plaster moldings while concealing modern ducted climate systems behind faux coffered friezes. Deep heritage olive and aged bone tones are complemented by fluted Calacatta Viola marble islands, ribbed glass pocket doors, and bespoke upholstery finished in British wool and saddle leather.',
    0,
    'https://my.matterport.com/show/?m=JGPnGQLBTJb&play=1&qs=1',
    'matterport'
),
(
    5,
    'lumina-flagship-ateliers',
    'Lumina Haute Parfumerie',
    'Sensory Retail Environment with Sculptural Fluted Travertine',
    'Commercial',
    'Rue Saint-Honoré, Paris',
    2800,
    2025,
    'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=2200&q=85',
    'An experiential flagship store and consultation salon for an artisanal French perfume house. The spatial program demanded high-end olfactory testing bars, intimate VIP consultation alcoves, and gallery-level acoustic privacy in central Paris.',
    'Centering around a monumental, curving counter carved from solid Navona travertine, the boutique surrounds visitors with warm ambient cove lighting, velvet-draped consultation chambers, and micro-perforated acoustic ceiling panels disguised as brushed alabaster.',
    1,
    'https://sketchfab.com/models/b27f6cfb04ea4f9588eb7d65ff9e6fc9/embed?autostart=0&ui_controls=1&ui_infos=0&ui_inspector=0&ui_watermark=0',
    'sketchfab'
),
(
    6,
    'bel-air-promontory-villa',
    'Bel Air Promontory Estate',
    'California Mid-Century Soul & Warm Architectural Volumes',
    'Turnkey',
    'Bel Air, Los Angeles',
    8200,
    2024,
    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
    'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=2200&q=85',
    'A sprawling hill-side turnkey residence designed for an entertainment industry executive who required both a restful retreat and a showcase for modern sculpture and automotive design.',
    'We blurred the threshold between the interior and the canyon landscape through 14-foot motorized glass pocket facades. Floors of hand-selected French limestone flow seamlessly onto infinity pool terraces, paired with dark American walnut wall paneling, brushed bronze accents, and low-slung Italian modular seating.',
    0,
    'https://my.matterport.com/show/?m=JGPnGQLBTJb&play=1&qs=1',
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
