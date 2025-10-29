// src/services/blogService.ts
export interface BlogPost {
  id: string;
  slug?: string;
  title: string;
  excerpt: string;
  content?: string;
  featuredImage?: string | null;
  category?: string;
  tags?: string[];
  author?: string;
  publishDate?: string | null;
  readTime?: string;
  featured?: boolean;
}

import { apiUrl } from '../lib/config';

async function fetchJson(url: string) {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status}: ${await res.text()}`);
  return res.json();
}

/** Get list of posts with optional filters */
export async function getPosts(options?: {
  category?: string;
  featured?: boolean;
  limit?: number;
  q?: string;
}): Promise<BlogPost[]> {
  const params = new URLSearchParams();
  if (options?.category) params.set('category', options.category);
  if (typeof options?.featured === 'boolean') params.set('featured', options.featured ? '1' : '0');
  if (options?.limit) params.set('limit', String(options.limit));
  if (options?.q) params.set('q', options.q);

  const url = `${apiUrl('get_blog_posts.php')}?${params.toString()}`;
  const json = await fetchJson(url);
  if (json.success) return json.posts as BlogPost[];
  throw new Error(json.error || 'Failed to load posts');
}

/** Get a single post by id or slug */
export async function getPostById(idOrSlug: string): Promise<BlogPost | undefined> {
  // try slug first
  const bySlug = `${apiUrl('get_post.php')}?slug=${encodeURIComponent(idOrSlug)}`;
  try {
    const json = await fetchJson(bySlug);
    if (json.success) return json.post as BlogPost;
  } catch (e) {
    // if slug lookup fails, try id
  }

  const byId = `${apiUrl('get_post.php')}?id=${encodeURIComponent(idOrSlug)}`;
  const json = await fetchJson(byId);
  if (json.success) return json.post as BlogPost;
  return undefined;
}

/** Get featured post (first featured) */
export async function getFeaturedPost(): Promise<BlogPost | undefined> {
  const posts = await getPosts({ featured: true, limit: 1 });
  return posts.length ? posts[0] : undefined;
}

/** Get posts by category */
export async function getPostsByCategory(category: string): Promise<BlogPost[]> {
  if (category === 'All') return getPosts();
  return getPosts({ category });
}

/** Get related posts by category (exclude current) */
export async function getRelatedPosts(currentPostId: string, category: string, limit = 3): Promise<BlogPost[]> {
  const posts = await getPosts({ category, limit: limit + 5 }); // fetch some extra
  return posts.filter(p => p.id !== currentPostId).slice(0, limit);
}
