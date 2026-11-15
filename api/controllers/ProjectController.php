<?php
/**
 * Project Controller
 * Handles portfolio inquiries, category filtering, and single case study endpoints.
 */

declare(strict_types=1);

namespace Atelier\Controllers;

use Atelier\Core\Request;
use Atelier\Core\Response;
use Atelier\Models\Project;
use Atelier\Models\ProjectGallery;
use Atelier\Models\BeforeAfter;

class ProjectController {
    private Project $projectModel;
    private ProjectGallery $galleryModel;
    private BeforeAfter $beforeAfterModel;

    public function __construct() {
        $this->projectModel = new Project();
        $this->galleryModel = new ProjectGallery();
        $this->beforeAfterModel = new BeforeAfter();
    }

    /**
     * GET /api/projects.php
     * Return list of projects with optional category and featured filters
     */
    public function index(Request $request): void {
        $request->requireMethod('GET');

        $filters = [
            'category' => $request->query('category', ''),
            'featured' => $request->query('featured', ''),
            'search'   => $request->query('search', ''),
            'limit'    => $request->query('limit', 0),
            'offset'   => $request->query('offset', 0)
        ];

        $projects = $this->projectModel->getAll($filters);

        Response::success($projects, '', 200, [
            'count'    => count($projects),
            'filters'  => array_filter($filters)
        ]);
    }

    /**
     * GET /api/project-detail.php?slug=...
     * Return full case study with gallery, before/after pairs, and 3D virtual tour configuration
     */
    public function detail(Request $request): void {
        $request->requireMethod('GET');

        $slug = $request->query('slug');
        $id = intval($request->query('id', 0));

        if (empty($slug) && $id <= 0) {
            Response::badRequest("Project slug or ID parameter is required.");
        }

        $project = !empty($slug) 
            ? $this->projectModel->findBySlug($slug)
            : $this->projectModel->findById($id);

        if (!$project) {
            Response::notFound("The requested project could not be found.");
        }

        $projectId = (int)$project['id'];

        // 1. Fetch gallery images
        $gallery = $this->galleryModel->getByProjectId($projectId);

        // 2. Fetch Before & After pairs
        $beforeAfter = $this->beforeAfterModel->getByProjectId($projectId);

        // 3. Fetch previous and next projects
        $navigation = $this->projectModel->getAdjacent($projectId);

        $project['gallery'] = $gallery;
        $project['before_after'] = $beforeAfter;
        $project['navigation'] = $navigation;

        Response::success($project);
    }
}
