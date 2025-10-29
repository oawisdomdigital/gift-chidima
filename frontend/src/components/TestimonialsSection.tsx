import { useState, useEffect, useCallback } from 'react';
import useEmblaCarousel from 'embla-carousel-react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { SectionWrapper } from './SectionWrapper';
import { TestimonialCard } from './TestimonialCard';
import { Button } from './ui/button';
import { apiUrl } from '../lib/config';

export function TestimonialsSection() {
interface Testimonial {
  quote: string;
  author: string;
  role: string;
}

  const [heading, setHeading] = useState('');
  const [subheading, setSubheading] = useState('');
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);

  useEffect(() => {
  fetch(apiUrl('get_testimonials.php'))
      .then(res => res.json())
      .then(data => {
        setHeading(data.heading || '');
        setSubheading(data.subheading || '');
        setTestimonials(data.testimonials || []);
      })
      .catch(err => console.error('Error fetching testimonials:', err));
  }, []);

  const [emblaRef, emblaApi] = useEmblaCarousel({ loop: true, align: 'start' });
  const [selectedIndex, setSelectedIndex] = useState(0);

  const scrollPrev = useCallback(() => emblaApi && emblaApi.scrollPrev(), [emblaApi]);
  const scrollNext = useCallback(() => emblaApi && emblaApi.scrollNext(), [emblaApi]);

  const onSelect = useCallback(() => {
    if (!emblaApi) return;
    setSelectedIndex(emblaApi.selectedScrollSnap());
  }, [emblaApi]);

  useEffect(() => {
    if (!emblaApi) return;
    onSelect();
    emblaApi.on('select', onSelect);
    return () => {
      emblaApi.off('select', onSelect);
    };
  }, [emblaApi, onSelect]);

  return (
    <SectionWrapper className="bg-slate-50">
      <div className="container mx-auto px-6 md:px-8">
        <div className="text-center mb-16">
          <h2 className="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-slate-900 mb-4">
            {heading}
          </h2>
          <p className="text-xl text-slate-600 max-w-3xl mx-auto">{subheading}</p>
        </div>

        <div className="relative max-w-6xl mx-auto">
          <div className="overflow-hidden" ref={emblaRef}>
            <div className="flex gap-6">
              {testimonials.map((t, index) => (
                <div
                  key={index}
                  className="flex-[0_0_100%] md:flex-[0_0_50%] lg:flex-[0_0_33.333%] min-w-0"
                >
                  <TestimonialCard quote={t.quote} author={t.author} role={t.role} />
                </div>
              ))}
            </div>
          </div>

          <div className="flex items-center justify-center gap-4 mt-8">
            <Button
              variant="outline"
              size="icon"
              onClick={scrollPrev}
              className="rounded-full w-12 h-12 border-2 hover:bg-amber-500 hover:border-amber-500 hover:text-white transition-all"
            >
              <ChevronLeft className="w-5 h-5" />
            </Button>

            <div className="flex gap-2">
              {testimonials.map((_, index) => (
                <button
                  key={index}
                  onClick={() => emblaApi?.scrollTo(index)}
                  className={`w-2 h-2 rounded-full transition-all ${
                    index === selectedIndex
                      ? 'bg-amber-500 w-8'
                      : 'bg-slate-300 hover:bg-slate-400'
                  }`}
                  aria-label={`Go to testimonial ${index + 1}`}
                />
              ))}
            </div>

            <Button
              variant="outline"
              size="icon"
              onClick={scrollNext}
              className="rounded-full w-12 h-12 border-2 hover:bg-amber-500 hover:border-amber-500 hover:text-white transition-all"
            >
              <ChevronRight className="w-5 h-5" />
            </Button>
          </div>
        </div>
      </div>
    </SectionWrapper>
  );
}
