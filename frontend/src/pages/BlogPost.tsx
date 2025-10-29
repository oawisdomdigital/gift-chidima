import { useParams, Link, useNavigate } from 'react-router-dom';
import { mediaPath } from '../lib/config';
import { motion } from 'framer-motion';
import { ArrowLeft, Calendar, Clock, Share2, Facebook, Linkedin, Twitter, Link2, ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from '../components/ui/button';
import { BlogCard } from '../components/BlogCard';
import { CommentsSection } from '../components/CommentsSection';
import { Advertisement } from '../components/Advertisement';
import { useEffect, useState } from 'react';
import { getPostById, getRelatedPosts, getPosts } from '../services/blogService';
import type { BlogPost as BlogPostType } from '../services/blogService';

export function BlogPost() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [copied, setCopied] = useState(false);
  const [post, setPost] = useState<BlogPostType | undefined>(undefined);
  const [relatedPosts, setRelatedPosts] = useState<BlogPostType[]>([]);
  const [allPosts, setAllPosts] = useState<BlogPostType[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [id]);

  useEffect(() => {
    let mounted = true;

    async function load() {
      if (!id) {
        setError('No post id');
        setLoading(false);
        return;
      }
      setLoading(true);
      setError(null);

      try {
        const fetchedPost = await getPostById(id);
        if (!mounted) return;
        if (!fetchedPost) {
          setPost(undefined);
          setError('Article not found');
          setLoading(false);
          return;
        }
        setPost(fetchedPost);

        const [related, postsList] = await Promise.all([
          getRelatedPosts(fetchedPost.id, fetchedPost.category || '', 6),
          getPosts(),
        ]);
        if (!mounted) return;
        setRelatedPosts(related || []);
        setAllPosts(postsList || []);
      } catch (err) {
        console.error('Error loading post:', err);
        setError(err instanceof Error ? err.message : 'Failed to load article');
      } finally {
        if (mounted) setLoading(false);
      }
    }

    load();
    return () => { mounted = false; };
  }, [id]);

  // --- 🔹 Shared Advertisement wrapper (flush below header) ---
  const BannerAd = () => (
    <div style={{ marginTop: 'calc(-1 * var(--nav-h))' }}>
      <Advertisement type="banner" className="transition-theme" />
    </div>
  );

  // --- 🔹 Loading view ---
  if (loading) {
    return (
      <div className="min-h-screen bg-app flex flex-col items-center justify-center transition-theme">
        <BannerAd />
        <div className="text-center py-24">
          <div className="text-lg text-neutral-600">Loading article…</div>
        </div>
      </div>
    );
  }

  // --- 🔹 Error view ---
  if (error) {
    return (
      <div className="min-h-screen bg-app flex flex-col items-center justify-center transition-theme">
        <BannerAd />
        <div className="text-center">
          <h1 className="font-['Playfair_Display'] text-4xl font-bold text-[#0B1C3B] mb-4">
            Article Not Found
          </h1>
          <p className="text-neutral-600 mb-8">{error}</p>
          <Link to="/blog">
            <Button className="bg-[#0B1C3B] hover:bg-[#1E293B] text-white">
              Back to Blog
            </Button>
          </Link>
        </div>
      </div>
    );
  }

  if (!post) return null;

  const currentIndex = allPosts.findIndex(p => p.id === post.id);
  const prevPost = currentIndex > 0 ? allPosts[currentIndex - 1] : null;
  const nextPost = (currentIndex >= 0 && currentIndex < allPosts.length - 1)
    ? allPosts[currentIndex + 1]
    : null;

  const handleShare = (platform: string) => {
    const url = window.location.href;
    const text = post.title;

    switch (platform) {
      case 'facebook':
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        break;
      case 'linkedin':
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank');
        break;
      case 'twitter':
        window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
        break;
      case 'whatsapp':
        window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`, '_blank');
        break;
      case 'copy':
        navigator.clipboard.writeText(url);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
        break;
    }
  };

  return (
    <div className="min-h-screen bg-app transition-theme">
      {/* ✅ Ad flush below header */}
      <BannerAd />

      <article className="py-12 md:py-20">
        <div className="container mx-auto px-6 md:px-8 max-w-5xl">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            {/* --- Back link --- */}
            <Link
              to="/blog"
              className="inline-flex items-center gap-2 text-app hover:text-accent transition-colors duration-300 mb-8 group"
            >
              <ArrowLeft className="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
              <span className="font-medium">Back to Blog</span>
            </Link>

            {/* --- Post Meta --- */}
            <div className="mb-8">
              <span className="inline-block bg-accent text-[#0B1523] px-4 py-2 rounded-full text-sm font-semibold uppercase tracking-wider mb-6">
                {post.category || 'Uncategorized'}
              </span>

              <h1 className="font-['Playfair_Display'] text-4xl md:text-5xl lg:text-6xl font-bold text-app mb-6 leading-tight">
                {post.title}
              </h1>

              <div className="flex flex-wrap items-center gap-6 text-muted mb-6">
                <div className="flex items-center gap-2">
                  <Calendar className="w-5 h-5" />
                  <time dateTime={post.publishDate || ''}>
                    {post.publishDate ? new Date(post.publishDate).toLocaleDateString('en-US', {
                      month: 'long',
                      day: 'numeric',
                      year: 'numeric'
                    }) : ''}
                  </time>
                </div>
                <div className="flex items-center gap-2">
                  <Clock className="w-5 h-5" />
                  <span>{post.readTime || ''}</span>
                </div>
              </div>

              {/* --- Share buttons --- */}
              <div className="flex items-center gap-4 pb-6 border-b border-app">
                <span className="text-sm font-medium text-app">Share:</span>
                <div className="flex gap-2">
                  <button onClick={() => handleShare('facebook')} className="p-2 rounded-full bg-neutral-100 hover:bg-blue-600 hover:text-white transition-all duration-300" aria-label="Share on Facebook"><Facebook className="w-4 h-4" /></button>
                  <button onClick={() => handleShare('linkedin')} className="p-2 rounded-full bg-neutral-100 hover:bg-blue-700 hover:text-white transition-all duration-300" aria-label="Share on LinkedIn"><Linkedin className="w-4 h-4" /></button>
                  <button onClick={() => handleShare('twitter')} className="p-2 rounded-full bg-neutral-100 hover:bg-black hover:text-white transition-all duration-300" aria-label="Share on Twitter"><Twitter className="w-4 h-4" /></button>
                  <button onClick={() => handleShare('whatsapp')} className="p-2 rounded-full bg-neutral-100 hover:bg-green-600 hover:text-white transition-all duration-300" aria-label="Share on WhatsApp"><Share2 className="w-4 h-4" /></button>
                  <button onClick={() => handleShare('copy')} className="p-2 rounded-full bg-neutral-100 hover:bg-[#D4AF37] hover:text-white transition-all duration-300 relative" aria-label="Copy link">
                    <Link2 className="w-4 h-4" />
                    {copied && (
                      <span className="absolute -top-8 left-1/2 -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded whitespace-nowrap">Copied!</span>
                    )}
                  </button>
                </div>
              </div>
            </div>

            {/* --- Featured Image --- */}
            <div className="aspect-[16/9] rounded-3xl overflow-hidden mb-12 shadow-2xl">
              <img
                src={post.featuredImage || mediaPath('uploads/default_cover.png')}
                alt={post.title || 'Article image'}
                className="w-full h-full object-cover"
                loading="eager"
                onError={(e) => {
                  const t = e.target as HTMLImageElement;
                  t.onerror = null;
                  t.src = mediaPath('uploads/default_cover.png');
                }}
              />
            </div>

            {/* --- Article Content --- */}
            <div className="grid lg:grid-cols-12 gap-12">
              <div className="lg:col-span-8">
                <div
                  className="prose prose-lg max-w-none
                    prose-headings:font-['Playfair_Display'] prose-headings:text-[#0B1C3B]
                    prose-p:text-neutral-700 prose-p:leading-relaxed prose-p:text-lg prose-p:mb-6
                    prose-a:text-[#D4AF37] prose-a:no-underline hover:prose-a:underline
                    prose-strong:text-[#0B1C3B] prose-strong:font-semibold
                    prose-blockquote:border-l-4 prose-blockquote:border-[#D4AF37] prose-blockquote:pl-6 prose-blockquote:italic"
                  dangerouslySetInnerHTML={{ __html: post.content || '' }}
                />

                <Advertisement type="inline" />

                <div className="mt-12 pt-8 border-t border-neutral-200">
                  <h3 className="text-lg font-semibold text-[#0B1C3B] mb-4">Tags:</h3>
                  <div className="flex flex-wrap gap-3">
                    {(post.tags && post.tags.length > 0) ? (
                      post.tags.map((tag) => (
                        <span key={tag} className="px-4 py-2 bg-neutral-100 hover:bg-[#0B1C3B] hover:text-white rounded-full text-sm font-medium transition-all duration-300 cursor-pointer">
                          #{tag}
                        </span>
                      ))
                    ) : (
                      <span className="text-sm text-neutral-500">No tags</span>
                    )}
                  </div>
                </div>

                <div className="mt-12 p-8 bg-gradient-to-br from-[#0B1C3B] to-[#1E293B] rounded-3xl text-white">
                  <div className="flex gap-6 items-start">
                    <div className="flex-shrink-0">
                      <div className="w-20 h-20 rounded-full bg-[#D4AF37] flex items-center justify-center text-2xl font-bold">DG</div>
                    </div>
                    <div>
                      <h3 className="font-['Playfair_Display'] text-2xl font-bold mb-2">About the Author</h3>
                      <p className="text-lg font-medium mb-3">{post.author || 'Dr. Gift Chidima'}</p>
                      <p className="text-neutral-300 leading-relaxed">
                        Dr. Gift Chidima Nnamoko Orairu is a transformational leader, visionary entrepreneur,
                        and passionate advocate for African excellence. As Founder and CEO of The New Africa Group,
                        she empowers the next generation of African leaders through mentorship, coaching, and
                        purpose-driven transformation.
                      </p>
                    </div>
                  </div>
                </div>

                <CommentsSection postId={parseInt(id || '0')} />
              </div>

              <aside className="lg:col-span-4">
                <div className="sticky top-8 space-y-8">
                  <Advertisement type="sidebar" />

                  <div className="bg-gradient-to-br from-[#D4AF37] to-[#B8941F] rounded-3xl p-8 text-white">
                    <h3 className="font-['Playfair_Display'] text-2xl font-bold mb-4">Work with Dr. Gift</h3>
                    <p className="mb-6 leading-relaxed">Transform your leadership journey with personalized coaching and mentorship.</p>
                    <Link to="/book-me">
                      <Button className="w-full bg-white text-[#0B1C3B] hover:bg-neutral-100 font-semibold">Book a Session</Button>
                    </Link>
                  </div>
                </div>
              </aside>
            </div>

            {/* --- Prev / Next navigation --- */}
            <div className="mt-16 flex items-center justify-between gap-4 pt-8 border-t border-neutral-200">
              {prevPost ? (
                <button onClick={() => navigate(`/blog/${prevPost.id}`)} className="flex items-center gap-3 group hover:text-[#D4AF37] transition-colors">
                  <ChevronLeft className="w-6 h-6 group-hover:-translate-x-1 transition-transform" />
                  <div className="text-left">
                    <p className="text-sm text-neutral-500 mb-1">Previous Article</p>
                    <p className="font-semibold">{prevPost.title}</p>
                  </div>
                </button>
              ) : <div />}

              {nextPost && (
                <button onClick={() => navigate(`/blog/${nextPost.id}`)} className="flex items-center gap-3 group hover:text-[#D4AF37] transition-colors text-right ml-auto">
                  <div>
                    <p className="text-sm text-neutral-500 mb-1">Next Article</p>
                    <p className="font-semibold">{nextPost.title}</p>
                  </div>
                  <ChevronRight className="w-6 h-6 group-hover:translate-x-1 transition-transform" />
                </button>
              )}
            </div>
          </motion.div>
        </div>
      </article>

      {/* --- Related Posts --- */}
      {relatedPosts.length > 0 && (
        <section className="py-20 bg-neutral-50">
          <div className="container mx-auto px-6 md:px-8">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
              className="text-center mb-12"
            >
              <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-[#0B1C3B] mb-4">
                Related Articles
              </h2>
              <p className="text-xl text-neutral-600">
                Continue exploring insights on {post.category?.toLowerCase()}
              </p>
            </motion.div>

            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
              {relatedPosts.map((relatedPost, index) => (
                <BlogCard key={relatedPost.id} post={relatedPost as any} index={index} />
              ))}
            </div>
          </div>
        </section>
      )}
    </div>
  );
}
