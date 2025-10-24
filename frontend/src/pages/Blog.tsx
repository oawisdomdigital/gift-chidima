import { useEffect, useMemo, useState } from 'react';
import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import { ArrowRight } from 'lucide-react';
import { BlogCard } from '../components/BlogCard';
import { NewsletterSignup } from '../components/NewsletterSignup';
import { Advertisement } from '../components/Advertisement';
import { Button } from '../components/ui/button';
import { getPosts, getFeaturedPost } from '../services/blogService';
import type { BlogPost as BlogPostType } from '../services/blogService';

export function Blog() {
  const [selectedCategory, setSelectedCategory] = useState('All');
  const [allPosts, setAllPosts] = useState<BlogPostType[]>([]);
  const [featuredPost, setFeaturedPost] = useState<BlogPostType | undefined>(undefined);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let mounted = true;

    async function load() {
      setLoading(true);
      setError(null);
      try {
        const [posts, featured] = await Promise.all([
          getPosts(), // all posts
          getFeaturedPost(), // single featured
        ]);
        if (!mounted) return;
        setAllPosts(posts || []);
        setFeaturedPost(featured);
      } catch (err) {
        console.error('Failed to load blog posts:', err);
        setError(err instanceof Error ? err.message : 'Failed to load posts');
      } finally {
        if (mounted) setLoading(false);
      }
    }

    load();
    return () => { mounted = false; };
  }, []);

  // Build categories (unique) from posts
  const categories = useMemo(() => {
    const set = new Set<string>();
    allPosts.forEach(p => {
      if (p.category) set.add(p.category);
    });
    return ['All', ...Array.from(set)];
  }, [allPosts]);

  // Filter posts excluding featured
  const filteredPosts = useMemo(() => {
    return allPosts.filter(p => {
      if (p.featured) return false;
      if (selectedCategory === 'All') return true;
      return p.category === selectedCategory;
    });
  }, [allPosts, selectedCategory]);

  // Helper snippet: pulled-up banner wrapper (so it sits flush under the header)
  const PulledBanner = (
    <div style={{ marginTop: `calc(-1 * var(--nav-h))` }} className="transition-theme">
      <Advertisement type="banner" className="transition-theme" />
    </div>
  );

  if (loading) {
    return (
      <div className="min-h-screen bg-app" style={{ paddingTop: 'var(--nav-h)' }}>
        {/* pulled-up banner so placeholder ad is flush beneath header */}
        {PulledBanner}

        <div className="container mx-auto px-6 md:px-8 py-28 text-center">
          <div className="text-xl text-neutral-600">Loading articles…</div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-app" style={{ paddingTop: 'var(--nav-h)' }}>
        {/* pulled-up banner so placeholder ad is flush beneath header */}
        {PulledBanner}

        <div className="container mx-auto px-6 md:px-8 py-28 text-center">
          <div className="text-red-600 font-semibold">Error: {error}</div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-app transition-theme" style={{ paddingTop: 'var(--nav-h)' }}>
      {/* pulled-up banner so it sits flush under the header */}
      {PulledBanner}

      {featuredPost && (
        <section className="relative bg-[#0B1C3B] py-20 md:py-32 shadow-xl">
          <div className="container mx-auto px-6 md:px-8">
            <div className="grid lg:grid-cols-2 gap-12 items-center max-w-7xl mx-auto">
              <motion.div
                initial={{ opacity: 0, x: -30 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.8 }}
                className="space-y-6"
              >
                <span className="inline-block bg-[#D4AF37] text-white px-4 py-2 rounded-full text-sm font-semibold uppercase tracking-wider">
                  Featured Article
                </span>

                <h1 className="font-['Playfair_Display'] text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                  {featuredPost.title}
                </h1>

                <p className="text-xl text-neutral-300 leading-relaxed">
                  {featuredPost.excerpt}
                </p>

                <div className="flex items-center gap-6 text-neutral-400">
                  <span className="flex items-center gap-2">
                    <span className="w-2 h-2 bg-[#D4AF37] rounded-full"></span>
                    {featuredPost.category}
                  </span>
                  <span>{featuredPost.readTime}</span>
                  {featuredPost.publishDate && (
                    <time dateTime={featuredPost.publishDate}>
                      {new Date(featuredPost.publishDate).toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                      })}
                    </time>
                  )}
                </div>

                <div className="mt-6">
                  <Link to={`/blog/${featuredPost.id}`}>
                    <Button
                      size="lg"
                      className="bg-[#D4AF37] hover:bg-[#B8941F] text-white px-10 py-7 text-base font-semibold uppercase tracking-wider rounded-2xl transition-all duration-300 shadow-xl group"
                    >
                      Read Full Article
                      <ArrowRight className="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
                    </Button>
                  </Link>
                </div>
              </motion.div>

              <motion.div
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: 0.2 }}
                className="relative order-first lg:order-last"
              >
                <div className="aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl">
                  <img
                    src={featuredPost.featuredImage || 'http://localhost/myapp/uploads/default_cover.png'}
                    alt={featuredPost.title || 'Featured article'}
                    className="w-full h-full object-cover"
                    loading="eager"
                    onError={(e) => {
                      const t = e.target as HTMLImageElement;
                      t.onerror = null;
                      t.src = 'http://localhost/myapp/uploads/default_cover.png';
                    }}
                  />
                </div>
                <div className="absolute -bottom-6 -right-6 w-full h-full border-2 border-[#D4AF37] rounded-3xl -z-10 hidden lg:block"></div>
              </motion.div>
            </div>
          </div>
        </section>
      )}

      <section className="py-20 bg-white">
        <div className="container mx-auto px-6 md:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="text-center mb-12"
          >
            <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-[#0B1C3B] mb-4">
              Latest Articles
            </h2>
            <p className="text-xl text-neutral-600 max-w-2xl mx-auto">
              Explore insights on leadership, mentorship, and transformation
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="flex flex-wrap justify-center gap-3 mb-16"
          >
            {categories.map((category) => (
              <button
                key={category}
                onClick={() => setSelectedCategory(category)}
                className={`px-6 py-3 rounded-full font-medium uppercase tracking-wider text-sm transition-all duration-300 ${
                  selectedCategory === category
                    ? 'bg-[#0B1C3B] text-white shadow-lg'
                    : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200'
                }`}
              >
                {category}
              </button>
            ))}
          </motion.div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            {filteredPosts.map((post, index) => (
              <BlogCard key={post.id} post={post} index={index} />
            ))}
          </div>

          {filteredPosts.length === 0 && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-16"
            >
              <p className="text-xl text-neutral-600">
                No articles found in this category. Check back soon!
              </p>
            </motion.div>
          )}
        </div>
      </section>

      <NewsletterSignup />
    </div>
  );
}
