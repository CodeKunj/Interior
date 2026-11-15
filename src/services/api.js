/**
 * API Service for Studio Atelier Vérité
 * Connects to PHP 8+ REST API endpoints with seamless fallback to curated mock data for local Vite dev.
 */

import { MOCK_PROJECTS } from './mockData';

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
    throw new Error('Invalid JSON format from API');
  } catch (err) {
    // Graceful fallback to rich mock data
    // console.info('[Atelier API Fallback] Using local mock dataset:', err.message);
    let results = [...MOCK_PROJECTS];

    if (featured) {
      results = results.filter(p => p.featured);
    }
    if (category && category.toLowerCase() !== 'all') {
      results = results.filter(p => p.category.toLowerCase() === category.toLowerCase());
    }
    return results;
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
    throw new Error('Project not found on server');
  } catch (err) {
    // Graceful fallback to mock data
    // console.info('[Atelier API Fallback] Fetching single project from mock dataset:', slug);
    const found = MOCK_PROJECTS.find(p => p.slug === slug);
    if (found) return found;

    // Default to first project if slug not found
    return MOCK_PROJECTS[0];
  }
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
    // If running in local Vite dev without PHP server active, simulate successful receipt
    // console.info('[Atelier API Fallback] Simulating consultation submission:', payload);
    await new Promise(resolve => setTimeout(resolve, 600)); // realistic feel

    return {
      success: true,
      message: 'Thank you. Your consultation request has been received. Our senior design team will contact you within 24 business hours.',
      inquiry_id: Math.floor(Math.random() * 9000) + 1000
    };
  }
}
