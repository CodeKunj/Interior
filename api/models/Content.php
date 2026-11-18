<?php
/**
 * Content Model & Repository
 * Serves dynamic website content: Journeys, Services, Process Steps, Testimonials,
 * FAQs, Disciplines, and Press Mentions directly from the MySQL database.
 */

declare(strict_types=1);

namespace Atelier\Models;

use Throwable;

class Content extends BaseModel {
    /**
     * Retrieve all Architectural Journeys
     */
    public function getJourneys(): array {
        try {
            $sql = "SELECT id, slug, title, tag, location, area, duration, hero_image,
                           overview, materials, span, has_3d_tour, display_order
                    FROM journeys
                    ORDER BY display_order ASC, id ASC";
            $rows = $this->fetchAll($sql);

            return array_map(function ($row) {
                $materials = json_decode((string)($row['materials'] ?? '[]'), true);
                if (!is_array($materials)) {
                    $materials = array_filter(array_map('trim', explode(',', (string)$row['materials'])));
                }

                return [
                    'id'          => $row['slug'] ?? (string)$row['id'],
                    'slug'        => (string)$row['slug'],
                    'title'       => (string)$row['title'],
                    'tag'         => (string)$row['tag'],
                    'location'    => (string)$row['location'],
                    'area'        => (string)$row['area'],
                    'duration'    => (string)$row['duration'],
                    'heroImage'   => (string)$row['hero_image'],
                    'imageSrc'    => (string)$row['hero_image'],
                    'overview'    => (string)$row['overview'],
                    'description' => (string)$row['overview'],
                    'materials'   => $materials,
                    'span'        => (string)($row['span'] ?: 'md:col-span-4'),
                    'has_3d_tour' => (bool)$row['has_3d_tour'],
                    'has3DTour'   => (bool)$row['has_3d_tour'],
                    'href'        => '/work/' . $row['slug']
                ];
            }, $rows);
        } catch (Throwable $e) {
            error_log('Content::getJourneys error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve Services & Design Methodology
     */
    public function getServices(): array {
        try {
            $sql = "SELECT id, service_key, title, subtitle, description, deliverables,
                           image_url, display_order
                    FROM services
                    ORDER BY display_order ASC, id ASC";
            $rows = $this->fetchAll($sql);

            return array_map(function ($row) {
                $deliverables = json_decode((string)($row['deliverables'] ?? '[]'), true);
                if (!is_array($deliverables)) {
                    $deliverables = array_filter(array_map('trim', explode("\n", (string)$row['deliverables'])));
                }

                return [
                    'id'           => (string)$row['service_key'],
                    'service_key'  => (string)$row['service_key'],
                    'title'        => (string)$row['title'],
                    'subtitle'     => (string)$row['subtitle'],
                    'description'  => (string)$row['description'],
                    'deliverables' => $deliverables,
                    'image'        => (string)$row['image_url'],
                    'image_url'    => (string)$row['image_url']
                ];
            }, $rows);
        } catch (Throwable $e) {
            error_log('Content::getServices error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve 4-Step Process Steps
     */
    public function getProcessSteps(): array {
        try {
            $sql = "SELECT id, step_number, title, phase, description, display_order
                    FROM process_steps
                    ORDER BY display_order ASC, id ASC";
            $rows = $this->fetchAll($sql);

            return array_map(function ($row) {
                return [
                    'id'          => (int)$row['id'],
                    'step'        => (string)$row['step_number'],
                    'step_number' => (string)$row['step_number'],
                    'title'       => (string)$row['title'],
                    'phase'       => (string)$row['phase'],
                    'description' => (string)$row['description']
                ];
            }, $rows);
        } catch (Throwable $e) {
            error_log('Content::getProcessSteps error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve Client Testimonials
     */
    public function getTestimonials(): array {
        try {
            $sql = "SELECT id, quote, author, role, project, avatar_url, display_order
                    FROM testimonials
                    ORDER BY display_order ASC, id ASC";
            $rows = $this->fetchAll($sql);

            return array_map(function ($row) {
                return [
                    'id'         => (int)$row['id'],
                    'quote'      => (string)$row['quote'],
                    'author'     => (string)$row['author'],
                    'role'       => (string)$row['role'],
                    'project'    => (string)$row['project'],
                    'avatar_url' => $row['avatar_url'] ? (string)$row['avatar_url'] : null
                ];
            }, $rows);
        } catch (Throwable $e) {
            error_log('Content::getTestimonials error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve Studio FAQs
     */
    public function getFaqs(): array {
        try {
            $sql = "SELECT id, question, answer, category, display_order
                    FROM faqs
                    ORDER BY display_order ASC, id ASC";
            $rows = $this->fetchAll($sql);

            return array_map(function ($row) {
                return [
                    'id'       => (int)$row['id'],
                    'question' => (string)$row['question'],
                    'answer'   => (string)$row['answer'],
                    'category' => (string)$row['category']
                ];
            }, $rows);
        } catch (Throwable $e) {
            error_log('Content::getFaqs error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve Disciplines for Bento / Media Grid
     */
    public function getDisciplines(): array {
        try {
            $sql = "SELECT id, title, description, image_url, grid_span, display_order
                    FROM disciplines
                    ORDER BY display_order ASC, id ASC";
            $rows = $this->fetchAll($sql);

            return array_map(function ($row) {
                return [
                    'id'          => (int)$row['id'],
                    'title'       => (string)$row['title'],
                    'description' => (string)$row['description'],
                    'imageSrc'    => (string)$row['image_url'],
                    'image_url'   => (string)$row['image_url'],
                    'span'        => (string)($row['grid_span'] ?: 'lg:col-span-4')
                ];
            }, $rows);
        } catch (Throwable $e) {
            error_log('Content::getDisciplines error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve Press Mentions
     */
    public function getPressMentions(): array {
        try {
            $sql = "SELECT id, outlet, quote, year, display_order
                    FROM press_mentions
                    ORDER BY display_order ASC, id ASC";
            $rows = $this->fetchAll($sql);

            return array_map(function ($row) {
                return [
                    'id'      => (int)$row['id'],
                    'outlet'  => (string)$row['outlet'],
                    'quote'   => (string)$row['quote'],
                    'year'    => $row['year'] ? (string)$row['year'] : null
                ];
            }, $rows);
        } catch (Throwable $e) {
            error_log('Content::getPressMentions error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve All Content at Once
     */
    public function getAllContent(): array {
        return [
            'journeys'       => $this->getJourneys(),
            'services'       => $this->getServices(),
            'process_steps'  => $this->getProcessSteps(),
            'testimonials'   => $this->getTestimonials(),
            'faqs'           => $this->getFaqs(),
            'disciplines'    => $this->getDisciplines(),
            'press_mentions' => $this->getPressMentions()
        ];
    }
}
