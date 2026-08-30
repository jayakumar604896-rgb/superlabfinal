const ALLOW_MOCK_FALLBACK = import.meta.env.VITE_ALLOW_MOCK_FALLBACK === 'true';

let cachedTestDatabase = null;

export function isMockFallbackEnabled() {
  return ALLOW_MOCK_FALLBACK;
}

/**
 * Lazy-load the offline demo catalog only when VITE_ALLOW_MOCK_FALLBACK=true.
 * Keeps ~1500 lines out of the default production bundle path.
 */
export async function getMockTestDatabase() {
  if (!ALLOW_MOCK_FALLBACK) {
    return [];
  }

  if (!cachedTestDatabase) {
    const module = await import('../data/testDatabase');
    cachedTestDatabase = module.testDatabase;
  }

  return cachedTestDatabase;
}
