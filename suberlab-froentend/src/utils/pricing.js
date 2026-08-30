/**
 * Sale price shown to the customer (offer price for packages, price for tests).
 */
export function getSalePrice(item) {
  return Number(item?.price ?? item?.offer_price ?? item?.discountedPrice ?? 0) || 0;
}

/**
 * Original/list price when the API provides one and it is higher than the sale price.
 * Tests in CRM only have `price` — this returns null for them.
 */
export function getOriginalPrice(item) {
  const salePrice = getSalePrice(item);
  const original = Number(item?.originalPrice ?? item?.original_price ?? 0) || 0;
  return original > salePrice ? original : null;
}

export function hasCatalogDiscount(item) {
  return getOriginalPrice(item) != null;
}

export function normalizeCartItem(item) {
  const price = getSalePrice(item);
  const originalPrice = getOriginalPrice(item);

  const normalized = {
    ...item,
    price,
    quantity: item?.quantity || 1,
  };

  if (originalPrice != null) {
    normalized.originalPrice = originalPrice;
  } else {
    delete normalized.originalPrice;
  }

  return normalized;
}

export function formatDiscountPercent(salePrice, originalPrice) {
  if (!originalPrice || originalPrice <= salePrice) return null;
  return Math.round((1 - salePrice / originalPrice) * 100);
}

export function isCustomPackageItem(item) {
  return item?.isCustom === true || String(item?.id || '').startsWith('custom-pkg-');
}

export function cartHasCustomPackage(items) {
  return Array.isArray(items) && items.some(isCustomPackageItem);
}
