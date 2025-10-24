import { useEffect, useState } from 'react';
import * as Icons from 'lucide-react';
import { SectionWrapper } from './SectionWrapper';
import { IconCard } from './IconCard';

interface HighlightItem {
  icon: string | null;
  title: string | null;
  description: string | null;
}

interface KeyHighlightsData {
  heading: string | null;
  subheading: string | null;
  highlights: HighlightItem[];
}

export function KeyHighlightsSection() {
  const [data, setData] = useState<KeyHighlightsData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetch('http://localhost/myapp/api/get_highlights.php')
      .then((res) => {
        if (!res.ok) throw new Error('Failed to fetch highlights');
        return res.json();
      })
      .then((data) => {
        setData(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error('Error loading key highlights:', err);
        setError('Unable to load highlights.');
        setLoading(false);
      });
  }, []);

  if (loading) return null; // or spinner
  if (error || !data) return null;

  return (
    <SectionWrapper id="highlights" className="bg-slate-50">
      <div className="container mx-auto px-6 md:px-8">
        {/* === Section Heading === */}
        {(data.heading || data.subheading) && (
          <div className="text-center mb-16">
            {data.heading && (
              <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-slate-900 mb-4">
                {data.heading}
              </h2>
            )}
            {data.subheading && (
              <p className="text-xl text-slate-600 max-w-3xl mx-auto">
                {data.subheading}
              </p>
            )}
          </div>
        )}

        {/* === Highlights Grid === */}
        {data.highlights?.length > 0 && (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            {data.highlights.map((highlight, index) => {
              const iconValue = highlight.icon || '';
              const isImage =
                iconValue.endsWith('.png') ||
                iconValue.endsWith('.jpg') ||
                iconValue.endsWith('.jpeg') ||
                iconValue.endsWith('.gif') ||
                iconValue.endsWith('.webp');

              // ✅ Pass URL string or icon component name
              const iconProp = isImage
                ? `http://localhost/myapp/${iconValue}`
                : (Icons as any)[iconValue] || Icons.Building2;

              return (
                <IconCard
                  key={`${highlight.title}-${index}`}
                  icon={iconProp}
                  title={highlight.title || 'Untitled'}
                  description={highlight.description || ''}
                  delay={index * 0.1}
                />
              );
            })}
          </div>
        )}
      </div>
    </SectionWrapper>
  );
}
