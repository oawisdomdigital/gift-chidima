import { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { Trophy } from 'lucide-react';
import { SectionWrapper } from './SectionWrapper';
import { apiUrl, mediaPath } from '../lib/config';

interface Award {
  award_title: string;
  award_icon: string;
}

interface Media {
  media_logo: string;
}

export function AwardsSection() {
  const [heading, setHeading] = useState('');
  const [subheading, setSubheading] = useState('');
  const [awards, setAwards] = useState<Award[]>([]);
  const [media, setMedia] = useState<Media[]>([]);

  useEffect(() => {
  fetch(apiUrl('get_awards.php'))
      .then(res => res.json())
      .then(data => {
        setHeading(data.heading || '');
        setSubheading(data.subheading || '');
        setAwards(data.awards || []);
        setMedia(data.media || []);
      });
  }, []);

  return (
    <SectionWrapper className="bg-gradient-to-br from-slate-900 to-slate-800 text-white">
      <div className="container mx-auto px-6 md:px-8">
        <div className="text-center mb-16">
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5 }}
            className="inline-block mb-6"
          >
            <div className="p-4 bg-amber-500/20 rounded-full">
              <Trophy className="w-12 h-12 text-amber-400" />
            </div>
          </motion.div>
          <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold mb-4">{heading}</h2>
          <p className="text-xl text-slate-300 max-w-3xl mx-auto">{subheading}</p>
        </div>

        <div className="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto mb-16">
          {awards.map((award, index) => (
            <motion.div
              key={index}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5 }}
              className="flex items-start gap-4 p-6 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-all"
            >
              <div className="flex-shrink-0 mt-1">
                <img
                  src={mediaPath(award.award_icon)}
                  alt=""
                  className="w-8 h-8 object-contain"
                  onError={(e) => (e.currentTarget.src = '/images/placeholder-logo.png')}
                />
              </div>
              <p className="text-lg text-slate-200 leading-relaxed">{award.award_title}</p>
            </motion.div>
          ))}
        </div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.4 }}
        >
          <div className="text-center mb-8">
            <h3 className="text-2xl font-semibold text-slate-200 mb-8">Featured In</h3>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
            {media.map((m, index) => (
              <div key={index} className="aspect-[3/2] bg-white/5 rounded-xl border border-white/10 flex items-center justify-center p-6 hover:bg-white/10 transition-all">
                <img
                  src={mediaPath(m.media_logo)}
                  alt="Media Logo"
                  className="max-h-10 object-contain"
                  onError={(e) => (e.currentTarget.src = '/images/placeholder-logo.png')}
                />
              </div>
            ))}
          </div>
        </motion.div>
      </div>
    </SectionWrapper>
  );
}
