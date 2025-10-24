import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Menu, X } from 'lucide-react';
import { Button } from './ui/button';

interface NavLink {
  label: string;
  href: string;
}

const navLinks: NavLink[] = [
  { label: 'Home', href: '#' },
  { label: 'About', href: '#about' },
  { label: 'Blog', href: '#blog' },
  { label: 'Store', href: '#store' },
  { label: 'Book Me', href: '#book-me' },
  { label: 'Contact', href: '#contact' },
];

export function Header() {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [activeLink, setActiveLink] = useState('Home');

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const handleLinkClick = (label: string) => {
    setActiveLink(label);
    setIsMobileMenuOpen(false);
  };

  return (
    <>
      <motion.header
        initial={{ y: -100, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1] }}
        className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
          isScrolled
            ? 'bg-white/95 backdrop-blur-md border-b border-neutral-200'
            : 'bg-transparent'
        }`}
      >
        <div className="container mx-auto px-4 sm:px-6 md:px-12 lg:px-16">
          <div className="flex items-center justify-between gap-4 h-16 sm:h-20 md:h-24">
            <motion.a
              href="#"
              className="relative group flex-shrink-0"
              whileHover={{ scale: 1.02 }}
              transition={{ duration: 0.2 }}
              onClick={() => handleLinkClick('Home')}
            >
              <span className="font-['Cormorant_Garamond'] text-lg sm:text-xl md:text-2xl lg:text-3xl font-light text-black tracking-tight whitespace-nowrap">
                <span className="hidden sm:inline">Dr. Gift Chidima Nnamoko</span>
                <span className="sm:hidden">Dr. Gift C.N.</span>
              </span>
            </motion.a>

            <nav className="hidden lg:flex items-center space-x-6 xl:space-x-8">
              {navLinks.map((link) => (
                <a
                  key={link.label}
                  href={link.href}
                  onClick={() => handleLinkClick(link.label)}
                  className="relative group whitespace-nowrap"
                >
                  <span
                    className={`text-xs xl:text-sm uppercase tracking-widest font-light transition-colors duration-200 ${
                      activeLink === link.label
                        ? 'text-black'
                        : 'text-neutral-500 hover:text-black'
                    }`}
                  >
                    {link.label}
                  </span>
                  {activeLink === link.label && (
                    <motion.div
                      layoutId="activeLink"
                      className="absolute -bottom-1 left-0 right-0 h-px bg-black"
                      transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}
                    />
                  )}
                </a>
              ))}
            </nav>

            <div className="hidden lg:flex items-center space-x-4 flex-shrink-0">
              <Button
                className="bg-black hover:bg-neutral-800 text-white font-light px-6 xl:px-8 py-4 xl:py-5 text-xs xl:text-sm uppercase tracking-widest transition-all duration-300 whitespace-nowrap"
                onClick={() => handleLinkClick('Book Me')}
              >
                Book Dr. Gift
              </Button>
            </div>

            <button
              className="lg:hidden p-3 -mr-3 text-black touch-manipulation flex-shrink-0"
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              aria-label="Toggle menu"
              aria-expanded={isMobileMenuOpen}
            >
              {isMobileMenuOpen ? (
                <X className="w-6 h-6" strokeWidth={1.5} />
              ) : (
                <Menu className="w-6 h-6" strokeWidth={1.5} />
              )}
            </button>
          </div>
        </div>
      </motion.header>

      <AnimatePresence>
        {isMobileMenuOpen && (
          <>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.3 }}
              className="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 lg:hidden"
              onClick={() => setIsMobileMenuOpen(false)}
            />

            <motion.div
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
              className="fixed top-0 right-0 bottom-0 w-full sm:w-80 max-w-sm bg-white z-50 lg:hidden shadow-2xl"
            >
              <div className="flex flex-col h-full">
                <div className="flex items-center justify-between p-6 border-b border-neutral-200">
                  <span className="font-['Cormorant_Garamond'] text-2xl font-light text-black">
                    Menu
                  </span>
                  <button
                    onClick={() => setIsMobileMenuOpen(false)}
                    className="p-3 -mr-2 text-black touch-manipulation"
                    aria-label="Close menu"
                  >
                    <X className="w-6 h-6" strokeWidth={1.5} />
                  </button>
                </div>

                <nav className="flex-1 px-6 py-8 space-y-6 overflow-y-auto">
                  {navLinks.map((link, index) => (
                    <motion.a
                      key={link.label}
                      href={link.href}
                      initial={{ opacity: 0, x: 20 }}
                      animate={{ opacity: 1, x: 0 }}
                      transition={{ duration: 0.3, delay: index * 0.05 }}
                      onClick={() => handleLinkClick(link.label)}
                      className="block py-2 touch-manipulation"
                    >
                      <span
                        className={`text-base uppercase tracking-widest font-light transition-colors duration-200 ${
                          activeLink === link.label
                            ? 'text-black'
                            : 'text-neutral-500'
                        }`}
                      >
                        {link.label}
                      </span>
                      {activeLink === link.label && (
                        <div className="h-px w-12 bg-black mt-2" />
                      )}
                    </motion.a>
                  ))}
                </nav>

                <div className="p-6 border-t border-neutral-200 flex-shrink-0">
                  <Button
                    className="w-full bg-black hover:bg-neutral-800 text-white font-light py-6 text-sm uppercase tracking-widest transition-all duration-300 touch-manipulation"
                    onClick={() => {
                      handleLinkClick('Book Me');
                      setIsMobileMenuOpen(false);
                    }}
                  >
                    Book Dr. Gift
                  </Button>
                </div>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </>
  );
}
