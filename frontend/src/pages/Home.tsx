import { HeroSection } from '../components/HeroSection';
import { BiographySection } from '../components/BiographySection';
import { KeyHighlightsSection } from '../components/KeyHighlightsSection';
import { VenturesSection } from '../components/VenturesSection';
import { AwardsSection } from '../components/AwardsSection';
import { StoreBanner } from '../components/StoreBanner';
import { TestimonialsSection } from '../components/TestimonialsSection';
import { FinalCTA } from '../components/FinalCTA';

export function Home() {
  return (
    <>
      <HeroSection />
      <BiographySection />
      <KeyHighlightsSection />
      <VenturesSection />
      <AwardsSection />
      <StoreBanner />
      <TestimonialsSection />
      <FinalCTA />
    </>
  );
}
