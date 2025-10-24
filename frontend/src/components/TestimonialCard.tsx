import { Quote } from 'lucide-react';
import { Card, CardContent } from './ui/card';

interface TestimonialCardProps {
  quote: string;
  author: string;
  role: string;
}

export function TestimonialCard({ quote, author, role }: TestimonialCardProps) {
  return (
    <Card className="border-none shadow-lg rounded-2xl h-full">
      <CardContent className="p-8 md:p-10">
        <Quote className="w-10 h-10 text-amber-500 mb-6" />
        <p className="text-lg text-slate-700 leading-relaxed mb-6 italic">
          "{quote}"
        </p>
        <div className="border-t border-slate-200 pt-4">
          <p className="font-semibold text-slate-800">{author}</p>
          <p className="text-sm text-slate-500 mt-1">{role}</p>
        </div>
      </CardContent>
    </Card>
  );
}
