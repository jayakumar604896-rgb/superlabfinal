const KNOWN_CATALOG_CATEGORIES = new Set([
  'Pathology',
  'Radiology',
  'Cardiology',
  'Pregnancy',
  'Heart',
  'HIV',
  'Fever',
  'Hormone',
  'Allergy',
  'Tuberculosis',
  'Diabetes',
  'Thyroid',
  'Full Body Health',
]);

/**
 * Navigate to lab tests page with a category filter or search query.
 * @param {string} label - e.g. "Heart Test", "Diabetes", "Kidney Test"
 */
export function navigateToLabTestsFilter(label) {
  const normalized = String(label || '').replace(/\s*Test\s*$/i, '').trim();
  if (!normalized) {
    window.location.hash = '#/lab-tests';
    return;
  }

  if (KNOWN_CATALOG_CATEGORIES.has(normalized)) {
    sessionStorage.setItem('superlab_selected_category', normalized);
  } else {
    sessionStorage.setItem('superlab_search_query', normalized);
    window.dispatchEvent(new Event('superlab_search_trigger'));
  }

  window.location.hash = '#/lab-tests';
  window.dispatchEvent(new Event('superlab_category_trigger'));
}
