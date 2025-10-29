import { motion } from 'framer-motion';
import { Calendar, Clock, ArrowRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import { mediaPath } from '../lib/config';
import type { BlogPost } from '../services/blogService';

interface BlogCardProps {
  post: BlogPost;
  index?: number;
}

export function BlogCard({ post, index = 0 }: BlogCardProps) {
  return (
    <motion.article
      initial={{ opacity: 0, y: 30 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.6, delay: index * 0.1 }}
      className="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500"
    >
      <Link to={`/blog/${post.id}`} className="block">
        <div className="aspect-[16/10] overflow-hidden bg-neutral-200">
          <img
            src={
              post.featuredImage
                ? (post.featuredImage.startsWith('http')
                  ? post.featuredImage
                  : mediaPath(post.featuredImage))
                : mediaPath('uploads/default_cover.png')
            }
            alt={post.title}
            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
            loading="lazy"
            onError={(e) => {
              const target = e.target as HTMLImageElement;
              target.onerror = null;
              target.src = mediaPath('uploads/default_cover.png');
            }}
          />
        </div>

        <div className="p-6 sm:p-8 space-y-4">
          <div className="flex items-center gap-4 text-sm text-neutral-600">
            {post.category && (
              <span className="inline-flex items-center gap-1.5 bg-[#0B1C3B] text-white px-3 py-1 rounded-full text-xs font-medium uppercase tracking-wider">
                {post.category}
              </span>
            )}
            {post.readTime && (
              <span className="flex items-center gap-1.5">
                <Clock className="w-4 h-4" />
                {post.readTime}
              </span>
            )}
          </div>

          <h3 className="font-['Playfair_Display'] text-2xl sm:text-3xl font-bold text-[#0B1C3B] group-hover:text-[#D4AF37] transition-colors duration-300 leading-tight">
            {post.title}
          </h3>

          <p className="text-neutral-700 leading-relaxed line-clamp-3">
            {post.excerpt}
          </p>

          <div className="flex items-center justify-between pt-4 border-t border-neutral-200">
            {post.publishDate && (
              <div className="flex items-center gap-2 text-sm text-neutral-600">
                <Calendar className="w-4 h-4" />
                <time dateTime={post.publishDate}>
                  {new Date(post.publishDate).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                  })}
                </time>
              </div>
            )}

            <span className="inline-flex items-center gap-2 text-[#0B1C3B] font-medium group-hover:text-[#D4AF37] transition-colors duration-300">
              Read More
              <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" />
            </span>
          </div>
        </div>
      </Link>
    </motion.article>
  );
}
