import { useEffect, useState } from 'react';
import { cn } from '../lib/utils';

interface AdData {
  id: number;
  name: string;
  type: string;
  content: string;
  image_url: string | null;
  link_url: string | null;
}

interface AdvertisementProps {
  type: 'banner' | 'inline' | 'sidebar';
  className?: string;
}

/**
 * Advertisement component
 * - Hides completely if no ad is available.
 * - Banner variant automatically sits flush under the header.
 */
export function Advertisement({ type, className }: AdvertisementProps) {
  const [ad, setAd] = useState<AdData | null>(null);
  const [loading, setLoading] = useState(true);

  const styles = {
    banner: 'w-full bg-surface-2 text-center',
    inline: 'my-12 bg-neutral-100 rounded-2xl p-8 text-center',
    sidebar: 'bg-neutral-100 rounded-2xl p-8 text-center',
  };

  useEffect(() => {
    const fetchAd = async () => {
      try {
        setLoading(true);
        const url = new URL('http://localhost/myapp/api/get_ad.php');
        url.searchParams.append('type', type);

        const response = await fetch(url.toString());
        const data = await response.json();

        if (data.success && data.data) {
          setAd(data.data);
        } else {
          setAd(null);
        }
      } catch (err) {
        console.error('Error fetching ad:', err);
        setAd(null);
      } finally {
        setLoading(false);
      }
    };

    fetchAd();
  }, [type]);

  // ✅ Hide the component if no ad is found and not loading
  if (!loading && !ad) return null;

  const getFullImageUrl = (path: string | null): string => {
    if (!path) return '';
    return `http://localhost/myapp/${path.replace(/^\/+/, '')}`;
  };

  const AdContent = () => (
    <div className="max-w-6xl mx-auto px-4 py-3">
      <div className="text-sm text-neutral-600">Advertisement</div>
      <div className="mt-4">
        {ad?.image_url && (
          <img
            src={getFullImageUrl(ad.image_url)}
            alt={ad?.name}
            className={cn(
              'w-full object-cover rounded-xl',
              type === 'sidebar' ? 'aspect-square' : 'aspect-video'
            )}
            onError={(e) => {
              const t = e.target as HTMLImageElement;
              t.onerror = null;
              t.src = 'http://localhost/myapp/uploads/default_cover.png';
            }}
          />
        )}
        {ad?.content && <div className="mt-2 text-sm text-gray-700">{ad.content}</div>}
      </div>
    </div>
  );

  // Banner variant flush under header
  if (type === 'banner') {
    return (
      <div
        style={{ marginTop: 'calc(-1 * var(--nav-h))' }}
        className={cn(styles[type], className)}
        data-ad-slot={`blog-${type}`}
      >
        {ad?.link_url ? (
          <a
            href={ad.link_url}
            target="_blank"
            rel="noopener noreferrer"
            className="block hover:opacity-90 transition-opacity"
          >
            <AdContent />
          </a>
        ) : (
          <AdContent />
        )}
      </div>
    );
  }

  // Inline / sidebar
  return (
    <div className={cn(styles[type], className)} data-ad-slot={`blog-${type}`}>
      {ad?.link_url ? (
        <a
          href={ad.link_url}
          target="_blank"
          rel="noopener noreferrer"
          className="block hover:opacity-90 transition-opacity"
        >
          <AdContent />
        </a>
      ) : (
        <AdContent />
      )}
    </div>
  );
}
