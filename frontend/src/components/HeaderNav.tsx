import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Menu, X } from 'lucide-react';
import { Link, useLocation } from 'react-router-dom';
import { Button } from './ui/button';
import { ThemeToggle } from './ThemeToggle';

interface NavLink {
  label: string;
  href: string;
}

const navLinks: NavLink[] = [
  { label: 'Home', href: '/' },
  { label: 'About', href: '/#about' },
  { label: 'Blog', href: '/blog' },
  { label: 'Store', href: '/store' },
  { label: 'Gallery', href: '/gallery' },
];

export function HeaderNav() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const location = useLocation();
  const [activeLink, setActiveLink] = useState('Home');

  useEffect(() => {
    if (location.pathname === '/') {
      setActiveLink('Home');
    } else if (location.pathname.startsWith('/blog')) {
      setActiveLink('Blog');
    } else if (location.pathname === '/store') {
      setActiveLink('Store');
    } else if (location.pathname === '/gallery') {
      setActiveLink('Gallery');
    }
  }, [location]);

  const handleLinkClick = (label: string, href: string) => {
    setActiveLink(label);
    setIsMobileMenuOpen(false);

    if (href.startsWith('/#')) {
      const elementId = href.substring(2);
      setTimeout(() => {
        const element = document.getElementById(elementId);
        if (element) {
          element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }, 100);
    }
  };

  return (
    <>
      <motion.header
        initial={{ y: -100, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1] }}
        className="sticky top-0 z-50 bg-white/90 backdrop-blur shadow-sm transition-theme"
        style={{ background: 'var(--bg)' }}
      >
        <div className="container mx-auto px-4 sm:px-6 md:px-12 lg:px-16" style={{ height: 'var(--nav-h)' }}>
          <div className="flex items-center justify-between gap-4 h-full">
            <Link
              to="/"
              className="relative group flex-shrink-0"
              onClick={() => handleLinkClick('Home', '/')}
            >
              <motion.div
                whileHover={{ scale: 1.02 }}
                transition={{ duration: 0.2 }}
              >
                <span className="font-['Cormorant_Garamond'] text-lg sm:text-xl md:text-2xl lg:text-3xl font-light text-app tracking-tight whitespace-nowrap">
                  <span className="hidden sm:inline">Dr. Gift Chidima Nnamoko</span>
                  <span className="sm:hidden">Dr. Gift C.N.</span>
                </span>
              </motion.div>
            </Link>

            <nav className="hidden lg:flex items-center space-x-6 xl:space-x-8">
              {navLinks.map((link) => (
                <Link
                  key={link.label}
                  to={link.href}
                  onClick={() => handleLinkClick(link.label, link.href)}
                  className="relative group whitespace-nowrap"
                >
                  <span
                    className={`text-xs xl:text-sm uppercase tracking-widest font-light transition-colors duration-200 ${
                      activeLink === link.label
                        ? 'text-app'
                        : 'text-gray-700 dark:text-muted hover:text-app'
                    }`}
                  >
                    {link.label}
                  </span>
                  {activeLink === link.label && (
                    <motion.div
                      layoutId="activeLink"
                      className="absolute -bottom-1 left-0 right-0 h-px bg-accent"
                      transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}
                    />
                  )}
                </Link>
              ))}
            </nav>

            <div className="hidden lg:flex items-center space-x-4 flex-shrink-0">
              <ThemeToggle />
              <Link to="/book-me" onClick={() => handleLinkClick('Book Me', '/book-me')}>
                <Button
                  className="btn-gold font-semibold px-6 xl:px-8 py-4 xl:py-5 text-xs xl:text-sm uppercase tracking-widest transition-all duration-300 whitespace-nowrap rounded-2xl shadow-md hover:shadow-xl border-2"
                >
                  Book Dr. Gift
                </Button>
              </Link>
            </div>

            <div className="lg:hidden flex items-center gap-2 flex-shrink-0">
              <ThemeToggle />
              <button
                className="p-3 -mr-3 text-app touch-manipulation"
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
              className="fixed inset-0 bg-black/20 dark:bg-black/70 backdrop-blur-sm z-40 lg:hidden"
              onClick={() => setIsMobileMenuOpen(false)}
            />

            <motion.div
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
              className="fixed top-0 right-0 bottom-0 w-full sm:w-80 max-w-sm bg-surface z-50 lg:hidden shadow-2xl transition-theme"
            >
              <div className="flex flex-col h-full">
                <div className="flex items-center justify-between p-6 border-b border-app">
                  <span className="font-['Cormorant_Garamond'] text-2xl font-light text-app">
                    Menu
                  </span>
                  <button
                    onClick={() => setIsMobileMenuOpen(false)}
                    className="p-3 -mr-2 text-app touch-manipulation"
                    aria-label="Close menu"
                  >
                    <X className="w-6 h-6" strokeWidth={1.5} />
                  </button>
                </div>

                <nav className="flex-1 px-6 py-8 space-y-6 overflow-y-auto">
                  {navLinks.map((link, index) => (
                    <Link
                      key={link.label}
                      to={link.href}
                      onClick={() => handleLinkClick(link.label, link.href)}
                      className="block py-2 touch-manipulation"
                    >
                      <motion.div
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.3, delay: index * 0.05 }}
                      >
                        <span
                          className={`text-base uppercase tracking-widest font-light transition-colors duration-200 ${
                            activeLink === link.label
                              ? 'text-accent'
                              : 'text-gray-700 dark:text-muted'
                          }`}
                        >
                          {link.label}
                        </span>
                        {activeLink === link.label && (
                          <div className="h-px w-12 bg-accent mt-2" />
                        )}
                      </motion.div>
                    </Link>
                  ))}
                </nav>

                <div className="p-6 border-t border-app flex-shrink-0">
                  <Link to="/book-me" onClick={() => {
                    handleLinkClick('Book Me', '/book-me');
                    setIsMobileMenuOpen(false);
                  }}>
                    <Button
                      className="w-full btn-gold font-semibold py-6 text-sm uppercase tracking-widest transition-all duration-300 touch-manipulation rounded-2xl shadow-md border-2"
                    >
                      Book Dr. Gift
                    </Button>
                  </Link>
                </div>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </>
  );
}
