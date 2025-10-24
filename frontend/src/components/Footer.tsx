import { Mail, Linkedin, Twitter, Instagram, Facebook, Heart } from 'lucide-react';
import { Separator } from './ui/separator';

export function Footer() {
  const currentYear = new Date().getFullYear();

  const navigation = {
    main: [
      { name: 'About', href: '#about' },
      { name: 'Ventures', href: '#ventures' },
      { name: 'Blog', href: '/blog' },
      { name: 'Gallery', href: '/gallery' },
      { name: 'Store', href: '/store' },
      { name: 'Book Me', href: '/book-me' },
    ],
    legal: [
      { name: 'Privacy Policy', href: '/privacy' },
      { name: 'Terms of Service', href: '/terms' },
    ],
    social: [
      { name: 'LinkedIn', icon: Linkedin, href: '#' },
      { name: 'Twitter', icon: Twitter, href: '#' },
      { name: 'Instagram', icon: Instagram, href: '#' },
      { name: 'Facebook', icon: Facebook, href: '#' },
    ],
  };

  return (
    <footer className="bg-slate-900 dark:bg-[#0A0F1A] text-slate-300 transition-theme">
      <div className="container mx-auto px-6 md:px-8 py-16">
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
          <div className="lg:col-span-2">
            <h3 className="font-['Playfair_Display'] text-3xl font-bold text-white mb-4">
              Dr. Gift Chidima Nnamoko Orairu
            </h3>
            <p className="text-slate-400 leading-relaxed mb-6 max-w-md">
              Empowering a new generation of African leaders through purpose, mentorship,
              and transformation.
            </p>
            <div className="flex items-center gap-2 text-slate-400">
              <Mail className="w-5 h-5" />
              <a
                href="mailto:contact@drgift.com"
                className="hover:text-accent transition-colors"
              >
                contact@drgift.com
              </a>
            </div>
          </div>

          <div>
            <h4 className="font-semibold text-white mb-4 text-lg">Quick Links</h4>
            <ul className="space-y-3">
              {navigation.main.map((item) => (
                <li key={item.name}>
                  <a
                    href={item.href}
                    className="hover:text-accent transition-colors inline-block"
                  >
                    {item.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold text-white mb-4 text-lg">Connect</h4>
            <div className="flex gap-4">
              {navigation.social.map((item) => (
                <a
                  key={item.name}
                  href={item.href}
                  className="w-10 h-10 bg-slate-800 dark:bg-surface-2 rounded-full flex items-center justify-center hover:bg-accent hover:text-[#0B1523] transition-all duration-300"
                  aria-label={item.name}
                >
                  <item.icon className="w-5 h-5" />
                </a>
              ))}
            </div>
          </div>
        </div>

        <Separator className="bg-slate-800 dark:bg-border-app mb-8" />

        <div className="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-400 dark:text-slate-400">
          <div className="flex items-center gap-2">
            <span>&copy; {currentYear} Dr. Gift Chidima Nnamoko Orairu. All rights reserved.</span>
          </div>

          <div className="flex items-center gap-1">
            <span>Made with</span>
            <Heart className="w-4 h-4 text-red-500 fill-current" />
            <span>for African Excellence</span>
          </div>

          <div className="flex gap-6">
            {navigation.legal.map((item) => (
              <a
                key={item.name}
                href={item.href}
                className="hover:text-accent transition-colors"
              >
                {item.name}
              </a>
            ))}
          </div>
        </div>
      </div>
    </footer>
  );
}
