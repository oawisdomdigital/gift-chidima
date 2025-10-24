import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { ArrowDown } from "lucide-react";
import { Link } from "react-router-dom";
import { Button } from "./ui/button";

interface HeroContent {
  welcome_text?: string;
  name?: string;
  tagline?: string;
  description?: string;
  button_text?: string;
  button_link?: string;
  image_path?: string;
}


export function HeroSection() {
  const [content, setContent] = useState<HeroContent | null>(null);

  useEffect(() => {
    fetch("http://localhost/myapp/api/get_hero.php")
      .then((res) => res.json())
      .then((data) => setContent(data))
      .catch((err) => console.error("Error fetching hero content:", err));
  }, []);

  const scrollToAbout = () => {
    const aboutSection = document.getElementById("about");
    if (aboutSection) {
      aboutSection.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  };

  if (!content) return null;

  return (
    <section
      className="relative bg-surface min-h-screen flex items-center py-16 md:py-24 transition-theme"
      style={{ paddingTop: "calc(var(--nav-h) + 4rem)" }}
    >
      <div className="container mx-auto px-6 md:px-12 lg:px-16">
        <div className="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          {/* TEXT SECTION */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }}
            className="space-y-6 md:space-y-8 order-2 lg:order-1"
          >
            {content.welcome_text && (
              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: 0.1 }}
                className="inline-block"
              >
                <span className="text-[#D4AF37] text-sm md:text-base font-semibold uppercase tracking-[0.3em] border-b-2 border-[#D4AF37] pb-1">
                  {content.welcome_text}
                </span>
              </motion.div>
            )}

            {(content.title_line1 || content.title_line2) && (
              <motion.h1
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{
                  duration: 0.8,
                  delay: 0.2,
                  ease: [0.16, 1, 0.3, 1],
                }}
                className="font-['Playfair_Display'] text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-bold text-app leading-[1.1] tracking-tight"
              >
                {content.title_line1}
                {content.title_line2 && (
                  <>
                    <br />
                    {content.title_line2}
                  </>
                )}
              </motion.h1>
            )}

            {content.subtitle && (
              <motion.h4
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: 0.4 }}
                className="text-base md:text-lg font-medium text-accent tracking-wider uppercase"
              >
                {content.subtitle}
              </motion.h4>
            )}

            {content.description && (
              <motion.p
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: 0.6 }}
                className="text-base md:text-lg text-gray-700 dark:text-app leading-relaxed font-light max-w-[35ch]"
              >
                {content.description}
              </motion.p>
            )}

            {content.button_text && (
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: 0.8 }}
                className="flex flex-col sm:flex-row gap-4 pt-4"
              >
                <Link to={content.button_link || "#"}>
                  <Button
                    size="lg"
                    className="w-full sm:w-auto btn-gold font-bold px-8 md:px-10 py-6 md:py-7 text-sm md:text-base uppercase tracking-widest transition-all duration-300 rounded-2xl shadow-lg hover:shadow-xl hover:scale-[1.02] border-2"
                  >
                    {content.button_text}
                  </Button>
                </Link>
              </motion.div>
            )}
          </motion.div>

          {/* IMAGE SECTION */}
          {content.image && (
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8, delay: 0.3, ease: [0.16, 1, 0.3, 1] }}
              className="relative order-1 lg:order-2 flex justify-center lg:justify-end"
            >
              <div className="relative">
                <motion.div
                  animate={{ rotate: [0, 5, 0] }}
                  transition={{ duration: 6, repeat: Infinity, ease: "easeInOut" }}
                  className="absolute -top-8 -left-8 w-32 h-32 md:w-40 md:h-40 border-4 border-[#D4AF37] rounded-full opacity-50"
                ></motion.div>

                <motion.div
                  animate={{ rotate: [0, -5, 0] }}
                  transition={{ duration: 8, repeat: Infinity, ease: "easeInOut" }}
                  className="absolute -bottom-6 -right-6 w-24 h-24 md:w-32 md:h-32 bg-[#D4AF37] rounded-full opacity-20"
                ></motion.div>

                <div className="relative z-10 w-full max-w-sm sm:max-w-md lg:max-w-lg">
                  <div className="relative">
                    <div className="aspect-[3/4] overflow-hidden rounded-3xl shadow-2xl border-4 border-white dark:border-surface-2">
                      <img
                        src={`http://localhost/myapp/uploads/${content.image_path}`}
                        alt={content.name || "Hero Image"}
                        className="w-full h-full object-cover object-center"
                        loading="eager"
                      />

                    </div>
                    <div className="absolute -bottom-4 -right-4 w-full h-full border-4 border-[#D4AF37] rounded-3xl -z-10"></div>
                  </div>
                </div>
              </div>
            </motion.div>
          )}
        </div>
      </div>

      {/* SCROLL BUTTON */}
      <motion.button
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 1, delay: 1.5 }}
        onClick={scrollToAbout}
        className="absolute bottom-12 left-1/2 transform -translate-x-1/2 cursor-pointer hover:opacity-70 transition-opacity"
        aria-label="Scroll to about section"
      >
        <motion.div
          animate={{ y: [0, 8, 0] }}
          transition={{ duration: 2, repeat: Infinity, ease: "easeInOut" }}
        >
          <ArrowDown className="w-6 h-6 text-accent" strokeWidth={2} />
        </motion.div>
      </motion.button>
    </section>
  );
}
