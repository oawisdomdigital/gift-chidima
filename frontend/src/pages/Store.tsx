import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { BookOpen } from "lucide-react";
import { apiUrl } from '../lib/config';
import { BookCard } from "../components/BookCard";
import { BookModal } from "../components/BookModal";

export interface Book {
  id: string;
  title: string;
  subtitle?: string;
  description: string;
  detailedDescription?: string;
  keyLessons?: string[];
  cover_image?: string;
  file_url?: string;
  price: number;
  currency: string;
  type: "physical" | "digital";
}

export function Store() {
  const [books, setBooks] = useState<Book[]>([]);
  const [selectedBook, setSelectedBook] = useState<Book | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchBooks = async () => {
      try {
        setLoading(true);
        const response = await fetch(apiUrl("get_books.php"));
        if (!response.ok) throw new Error("Failed to fetch books");

        const data = await response.json();
        
        if (!data.books || !Array.isArray(data.books)) {
          throw new Error("Invalid response format");
        }

        setBooks(
          data.books.map((b: any) => ({
            ...b,
            detailedDescription: b.detailed_description,
            keyLessons: b.key_lessons,
            cover_image: b.cover_image,
            file_url: b.file_url,
          }))
        );
      } catch (err: any) {
        console.error("Error fetching books:", err);
        setError("Unable to load books at the moment.");
      } finally {
        setLoading(false);
      }
    };

    fetchBooks();
  }, []);

  return (
    <div className="min-h-screen bg-white">
      {/* === HERO SECTION === */}
      <section className="relative bg-gradient-to-br from-white via-slate-50 to-white pt-24 pb-16">
        <div className="container mx-auto px-6 md:px-12">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="text-center max-w-3xl mx-auto"
          >
            <div className="inline-flex items-center justify-center w-20 h-20 bg-[#D4AF37] rounded-full mb-6">
              <BookOpen className="w-10 h-10 text-white" />
            </div>

            <h1 className="font-['Playfair_Display'] text-5xl md:text-6xl font-bold text-[#0B1C3B] mb-4">
              Explore Dr. Gift’s Books
            </h1>

            <p className="text-lg md:text-xl text-slate-600 leading-relaxed">
              Discover powerful insights, leadership wisdom, and practical lessons
              for growth and impact.
            </p>
          </motion.div>
        </div>
      </section>

      {/* === BOOKS GRID === */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-6 md:px-12">
          {loading && (
            <div className="text-center py-20 text-slate-500 text-lg">
              Loading books...
            </div>
          )}

          {error && (
            <div className="text-center py-20 text-red-600 text-lg">
              {error}
            </div>
          )}

          {!loading && !error && (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
              {books.length > 0 ? (
                books.map((book, index) => (
                  <BookCard
                    key={book.id}
                    book={book}
                    delay={index * 0.1}
                    onBuyNow={() => setSelectedBook(book)}
                  />
                ))
              ) : (
                <div className="col-span-full text-center text-slate-500 text-lg">
                  No books available right now.
                </div>
              )}
            </div>
          )}
        </div>
      </section>

      {/* === MODAL === */}
      {selectedBook && (
        <BookModal book={selectedBook} onClose={() => setSelectedBook(null)} />
      )}
    </div>
  );
}
