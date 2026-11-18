/**
 * API Service for Studio Atelier Vérité
 * Connects directly to PHP 8+ REST API endpoints.
 * All project data, journeys, services, testimonials, and FAQs are loaded dynamically from MySQL.
 */

const API_BASE = '/api';

/**
 * Fetch projects list with optional category and featured filtering
 */
export async function getProjects(filters = {}) {
  const { category, featured } = filters;
  const params = new URLSearchParams();
  if (category && category !== 'All') params.append('category', category);
  if (featured) params.append('featured', '1');

  const url = `${API_BASE}/projects.php${params.toString() ? '?' + params.toString() : ''}`;

  try {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const data = await res.json();
    if (data && Array.isArray(data.data)) {
      return data.data;
    }
    return [];
  } catch (err) {
    console.error('[Atelier API] Error fetching projects:', err);
    return [];
  }
}

/**
 * Fetch single project by slug including gallery & before/after pairs
 */
export async function getProjectBySlug(slug) {
  const url = `${API_BASE}/project-detail.php?slug=${encodeURIComponent(slug)}`;

  try {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const data = await res.json();
    if (data && data.success && data.data) {
      return data.data;
    }
    return null;
  } catch (err) {
    console.error('[Atelier API] Error fetching project detail:', err);
    return null;
  }
}

/**
 * Generic content fetcher for dynamic website sections from /api/content.php
 * @param {string} section - 'journeys' | 'services' | 'process' | 'testimonials' | 'faqs' | 'disciplines' | 'press' | 'all'
 */
export async function getContent(section = 'all') {
  const url = `${API_BASE}/content.php?section=${encodeURIComponent(section)}`;

  try {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const data = await res.json();
    if (data && data.success && data.data !== undefined) {
      return data.data;
    }
    return section === 'all' ? {} : [];
  } catch (err) {
    console.error(`[Atelier API] Error fetching content section "${section}":`, err);
    return section === 'all' ? {} : [];
  }
}

/**
 * Specialized Content Helpers
 */
export async function getJourneys() {
  const data = await getContent('journeys');
  return Array.isArray(data) ? data : [];
}

export async function getServices() {
  const data = await getContent('services');
  return Array.isArray(data) ? data : [];
}

export async function getProcessSteps() {
  const data = await getContent('process');
  return Array.isArray(data) ? data : [];
}

export async function getTestimonials() {
  const data = await getContent('testimonials');
  return Array.isArray(data) ? data : [];
}

export async function getFaqs() {
  const data = await getContent('faqs');
  return Array.isArray(data) ? data : [];
}

export async function getDisciplines() {
  const data = await getContent('disciplines');
  return Array.isArray(data) ? data : [];
}

export async function getPressMentions() {
  const data = await getContent('press');
  return Array.isArray(data) ? data : [];
}

/**
 * Submit consultation inquiry form to /api/inquiry.php
 */
export async function submitInquiry(payload) {
  const url = `${API_BASE}/inquiry.php`;

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (!res.ok || !data.success) {
      throw new Error(data.error || 'Failed to submit inquiry to server.');
    }
    return data;
  } catch (err) {
    throw err;
  }
}
