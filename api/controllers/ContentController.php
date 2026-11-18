<?php
/**
 * Content Controller
 * Handles dynamic content retrieval for Journeys, Services, Process Steps,
 * Testimonials, FAQs, Disciplines, and Press Mentions.
 */

declare(strict_types=1);

namespace Atelier\Controllers;

use Atelier\Core\Request;
use Atelier\Core\Response;
use Atelier\Models\Content;

class ContentController {
    private Content $contentModel;

    public function __construct() {
        $this->contentModel = new Content();
    }

    /**
     * GET /api/content.php
     * Query param: ?section=journeys|services|process|testimonials|faqs|disciplines|press|all
     */
    public function index(Request $request): void {
        $request->requireMethod('GET');

        $section = strtolower(trim((string)$request->query('section', 'all')));

        switch ($section) {
            case 'journeys':
                $data = $this->contentModel->getJourneys();
                break;

            case 'services':
                $data = $this->contentModel->getServices();
                break;

            case 'process':
            case 'process_steps':
                $data = $this->contentModel->getProcessSteps();
                break;

            case 'testimonials':
                $data = $this->contentModel->getTestimonials();
                break;

            case 'faqs':
                $data = $this->contentModel->getFaqs();
                break;

            case 'disciplines':
                $data = $this->contentModel->getDisciplines();
                break;

            case 'press':
            case 'press_mentions':
                $data = $this->contentModel->getPressMentions();
                break;

            case 'all':
            default:
                $data = $this->contentModel->getAllContent();
                break;
        }

        Response::success($data, '', 200, [
            'section' => $section
        ]);
    }
}
