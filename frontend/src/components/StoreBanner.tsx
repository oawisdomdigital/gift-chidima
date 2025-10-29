import { motion } from "framer-motion";
import { ShoppingBag, ArrowRight } from "lucide-react";
import { Link } from "react-router-dom";
import { Button } from "./ui/button";
import { SectionWrapper } from "./SectionWrapper";
import { apiUrl, mediaPath } from '../lib/config';
import { useEffect, useState } from "react";

interface Book {
  title: string;
  cover_label: string;
  cover_image?: string;
}

interface StoreBannerData {
  section_title: string;
  section_subtitle: string;
  description: string;
  button_text: string;
  button_link: string;
  bg_color_from: string;
  bg_color_to: string;
  books: Book[];
}

export function StoreBanner() {
  const [data, setData] = useState<StoreBannerData | null>(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await fetch(`${apiUrl}/get_books.php`);
        if (!response.ok) {
          throw new Error('Failed to fetch store banner data');
        }

        const result = await response.json();
        setData(result);
      } catch (error) {
        console.error('Error fetching store banner data:', error);
      }
    };

    fetchData();
  }, []);

  if (!data) return null;

  return (
    <SectionWrapper
      className={`bg-gradient-to-br ${data.bg_color_from} ${data.bg_color_to}`}
    >
      <div className="container mx-auto px-6 md:px-8">
        <div className="max-w-6xl mx-auto">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            {/* ===== Left Section: Banner Text ===== */}
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="inline-flex items-center gap-2 bg-amber-500/20 px-4 py-2 rounded-full mb-6">
                <ShoppingBag className="w-5 h-5 text-amber-700" />
                <span className="text-sm font-semibold text-amber-700 uppercase tracking-wide">
                  {data.section_subtitle}
                </span>
              </div>

              <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-slate-900 mb-6">
                {data.section_title}
              </h2>

              <p className="text-xl text-slate-700 leading-relaxed mb-8">
                {data.description}
              </p>

              <Link to={data.button_link}>
                <Button
                  size="lg"
                  className="bg-amber-500 hover:bg-amber-600 text-slate-900 font-semibold px-8 py-6 text-lg rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 group"
                >
                  {data.button_text}
                  <ArrowRight className="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
                </Button>
              </Link>
            </motion.div>

            {/* ===== Right Section: Books Grid ===== */}
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="grid grid-cols-3 gap-4"
            >
              {data.books.map((book, index) => (
                <motion.div
                  key={index}
                  whileHover={{ y: -10, scale: 1.05 }}
                  transition={{ duration: 0.3 }}
                  className="cursor-pointer"
                >
                  <div className="relative aspect-[3/4] bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-slate-200 hover:border-amber-400 transition-all group">
                    {book.cover_image ? (
                      <img
                        src={
                          book.cover_image.startsWith("http")
                            ? book.cover_image
                            : mediaPath(book.cover_image.replace(/^\/+/, ""))
                        }
                        alt={book.title}
                        onError={(e) => {
                          console.error("Image load error:", book.cover_image);
                          const target = e.target as HTMLImageElement;
                          target.onerror = null;
                          target.src = mediaPath('uploads/default_cover.png');
                        }}
                        className="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                      />
                    ) : (
                      <div className="absolute inset-0 flex flex-col items-center justify-center bg-amber-50">
                        <div className="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center mb-3">
                          <ShoppingBag className="w-6 h-6 text-amber-600" />
                        </div>
                        <p className="text-xs font-medium text-slate-600">
                          {book.cover_label}
                        </p>
                      </div>
                    )}
                  </div>
                  <p className="text-sm font-semibold text-slate-800 text-center mt-3">
                    {book.title}
                  </p>
                </motion.div>
              ))}
            </motion.div>
          </div>
        </div>
      </div>
    </SectionWrapper>
  );
}
