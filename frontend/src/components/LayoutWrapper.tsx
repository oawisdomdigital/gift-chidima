import { ReactNode } from 'react';
import { HeaderNav } from './HeaderNav';
import { Footer } from './Footer';
import { ScrollToTop } from './ScrollToTop';

interface LayoutWrapperProps {
  children: ReactNode;
}

export function LayoutWrapper({ children }: LayoutWrapperProps) {
  return (
    <div className="min-h-screen bg-app text-app transition-theme">
      <HeaderNav />
      <main className="bg-app">{children}</main>
      <Footer />
      <ScrollToTop />
    </div>
  );
}
