import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { Camera } from "lucide-react";
import { apiUrl, mediaPath } from '../lib/config';
import { GalleryItem } from "../components/GalleryItem";
import { Lightbox } from "../components/Lightbox";

export interface MediaItem {
  id: string;
  type: "image" | "video";
  src: string;
  thumbnail?: string;
  title?: string;
  description?: string;
  is_embedded?: number | boolean;
  created_at?: string;
}

export function Gallery() {
  const [galleryMedia, setGalleryMedia] = useState<MediaItem[]>([]);
  const [selectedMedia, setSelectedMedia] = useState<MediaItem | null>(null);
  const [currentIndex, setCurrentIndex] = useState(0);

  // Fetch gallery data from backend
  useEffect(() => {
    fetch(apiUrl('get_gallery.php'))
      .then((res) => {
        if (!res.ok) throw new Error("Failed to fetch gallery");
        return res.json();
      })
      .then((data) => {
        if (Array.isArray(data)) {
          const normalized = data.map((item) => ({
            ...item,
            src: normalizePath(item.src),
            thumbnail: item.thumbnail ? normalizePath(item.thumbnail) : undefined,
          }));
          setGalleryMedia(normalized);
        }
      })
      .catch((err) => console.error("Error fetching gallery:", err));
  }, []);

  const openLightbox = (media: MediaItem, index: number) => {
    setSelectedMedia(media);
    setCurrentIndex(index);
  };

  const closeLightbox = () => {
    setSelectedMedia(null);
  };

  const navigateNext = () => {
    if (galleryMedia.length === 0) return;
    const nextIndex = (currentIndex + 1) % galleryMedia.length;
    setCurrentIndex(nextIndex);
    setSelectedMedia(galleryMedia[nextIndex]);
  };

  const navigatePrevious = () => {
    if (galleryMedia.length === 0) return;
    const prevIndex =
      (currentIndex - 1 + galleryMedia.length) % galleryMedia.length;
    setCurrentIndex(prevIndex);
    setSelectedMedia(galleryMedia[prevIndex]);
  };

  return (
    <div className="min-h-screen bg-white">
      {/* ======= HEADER SECTION ======= */}
      <section className="relative bg-gradient-to-br from-white via-slate-50 to-white pt-32 pb-20">
        <div className="container mx-auto px-6 md:px-12">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="text-center max-w-4xl mx-auto"
          >
            <div className="inline-flex items-center justify-center w-20 h-20 bg-[#D4AF37] rounded-full mb-6">
              <Camera className="w-10 h-10 text-white" />
            </div>

            <h1 className="font-['Playfair_Display'] text-5xl md:text-6xl lg:text-7xl font-bold text-[#0B1C3B] mb-6">
              Gallery
            </h1>

            <p className="text-lg md:text-xl text-slate-600 leading-relaxed max-w-3xl mx-auto">
              Explore a collection of inspiring moments from leadership events,
              speaking engagements, community initiatives, and media
              appearances.
            </p>
          </motion.div>
        </div>
      </section>

      {/* ======= GALLERY GRID ======= */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-6 md:px-12">
          {galleryMedia.length === 0 ? (
            <p className="text-center text-gray-500">No media found.</p>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {galleryMedia.map((media, index) => (
                <GalleryItem
                  key={media.id}
                  media={media}
                  index={index}
                  onClick={() => openLightbox(media, index)}
                />
              ))}
            </div>
          )}
        </div>
      </section>

      {/* ======= LIGHTBOX MODAL ======= */}
      {selectedMedia && (
        <Lightbox
          media={selectedMedia}
          onClose={closeLightbox}
          onNext={navigateNext}
          onPrevious={navigatePrevious}
          hasNext={currentIndex < galleryMedia.length - 1}
          hasPrevious={currentIndex > 0}
        />
      )}
    </div>
  );
}

// --- Helper function to normalize paths from backend ---
function normalizePath(path: string): string {
  if (!path) return "";
  if (path.startsWith("http")) return path;
  return mediaPath(path.replace("../", ""));
}
