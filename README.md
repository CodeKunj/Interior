# RiTiVerse &bull; ATELIER VÉRITÉ — Architecture & Luxury Interior Sanctuaries

A production-ready multipage website and modular plain PHP 8+ REST API backend for an upscale Interior Design Studio, inspired by the editorial luxury aesthetic of **[Webild Boutique Hotel Template](https://www.webild.io/templates/boutique-hotel)**, tailored with a **Calacatta Marble White background (`#FAF9F5`), soft warm neutrals, Bodoni Moda luxury serif typography, and an interactive 3D virtual tour player**.

---

## 1. Visual Design System: Calacatta Marble & Editorial Soft Neutrals

Crafted specifically for upscale interior architecture studios:

- **Marble White Color Palette:**
  - **Background Primary:** `#FAF9F5` (Calacatta / Alabaster Marble White — luminous, soft, daylight ambiance)
  - **Background Secondary:** `#F5F3EE` (Soft Travertine / Limestone)
  - **Surface & Cards:** `#FFFFFF` (Crisp Pearl White with subtle sand borders `#E5E0D8` and soft shadows)
  - **Text Primary:** `#1C1B19` (Deep Charcoal Obsidian — crisp contrast, editorial legibility)
  - **Text Muted / Stone:** `#6E6A63` (Warm Cashmere Stone Gray)
  - **Accent:** `#8C6D53` (Muted Bronze / Terracotta)
  - **Borders & Dividers:** `#E5E0D8` / `#E2DDD5` (Subtle Sand & Vein line)
  - **Pill Badges:** Translucent bronze tint (`rgba(140, 109, 83, 0.08)` with `#8C6D53` text and `#E2DDD5` borders)
- **High-Contrast Editorial Typography:**
  - **Headings & Brand Titles:** `Bodoni Moda` (High-contrast Italian luxury maison serif with full uppercase & lowercase ball terminals: `font-serif text-5xl sm:text-7xl md:text-9xl`)
  - **Body & Technical Specs:** `Plus Jakarta Sans` (Clean, contemporary high-legibility sans-serif)
- **Motion & Micro-Interactions:**
  - **Lenis Inertial Scrolling** for fluid editorial pacing.
  - **Split-Text Roll-Up Animation:** Button letters roll upwards on hover with bronze accent reveal.
  - **Parallax Image Drifting:** Framer Motion `useScroll` with dual-plane depth effects.

---

## 2. Refined Header & Component Architecture

1. **`NavbarFullscreen.jsx` (Floating Marble Glass Header):**
   - Floating pill with `bg-white/90` and `backdrop-blur-xl`, hairlike sand border `#E2DDD5`.
   - **Left:** Refined serif brand mark `RiTiVerse` with delicate subscript `ATELIER VÉRITÉ` (no awkward badge wrapping).
   - **Center:** Clean, concise single-word links (`Portfolio`, `Services`, `Journeys`, `Philosophy`) with `whitespace-nowrap` that never wrap lines or collide with buttons.
   - **Right:** High-contrast `Book Consultation ↗` pill button (`bg-[#1C1B19] text-[#FAF9F5]` hovering to soft bronze) + circular menu trigger.
   - **Fullscreen Overlay Menu:** Cinematic full-page overlay with numbered editorial links (`01 Selected Works`, `02 Disciplines & Process`, `03 Architectural Journeys`, `04 Studio Philosophy`, `05 Private Commissions`), direct WhatsApp desk, and studio locations.
2. **`HeroExpand.jsx`:**
   - Architectural photography at 100% full opacity with multi-image fallback and subtle ambient film.
   - Title `RiTiVerse`, studio pill badge, and split-text roll action buttons.
3. **`AboutParallax.jsx`:**
   - Layered dual-image composition with inverse parallax drifting.
4. **`FeaturesAttributeCards.jsx`:**
   - 3-column sanctuary cards on soft travertine (`#F5F3EE`) with room specs, badges, and 3D tour indicators.
5. **`FeaturesMediaGrid.jsx`:**
   - 5-card bento media grid highlighting turnkey execution, digital twins, and artisan stone sourcing.
6. **`FeaturesRevealCardsBento.jsx`:**
   - 7-card bento grid displaying residential transformations across New York, Amalfi, Kyoto, London, and Paris.
7. **`ContactSplitForm.jsx`:**
   - Split 2-column contact section (pure white form card + full-bleed photograph).
8. **`FooterBrand.jsx`:**
   - Monumental brand typography (`RiTiVerse`) in soft linen (`#EFECE6`) with chevron columns and CMS link.
9. **`VirtualTourEmbed.jsx` (Critical 3D Feature):**
   - Responsive 16:9 3D virtual tour player supporting Matterport, Kuula, Sketchfab, and Spline embeds with full-screen toggle.
10. **`BeforeAfterSlider.jsx`:**
    - Interactive draggable comparison slider for renovation transformations.
11. **`ProjectGallery.jsx`:**
    - Curated photo grid with full-screen architectural lightbox modal.

---

## 3. Multipage Routing (`src/pages/`)

- **`/` (Home):** Complete boutique hotel editorial flow.
- **`/work` (Portfolio):** Category filter bar (*All, Residential, Commercial, Turnkey, Renovation*), 3D tour indicators, and specifications.
- **`/work/:slug` (Project Detail & Case Study):**
  - Hero image, Client Brief, and Design Solution split block.
  - **3-Tab View Switcher:**
    1. *Curated Photography* (High-res photo grid with lightbox).
    2. *Before & After* (Interactive draggable comparison slider).
    3. *Interactive 3D Virtual Tour* (Embedded 16:9 Matterport / Sketchfab / Kuula player).
- **`/services` (Services & Process):** Detailed service offerings, 4-step roadmap, and interactive FAQ accordion.
- **`/journeys` (Architectural Journeys):** Case study transformations, typology filters, materiality badges, and interactive spotlight before/after slider.
- **`/about` (Studio Heritage):** Founder Elena Vance story, studio metrics, and press accolades.
- **`/contact` (Consultation):** Consultation inquiry form submitting via POST to `/api/inquiry.php` with direct WhatsApp desk.

---

## 4. Well-Structured PHP 8+ REST API Backend (`/api`)

Built with **zero heavy frameworks**, completely lightweight, and optimized to run out-of-the-box on standard cPanel shared/cloud hosting with PHP 8+.

### Architecture Structure
```
api/
├── bootstrap.php            <- PSR-4 native autoloader for Atelier\ namespace, CORS headers & JSON exception handler
├── config/
│   ├── app.php              <- App configuration, upload limits, CORS origins, mail settings
│   ├── Database.php         <- Singleton PDO connection manager with strict error modes & transactions
│   └── db.php               <- Legacy compatibility bridge
├── core/
│   ├── Response.php         <- Standardized JSON response emitter (success, error, notFound, validationFailed, etc.)
│   ├── Request.php          <- HTTP method, query, JSON body, sanitization & bearer token helpers
│   ├── Validator.php        <- Declarative validation rules (required, email, numeric, url, in, min, max)
│   ├── Mailer.php           <- Native PHP mail() consultation notifier with responsive HTML & text templates
│   └── Uploader.php         <- Secure image uploader with MIME verification via finfo, size caps & .htaccess protection
├── models/
│   ├── BaseModel.php        <- Abstract base model with PDO execution helpers
│   ├── Project.php          <- CRUD, category filtering, search, slug generation, 3D tour formatting
│   ├── ProjectGallery.php   <- Gallery photography repository (ordered images, add, delete)
│   ├── BeforeAfter.php      <- Renovation transformation pairs for comparison sliders
│   └── Inquiry.php          <- Consultation inquiry repository with status tracking (new, contacted, scheduled, archived)
├── controllers/
│   ├── ProjectController.php  <- GET /api/projects.php and GET /api/project-detail.php
│   ├── InquiryController.php  <- POST /api/inquiry.php with validation, database insertion & mail notification
│   └── CategoryController.php <- GET /api/categories.php with counts
├── projects.php             <- Slim REST endpoint
├── project-detail.php       <- Slim REST endpoint
├── inquiry.php              <- Slim REST endpoint
├── categories.php           <- Slim REST endpoint
├── upload.php               <- Secure file upload endpoint for studio imagery
├── admin/
│   └── index.php            <- Password-protected Calacatta Marble CMS portal with live 3D preview & direct upload
└── schema.sql               <- MySQL schema with 6 luxury interior projects, galleries, before/after pairs & seeds
```

---

## 5. cPanel Shared Hosting Deployment Guide

### Target File Layout on cPanel (`public_html/`)
```
public_html/
├── .htaccess                 <- Routes SPA paths to index.html, preserves /api and /uploads
├── index.html                <- From dist/index.html
├── assets/                   <- From dist/assets/
│   ├── index-*.js
│   └── index-*.css
├── uploads/                  <- Directory for uploaded project photography (chmod 755)
│   └── .htaccess             <- Auto-generated by Uploader.php to block PHP script execution
└── api/                      <- Plain PHP 8+ REST API
    ├── bootstrap.php
    ├── schema.sql
    ├── config/
    │   └── app.php           <- Update MySQL credentials here
    ├── core/
    ├── models/
    ├── controllers/
    └── admin/
        └── index.php         <- Studio CMS (default: admin / atelier2026Password!)
```

### Steps to Deploy:
1. **Import Database:**
   - Log into cPanel, open **phpMyAdmin**.
   - Create a database (e.g. `cpaneluser_atelier`).
   - Click **Import** and select `api/schema.sql`.
2. **Update Database Credentials:**
   - Open `api/config/app.php` (or `api/config/db.php`).
   - Fill in your database name, username, and password:
     ```php
     'database' => [
         'host'     => 'localhost',
         'name'     => 'cpaneluser_atelier',
         'user'     => 'cpaneluser_dbuser',
         'pass'     => 'YourSecurePasswordHere',
         'port'     => 3306,
     ],
     ```
3. **Build Frontend Bundle:**
   ```bash
   npm run build
   ```
4. **Upload to cPanel:**
   - Upload all files from the local `dist/` directory directly into `public_html/`.
   - Upload the `api/` folder into `public_html/api/`.
   - Ensure `public_html/.htaccess` exists (automatically copied from `public/.htaccess`).
5. **Access Studio CMS:**
   - Navigate to `https://yourdomain.com/api/admin/index.php`.
   - Log in with default credentials: `admin` / `atelier2026Password!`.
   - Create or edit projects, upload high-res images, and paste Matterport / Sketchfab / Kuula 3D tour URLs with live iframe preview.

---

## 6. Local Development

```bash
# Start Vite development server (Instant preview with mock fallback data)
npm run dev

# Compile production bundle into dist/
npm run build
```
When running on the Vite development server without a live PHP/MySQL environment, `src/services/api.js` automatically uses the local luxury mock dataset with working 3D tour embeds. When deployed to cPanel, it automatically communicates with the live PHP REST API.
