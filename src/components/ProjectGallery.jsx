import React, { useState, useEffect, useCallback } from 'react';
import { X, ChevronLeft, ChevronRight, Maximize2 } from 'lucide-react';

export default function ProjectGallery({ images = [], title = '' }) {
  const [lightboxIndex, setLightboxIndex] = useState(null);

  const openLightbox = (index) => setLightboxIndex(index);
  const closeLightbox = () => setLightboxIndex(null);

  const nextImage = useCallback(() => {
    if (lightboxIndex !== null) {
      setLightboxIndex((prev) => (prev + 1) % images.length);
    }
  }, [lightboxIndex, images.length]);

  const prevImage = useCallback(() => {
    if (lightboxIndex !== null) {
      setLightboxIndex((prev) => (prev - 1 + images.length) % images.length);
    }
  }, [lightboxIndex, images.length]);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (lightboxIndex === null) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowRight') nextImage();
      if (e.key === 'ArrowLeft') prevImage();
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [lightboxIndex, nextImage, prevImage]);

  if (!images || images.length === 0) {
    return (
      <div className="py-12 text-center text-stone text-sm italic">
        No curated photography records available for this project.
      </div>
    );
  }

  return (
    <>
      {/* Editorial Photography Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
        {images.map((item, idx) => {
          const isWide = idx % 3 === 0;

          return (
            <div
              key={item.id || idx}
              onClick={() => openLightbox(idx)}
              className={`group relative overflow-hidden cursor-pointer rounded-2xl border border-[#E5E0D8] bg-[#FAF9F5] shadow-lg ${
                isWide ? 'md:col-span-2 aspect-[16/9]' : 'aspect-[4/3]'
              }`}
            >
              <img
                src={item.image_url}
                alt={item.caption || `${title} detail ${idx + 1}`}
                loading="lazy"
                className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
              />

              {/* Hover Overlay */}
              <div className="absolute inset-0 bg-[#1C1B19]/35 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-between p-6">
                <div className="self-end bg-white/95 backdrop-blur-md text-[#1C1B19] p-2.5 rounded-full border border-[#E2DDD5] shadow-md">
                  <Maximize2 className="w-4 h-4 text-accent" />
                </div>
                {item.caption && (
                  <p className="text-[#1C1B19] text-xs md:text-sm font-light tracking-wide bg-white/95 rounded-xl px-4 py-2.5 backdrop-blur-md self-start max-w-lg border border-[#E2DDD5] shadow-lg">
                    {item.caption}
                  </p>
                )}
              </div>
            </div>
          );
        })}
      </div>

      {/* Lightbox Modal */}
      {lightboxIndex !== null && (
        <div
          role="dialog"
          aria-modal="true"
          className="fixed inset-0 z-50 bg-[#141312]/98 backdrop-blur-2xl flex items-center justify-center p-4 md:p-8 animate-fadeIn"
          onClick={closeLightbox}
        >
          {/* Top Bar with Counter and Close */}
          <div className="absolute top-6 left-6 right-6 flex items-center justify-between text-white/90 z-20 pointer-events-none">
            <div className="text-xs uppercase tracking-[0.25em] font-light text-[#FAF9F5]">
              {lightboxIndex + 1} / {images.length} &bull; {title}
            </div>
            <button
              onClick={closeLightbox}
              className="pointer-events-auto p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors text-white border border-white/20"
              aria-label="Close lightbox"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Previous Arrow */}
          <button
            onClick={(e) => {
              e.stopPropagation();
              prevImage();
            }}
            className="absolute left-4 md:left-8 z-20 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 transition-colors"
            aria-label="Previous image"
          >
            <ChevronLeft className="w-6 h-6" />
          </button>

          {/* Active Image Container */}
          <div
            className="max-w-5xl max-h-[80vh] flex flex-col items-center justify-center relative select-none"
            onClick={(e) => e.stopPropagation()}
          >
            <img
              src={images[lightboxIndex].image_url}
              alt={images[lightboxIndex].caption || `${title} photo ${lightboxIndex + 1}`}
              className="max-h-[75vh] w-auto max-w-full object-contain rounded-xl shadow-2xl border border-white/15"
            />
            {images[lightboxIndex].caption && (
              <p className="text-white text-xs md:text-sm font-light mt-4 text-center tracking-wide max-w-xl">
                {images[lightboxIndex].caption}
              </p>
            )}
          </div>

          {/* Next Arrow */}
          <button
            onClick={(e) => {
              e.stopPropagation();
              nextImage();
            }}
            className="absolute right-4 md:right-8 z-20 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 transition-colors"
            aria-label="Next image"
          >
            <ChevronRight className="w-6 h-6" />
          </button>
        </div>
      )}
    </>
  );
}
