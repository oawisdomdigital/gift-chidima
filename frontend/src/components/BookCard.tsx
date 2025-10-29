import { motion } from "framer-motion";
import { ShoppingCart } from "lucide-react";
import { Card, CardContent, CardFooter, CardHeader } from "./ui/card";
import { Button } from "./ui/button";
import { Book } from "../pages/Store";
import { mediaPath } from "../lib/config";

interface BookCardProps {
  book: Book;
  delay?: number;
  onBuyNow: () => void;
}

export function BookCard({ book, delay = 0, onBuyNow }: BookCardProps) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.5, delay }}
      whileHover={{ y: -8 }}
    >
      <Card className="h-full border-none shadow-lg hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden flex flex-col">
        <CardHeader className="p-0">
          <div className="aspect-[3/4] relative overflow-hidden">
            {book.cover_image ? (
              <img
                src={
                  book.cover_image.startsWith('http')
                    ? book.cover_image
                    : mediaPath(book.cover_image.replace(/^\/+/, ''))
                }
                alt={book.title}
                className="w-full h-full object-cover"
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center bg-[#D4AF37]">
                <ShoppingCart className="w-10 h-10 text-white" />
              </div>
            )}
            <div className="absolute top-4 right-4 bg-[#D4AF37] text-white px-3 py-1 rounded-full text-xs font-bold uppercase">
              {book.type}
            </div>
          </div>

        </CardHeader>

        <CardContent className="p-6 flex-1 flex flex-col">
          <h3 className="text-xl font-bold text-slate-900 mb-2 line-clamp-2">
            {book.title}
          </h3>
          {book.subtitle && (
            <p className="text-sm font-medium text-[#D4AF37] mb-3">
              {book.subtitle}
            </p>
          )}
          <p className="text-slate-600 leading-relaxed line-clamp-2 flex-1">
            {book.description}
          </p>
          <div className="mt-4 pt-4 border-t border-slate-200">
            <p className="text-2xl font-bold text-slate-900">
              {book.currency === "NGN" ? "₦" : "$"}
              {book.price.toLocaleString()}
            </p>
          </div>
        </CardContent>

        <CardFooter className="p-6 pt-0">
          <Button
            onClick={onBuyNow}
            className="w-full bg-[#0B1C3B] hover:bg-[#D4AF37] text-white hover:text-black font-semibold py-6 rounded-xl transition-all duration-300 group"
          >
            <ShoppingCart className="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" />
            Buy Now
          </Button>
        </CardFooter>
      </Card>
    </motion.div>
  );
}
