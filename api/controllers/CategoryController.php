<?php
/**
 * Category Controller
 * Returns portfolio categories and counts for filter bars.
 */

declare(strict_types=1);

namespace Atelier\Controllers;

use Atelier\Core\Request;
use Atelier\Core\Response;
use Atelier\Models\Project;

class CategoryController {
    private Project $projectModel;

    public function __construct() {
        $this->projectModel = new Project();
    }

    /**
     * GET /api/categories.php
     */
    public function index(Request $request): void {
        $request->requireMethod('GET');
        $categories = $this->projectModel->getCategoriesWithCounts();
        Response::success($categories);
    }
}
