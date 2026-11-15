<?php
/**
 * Atelier Vérité CMS — Architectural Projects, 3D Tours & Galleries Manager
 * Comprehensive CRUD, WebP photo uploading, Before/After sliders, and Matterport/Sketchfab embeds.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/upload-handler.php';

use Atelier\Models\Project;
use Atelier\Models\ProjectGallery;
use Atelier\Models\BeforeAfter;

require_admin_auth();

$projectModel = new Project();
$galleryModel = new ProjectGallery();
$beforeAfterModel = new BeforeAfter();

$feedback = '';
$feedbackType = 'success';
$action = $_GET['action'] ?? 'list';
$projectId = intval($_GET['id'] ?? 0);

// -------------------------------------------------------------
// POST HANDLERS
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['post_action'] ?? '';

    // 1. SAVE PROJECT (CREATE OR UPDATE)
    if ($postAction === 'save_project') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $coverImage = trim($_POST['cover_image'] ?? '');
        $heroImage = trim($_POST['hero_image'] ?? '');

        // Handle Cover File Upload with WebP conversion
        if (!empty($_FILES['cover_file']['name'])) {
            try {
                $up = handle_image_upload_to_webp($_FILES['cover_file'], 'projects', 85);
                $coverImage = $up['url'];
            } catch (\Throwable $e) {
                $feedback = 'Cover image upload warning: ' . $e->getMessage();
                $feedbackType = 'error';
            }
        }

        // Handle Hero File Upload with WebP conversion
        if (!empty($_FILES['hero_file']['name'])) {
            try {
                $up = handle_image_upload_to_webp($_FILES['hero_file'], 'projects', 85);
                $heroImage = $up['url'];
            } catch (\Throwable $e) {
                $feedback = 'Hero image upload warning: ' . $e->getMessage();
                $feedbackType = 'error';
            }
        }

        if (empty($heroImage)) {
            $heroImage = $coverImage;
        }

        $projectData = [
            'title'           => $title,
            'slug'            => trim($_POST['slug'] ?? ''),
            'subtitle'        => trim($_POST['subtitle'] ?? ''),
            'category'        => trim($_POST['category'] ?? 'Residential'),
            'location'        => trim($_POST['location'] ?? ''),
            'area_sqft'       => intval($_POST['area_sqft'] ?? 0),
            'year'            => intval($_POST['year'] ?? date('Y')),
            'cover_image'     => $coverImage,
            'hero_image'      => $heroImage,
            'client_brief'    => trim($_POST['client_brief'] ?? ''),
            'design_solution' => trim($_POST['design_solution'] ?? ''),
            'tour_3d_url'     => trim($_POST['tour_3d_url'] ?? ''),
            'tour_type'       => trim($_POST['tour_type'] ?? 'matterport'),
            'featured'        => !empty($_POST['featured']) ? 1 : 0
        ];

        try {
            if ($id > 0) {
                $projectModel->update($id, $projectData);
                $feedback = "Project '{$title}' updated successfully.";
                $action = 'edit';
                $projectId = $id;
            } else {
                $newId = $projectModel->create($projectData);
                header("Location: projects-manage.php?action=edit&id={$newId}&msg=created");
                exit();
            }
        } catch (\Throwable $e) {
            $feedback = 'Error saving project: ' . $e->getMessage();
            $feedbackType = 'error';
        }
    }

    // 2. ADD GALLERY PHOTO
    if ($postAction === 'add_gallery_photo') {
        $projId = intval($_POST['project_id'] ?? 0);
        $imageUrl = trim($_POST['gallery_image_url'] ?? '');
        $caption = trim($_POST['caption'] ?? '');
        $displayOrder = intval($_POST['display_order'] ?? 0);

        if (!empty($_FILES['gallery_file']['name'])) {
            try {
                $up = handle_image_upload_to_webp($_FILES['gallery_file'], 'gallery', 85);
                $imageUrl = $up['url'];
            } catch (\Throwable $e) {
                $feedback = 'Gallery upload error: ' . $e->getMessage();
                $feedbackType = 'error';
            }
        }

        if (!empty($imageUrl) && $projId > 0) {
            try {
                $galleryModel->add($projId, $imageUrl, $caption ?: null, $displayOrder);
                header("Location: projects-manage.php?action=edit&id={$projId}&subtab=gallery&msg=photo_added");
                exit();
            } catch (\Throwable $e) {
                $feedback = 'Failed to save gallery photo: ' . $e->getMessage();
                $feedbackType = 'error';
            }
        }
    }

    // 3. ADD BEFORE & AFTER PAIR
    if ($postAction === 'add_before_after') {
        $projId = intval($_POST['project_id'] ?? 0);
        $roomTitle = trim($_POST['room_title'] ?? 'Renovation Space');
        $beforeImage = trim($_POST['before_image_url'] ?? '');
        $afterImage = trim($_POST['after_image_url'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        // Before Image upload
        if (!empty($_FILES['before_file']['name'])) {
            try {
                $up = handle_image_upload_to_webp($_FILES['before_file'], 'before-after', 85);
                $beforeImage = $up['url'];
            } catch (\Throwable $e) {
                $feedback = 'Before image error: ' . $e->getMessage();
                $feedbackType = 'error';
            }
        }

        // After Image upload
        if (!empty($_FILES['after_file']['name'])) {
            try {
                $up = handle_image_upload_to_webp($_FILES['after_file'], 'before-after', 85);
                $afterImage = $up['url'];
            } catch (\Throwable $e) {
                $feedback = 'After image error: ' . $e->getMessage();
                $feedbackType = 'error';
            }
        }

        if (!empty($beforeImage) && !empty($afterImage) && $projId > 0) {
            try {
                $beforeAfterModel->add($projId, $roomTitle, $beforeImage, $afterImage, $desc ?: null);
                header("Location: projects-manage.php?action=edit&id={$projId}&subtab=before_after&msg=pair_added");
                exit();
            } catch (\Throwable $e) {
                $feedback = 'Failed to save comparison pair: ' . $e->getMessage();
                $feedbackType = 'error';
            }
        } else {
            $feedback = 'Please provide both Before and After images.';
            $feedbackType = 'error';
        }
    }
}

// -------------------------------------------------------------
// GET ACTION HANDLERS (DELETE, ETC.)
// -------------------------------------------------------------
if ($action === 'delete' && $projectId > 0) {
    try {
        $projectModel->delete($projectId);
        header("Location: projects-manage.php?msg=deleted");
        exit();
    } catch (\Throwable $e) {
        $feedback = 'Error deleting project: ' . $e->getMessage();
        $feedbackType = 'error';
    }
}

if ($action === 'delete_gallery_photo') {
    $imgId = intval($_GET['img_id'] ?? 0);
    $projId = intval($_GET['project_id'] ?? 0);
    if ($imgId > 0) {
        $galleryModel->delete($imgId);
        header("Location: projects-manage.php?action=edit&id={$projId}&subtab=gallery&msg=photo_deleted");
        exit();
    }
}

if ($action === 'delete_before_after') {
    $pairId = intval($_GET['pair_id'] ?? 0);
    $projId = intval($_GET['project_id'] ?? 0);
    if ($pairId > 0) {
        $beforeAfterModel->delete($pairId);
        header("Location: projects-manage.php?action=edit&id={$projId}&subtab=before_after&msg=pair_deleted");
        exit();
    }
}

// Flash messages from redirects
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'created') $feedback = 'New architectural project created successfully.';
    if ($_GET['msg'] === 'deleted') $feedback = 'Project permanently deleted.';
    if ($_GET['msg'] === 'photo_added') $feedback = 'Gallery photography added successfully.';
    if ($_GET['msg'] === 'photo_deleted') $feedback = 'Gallery photo removed.';
    if ($_GET['msg'] === 'pair_added') $feedback = 'Before & After comparison slider pair saved.';
    if ($_GET['msg'] === 'pair_deleted') $feedback = 'Comparison slider pair removed.';
}

// Load data based on view
$allProjects = [];
$currentProject = null;
$galleryPhotos = [];
$beforeAfterPairs = [];
$subtab = $_GET['subtab'] ?? 'details';

if ($action === 'list') {
    $filterCategory = trim($_GET['category'] ?? '');
    $search = trim($_GET['search'] ?? '');
    $allProjects = $projectModel->getAll([
        'category' => $filterCategory,
        'search'   => $search
    ]);
} elseif ($action === 'edit' && $projectId > 0) {
    $currentProject = $projectModel->findById($projectId);
    if ($currentProject) {
        $galleryPhotos = $galleryModel->getByProjectId($projectId);
        $beforeAfterPairs = $beforeAfterModel->getByProjectId($projectId);
    }
}

render_admin_head('Projects & 3D Tours', 'projects');
render_admin_nav('projects');
?>

<main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full space-y-8">

    <!-- Flash message -->
    <?php if ($feedback): ?>
        <div class="<?= $feedbackType === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800' ?> border p-4 rounded-2xl text-xs flex items-center justify-between shadow-sm">
            <span><?= htmlspecialchars($feedback) ?></span>
            <button onclick="this.parentElement.remove()" class="text-stone hover:text-charcoal font-bold text-base px-2">&times;</button>
        </div>
    <?php endif; ?>

    <!-- VIEW 1: PROJECTS LIST -->
    <?php if ($action === 'list'): ?>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-[10px] uppercase tracking-[0.25em] text-accent font-semibold block mb-1">Portfolio Dossiers</span>
                <h1 class="font-serif text-3xl sm:text-4xl text-charcoal">Architectural Sanctuaries</h1>
                <p class="text-stone text-xs font-light mt-1">Manage project case studies, high-res photography, and interactive 3D virtual tours.</p>
            </div>
            <a href="projects-manage.php?action=new" class="inline-flex items-center space-x-2 bg-charcoal text-white hover:bg-accent px-6 py-3 rounded-full text-xs uppercase tracking-[0.18em] font-semibold transition-all shadow-md">
                <span>+ Create Sanctuary</span>
            </a>
        </div>

        <!-- Filter bar -->
        <div class="bg-white border border-sand rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center flex-wrap gap-2 text-xs">
                <?php 
                $activeCat = trim($_GET['category'] ?? '');
                $cats = ['All', 'Residential', 'Commercial', 'Turnkey', 'Renovation', 'Penthouse'];
                foreach ($cats as $cat): 
                    $isSelected = ($cat === 'All' && empty($activeCat)) || (strtolower($cat) === strtolower($activeCat));
                ?>
                    <a href="projects-manage.php?category=<?= $cat === 'All' ? '' : urlencode($cat) ?>" class="px-3.5 py-1.5 rounded-full text-[11px] uppercase tracking-wider transition-colors <?= $isSelected ? 'bg-charcoal text-white font-medium shadow-sm' : 'bg-linen hover:bg-sand text-stone hover:text-charcoal' ?>">
                        <?= $cat ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search box -->
            <form method="GET" class="w-full sm:w-64">
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search projects..." class="w-full bg-marble border border-sand rounded-xl px-4 py-2 text-xs text-charcoal focus:outline-none focus:border-accent">
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white border border-sand rounded-3xl overflow-hidden shadow-[0_4px_25px_rgba(28,27,25,0.03)]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-linen text-stone text-[10px] uppercase tracking-[0.18em] border-b border-sand">
                        <tr>
                            <th class="p-4 pl-6">Cover</th>
                            <th class="p-4">Title & Slug</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Location</th>
                            <th class="p-4">Area / Year</th>
                            <th class="p-4">3D Tour Player</th>
                            <th class="p-4 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sand/60">
                        <?php if (empty($allProjects)): ?>
                            <tr>
                                <td colspan="7" class="p-12 text-center text-stone italic text-xs">
                                    No architectural sanctuaries found. Click "+ Create Sanctuary" above to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allProjects as $p): ?>
                                <tr class="hover:bg-marble/60 transition-colors">
                                    <td class="p-4 pl-6 w-20">
                                        <img src="<?= htmlspecialchars($p['cover_image']) ?>" class="w-16 h-12 object-cover rounded-xl border border-sand shadow-sm" alt="" onerror="this.src='https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=300&q=80'">
                                    </td>
                                    <td class="p-4 font-medium text-charcoal">
                                        <div class="font-serif text-sm text-charcoal"><?= htmlspecialchars($p['title']) ?></div>
                                        <div class="text-[11px] text-stone font-mono">/work/<?= htmlspecialchars($p['slug']) ?></div>
                                        <?php if (!empty($p['featured'])): ?>
                                            <span class="inline-block mt-1 bg-accentLight text-accent text-[9px] px-2 py-0.5 rounded-full uppercase tracking-wider font-semibold border border-accent/20">
                                                Home Featured
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-stone"><?= htmlspecialchars($p['category']) ?></td>
                                    <td class="p-4 text-stone"><?= htmlspecialchars($p['location']) ?></td>
                                    <td class="p-4 text-stone">
                                        <?= number_format($p['area_sqft']) ?> sq. ft.<br>
                                        <span class="text-[10px] text-stone/80"><?= $p['year'] ?></span>
                                    </td>
                                    <td class="p-4">
                                        <?php if (!empty($p['tour_3d_url'])): ?>
                                            <span class="inline-flex items-center text-[10px] text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 font-medium">
                                                &check; <?= strtoupper(htmlspecialchars($p['tour_type'] ?? '3D')) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-stone/60">— None —</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 pr-6 text-right space-x-3 whitespace-nowrap">
                                        <a href="/work/<?= htmlspecialchars($p['slug']) ?>" target="_blank" class="text-stone hover:text-charcoal text-[11px] uppercase font-medium">
                                            View &nearr;
                                        </a>
                                        <a href="projects-manage.php?action=edit&id=<?= $p['id'] ?>" class="text-accent hover:text-charcoal text-[11px] uppercase font-semibold">
                                            Edit
                                        </a>
                                        <a href="projects-manage.php?action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Permanently delete project \'<?= addslashes($p['title']) ?>\'?')" class="text-red-500 hover:text-red-700 text-[11px] uppercase font-medium">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- VIEW 2: ADD / EDIT PROJECT -->
    <?php elseif ($action === 'new' || $action === 'edit'): ?>
        <?php $isEdit = ($action === 'edit' && $currentProject); ?>

        <div class="flex items-center justify-between">
            <a href="projects-manage.php" class="inline-flex items-center space-x-2 text-stone hover:text-charcoal text-xs uppercase tracking-wider font-medium">
                <span>&larr; Back to Sanctuaries List</span>
            </a>
            <?php if ($isEdit): ?>
                <a href="/work/<?= htmlspecialchars($currentProject['slug']) ?>" target="_blank" class="inline-flex items-center space-x-1.5 text-accent hover:text-charcoal text-xs font-semibold">
                    <span>View Public Case Study</span>
                    <span>&nearr;</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Header -->
        <div>
            <span class="text-[10px] uppercase tracking-[0.25em] text-accent font-semibold block mb-1">
                <?= $isEdit ? 'Modify Architectural Dossier' : 'New Commission Showcase' ?>
            </span>
            <h1 class="font-serif text-3xl sm:text-4xl text-charcoal">
                <?= $isEdit ? 'Edit: ' . htmlspecialchars($currentProject['title']) : 'Create New Studio Project' ?>
            </h1>
        </div>

        <!-- Subtabs for Edit Mode -->
        <?php if ($isEdit): ?>
            <div class="flex border-b border-sand space-x-8 text-xs font-medium">
                <a href="projects-manage.php?action=edit&id=<?= $projectId ?>&subtab=details" class="pb-3 border-b-2 uppercase tracking-[0.18em] transition-colors <?= $subtab === 'details' ? 'border-accent text-charcoal font-semibold' : 'border-transparent text-stone hover:text-charcoal' ?>">
                    1. Core Details & 3D Tour
                </a>
                <a href="projects-manage.php?action=edit&id=<?= $projectId ?>&subtab=gallery" class="pb-3 border-b-2 uppercase tracking-[0.18em] transition-colors <?= $subtab === 'gallery' ? 'border-accent text-charcoal font-semibold' : 'border-transparent text-stone hover:text-charcoal' ?>">
                    2. Photography Gallery (<?= count($galleryPhotos) ?>)
                </a>
                <a href="projects-manage.php?action=edit&id=<?= $projectId ?>&subtab=before_after" class="pb-3 border-b-2 uppercase tracking-[0.18em] transition-colors <?= $subtab === 'before_after' ? 'border-accent text-charcoal font-semibold' : 'border-transparent text-stone hover:text-charcoal' ?>">
                    3. Before & After Pairs (<?= count($beforeAfterPairs) ?>)
                </a>
            </div>
        <?php endif; ?>

        <!-- SUBTAB A: CORE DETAILS & 3D TOUR -->
        <?php if (!$isEdit || $subtab === 'details'): ?>
            <div class="bg-white border border-sand rounded-3xl p-8 sm:p-10 shadow-[0_4px_25px_rgba(28,27,25,0.03)]">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="post_action" value="save_project">
                    <input type="hidden" name="id" value="<?= $isEdit ? $currentProject['id'] : 0 ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Project Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($currentProject['title'] ?? '') ?>" required class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent" placeholder="e.g. The Grand Suite">
                        </div>
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">URL Slug (Auto-generated if empty)</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($currentProject['slug'] ?? '') ?>" class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent" placeholder="the-grand-suite">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Design Concept Subtitle</label>
                        <input type="text" name="subtitle" value="<?= htmlspecialchars($currentProject['subtitle'] ?? '') ?>" class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent" placeholder="Panoramic, Terrace, Italian Travertine">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Category</label>
                            <select name="category" class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                                <?php 
                                $cats = ['Residential', 'Commercial', 'Turnkey', 'Renovation', 'Penthouse'];
                                foreach ($cats as $c): ?>
                                    <option value="<?= $c ?>" <?= ($currentProject['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Location</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($currentProject['location'] ?? '') ?>" required class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent" placeholder="Tribeca, New York">
                        </div>
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Carpet Area (sq. ft.)</label>
                            <input type="number" name="area_sqft" value="<?= htmlspecialchars((string)($currentProject['area_sqft'] ?? 3500)) ?>" required class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Completion Year</label>
                            <input type="number" name="year" value="<?= htmlspecialchars((string)($currentProject['year'] ?? date('Y'))) ?>" required class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                        </div>
                    </div>

                    <!-- Imagery: URL input + WebP File Upload -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-linen/50 rounded-2xl border border-sand">
                        <div class="space-y-3">
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone font-medium">Cover Image</label>
                            <input type="text" name="cover_image" id="cover_image_input" value="<?= htmlspecialchars($currentProject['cover_image'] ?? '') ?>" class="w-full bg-white border border-sand rounded-xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-accent" placeholder="URL or leave to upload below">
                            <div class="text-[10px] text-stone">Or upload high-res photo (auto-converted to WebP):</div>
                            <input type="file" name="cover_file" accept="image/*" class="w-full text-xs text-stone file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:uppercase file:tracking-wider file:font-semibold file:bg-charcoal file:text-white hover:file:bg-accent file:cursor-pointer">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone font-medium">Hero Image (optional, defaults to cover)</label>
                            <input type="text" name="hero_image" id="hero_image_input" value="<?= htmlspecialchars($currentProject['hero_image'] ?? '') ?>" class="w-full bg-white border border-sand rounded-xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-accent" placeholder="Hero Banner URL">
                            <div class="text-[10px] text-stone">Or upload hero banner (auto-converted to WebP):</div>
                            <input type="file" name="hero_file" accept="image/*" class="w-full text-xs text-stone file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:uppercase file:tracking-wider file:font-semibold file:bg-charcoal file:text-white hover:file:bg-accent file:cursor-pointer">
                        </div>
                    </div>

                    <!-- 3D Virtual Tour Embed Section with Live Previewer -->
                    <div class="rounded-2xl border border-sand bg-marble p-6 sm:p-8 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="font-serif text-2xl text-charcoal">Interactive 3D Virtual Tour Embed</h3>
                                <p class="text-xs text-stone font-light">Paste the embed URL from Matterport, Sketchfab, Kuula, or Spline.</p>
                            </div>
                            <span class="text-[9px] uppercase tracking-[0.2em] bg-accentLight px-3 py-1 rounded-full border border-accent/20 text-accent font-semibold self-start sm:self-auto">
                                Matterport &bull; Kuula &bull; Sketchfab
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1 font-medium">3D Tour Player URL</label>
                                <input type="url" name="tour_3d_url" id="tour_url_input" value="<?= htmlspecialchars($currentProject['tour_3d_url'] ?? '') ?>" class="w-full bg-white border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent" placeholder="https://my.matterport.com/show/?m=... or https://sketchfab.com/models/.../embed">
                            </div>
                            <div>
                                <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1 font-medium">Provider Type</label>
                                <select name="tour_type" class="w-full bg-white border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                                    <option value="matterport" <?= ($currentProject['tour_type'] ?? '') === 'matterport' ? 'selected' : '' ?>>Matterport</option>
                                    <option value="sketchfab" <?= ($currentProject['tour_type'] ?? '') === 'sketchfab' ? 'selected' : '' ?>>Sketchfab</option>
                                    <option value="kuula" <?= ($currentProject['tour_type'] ?? '') === 'kuula' ? 'selected' : '' ?>>Kuula</option>
                                    <option value="iframe" <?= ($currentProject['tour_type'] ?? '') === 'iframe' ? 'selected' : '' ?>>Other Iframe</option>
                                </select>
                            </div>
                        </div>

                        <!-- Live 3D Previewer -->
                        <div class="mt-4 pt-4 border-t border-sand">
                            <span class="text-[11px] uppercase tracking-[0.16em] text-stone font-medium block mb-2">Live 3D Embed Player Preview:</span>
                            <div class="aspect-video w-full max-w-2xl bg-[#1C1B19] rounded-2xl overflow-hidden border border-sand shadow-inner relative flex items-center justify-center text-stone text-xs">
                                <?php if (!empty($currentProject['tour_3d_url'])): ?>
                                    <iframe id="live_preview_iframe" src="<?= htmlspecialchars($currentProject['tour_3d_url']) ?>" class="w-full h-full border-0" allow="fullscreen; xr-spatial-tracking" loading="lazy"></iframe>
                                <?php else: ?>
                                    <span id="preview_placeholder" class="text-stone/70">Paste a 3D tour embed link above to preview player</span>
                                    <iframe id="live_preview_iframe" src="" class="w-full h-full border-0 hidden" allow="fullscreen; xr-spatial-tracking"></iframe>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Editorial Case Study Content -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Client Brief & Spatial Demands</label>
                            <textarea name="client_brief" rows="5" required class="w-full bg-marble border border-sand rounded-xl p-4 text-xs text-charcoal focus:outline-none focus:border-accent leading-relaxed"><?= htmlspecialchars($currentProject['client_brief'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.16em] text-stone mb-1.5 font-medium">Design Solution & Materiality</label>
                            <textarea name="design_solution" rows="5" required class="w-full bg-marble border border-sand rounded-xl p-4 text-xs text-charcoal focus:outline-none focus:border-accent leading-relaxed"><?= htmlspecialchars($currentProject['design_solution'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <input type="checkbox" id="featured" name="featured" value="1" <?= !empty($currentProject['featured']) ? 'checked' : '' ?> class="h-4 w-4 text-accent border-sand rounded accent-accent">
                        <label for="featured" class="text-xs text-charcoal font-medium">Feature this sanctuary prominently in the Home Page editorial showcase</label>
                    </div>

                    <div class="flex items-center space-x-4 pt-6 border-t border-sand">
                        <button type="submit" class="bg-charcoal text-white hover:bg-accent transition-colors px-8 py-3.5 rounded-full text-xs uppercase tracking-[0.2em] font-semibold shadow-lg">
                            <?= $isEdit ? 'Update Sanctuary' : 'Publish Sanctuary' ?>
                        </button>
                        <a href="projects-manage.php" class="text-stone hover:text-charcoal text-xs uppercase tracking-[0.18em]">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- SUBTAB B: CURATED PHOTOGRAPHY GALLERY -->
        <?php if ($isEdit && $subtab === 'gallery'): ?>
            <div class="space-y-8">
                <!-- Add new photo card -->
                <div class="bg-white border border-sand rounded-3xl p-6 sm:p-8 shadow-[0_4px_25px_rgba(28,27,25,0.03)]">
                    <h3 class="font-serif text-2xl text-charcoal mb-1">Add Gallery Photography</h3>
                    <p class="text-xs text-stone font-light mb-6">Upload high-resolution architectural shots. JPG/PNG images are auto-converted to compressed WebP.</p>

                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <input type="hidden" name="post_action" value="add_gallery_photo">
                        <input type="hidden" name="project_id" value="<?= $currentProject['id'] ?>">

                        <div class="space-y-2">
                            <label class="block text-[11px] uppercase tracking-wider text-stone font-medium">Upload Image (WebP auto-convert)</label>
                            <input type="file" name="gallery_file" accept="image/*" class="w-full text-xs text-stone file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:uppercase file:tracking-wider file:font-semibold file:bg-charcoal file:text-white hover:file:bg-accent file:cursor-pointer">
                            <div class="text-[10px] text-stone mt-1">Or paste external URL:</div>
                            <input type="url" name="gallery_image_url" placeholder="https://..." class="w-full bg-marble border border-sand rounded-xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-accent">
                        </div>

                        <div>
                            <label class="block text-[11px] uppercase tracking-wider text-stone mb-1 font-medium">Caption (Optional)</label>
                            <input type="text" name="caption" placeholder="e.g. Master Bedroom with Calacatta Marble" class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                        </div>

                        <div>
                            <label class="block text-[11px] uppercase tracking-wider text-stone mb-1 font-medium">Display Sequence</label>
                            <input type="number" name="display_order" value="<?= count($galleryPhotos) + 1 ?>" class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                            <button type="submit" class="w-full mt-3 bg-charcoal text-white hover:bg-accent py-2.5 rounded-full text-xs uppercase tracking-wider font-semibold transition-colors shadow-sm">
                                + Add Photo to Gallery
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing gallery photo grid -->
                <div class="bg-white border border-sand rounded-3xl p-6 sm:p-8 shadow-[0_4px_25px_rgba(28,27,25,0.03)]">
                    <h3 class="font-serif text-2xl text-charcoal mb-4">Current Gallery Showcase (<?= count($galleryPhotos) ?> photos)</h3>

                    <?php if (empty($galleryPhotos)): ?>
                        <p class="text-stone text-xs italic py-8 text-center">No additional gallery photos added yet. Upload photos above.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <?php foreach ($galleryPhotos as $photo): ?>
                                <div class="bg-marble border border-sand rounded-2xl overflow-hidden group shadow-sm flex flex-col justify-between">
                                    <div class="aspect-[4/3] bg-linen relative overflow-hidden">
                                        <img src="<?= htmlspecialchars($photo['image_url']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="">
                                        <span class="absolute top-2 left-2 bg-charcoal/80 text-white text-[9px] px-2 py-0.5 rounded-full">
                                            #<?= $photo['display_order'] ?>
                                        </span>
                                    </div>
                                    <div class="p-3.5 flex items-center justify-between text-xs border-t border-sand">
                                        <span class="text-stone truncate text-[11px] flex-1 mr-2"><?= htmlspecialchars($photo['caption'] ?: 'No caption') ?></span>
                                        <a href="projects-manage.php?action=delete_gallery_photo&img_id=<?= $photo['id'] ?>&project_id=<?= $currentProject['id'] ?>" onclick="return confirm('Remove this gallery photo?')" class="text-red-500 hover:text-red-700 text-[10px] uppercase font-semibold flex-shrink-0">
                                            Remove
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- SUBTAB C: BEFORE & AFTER RENOVATION PAIRS -->
        <?php if ($isEdit && $subtab === 'before_after'): ?>
            <div class="space-y-8">
                <!-- Add new pair card -->
                <div class="bg-white border border-sand rounded-3xl p-6 sm:p-8 shadow-[0_4px_25px_rgba(28,27,25,0.03)]">
                    <h3 class="font-serif text-2xl text-charcoal mb-1">Add Before & After Renovation Pair</h3>
                    <p class="text-xs text-stone font-light mb-6">Paired renovation shots for the interactive draggable comparison slider on the project case study page.</p>

                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="post_action" value="add_before_after">
                        <input type="hidden" name="project_id" value="<?= $currentProject['id'] ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-stone mb-1 font-medium">Room / Spatial Label</label>
                                <input type="text" name="room_title" placeholder="e.g. Master Living Pavilion & Fireplace" required class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                            </div>
                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-stone mb-1 font-medium">Transformation Notes (Optional)</label>
                                <input type="text" name="description" placeholder="e.g. Complete gut renovation, Italian travertine hearth" class="w-full bg-marble border border-sand rounded-xl px-4 py-3 text-xs text-charcoal focus:outline-none focus:border-accent">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-linen/50 rounded-2xl border border-sand">
                            <div class="space-y-2">
                                <strong class="text-xs text-charcoal block">1. BEFORE RENOVATION PHOTO</strong>
                                <input type="file" name="before_file" accept="image/*" class="w-full text-xs text-stone file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:uppercase file:tracking-wider file:font-semibold file:bg-charcoal file:text-white hover:file:bg-accent file:cursor-pointer">
                                <div class="text-[10px] text-stone mt-1">Or paste Before Image URL:</div>
                                <input type="url" name="before_image_url" placeholder="https://..." class="w-full bg-white border border-sand rounded-xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-accent">
                            </div>

                            <div class="space-y-2">
                                <strong class="text-xs text-charcoal block">2. AFTER COMPLETION PHOTO</strong>
                                <input type="file" name="after_file" accept="image/*" class="w-full text-xs text-stone file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:uppercase file:tracking-wider file:font-semibold file:bg-charcoal file:text-white hover:file:bg-accent file:cursor-pointer">
                                <div class="text-[10px] text-stone mt-1">Or paste After Image URL:</div>
                                <input type="url" name="after_image_url" placeholder="https://..." class="w-full bg-white border border-sand rounded-xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-accent">
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="bg-charcoal text-white hover:bg-accent px-8 py-3 rounded-full text-xs uppercase tracking-wider font-semibold transition-colors shadow-md">
                                Save Before & After Slider Pair
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing pairs -->
                <div class="bg-white border border-sand rounded-3xl p-6 sm:p-8 shadow-[0_4px_25px_rgba(28,27,25,0.03)]">
                    <h3 class="font-serif text-2xl text-charcoal mb-4">Transformation Slider Pairs (<?= count($beforeAfterPairs) ?>)</h3>

                    <?php if (empty($beforeAfterPairs)): ?>
                        <p class="text-stone text-xs italic py-8 text-center">No comparison pairs saved yet. Use the form above to pair raw space vs completed luxury interior.</p>
                    <?php else: ?>
                        <div class="space-y-6">
                            <?php foreach ($beforeAfterPairs as $pair): ?>
                                <div class="border border-sand rounded-2xl p-4 bg-marble flex flex-col md:flex-row items-center justify-between gap-6">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <div class="relative w-28 h-20 rounded-xl overflow-hidden border border-sand shadow-sm flex-shrink-0">
                                            <img src="<?= htmlspecialchars($pair['before_image']) ?>" class="w-full h-full object-cover" alt="Before">
                                            <span class="absolute bottom-1 left-1 bg-black/70 text-white text-[8px] px-1.5 py-0.5 rounded uppercase">Before</span>
                                        </div>
                                        <span class="text-stone font-serif text-lg">&rarr;</span>
                                        <div class="relative w-28 h-20 rounded-xl overflow-hidden border border-sand shadow-sm flex-shrink-0">
                                            <img src="<?= htmlspecialchars($pair['after_image']) ?>" class="w-full h-full object-cover" alt="After">
                                            <span class="absolute bottom-1 left-1 bg-accent text-white text-[8px] px-1.5 py-0.5 rounded uppercase">After</span>
                                        </div>
                                        <div>
                                            <div class="font-serif text-base text-charcoal font-medium"><?= htmlspecialchars($pair['title']) ?></div>
                                            <div class="text-stone text-[11px]"><?= htmlspecialchars($pair['description'] ?: 'Spatial renovation') ?></div>
                                        </div>
                                    </div>

                                    <a href="projects-manage.php?action=delete_before_after&pair_id=<?= $pair['id'] ?>&project_id=<?= $currentProject['id'] ?>" onclick="return confirm('Delete this comparison pair?')" class="text-red-500 hover:text-red-700 text-xs uppercase font-semibold">
                                        Delete Pair
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</main>

<script>
    // Live 3D iframe preview update when editing URL
    const tourInput = document.getElementById('tour_url_input');
    const previewIframe = document.getElementById('live_preview_iframe');
    const previewPlaceholder = document.getElementById('preview_placeholder');

    if (tourInput && previewIframe) {
        tourInput.addEventListener('input', function() {
            const val = this.value.trim();
            if (val && (val.startsWith('http://') || val.startsWith('https://'))) {
                previewIframe.src = val;
                previewIframe.classList.remove('hidden');
                if (previewPlaceholder) previewPlaceholder.classList.add('hidden');
            } else if (!val) {
                previewIframe.src = '';
                previewIframe.classList.add('hidden');
                if (previewPlaceholder) previewPlaceholder.classList.remove('hidden');
            }
        });
    }
</script>

<?php render_admin_footer(); ?>
