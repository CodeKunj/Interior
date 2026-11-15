Final Implementation Plan: Interior Design Studio (Multipage ReactJS + PHP + 3D Gallery)1. Project Overview & Aesthetic DirectionReference Inspiration: Luxury Boutique Hotel editorial aesthetic (https://www.webild.io/templates/boutique-hotel) — warm neutral backgrounds, high-contrast serif headings, generous whitespace, asymmetric editorial grids, slow hover-zoom imagery, and smooth scroll reveals.Tech Stack:Frontend: ReactJS (Vite), Tailwind CSS, react-router-dom (multipage routing), framer-motion (animations), lucide-react (minimal line icons), react-helmet-async (per-page SEO).Backend: Plain PHP 8+ REST API with PDO & MySQL (lightweight, zero framework overhead).Hosting Target: cPanel shared/cloud hosting (public_html static React build + /api PHP backend).Core Differentiator: Each project case study (/work/:slug) features an Interactive 3D Map / Virtual Tour tab inside the gallery section supporting Matterport, Kuula, Sketchfab, or Spline embed URLs alongside curated photography and a draggable Before & After comparison slider.2. Complete Project File Structureinterior-design-studio/
├── public/
│   ├── .htaccess                     # Apache SPA + PHP API routing rules for cPanel
│   ├── favicon.ico
│   └── robots.txt
│
├── src/
│   ├── assets/
│   │   └── styles/
│   │       └── editorial.css         # Custom serif typography & boutique animations
│   │
│   ├── components/
│   │   ├── layout/
│   │   │   ├── Navbar.jsx            # Transparent-to-solid scroll header + mobile overlay
│   │   │   ├── Footer.jsx            # Editorial studio footer + navigation
│   │   │   └── ScrollToTop.jsx       # Auto scroll-to-top on route change
│   │   ├── project/
│   │   │   ├── ProjectCard.jsx       # Asymmetric grid card + "3D Tour" badge
│   │   │   ├── ProjectGallery.jsx    # Tabbed gallery (Photos | Before/After | 3D Map)
│   │   │   ├── BeforeAfterSlider.jsx # Touch & mouse draggable comparison slider
│   │   │   └── Tour3DEmbed.jsx       # Responsive 16:9 Matterport/Kuula/Sketchfab player
│   │   └── common/
│   │       ├── TestimonialSlider.jsx
│   │       ├── ProcessTimeline.jsx
│   │       └── WhatsAppFloat.jsx     # Floating direct consultation button
│   │
│   ├── pages/
│   │   ├── Home.jsx                  # `/` — Hero, manifesto, featured work, services
│   │   ├── Portfolio.jsx             # `/work` — Filterable architectural portfolio
│   │   ├── ProjectDetail.jsx         # `/work/:slug` — Case study + 3D Map gallery
│   │   ├── Services.jsx              # `/services` — Offerings, 4-step workflow, FAQ
│   │   ├── About.jsx                 # `/about` — Founder story, philosophy, press
│   │   └── Contact.jsx               # `/contact` — Multi-field inquiry form + studio info
│   │
│   ├── services/
│   │   ├── api.js                    # Fetch wrapper with local mock fallback for Vite dev
│   │   └── mockData.js               # Sample projects & 3D tour URLs for offline preview
│   │
│   ├── App.jsx                       # React Router DOM multipage routes
│   └── main.jsx                      # Entry point with HelmetProvider
│
├── api/                              # PHP 8+ Backend (uploaded to public_html/api/)
│   ├── config/
│   │   └── db.php                    # PDO MySQL connection & JSON CORS headers
│   ├── admin/                        # Client CMS Portal
│   │   ├── index.php                 # Session login & dashboard
│   │   ├── projects-manage.php       # Add/Edit/Delete projects & paste 3D Tour URL
│   │   ├── upload-handler.php        # Auto-converts JPG/PNG uploads to WebP
│   │   └── leads.php                 # View & export consultation inquiries
│   ├── uploads/
│   │   └── .htaccess                 # Security rule: `php_flag engine off`
│   ├── schema.sql                    # Complete MySQL database + sample data
│   ├── projects.php                  # GET `/api/projects.php` (supports ?category & ?featured)
│   ├── project-detail.php            # GET `/api/project-detail.php?slug=...`
│   └── inquiry.php                   # POST `/api/inquiry.php` (saves lead + sends email)
│
├── index.html                        # Loads Google Fonts (Cormorant Garamond + Plus Jakarta Sans)
├── package.json
├── tailwind.config.js                # Boutique color palette & typography tokens
├── vite.config.js
└── README.md                         # Step-by-step cPanel deployment guide
3. Visual Design Tokens (tailwind.config.js)Color Palette:bg-primary: #F7F5F0 (Warm Alabaster / Cream)bg-secondary: #EFECE6 (Soft Linen)text-primary: #1C1B19 (Deep Charcoal)text-muted: #6E6A63 (Warm Stone)accent: #8C6D53 (Muted Bronze / Terracotta)border-subtle: #E2DDD5 (Warm Sand)Typography:Display / Headings: Cormorant Garamond (Editorial Serif, weights 400, 500, 600, italic)Body / UI / Labels: Plus Jakarta Sans (Clean Sans-Serif, weights 300, 400, 500, 600)4. Database Architecture (api/schema.sql)Table NameColumnsPurposeprojectsid, title, slug (UNIQUE), category, location, area_sqft, year, client_brief, design_solution, cover_image, tour_3d_url, tour_type, is_featured, sort_order, created_atStores core case study info and the optional Matterport / Kuula / Sketchfab 3D embed URL.project_galleryid, project_id (FK), image_path, caption, sort_orderStores high-resolution WebP photographs for each project.before_afterid, project_id (FK), before_image, after_image, room_labelStores paired renovation shots for the draggable comparison slider.inquiriesid, full_name, email, phone, property_type, area_sqft, budget_range, city, message, status, created_atStores consultation requests submitted from /contact.admin_usersid, username, password_hash, created_atStores hashed credentials for the client's PHP CMS portal.5. Step-by-Step Build & Execution PhasesPhase 1: Design System & Frontend FoundationInitialize Vite + React Project: Install react-router-dom, framer-motion, lucide-react, and react-helmet-async.Configure Boutique Theme: Set up Tailwind CSS color tokens, Google Fonts (Cormorant Garamond & Plus Jakarta Sans), and smooth scroll/fade-up utility classes.API Service with Local Fallback (src/services/api.js): Build a fetch wrapper pointing to /api that gracefully falls back to mockData.js when running locally on Vite without a PHP server, ensuring instant browser preview.Phase 2: PHP Backend & Client Admin CMS (/api)Database & REST APIs:api/config/db.php: PDO connection with prepared statement defaults and JSON headers.api/projects.php: Fetches all projects (supports ?featured=1 and ?category=...), returning a has_3d_tour boolean flag for portfolio badges.api/project-detail.php: Fetches single project details by slug, along with gallery images, before_after pairs, and tour_3d_url.api/inquiry.php: Validates POST requests, inserts leads into inquiries, and dispatches email alerts via cPanel SMTP / PHP mail.Client Admin Portal (api/admin/):Password-protected PHP dashboard (password_verify) allowing the interior designer to:Create/edit/delete projects.Bulk-upload photos (auto-converted to compressed .webp via PHP GD imagewebp()).Upload Before & After renovation photo pairs.Paste their 3D Tour Embed URL (Matterport, Kuula, Sketchfab, or Spline).View and manage client consultation leads.Phase 3: Multipage Frontend & 3D Gallery Engine (src/)Shared Shell: Transparent-over-hero Navbar transitioning to solid cream on scroll, editorial Footer, WhatsAppFloat, and ScrollToTop.Core Pages:Home.jsx (/): Full-viewport hero, studio manifesto, asymmetric featured work grid, services strip, testimonials.Portfolio.jsx (/work): Instant category filter pills (All, Residential, Commercial, Turnkey, Renovation) and editorial project cards with "3D Tour Available" indicators.Services.jsx (/services): Service breakdown, 4-step workflow timeline (Discovery → Concept & 3D → Material Sourcing → Handover), and FAQ accordion.About.jsx (/about): Founder portrait, design philosophy, metrics, and press features.Contact.jsx (/contact): Validated consultation form (Property Type, Carpet Area, Budget, City) + direct WhatsApp button.Project Case Study (/work/:slug) & 3D Gallery (ProjectGallery.jsx):Displays project hero, brief/solution split block, and a tabbed Gallery Mode Switcher:Tab 1 — Curated Photography: Editorial grid with lightbox zoom.Tab 2 — Before & After: Interactive touch/mouse draggable comparison slider (BeforeAfterSlider.jsx).Tab 3 — Interactive 3D Map / Tour: Automatically enabled when tour_3d_url exists. Renders Tour3DEmbed.jsx—a responsive 16:9 container with a click-to-interact overlay (prevents accidental scroll-hijacking while browsing) and full-screen mode.Phase 4: cPanel Production Deployment (public_html/)Database Setup: Create MySQL database & user in cPanel MySQL Database Wizard, import api/schema.sql via phpMyAdmin, and update api/config/db.php.Build & Upload:Run npm run build locally to generate dist/.Upload the contents of dist/ (index.html, assets/, .htaccess) directly into public_html/.Upload the api/ folder into public_html/api/ and ensure api/uploads/ has 755 permissions.Apache SPA + API Routing (public_html/.htaccess):<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /

  # Allow direct access to PHP API and uploaded media
  RewriteCond %{REQUEST_URI} ^/(api|uploads) [NC,OR]
  RewriteCond %{REQUEST_FILENAME} -f [OR]
  RewriteCond %{REQUEST_FILENAME} -d
  RewriteRule ^ - [L]

  # Route all React multipage paths (/work, /services, /about, /contact) to index.html
  RewriteRule ^ index.html [L]
</IfModule>
SSL & Final QA: Enable AutoSSL in cPanel, verify deep-link page refreshes (/work/sample-project), test the 3D tour iframe on desktop/mobile, and test lead form submissions.