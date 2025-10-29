// Centralized API base and helper for frontend API calls.
// Default is relative to XAMPP document root. You can override with Vite env VITE_API_BASE.
// Default API base: allow VITE_API_BASE to override. When running the frontend dev server
// (Vite on a different port), using an absolute origin avoids the dev-server returning its
// index.html for requests like '/myapp/api/...'. For local XAMPP setups we default to
// the Apache origin at http://localhost/myapp.
const envApiBase = (import.meta as any).env?.VITE_API_BASE;
const isDevelopment = (import.meta as any).env?.MODE === 'development';
export const API_BASE = envApiBase || (isDevelopment ? 'http://localhost/myapp' : 'https://gift.infinityfree.me');

/**
 * Build a URL for an API endpoint inside the backend `api/` folder.
 * Usage: apiUrl('get_hero.php') -> '/myapp/api/get_hero.php'
 */
export function apiUrl(path: string) {
  const p = path.replace(/^\/+/, ''); // remove leading slash if present
  return `${API_BASE.replace(/\/$/, '')}/api/${p}`;
}

/**
 * Build a URL for media or uploaded files served from the project root.
 * Usage: mediaPath('uploads/image.jpg') -> '/myapp/uploads/image.jpg'
 */
export function mediaPath(path: string) {
  const p = path.replace(/^\/+/, '').replace(/^\.\//, '');
  return `${API_BASE.replace(/\/$/, '')}/${p}`;
}

export default apiUrl;
