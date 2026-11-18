import React, { useRef, useState } from 'react';
import { motion, useScroll, useTransform } from 'framer-motion';
import { ArrowUpRight, Compass } from 'lucide-react';
import { Link } from 'react-router-dom';

const FALLBACK_IMAGES = [
  'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=2400&q=90',
  'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=2400&q=90',
  'https://storage.googleapis.com/webild/default/templates/hotel/rooms/grandsuite.webp',
  'https://storage.googleapis.com/webild/default/templates/hotel/about/magnific_recreate-this-in-higher-q_te7wx9evnr.webp'
];

export default function HeroExpand({
  title = 'RiTiVerse',
  subtitle = 'An upscale interior architecture studio sculpted in marble white, daylight, and quiet luxury.',
  imageSrc = 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=2400&q=90',
  videoSrc = 'https://storage.googleapis.com/webild/default/templates/hotel/hero.mp4',
  primaryButton = { text: 'Browse Sanctuaries', href: '/work' },
  secondaryButton = { text: 'Watch 3D Tour', href: '/work/the-tribeca-loft' }
}) {
  const containerRef = useRef(null);
  const [currentImgIndex, setCurrentImgIndex] = useState(0);
  const [isVideoPlaying, setIsVideoPlaying] = useState(false);

  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ['start start', 'end start']
  });

  const mediaScale = useTransform(scrollYProgress, [0, 1], [1, 1.08]);
  const textTranslateY = useTransform(scrollYProgress, [0, 1], ['0px', '90px']);
  const contentOpacity = useTransform(scrollYProgress, [0, 0.8], [1, 0]);

  const handleImageError = () => {
    if (currentImgIndex < FALLBACK_IMAGES.length - 1) {
      setCurrentImgIndex((prev) => prev + 1);
    }
  };

  const activeImage = imageSrc || FALLBACK_IMAGES[currentImgIndex];

  return (
    <section
      ref={containerRef}
      className="relative min-h-[100dvh] flex flex-col justify-end items-center px-4 sm:px-6 md:px-12 pb-16 sm:pb-24 pt-28 sm:pt-36 overflow-hidden bg-[#FAF9F5]"
    >
      {/* Background Architectural Media Frame with Cinematic Sharpness */}
      <motion.div
        style={{ scale: mediaScale }}
        className="absolute inset-0 z-0 overflow-hidden bg-[#141312] transform-gpu will-change-transform"
      >
        {/* 1. Signature High-Resolution Interior Photograph */}
        <img
          src={activeImage}
          alt="RiTiVerse Atelier Vérité Architecture & Interior Design"
          loading="eager"
          fetchPriority="high"
          onError={handleImageError}
          className={`absolute inset-0 w-full h-full object-cover object-center transition-opacity duration-1000 ${
            isVideoPlaying ? 'opacity-0' : 'opacity-100'
          }`}
        />

        {/* 2. Ambient Video */}
        {videoSrc && (
          <video
            autoPlay
            loop
            muted
            playsInline
            onPlaying={() => setIsVideoPlaying(true)}
            className={`absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ${
              isVideoPlaying ? 'opacity-100' : 'opacity-0'
            }`}
          >
            <source src={videoSrc} type="video/mp4" />
          </video>
        )}

        {/* 3. Cinematic Film Scrim (Ensures crystal-clear high contrast for white luxury typography) */}
        <div className="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/75 pointer-events-none" />

        {/* 4. Bottom Seamless Gradient Merging Into Alabaster Marble White Canvas */}
        <div className="absolute bottom-0 left-0 right-0 h-32 sm:h-48 bg-gradient-to-t from-[#FAF9F5] via-[#FAF9F5]/40 to-transparent pointer-events-none" />
      </motion.div>

      {/* Hero Content Overlay */}
      <motion.div
        style={{ y: textTranslateY, opacity: contentOpacity }}
        className="relative z-10 w-full max-w-5xl mx-auto flex flex-col items-center text-center space-y-4 sm:space-y-6 md:space-y-8 transform-gpu will-change-transform"
      >
        {/* Eyebrow Pill Tag */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.15 }}
          className="inline-flex items-center space-x-2.5 px-4 sm:px-5 py-1.5 sm:py-2 rounded-full bg-black/45 backdrop-blur-md border border-white/20 text-[#FAF9F5] shadow-xl text-[10px] sm:text-xs tracking-[0.2em] sm:tracking-[0.24em] uppercase font-medium"
        >
          <span className="w-1.5 h-1.5 rounded-full bg-[#E5C158] animate-pulse shadow-[0_0_8px_#E5C158]" />
          <span className="inline sm:hidden">Atelier Vérité Studio</span>
          <span className="hidden sm:inline">Atelier Vérité &bull; Architecture & Luxury Interior Sanctuaries</span>
        </motion.div>

        {/* Massive Bodoni Moda Display Title in Radiant White */}
        <motion.h1
          initial={{ opacity: 0, y: 35 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 1, delay: 0.35, ease: [0.16, 1, 0.3, 1] }}
          className="font-serif text-5xl sm:text-7xl md:text-8xl lg:text-[9.5rem] font-normal text-white tracking-tight leading-[0.92] drop-shadow-[0_4px_32px_rgba(0,0,0,0.65)] break-words w-full select-none"
        >
          {title}
        </motion.h1>

        {/* Subtitle in High-Contrast Alabaster White */}
        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.55 }}
          className="text-[#FAF8F5] text-xs sm:text-base md:text-xl font-light max-w-2xl leading-relaxed drop-shadow-[0_2px_12px_rgba(0,0,0,0.8)] px-2"
        >
          {subtitle}
        </motion.p>

        {/* Action Buttons with High-Contrast Editorial Styling */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.75 }}
          className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 pt-2 w-full sm:w-auto max-w-xs sm:max-w-none"
        >
          <Link
            to={primaryButton.href}
            className="group inline-flex items-center justify-center space-x-3 bg-white text-[#1C1B19] hover:bg-accent hover:text-white px-7 sm:px-9 py-3.5 sm:py-4 rounded-full text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-semibold transition-all duration-300 shadow-[0_12px_32px_rgba(0,0,0,0.4)] hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] text-center"
          >
            <span>{primaryButton.text}</span>
            <ArrowUpRight className="w-4 h-4 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
          </Link>

          <Link
            to={secondaryButton.href}
            className="group inline-flex items-center justify-center space-x-3 bg-black/40 hover:bg-black/60 text-white border border-white/30 hover:border-white/60 px-7 sm:px-9 py-3.5 sm:py-4 rounded-full text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-medium backdrop-blur-md transition-all duration-300 shadow-[0_8px_24px_rgba(0,0,0,0.3)] hover:scale-[1.02] active:scale-[0.98] text-center"
          >
            <Compass className="w-4 h-4 text-[#E5C158] transition-transform duration-500 group-hover:rotate-45" />
            <span>{secondaryButton.text}</span>
          </Link>
        </motion.div>
      </motion.div>

      {/* Bottom Scroll Cue */}
      <div className="absolute bottom-3 sm:bottom-6 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center space-y-1 sm:space-y-2 opacity-90 pointer-events-none">
        <span className="text-[9px] uppercase tracking-[0.3em] text-white/90 font-medium drop-shadow-md">Scroll to Explore</span>
        <div className="w-[1px] h-6 sm:h-8 bg-gradient-to-b from-white/90 to-transparent" />
      </div>
    </section>
  );
}
