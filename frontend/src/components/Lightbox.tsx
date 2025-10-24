import { motion, AnimatePresence } from "framer-motion";
import { X, ChevronLeft, ChevronRight } from "lucide-react";
import { MediaItem } from "../pages/Gallery";

interface LightboxProps {
  media: MediaItem;
  onClose: () => void;
  onNext: () => void;
  onPrevious: () => void;
  hasNext: boolean;
  hasPrevious: boolean;
}

export function Lightbox({
  media,
  onClose,
  onNext,
  onPrevious,
  hasNext,
  hasPrevious,
}: LightboxProps) {
  // Keyboard navigation
  window.onkeydown = (e) => {
    if (e.key === "ArrowRight") onNext();
    if (e.key === "ArrowLeft") onPrevious();
    if (e.key === "Escape") onClose();
  };

  const renderMedia = () => {
    if (media.is_embedded) {
      // Embedded YouTube Video
      const videoId = extractYouTubeId(media.src);
      if (!videoId) return <div className="text-white text-center p-4">Invalid YouTube URL</div>;
      
      return (
        <div className="relative w-full max-w-4xl mx-auto aspect-video">
          <iframe
            className="w-full h-full rounded-xl shadow-lg"
            src={`https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1`}
            title={media.title || "YouTube Video"}
            frameBorder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowFullScreen
          />
        </div>
      );
    } else if (media.type === "video") {
      // Uploaded Video File
      return (
        <video
          className="max-h-[80vh] rounded-xl shadow-lg"
          controls
          autoPlay
          playsInline
          controlsList="nodownload"
          preload="auto"
          src={media.src}
          onError={(e) => {
            console.error("Video playback error:", e);
          }}
        >
          <source src={media.src} type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      );
    } else {
      // Image
      return (
        <img
          src={media.src}
          alt={media.title || "Gallery Image"}
          className="max-h-[80vh] rounded-xl shadow-lg"
        />
      );
    }
  };

  return (
    <AnimatePresence>
      <motion.div
        className="fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50"
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
      >
        {/* Close Button */}
        <button
          className="absolute top-6 right-6 text-white hover:text-[#D4AF37] transition-colors"
          onClick={onClose}
        >
          <X size={32} />
        </button>

        {/* Media Container */}
        <motion.div
          key={media.id}
          initial={{ scale: 0.9, opacity: 0 }}
          animate={{ scale: 1, opacity: 1 }}
          transition={{ duration: 0.3 }}
          className="flex flex-col items-center px-4"
        >
          {renderMedia()}

          {/* Title and Description */}
          {((media.title && media.title.trim() !== "") || (media.description && media.description.trim() !== "")) && (
            <div className="text-center mt-6 text-white max-w-2xl">
              {media.title && media.title.trim() !== "" && (
                <h2 className="text-2xl font-semibold mb-2">
                  {media.title}
                </h2>
              )}
              {media.description && media.description.trim() !== "" && (
                <p className="text-gray-300 text-sm leading-relaxed">
                  {media.description}
                </p>
              )}
            </div>
          )}
        </motion.div>

        {/* Navigation Buttons */}
        {hasPrevious && (
          <button
            className="absolute left-6 text-white hover:text-[#D4AF37] transition"
            onClick={onPrevious}
          >
            <ChevronLeft size={42} />
          </button>
        )}
        {hasNext && (
          <button
            className="absolute right-6 text-white hover:text-[#D4AF37] transition"
            onClick={onNext}
          >
            <ChevronRight size={42} />
          </button>
        )}
      </motion.div>
    </AnimatePresence>
  );
}

// Helper to extract YouTube video ID
function extractYouTubeId(url: string): string | null {
  if (!url) return null;
  
  // Try standard YouTube URL first
  let match = url.match(/[?&]v=([^&]+)/i);
  if (match && match[1]) return match[1];

  // Try short URL format
  match = url.match(/youtu\.be\/([^/?]+)/i);
  if (match && match[1]) return match[1];

  // Try embed format
  match = url.match(/youtube\.com\/embed\/([^/?]+)/i);
  if (match && match[1]) return match[1];

  return null;
}
