import { useEffect, useState } from 'react';

import { SectionWrapper } from './SectionWrapper';
import { apiUrl, mediaPath } from '../lib/config';
import { VentureCard } from './VentureCard';

interface Venture {
  logo: string;
  name: string;
  description: string;
}

export function VenturesSection() {
  const [ventures, setVentures] = useState<Venture[]>([]);
  const [heading, setHeading] = useState('');
  const [subheading, setSubheading] = useState('');

  useEffect(() => {
    fetch(apiUrl('get_ventures.php'))
      .then((res) => res.json())
      .then((data) => {
        setVentures(data.ventures || []);
        setHeading(data.heading || '');
        setSubheading(data.subheading || '');
      });
  }, []);

  return (
    <SectionWrapper id="ventures" className="bg-white">
      <div className="container mx-auto px-6 md:px-8">
        <div className="text-center mb-16">
          <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-slate-900 mb-4">
            {heading}
          </h2>
          <p className="text-xl text-slate-600 max-w-3xl mx-auto">{subheading}</p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
          {ventures.map((venture, index) => (
            <VentureCard
              key={index}
              name={venture.name}
              description={venture.description}
              logoSrc={mediaPath(venture.logo)}
              delay={index * 0.1}
            />
          ))}
        </div>
      </div>
    </SectionWrapper>
  );
}
