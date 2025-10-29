import React, { useState, useEffect } from 'react';
import { apiUrl } from '../lib/config';
import { motion } from 'framer-motion';
import {
  Mail,
  Send,
  Lightbulb,
  Users,
  Target,
  Heart,
  Sparkles,
  BookOpen,
  Brain,
  Gem,
  Star,
  Trophy
} from 'lucide-react';
import { Button } from '../components/ui/button';
import { Input } from '../components/ui/input';
import { Textarea } from '../components/ui/textarea';
import { Label } from '../components/ui/label';

interface Section {
  title: string;
  subtitle: string;
  content?: {
    description?: string;
    bullet_points?: string[];
    email?: string;
  };
  image_url?: string;
}

interface Topic {
  icon: string;
  title: string;
  description: string;
}

interface PageData {
  sections: {
    [key: string]: Section;
  };
  topics: Topic[];
}

export function BookMe() {
  const [pageData, setPageData] = useState<PageData>({
    sections: {},
    topics: []
  });
  const [loading, setLoading] = useState(true);
  const [formData, setFormData] = useState({
    fullName: '',
    email: '',
    organization: '',
    eventType: '',
    preferredDate: '',
    location: '',
    audienceSize: '',
    topics: '',
    budget: '',
    message: '',
  });
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    const fetchPageData = async () => {
      try {
        const response = await fetch(apiUrl('get_bookme.php'));
        const result = await response.json();
        if (result.success) {
          setPageData(result.data);
        }
      } catch (error) {
        console.error('Error fetching page data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchPageData();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    try {
      const response = await fetch(apiUrl('bookme_form.php'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      const result = await response.json();
      if (result.success) {
        setIsSubmitted(true);
      } else {
        alert(result.message || 'Failed to submit booking request');
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      alert('Failed to submit booking request');
    } finally {
      setIsSubmitting(false);
    }

    setTimeout(() => {
      setIsSubmitted(false);
      setFormData({
        fullName: '',
        email: '',
        organization: '',
        eventType: '',
        preferredDate: '',
        location: '',
        audienceSize: '',
        topics: '',
        budget: '',
        message: '',
      });
    }, 5000);
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-white flex items-center justify-center">
        <div className="text-center">
          <div className="w-16 h-16 border-4 border-[#D4AF37] border-t-transparent rounded-full animate-spin mb-4"></div>
          <p className="text-slate-600">Loading...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-white">
      <section className="relative bg-gradient-to-br from-white via-slate-50 to-white pt-32 pb-20">
        <div className="container mx-auto px-6 md:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-center max-w-7xl mx-auto">
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
              className="space-y-6"
            >
              <div className="inline-block">
                <span className="text-[#D4AF37] text-sm font-semibold uppercase tracking-[0.3em] border-b-2 border-[#D4AF37] pb-1">
                  {pageData.sections.hero?.subtitle || "Let's Connect"}
                </span>
              </div>

              <h1 className="font-['Playfair_Display'] text-5xl md:text-6xl lg:text-7xl font-bold text-[#0B1C3B] leading-tight">
                {pageData.sections.hero?.title || 'Book Dr. Gift'}
              </h1>

              <p className="text-lg md:text-xl text-slate-700 leading-relaxed">
                {pageData.sections.hero?.content?.description || ''}
              </p>

              <div className="pt-4 space-y-4">
                {pageData.sections.hero?.content?.bullet_points?.map((point, index) => (
                  <div key={index} className="flex items-start gap-3">
                    <div className="w-2 h-2 rounded-full bg-[#D4AF37] mt-2"></div>
                    <p className="text-slate-600">{point}</p>
                  </div>
                ))}
              </div>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, x: 30 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="relative"
            >
              <div className="relative">
                <div className="aspect-[4/3] overflow-hidden rounded-3xl shadow-2xl border-4 border-white">
                  <img
                    src="/images/dr-gift-chidima-2.jpg"
                    alt="Dr. Gift Chidima Nnamoko Orairu"
                    className="w-full h-full object-cover object-center"
                  />
                </div>
                <div className="absolute -bottom-4 -right-4 w-full h-full border-4 border-[#D4AF37] rounded-3xl -z-10"></div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      <section className="py-20 bg-white">
        <div className="container mx-auto px-6 md:px-8">
          <div className="max-w-7xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
              className="text-center mb-16"
            >
              <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-[#0B1C3B] mb-4">
                {pageData.sections.topics?.title || 'Speaking & Coaching Topics'}
              </h2>
              <p className="text-xl text-slate-600 max-w-3xl mx-auto">
                {pageData.sections.topics?.subtitle || ''}
              </p>
            </motion.div>

            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
              {pageData.topics.map((topic, index) => (
                <motion.div
                  key={topic.title}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  whileHover={{ y: -8 }}
                  className="bg-gradient-to-br from-slate-50 to-slate-100 p-8 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-slate-200"
                >
                  <div className="w-14 h-14 bg-[#D4AF37] rounded-xl flex items-center justify-center mb-6">
                    {(() => {
                      const IconComponent = {
                        Lightbulb, Users, Target, Heart, Sparkles, BookOpen,
                        Brain, Gem, Star, Trophy
                      }[topic.icon];
                      return IconComponent ? <IconComponent className="w-7 h-7 text-white" /> : null;
                    })()}
                  </div>
                  <h3 className="text-xl font-bold text-slate-900 mb-3">
                    {topic.title}
                  </h3>
                  <p className="text-slate-600 leading-relaxed">
                    {topic.description}
                  </p>
                </motion.div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="py-20 bg-gradient-to-br from-slate-50 to-white">
        <div className="container mx-auto px-6 md:px-8">
          <div className="max-w-2xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
              className="text-center mb-12"
            >
              <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-[#0B1C3B] mb-4">
                {pageData.sections.contact?.title || 'Send a Booking Request'}
              </h2>
              <p className="text-lg text-slate-600">
                {pageData.sections.contact?.subtitle || ''}
              </p>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-slate-200"
            >
              {isSubmitted ? (
                <div className="text-center py-12">
                  <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <Send className="w-10 h-10 text-green-600" />
                  </div>
                  <h3 className="text-2xl font-bold text-slate-900 mb-3">
                    Request Sent Successfully!
                  </h3>
                  <p className="text-slate-600 text-lg">
                    Thank you for reaching out. We'll be in touch soon.
                  </p>
                </div>
              ) : (
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div>
                    <Label htmlFor="fullName" className="text-slate-700 font-semibold mb-2">
                      Full Name *
                    </Label>
                    <Input
                      id="fullName"
                      name="fullName"
                      type="text"
                      required
                      value={formData.fullName}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="Enter your full name"
                    />
                  </div>

                  <div>
                    <Label htmlFor="email" className="text-slate-700 font-semibold mb-2">
                      Email Address *
                    </Label>
                    <Input
                      id="email"
                      name="email"
                      type="email"
                      required
                      value={formData.email}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="your.email@example.com"
                    />
                  </div>

                  <div>
                    <Label htmlFor="organization" className="text-slate-700 font-semibold mb-2">
                      Organization / Company
                    </Label>
                    <Input
                      id="organization"
                      name="organization"
                      type="text"
                      value={formData.organization}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="Your organization name"
                    />
                  </div>

                  <div>
                    <Label htmlFor="eventType" className="text-slate-700 font-semibold mb-2">
                      Event Type
                    </Label>
                    <Input
                      id="eventType"
                      name="eventType"
                      type="text"
                      value={formData.eventType}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="e.g., Conference, Workshop, Private Coaching"
                    />
                  </div>

                  <div>
                    <Label htmlFor="preferredDate" className="text-slate-700 font-semibold mb-2">
                      Preferred Date
                    </Label>
                    <Input
                      id="preferredDate"
                      name="preferredDate"
                      type="text"
                      value={formData.preferredDate}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="e.g., March 2026 or Flexible"
                    />
                  </div>

                  <div>
                    <Label htmlFor="location" className="text-slate-700 font-semibold mb-2">
                      Event Location
                    </Label>
                    <Input
                      id="location"
                      name="location"
                      type="text"
                      value={formData.location}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="City, Country or Virtual"
                    />
                  </div>

                  <div>
                    <Label htmlFor="audienceSize" className="text-slate-700 font-semibold mb-2">
                      Expected Audience Size
                    </Label>
                    <Input
                      id="audienceSize"
                      name="audienceSize"
                      type="text"
                      value={formData.audienceSize}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="e.g., 50-100 people"
                    />
                  </div>

                  <div>
                    <Label htmlFor="topics" className="text-slate-700 font-semibold mb-2">
                      Preferred Topics
                    </Label>
                    <Input
                      id="topics"
                      name="topics"
                      type="text"
                      value={formData.topics}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="Select topics of interest"
                    />
                  </div>

                  <div>
                    <Label htmlFor="budget" className="text-slate-700 font-semibold mb-2">
                      Budget Range
                    </Label>
                    <Input
                      id="budget"
                      name="budget"
                      type="text"
                      value={formData.budget}
                      onChange={handleChange}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37] py-6"
                      placeholder="Your budget range for this engagement"
                    />
                  </div>

                  <div>
                    <Label htmlFor="message" className="text-slate-700 font-semibold mb-2">
                      Message / Details *
                    </Label>
                    <Textarea
                      id="message"
                      name="message"
                      required
                      value={formData.message}
                      onChange={handleChange}
                      rows={6}
                      className="w-full rounded-xl border-slate-300 focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                      placeholder="Tell us about your event, audience size, and what you're hoping to achieve..."
                    />
                  </div>

                  <div className="pt-4">
                    <Button
                      type="submit"
                      disabled={isSubmitting}
                      className="w-full bg-[#D4AF37] hover:bg-[#c39f31] text-black font-bold py-7 text-base uppercase tracking-widest rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300"
                    >
                      {isSubmitting ? 'Sending...' : 'Send Booking Request'}
                    </Button>
                  </div>
                </form>
              )}
            </motion.div>
          </div>
        </div>
      </section>

      <section className="py-16 bg-white border-t border-slate-200">
        <div className="container mx-auto px-6 md:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="max-w-3xl mx-auto text-center"
          >
            <div className="inline-flex items-center justify-center w-16 h-16 bg-[#D4AF37] rounded-full mb-6">
              <Mail className="w-8 h-8 text-white" />
            </div>

            <h3 className="text-2xl font-bold text-slate-900 mb-4">
              {pageData.sections.direct_inquiries?.title || 'Direct Booking Inquiries'}
            </h3>

            <p className="text-slate-600 mb-6 leading-relaxed">
              {pageData.sections.direct_inquiries?.content?.description || ''}
            </p>

            <a
              href={`mailto:${pageData.sections.direct_inquiries?.content?.email || 'bookings@drgiftnnamoko.com'}`}
              className="inline-block text-lg font-semibold text-[#D4AF37] hover:text-[#c39f31] transition-colors"
            >
              {pageData.sections.direct_inquiries?.content?.email || 'bookings@drgiftnnamoko.com'}
            </a>
          </motion.div>
        </div>
      </section>
    </div>
  );
}
