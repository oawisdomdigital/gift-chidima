import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { MessageCircle, Send } from 'lucide-react';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Textarea } from './ui/textarea';
import { Label } from './ui/label';
import { apiUrl } from '../lib/config';

interface Comment {
  id: string;
  name: string;
  email: string;
  comment: string;
  timestamp: Date;
}

interface CommentsSectionProps {
  postId: number; // ✅ we’ll pass this from BlogPostPage
}

export function CommentsSection({ postId }: CommentsSectionProps) {
  const [comments, setComments] = useState<Comment[]>([]);
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [comment, setComment] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchComments = async () => {
      try {
  const response = await fetch(apiUrl(`comments.php?post_id=${postId}`));
        const data = await response.json();
        if (data.success && data.comments) {
          setComments(data.comments.map((c: any) => ({
            id: c.id,
            name: c.name,
            email: c.email,
            comment: c.comment,
            timestamp: new Date(c.created_at)
          })));
        }
      } catch (error) {
        console.error('Error fetching comments:', error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchComments();
  }, [postId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!name.trim() || !email.trim() || !comment.trim()) {
      alert("Please fill in all fields");
      return;
    }

    setIsSubmitting(true);

    try {
      const formData = new URLSearchParams({
        name: name.trim(),
        email: email.trim(),
        comment: comment.trim(),
        post_id: String(postId),
      });

      console.log('Sending data:', Object.fromEntries(formData));

  const res = await fetch(apiUrl('comments.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData,
      });

      const data = await res.json();

      if (data.success) {
        const newComment: Comment = {
          id: data.comment.id.toString(),
          name: data.comment.name,
          email: data.comment.email,
          comment: data.comment.comment,
          timestamp: new Date(data.comment.created_at),
        };
        setComments([newComment, ...comments]);
        setName('');
        setEmail('');
        setComment('');
      } else {
        alert(data.message || 'Failed to submit comment');
      }
    } catch (error) {
      console.error('Error submitting comment:', error);
      alert('Error submitting comment');
    } finally {
      setIsSubmitting(false);
    }
  };

  const formatDate = (date: Date) => {
    return (
      date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      }) +
      ' at ' +
      date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
      })
    );
  };

  return (
    <div className="mt-12 p-6 md:p-8 bg-surface-2 rounded-3xl transition-theme">
      <div className="flex items-center gap-3 mb-6">
        <MessageCircle className="w-6 h-6 text-accent" />
        <h3 className="font-['Playfair_Display'] text-2xl md:text-3xl font-bold text-app">
          Join the Conversation
        </h3>
      </div>

      <p className="text-gray-700 dark:text-app mb-8">
        Share your thoughts and insights on this article.
      </p>

      <motion.form
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        onSubmit={handleSubmit}
        className="bg-white dark:bg-surface rounded-2xl p-6 md:p-8 shadow-md mb-8 transition-theme"
      >
        <div className="space-y-6">
          <div>
            <Label htmlFor="name" className="text-gray-700 dark:text-app font-semibold mb-2">
              Name *
            </Label>
            <Input
              id="name"
              type="text"
              required
              value={name}
              onChange={(e) => setName(e.target.value)}
              disabled={isSubmitting}
            />
          </div>

          <div>
            <Label htmlFor="email" className="text-gray-700 dark:text-app font-semibold mb-2">
              Email *
            </Label>
            <Input
              id="email"
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={isSubmitting}
            />
          </div>

          <div>
            <Label htmlFor="comment" className="text-gray-700 dark:text-app font-semibold mb-2">
              Comment *
            </Label>
            <Textarea
              id="comment"
              required
              value={comment}
              onChange={(e) => setComment(e.target.value)}
              rows={4}
              disabled={isSubmitting}
            />
          </div>

          <Button
            type="submit"
            disabled={isSubmitting}
            className="w-full md:w-auto bg-[#c7a449] border-2 border-[#c7a449] text-black hover:bg-transparent hover:text-[#c7a449] font-semibold px-8 py-6 text-sm uppercase tracking-widest rounded-xl"
          >
            <Send className="w-4 h-4 mr-2" />
            {isSubmitting ? 'Submitting...' : 'Submit Comment'}
          </Button>
        </div>
      </motion.form>

      <div className="space-y-4">
        {isLoading ? (
          <div className="text-center py-8 text-muted">
            Loading comments...
          </div>
        ) : comments.length === 0 ? (
          <div className="text-center py-8 text-muted">
            No comments yet. Be the first to share your thoughts!
          </div>
        ) : (
          <div className="mb-4">
            <h4 className="font-semibold text-lg text-app mb-6">
              {comments.length} {comments.length === 1 ? 'Comment' : 'Comments'}
            </h4>
          </div>
        )}

        <AnimatePresence mode="popLayout">
          {comments.map((c) => (
            <motion.div
              key={c.id}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.3 }}
              className="bg-white dark:bg-surface-2 rounded-2xl p-6 shadow-sm hover:shadow-md transition-theme"
            >
              <div className="flex items-start gap-4">
                <div className="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-[#D4AF37] to-[#B8941F] flex items-center justify-center text-white font-bold text-lg shadow-lg transform hover:scale-105 transition-transform">
                  {c.name.split(' ').map(word => word.charAt(0).toUpperCase()).join('').slice(0, 2)}
                </div>
                <div className="flex-1">
                  <div className="flex items-baseline gap-2 mb-2">
                    <h5 className="font-bold text-[#0B1C3B] dark:text-app text-lg">{c.name}</h5>
                    <time className="text-xs text-neutral-500">{formatDate(c.timestamp)}</time>
                  </div>
                  <p className="text-gray-700 dark:text-app leading-relaxed">{c.comment}</p>
                </div>
              </div>
            </motion.div>
          ))}
        </AnimatePresence>
      </div>
    </div>
  );
}
