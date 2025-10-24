import { motion } from 'framer-motion';
import { Mail, ArrowRight } from 'lucide-react';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { useState } from 'react';

export function NewsletterSignup() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);

    // basic validation
    if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      setError('Please enter a valid email');
      return;
    }

    setLoading(true);

    try {
      const res = await fetch('http://localhost/myapp/api/subscribe_newsletter.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email }),
      });

      const json = await res.json();
      if (json.success) {
        setIsSubmitted(true);
        setName('');
        setEmail('');
        setTimeout(() => setIsSubmitted(false), 4000);
      } else {
        setError(json.error || 'Subscription failed');
      }
    } catch (err) {
      console.error('Subscribe error:', err);
      setError('Network error');
    } finally {
      setLoading(false);
    }
  };

  return (
    <section className="py-20 bg-gradient-to-br from-[#0B1C3B] to-[#1E293B]">
      <div className="container mx-auto px-6 md:px-8">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="max-w-4xl mx-auto text-center"
        >
          <div className="inline-flex items-center justify-center w-16 h-16 bg-[#D4AF37] rounded-full mb-6">
            <Mail className="w-8 h-8 text-white" />
          </div>

          <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-white mb-4">
            Leadership Insights in Your Inbox
          </h2>

          <p className="text-xl text-neutral-300 mb-10 max-w-2xl mx-auto leading-relaxed">
            Get leadership insights, mentorship wisdom, and transformational content from Dr. Gift directly to your inbox every week.
          </p>

          {isSubmitted ? (
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              className="bg-green-500/20 border border-green-500 rounded-2xl p-6 text-green-100"
            >
              Thank you for subscribing! Check your email for confirmation.
            </motion.div>
          ) : (
            <form onSubmit={handleSubmit} className="max-w-2xl mx-auto">
              <div className="grid sm:grid-cols-2 gap-4 mb-4">
                <Input
                  type="text"
                  placeholder="Your Name"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  disabled={loading}
                  className="h-14 bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-[#D4AF37] rounded-xl"
                />
                <Input
                  type="email"
                  placeholder="Your Email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  disabled={loading}
                  className="h-14 bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-[#D4AF37] rounded-xl"
                />
              </div>

              {error && <div className="text-sm text-red-400 mb-3">{error}</div>}

              <Button
                type="submit"
                size="lg"
                disabled={loading}
                className="w-full sm:w-auto bg-[#D4AF37] hover:bg-[#B8941F] text-white px-12 py-7 text-base font-semibold uppercase tracking-wider rounded-xl transition-all duration-300 shadow-xl hover:shadow-2xl"
              >
                {loading ? 'Subscribing...' : 'Subscribe Now'}
                <ArrowRight className="w-5 h-5 ml-2" />
              </Button>

              <p className="text-sm text-neutral-400 mt-4">
                Join 10,000+ leaders receiving weekly insights. Unsubscribe anytime.
              </p>
            </form>
          )}
        </motion.div>
      </div>
    </section>
  );
}
