import { motion } from 'framer-motion';
import { Card, CardContent, CardHeader } from './ui/card';

interface VentureCardProps {
  name: string;
  description: string;
  logoSrc: string;
  delay?: number;
}

export function VentureCard({ name, description, logoSrc, delay = 0 }: VentureCardProps) {
  return (
    <motion.div
      initial={{ opacity: 0, scale: 0.95 }}
      whileInView={{ opacity: 1, scale: 1 }}
      viewport={{ once: true }}
      transition={{ duration: 0.5, delay, ease: 'easeOut' }}
      whileHover={{ y: -8 }}
    >
      <Card className="h-full border-none shadow-md hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden">
        <CardHeader className="bg-gradient-to-br from-slate-50 to-slate-100 p-8">
          <div className="w-full bg-white rounded-xl shadow-sm flex items-center justify-center p-4">
            <img
              src={logoSrc}
              alt={name}
              className="w-auto h-16 mx-auto object-contain mb-4"
              onError={(e) => ((e.currentTarget.src = '/images/placeholder-logo.png'))}
            />
          </div>
        </CardHeader>
        <CardContent className="p-6">
          <h3 className="text-xl font-bold text-slate-800 mb-3">{name}</h3>
          <p className="text-slate-600 leading-relaxed">{description}</p>
        </CardContent>
      </Card>
    </motion.div>
  );
}
