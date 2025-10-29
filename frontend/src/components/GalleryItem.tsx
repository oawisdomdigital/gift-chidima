import { motion } from "framer-motion";
import { Play } from "lucide-react";
import { useState, useEffect, useCallback, useRef } from "react";
import { MediaItem } from "../pages/Gallery";

interface GalleryItemProps {
  media: MediaItem;
  index: number;
  onClick: () => void;
}

export function GalleryItem({ media, index, onClick }: GalleryItemProps) {
  const isVideo = media.type === "video";
  const isEmbedded = Boolean(media.is_embedded);

  const [videoThumbnail, setVideoThumbnail] = useState<string>("");
  const [thumbnailExists, setThumbnailExists] = useState<boolean>(false);
  const [videoError, setVideoError] = useState(false);
  const [isVisible, setIsVisible] = useState(false);

  const videoRef = useRef<HTMLVideoElement | null>(null);
  const observerRef = useRef<IntersectionObserver | null>(null);

  // --- Check if file exists ---
  const checkFileExists = useCallback(async (url: string): Promise<boolean> => {
    try {
      const response = await fetch(url, { method: "HEAD" });
      return response.ok;
    } catch {
      return false;
    }
  }, []);

  // --- Generate thumbnail from video frame (if no thumbnail provided) ---
  const generateThumbnail = useCallback(async () => {
    if (!isVideo || isEmbedded || media.thumbnail) return;

    try {
      const video = document.createElement("video");
      video.crossOrigin = "anonymous";
      video.src = normalizePath(media.src);
      video.preload = "metadata";

      await new Promise<void>((resolve, reject) => {
        const timeout = setTimeout(() => reject(new Error("Timeout")), 10000);

        video.onloadeddata = () => {
          clearTimeout(timeout);
          const canvas = document.createElement("canvas");
          const ratio =
            video.videoWidth && video.videoHeight
              ? video.videoWidth / video.videoHeight
              : 16 / 9;
          const maxWidth = 800;
          canvas.width = Math.min(video.videoWidth || maxWidth, maxWidth);
          canvas.height = canvas.width / ratio;

          const ctx = canvas.getContext("2d");
          if (ctx) {
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            setVideoThumbnail(canvas.toDataURL("image/jpeg", 0.8));
          }
          resolve();
        };

        video.onerror = () => {
          clearTimeout(timeout);
          reject(new Error("Video load error"));
        };

        video.currentTime = 0.1;
      });
    } catch (err) {
      console.error("Thumbnail generation failed:", err);
    }
  }, [isVideo, isEmbedded, media.thumbnail, media.src]);

  // --- Verify thumbnail existence ---
  useEffect(() => {
    const verify = async () => {
      const youTubeId = isEmbedded ? extractYouTubeId(media.src) : null;
      const possibleThumb =
        media.thumbnail ||
        videoThumbnail ||
        (youTubeId
          ? `https://img.youtube.com/vi/${youTubeId}/hqdefault.jpg`
          : undefined);

      if (possibleThumb) {
        const exists = await checkFileExists(normalizePath(possibleThumb));
        setThumbnailExists(exists);
      } else {
        setThumbnailExists(false);
      }
    };
    verify();
  }, [media.thumbnail, videoThumbnail, media.src, isEmbedded, checkFileExists]);

  useEffect(() => {
    generateThumbnail();
  }, [generateThumbnail]);

  // --- Lazy load videos + single active playback ---
  useEffect(() => {
    if (!isVideo) return;

    const node = videoRef.current;
    if (!node) return;

    // Lazy loading observer
    observerRef.current = new IntersectionObserver(
      (entries) => {
        const entry = entries[0];
        if (entry.isIntersecting) {
          setIsVisible(true);
        } else {
          setIsVisible(false);
          node.pause();
        }
      },
      { threshold: 0.25 }
    );

    observerRef.current.observe(node);

    // Event listener: pause other videos when one plays
    const handlePlay = () => {
      document
        .querySelectorAll("video")
        .forEach((vid) => vid !== node && vid.pause());
    };
    node.addEventListener("play", handlePlay);

    return () => {
      if (observerRef.current && node) observerRef.current.unobserve(node);
      node.removeEventListener("play", handlePlay);
    };
  }, [isVideo]);

  const youTubeId = isEmbedded ? extractYouTubeId(media.src) : null;
  const displayImage =
    media.thumbnail ||
    videoThumbnail ||
    (youTubeId ? `https://img.youtube.com/vi/${youTubeId}/hqdefault.jpg` : undefined);

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.5, delay: index * 0.05 }}
      whileHover={{ scale: 1.05 }}
      className="relative cursor-pointer group"
      onClick={onClick}
    >
      <div className="aspect-square overflow-hidden rounded-xl shadow-md group-hover:shadow-2xl transition-all duration-300 bg-slate-200">
        {isVideo ? (
          <div className="relative w-full h-full">
            {thumbnailExists && displayImage && !videoError ? (
              <img
                src={normalizePath(displayImage)}
                alt={media.title || "Gallery video thumbnail"}
                className="w-full h-full object-cover"
                onError={() => setThumbnailExists(false)}
              />
            ) : (
              <video
                ref={videoRef}
                src={isVisible ? normalizePath(media.src) : undefined}
                className="w-full h-full object-cover"
                muted
                playsInline
                loop
                preload="none"
                onError={(e) => {
                  console.error("Error loading video:", e);
                  setVideoError(true);
                }}
              />
            )}

            {/* Play Button Overlay */}
            <div className="absolute inset-0 bg-black/30 flex items-center justify-center group-hover:bg-black/40 transition-all duration-300">
              <div className="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <Play className="w-8 h-8 text-[#D4AF37] ml-1" />
              </div>
            </div>
          </div>
        ) : (
          <img
            src={normalizePath(media.src)}
            alt={media.title || "Gallery image"}
            className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
          />
        )}
      </div>

      {media.title && media.title.trim() !== "" && (
        <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <h3 className="text-white font-semibold text-sm line-clamp-2">
            {media.title}
          </h3>
        </div>
      )}
    </motion.div>
  );
}

// --- Helper functions ---
function extractYouTubeId(url: string): string | null {
  if (!url) return null;
  const match =
    url.match(/[?&]v=([^&]+)/i) ||
    url.match(/youtu\.be\/([^/?]+)/i) ||
    url.match(/youtube\.com\/embed\/([^/?]+)/i);
  return match ? match[1] : null;
}

function normalizePath(path: string): string {
  if (!path) return "";

  // If it's already a base64 data URI, return it directly
  if (path.startsWith("data:image") || path.startsWith("data:video")) {
    return path;
  }

  // Allow absolute URLs
  if (path.startsWith("http")) {
    return path;
  }

  // Otherwise treat as a local file path and use a relative URL (no hard-coded host)
  // This returns a path relative to the app root so it works in different environments.
  return `../api/serve_media.php?file=${encodeURIComponent(path)}`;
}

