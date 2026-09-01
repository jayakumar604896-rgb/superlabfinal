import axios from 'axios';
import { getMockTestDatabase, isMockFallbackEnabled } from './mockCatalog';

const LOCAL_API_URL = normalizeBaseUrl('https://portal.mysuperlab.in/api/v1');
const PRIMARY_API_URL = normalizeBaseUrl(import.meta.env.VITE_API_BASE_URL || LOCAL_API_URL);
const REMOTE_API_URL = normalizeBaseUrl('https://portal.mysuperlab.in/api/v1');

const apiClient = axios.create({
  timeout: 8000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

function normalizeBaseUrl(url) {
  return String(url || '').replace(/\/+$/, '');
}

function getApiBaseUrls() {
  return [...new Set([PRIMARY_API_URL, LOCAL_API_URL, REMOTE_API_URL].filter(Boolean))];
}

function parseApiListPayload(result) {
  if (!result) return null;
  if (result.status === 'success' && Array.isArray(result.data)) {
    return result.data;
  }
  if (Array.isArray(result)) {
    return result;
  }
  return null;
}

function parseApiObjectPayload(result) {
  if (!result) return null;
  if (result.status === 'success' && result.data && typeof result.data === 'object') {
    return result.data;
  }
  if (result.data && typeof result.data === 'object') {
    return result.data;
  }
  if (typeof result === 'object' && !Array.isArray(result)) {
    return result;
  }
  return null;
}

/**
 * GET list endpoints — tries configured API bases in order.
 * Empty arrays from a reachable API are treated as live data (not a failure).
 */
async function fetchWithFallback(endpoint, fallbackData = [], options = {}) {
  const urlsToTry = getApiBaseUrls();
  let lastError = null;

  for (const baseUrl of urlsToTry) {
    try {
      const response = await apiClient.get(`${baseUrl}${endpoint}`, {
        headers: options.headers || {},
      });
      if (response.status === 200 && response.data !== undefined && response.data !== null) {
        const list = parseApiListPayload(response.data);
        if (list !== null) {
          return { data: list, isLive: true };
        }
      }
    } catch (err) {
      lastError = err;
    }
  }

  if (isMockFallbackEnabled() && fallbackData.length > 0) {
    return {
      data: fallbackData,
      isLive: false,
      error: getApiErrorMessage(lastError, 'Using offline catalog data.'),
    };
  }

  return {
    data: [],
    isLive: false,
    error: getApiErrorMessage(lastError, 'Unable to load data from the server.'),
  };
}

async function getFromApi(endpoint, options = {}) {
  const urlsToTry = getApiBaseUrls();
  let lastError = null;

  for (const baseUrl of urlsToTry) {
    try {
      const response = await apiClient.get(`${baseUrl}${endpoint}`, {
        headers: options.headers || {},
      });
      if (response.status === 200 && response.data !== undefined && response.data !== null) {
        const payload = parseApiObjectPayload(response.data);
        if (payload) {
          return { data: payload, isLive: true };
        }
      }
    } catch (err) {
      lastError = err;
    }
  }

  return {
    data: null,
    isLive: false,
    error: getApiErrorMessage(lastError, 'Unable to load data from the server.'),
  };
}

async function postToApi(endpoint, body, options = {}) {
  const urlsToTry = getApiBaseUrls();
  let lastError = null;

  for (const baseUrl of urlsToTry) {
    try {
      const response = await apiClient.post(`${baseUrl}${endpoint}`, body, {
        headers: options.headers || {},
      });
      if (response.status >= 200 && response.status < 300 && response.data) {
        return { ok: true, response: response.data, status: response.status };
      }
    } catch (err) {
      lastError = err;
      if (err.response) {
        return {
          ok: false,
          message: getApiErrorMessage(err, options.errorMessage || 'Request failed.'),
          status: err.response.status,
        };
      }
    }
  }

  return {
    ok: false,
    message: getApiErrorMessage(lastError, options.networkMessage || 'Unable to reach the server. Please try again.'),
  };
}

async function putToApi(endpoint, body, options = {}) {
  const urlsToTry = getApiBaseUrls();
  let lastError = null;

  for (const baseUrl of urlsToTry) {
    try {
      const response = await apiClient.put(`${baseUrl}${endpoint}`, body, {
        headers: options.headers || {},
      });
      if (response.status >= 200 && response.status < 300 && response.data) {
        return { ok: true, response: response.data, status: response.status };
      }
    } catch (err) {
      lastError = err;
      if (err.response) {
        return {
          ok: false,
          message: getApiErrorMessage(err, options.errorMessage || 'Request failed.'),
          status: err.response.status,
        };
      }
    }
  }

  return {
    ok: false,
    message: getApiErrorMessage(lastError, options.networkMessage || 'Unable to reach the server. Please try again.'),
  };
}

/**
 * Get Categories
 */
export async function fetchCategories() {
  return fetchWithFallback('/categories', []);
}

/**
 * Get Footer Locations
 */
export async function fetchFooterLocations() {
  return fetchWithFallback('/footer-locations', []);
}

/**
 * Get Test Categories (Specific for All Categories page)
 */
export async function fetchTestCategories() {
  return fetchWithFallback('/test-categories', []);
}

/**
 * Get Category by Slug with its Tests
 */
export async function fetchCategoryBySlug(slug) {
  const result = await getFromApi(`/test-categories/${slug}`);
  if (result.isLive && result.data) {
    return result;
  }

  return {
    data: null,
    isLive: false,
    error: result.error || 'Category not found.',
  };
}

/**
 * Get Services / Lab Tests
 */
export async function fetchServices(search = '', categoryId = '') {
  let endpoint = '/services';
  const params = new URLSearchParams();
  if (search) params.append('search', search);
  if (categoryId) params.append('category_id', categoryId);
  if (params.toString()) endpoint += `?${params.toString()}`;

  const fallback = isMockFallbackEnabled() ? await getMockTestDatabase() : [];
  return fetchWithFallback(endpoint, fallback);
}

/**
 * Get a single lab test by slug (unified catalog)
 */
export async function fetchServiceBySlug(slug) {
  const result = await getFromApi(`/services/${slug}`);
  if (result.isLive && result.data) {
    return result;
  }

  if (isMockFallbackEnabled()) {
    const mockDatabase = await getMockTestDatabase();
    const fallback = mockDatabase.find((item) => item.slug === slug);
    if (fallback) {
      return { data: fallback, isLive: false, error: result.error };
    }
  }

  return { data: null, isLive: false, error: result.error || 'Test not found.' };
}

/**
 * Get Health Packages
 */
export async function fetchPackages() {
  return fetchWithFallback('/packages', []);
}

/**
 * Get a single health package by slug
 */
export async function fetchPackageBySlug(slug) {
  const result = await getFromApi(`/packages/${slug}`);
  if (result.isLive && result.data) {
    return result;
  }

  if (isMockFallbackEnabled()) {
    const list = await fetchPackages();
    const fallback = (list.data || []).find((item) => item.slug === slug);
    if (fallback) {
      return { data: fallback, isLive: false, error: result.error };
    }
  }

  return { data: null, isLive: false, error: result.error || 'Package not found.' };
}

/**
 * Get Testimonials
 */
export async function fetchTestimonials() {
  return fetchWithFallback('/testimonials', []);
}

/**
 * Coupons visible to the current visitor (guest offers or account-assigned).
 */
export async function fetchAvailableCoupons() {
  const result = await fetchWithFallback('/coupons/available', [], {
    headers: getCustomerAuthHeaders(),
  });

  if (result.isLive) {
    return { success: true, data: result.data || [] };
  }

  return { success: false, data: [], message: result.error };
}

/**
 * Validate a coupon against the current cart subtotal.
 */
export async function validateCoupon(code, subtotal) {
  const result = await postToApi('/coupons/validate', { code, subtotal }, {
    headers: getCustomerAuthHeaders(),
    errorMessage: 'Coupon validation failed.',
    networkMessage: 'Unable to validate coupon. Please try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return { success: true, data: result.response.data };
  }

  return {
    success: false,
    message: result.message || 'Invalid coupon code.',
  };
}

/**
 * Look up the latest lab report for a guest by mobile number.
 */
export async function lookupGuestReport(mobile) {
  const result = await postToApi('/reports/lookup', { mobile }, {
    errorMessage: 'Report lookup failed.',
    networkMessage: 'Unable to look up report. Please try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return { success: true, data: result.response.data };
  }

  return {
    success: false,
    message: result.message || 'No report found for this mobile number.',
  };
}

/**
 * Submit Booking
 */
export async function submitBooking(bookingData) {
  const result = await postToApi('/bookings', bookingData, {
    headers: getCustomerAuthHeaders(),
    errorMessage: 'Booking submission failed.',
    networkMessage: 'Unable to submit booking. Please check your connection and try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return {
      success: true,
      status: 'success',
      message: result.response.message,
      data: result.response.data,
    };
  }

  return {
    success: false,
    status: 'error',
    message: result.message || 'Booking submission failed. Please try again.',
  };
}

export async function getActivePaymentGateways() {
  const result = await getFromApi('/payment-gateways');
  if (result.isLive && result.data) {
    return result.data;
  }
  return [
    { name: 'Razorpay', slug: 'razorpay', additional_settings: { theme_color: '#3B82F6', company_name: 'SuperLab Diagnostics' } },
    { name: 'Stripe', slug: 'stripe', additional_settings: { theme_color: '#635BFF', company_name: 'SuperLab Diagnostics' } },
    { name: 'PayPal', slug: 'paypal', additional_settings: { theme_color: '#003087', company_name: 'SuperLab Diagnostics' } }
  ];
}

export async function createUnifiedPaymentOrder(gateway, amount) {
  const result = await postToApi('/payment/order', { gateway, amount }, {
    errorMessage: 'Failed to initialize payment.',
    networkMessage: 'Unable to reach payment service. Please try again.',
  });

  if (result.ok && result.response) {
    return result.response;
  }

  return null;
}

export async function verifyUnifiedPayment(verificationData) {
  const result = await postToApi('/payment/verify', verificationData, {
    headers: getCustomerAuthHeaders(),
    errorMessage: 'Payment verification failed.',
    networkMessage: 'Unable to verify payment. Please contact support.',
  });

  if (result.ok && result.response) {
    return result.response;
  }

  return null;
}

/**
 * Submit Enquiry / Callback Request
 */
export async function submitEnquiry(enquiryData) {
  const result = await postToApi('/enquiries', enquiryData, {
    errorMessage: 'Enquiry submission failed.',
    networkMessage: 'Unable to submit enquiry. Please try again.',
  });

  if (result.ok && result.response) {
    return {
      success: true,
      message: result.response.message || 'Enquiry submitted successfully!',
      data: result.response.data,
    };
  }

  return {
    success: false,
    message: result.message || 'Enquiry submission failed. Please try again.',
  };
}

/**
 * Extract a readable error message from an axios/Laravel error response.
 */
function getApiErrorMessage(err, fallback = 'Something went wrong. Please try again.') {
  const data = err?.response?.data;
  if (!data) return fallback;
  if (data.message) return data.message;
  if (data.errors) {
    const firstKey = Object.keys(data.errors)[0];
    if (firstKey && Array.isArray(data.errors[firstKey])) {
      return data.errors[firstKey][0];
    }
  }
  return fallback;
}

/**
 * Register Customer
 */
export async function registerCustomer(customerData) {
  const result = await postToApi('/register', customerData, {
    errorMessage: 'Registration failed.',
    networkMessage: 'Unable to reach the server. Please try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return { success: true, message: result.response.message, data: result.response.data };
  }

  return { success: false, message: result.message || 'Registration failed.' };
}

/**
 * Login Customer
 */
export async function loginCustomer(credentials) {
  const result = await postToApi('/login', credentials, {
    errorMessage: 'Login failed.',
    networkMessage: 'Unable to reach the server. Please try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return { success: true, message: result.response.message, data: result.response.data };
  }

  return { success: false, message: result.message || 'Login failed.' };
}

/**
 * Logout Customer API
 */
export async function logoutCustomer() {
  const result = await postToApi('/logout', {}, {
    headers: getCustomerAuthHeaders(),
    errorMessage: 'Logout failed.',
    networkMessage: 'Unable to reach the server.',
  });

  if (result.ok) {
    return { success: true, message: result.response?.message || 'Logged out successfully.' };
  }

  return { success: false, message: result.message || 'Logout failed.' };
}

/**
 * Global Customer Session Helpers
 */
export function getSuperlabCustomer() {
  try {
    const raw = localStorage.getItem('superlab_customer');
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

export function getCustomerAuthHeaders() {
  const customer = getSuperlabCustomer();
  if (customer?.token) {
    return { Authorization: `Bearer ${customer.token}` };
  }
  return {};
}

export function setSuperlabCustomer(customerData) {
  if (customerData) {
    localStorage.setItem('superlab_customer', JSON.stringify(customerData));
  }
  window.dispatchEvent(new Event('superlab_auth_update'));
}

export async function logoutSuperlabCustomer() {
  await logoutCustomer();
  localStorage.removeItem('superlab_customer');
  window.dispatchEvent(new Event('superlab_auth_update'));
  window.location.hash = '#/';
}

/**
 * Fetch Customer Profile Data (Vitals + Bookings) — requires auth token
 */
export async function fetchCustomerProfileData() {
  const headers = getCustomerAuthHeaders();
  if (!headers.Authorization) {
    return {
      success: false,
      message: 'Not authenticated.',
      data: { customer: null, vitals: [], bookings: [] },
    };
  }

  const urlsToTry = getApiBaseUrls();
  let lastError = null;

  for (const baseUrl of urlsToTry) {
    try {
      const response = await apiClient.get(`${baseUrl}/customer/profile-data`, { headers });
      if (response.status === 200 && response.data?.data) {
        return { success: true, data: response.data.data };
      }
    } catch (err) {
      lastError = err;
      if (err.response?.status === 401) {
        return {
          success: false,
          message: 'Session expired. Please log in again.',
          data: { customer: null, vitals: [], bookings: [] },
        };
      }
    }
  }

  return {
    success: false,
    message: getApiErrorMessage(lastError, 'Unable to load profile data.'),
    data: { customer: null, vitals: [], bookings: [] },
  };
}

/**
 * Update authenticated customer profile fields
 */
export async function updateCustomerProfile(profileData) {
  const headers = getCustomerAuthHeaders();
  if (!headers.Authorization) {
    return { success: false, message: 'Not authenticated.' };
  }

  const result = await putToApi('/customer/profile', profileData, {
    headers,
    errorMessage: 'Profile update failed.',
    networkMessage: 'Unable to update profile. Please try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return {
      success: true,
      message: result.response.message,
      data: result.response.data,
    };
  }

  return {
    success: false,
    message: result.message || 'Profile update failed.',
  };
}

/**
 * Fetch approved reviews for a test or package detail page.
 */
export async function fetchServiceReviews({ serviceId, serviceSlug, packageId, packageSlug } = {}) {
  const params = new URLSearchParams();
  if (serviceId) params.set('service_id', String(serviceId));
  if (serviceSlug) params.set('service_slug', serviceSlug);
  if (packageId) params.set('package_id', String(packageId));
  if (packageSlug) params.set('package_slug', packageSlug);

  const query = params.toString();
  const result = await fetchWithFallback(query ? `/reviews?${query}` : '/reviews', []);

  return {
    success: result.isLive || (result.data && result.data.length >= 0),
    data: result.data || [],
    message: result.error,
  };
}

/**
 * Submit a customer review for a test or package (moderated before display).
 */
export async function submitServiceReview(reviewData) {
  const result = await postToApi('/reviews', reviewData, {
    headers: getCustomerAuthHeaders(),
    errorMessage: 'Could not submit your review.',
    networkMessage: 'Unable to submit review. Please try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return {
      success: true,
      message: result.response.message || 'Review submitted successfully.',
      data: result.response.data,
    };
  }

  return {
    success: false,
    message: result.message || 'Could not submit your review.',
  };
}

/**
 * Save a Make Your Own Package bundle on the server.
 */
export async function createCustomPackage({ items, name = null }) {
  const result = await postToApi('/custom-packages', { items, name }, {
    headers: getCustomerAuthHeaders(),
    errorMessage: 'Could not save your custom package.',
    networkMessage: 'Unable to save package. Please try again.',
  });

  if (result.ok && result.response?.status === 'success') {
    return {
      success: true,
      message: result.response.message || 'Custom package saved.',
      data: result.response.data,
    };
  }

  return {
    success: false,
    message: result.message || 'Could not save your custom package.',
  };
}

/**
 * List saved custom packages for the logged-in customer.
 */
export async function fetchCustomPackages() {
  const headers = getCustomerAuthHeaders();
  if (!headers.Authorization) {
    return { success: true, data: [] };
  }

  const urlsToTry = getApiBaseUrls();
  let lastError = null;

  for (const baseUrl of urlsToTry) {
    try {
      const response = await apiClient.get(`${baseUrl}/custom-packages`, { headers });
      if (response.status === 200) {
        const list = parseApiListPayload(response.data);
        return { success: true, data: list || [] };
      }
    } catch (err) {
      lastError = err;
      if (err.response?.status === 401) {
        return { success: true, data: [] };
      }
    }
  }

  return {
    success: false,
    data: [],
    message: getApiErrorMessage(lastError, 'Unable to load saved packages.'),
  };
}

// Bind to window for global access
if (typeof window !== 'undefined') {
  window.getSuperlabCustomer = getSuperlabCustomer;
  window.setSuperlabCustomer = setSuperlabCustomer;
  window.logoutSuperlabCustomer = logoutSuperlabCustomer;
}
