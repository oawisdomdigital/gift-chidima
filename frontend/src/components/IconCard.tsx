import { motion } from 'framer-motion';
import { type LucideIcon } from 'lucide-react';
import { Card, CardContent } from './ui/card';

interface IconCardProps {
  icon?: LucideIcon | string | null;
  title: string;
  description: string;
  delay?: number;
}

export function IconCard({ icon, title, description, delay = 0 }: IconCardProps) {
  const isImage = typeof icon === 'string';
  const IconComponent = icon as LucideIcon;

  return (
    <motion.div
      initial={{ opacity: 0, y: 30 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.5, delay, ease: 'easeOut' }}
    >
      <Card className="h-full border-none shadow-lg hover:shadow-xl transition-all duration-300 rounded-2xl">
        <CardContent className="p-8 flex flex-col items-center text-center">
          <div className="mb-6 p-4 rounded-full bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center">
            {isImage ? (
              <img
                src={icon || '/placeholder.png'}
                alt={title}
                className="w-10 h-10 object-contain"
              />
            ) : IconComponent ? (
              <IconComponent className="w-8 h-8 text-amber-600" />
            ) : null}
          </div>
          <h3 className="text-xl font-semibold text-slate-800 mb-3">{title}</h3>
          <p className="text-slate-600 leading-relaxed">{description}</p>
        </CardContent>
      </Card>
    </motion.div>
  );
}
