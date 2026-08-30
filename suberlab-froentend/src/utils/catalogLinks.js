export function getServiceDetailHash(service) {
  if (service?.hash) {
    return service.hash.startsWith('#') ? service.hash : `#${service.hash}`;
  }
  if (service?.slug) {
    return `#/test/${service.slug}`;
  }
  return '#/lab-tests';
}

export function getPackageDetailHash(pkg) {
  if (pkg?.hash) {
    return pkg.hash.startsWith('#') ? pkg.hash : `#${pkg.hash}`;
  }
  if (pkg?.slug) {
    return `#/package/${pkg.slug}`;
  }
  return '#/lab-tests';
}

export function buildServiceNavItem(service) {
  const name = service?.name || service?.title || '';
  const slug = service?.slug || null;

  return {
    name,
    slug,
    hash: getServiceDetailHash(service),
  };
}
