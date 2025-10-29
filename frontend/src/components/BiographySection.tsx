import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { SectionWrapper } from "./SectionWrapper";
import { apiUrl, mediaPath } from '../lib/config';

interface BioContent {
  title: string;
  image_path: string;
  paragraph1: string;
  paragraph2: string;
  paragraph3: string;
  quote: string;
}

export function BiographySection() {
  const [content, setContent] = useState<BioContent | null>(null);

  useEffect(() => {
    fetch(apiUrl('get_biography.php'))
      .then((res) => res.json())
      .then((data) => setContent(data))
      .catch((err) => console.error("Error fetching biography:", err));
  }, []);

  if (!content) return null;

  return (
    <SectionWrapper id="about" className="bg-white">
      <div className="container mx-auto px-6 md:px-8">
        <div className="grid md:grid-cols-2 gap-12 lg:gap-16 items-center">
          {/* IMAGE SECTION */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="relative"
          >
            <div className="aspect-[4/3] sm:aspect-[3/4] rounded-3xl overflow-hidden shadow-2xl">
              <img
                src={mediaPath(`uploads/${content.image_path}`)}
                alt={content.title || "Biography image"}
                className="w-full h-full object-cover object-center"
                loading="lazy"
              />
            </div>
            <div className="absolute -bottom-4 sm:-bottom-6 -right-4 sm:-right-6 w-24 h-24 sm:w-32 sm:h-32 bg-amber-500 rounded-full opacity-20 blur-3xl"></div>
            <div className="absolute -top-4 sm:-top-6 -left-4 sm:-left-6 w-32 h-32 sm:w-40 sm:h-40 bg-slate-800 rounded-full opacity-10 blur-3xl"></div>
          </motion.div>

          {/* TEXT SECTION */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="space-y-6"
          >
            <div>
              <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-slate-900 mb-4">
                {content.title || "About"}
              </h2>
              <div className="w-20 h-1 bg-amber-500 rounded-full"></div>
            </div>

            <div className="space-y-4 text-slate-700 leading-relaxed text-lg">
              {content.paragraph1 && <p>{content.paragraph1}</p>}
              {content.paragraph2 && <p>{content.paragraph2}</p>}
              {content.paragraph3 && <p>{content.paragraph3}</p>}
              {content.quote && (
                <p className="font-semibold text-slate-900 italic">
                  “{content.quote}”
                </p>
              )}
            </div>
          </motion.div>
        </div>
      </div>
    </SectionWrapper>
  );
}
