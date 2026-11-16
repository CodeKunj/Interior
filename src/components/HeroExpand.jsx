import React, { useRef, useState } from 'react';
import { motion, useScroll, useTransform } from 'framer-motion';
import { Link } from 'react-router-dom';
import { ArrowUpRight, Compass } from 'lucide-react';

function SplitTextRoll({ text }) {
  return (
    <span className="inline-flex overflow-hidden relative">
      <span className="flex">
        {[...text].map((char, i) => (
          <span
            key={i}
            className="inline-block transition-transform duration-400 ease-out group-hover:-translate-y-full"
            style={{
              transitionDelay: `${i * 12}ms`,
              whiteSpace: char === ' ' ? 'pre' : undefined
            }}
          >
            {char}
          </span>
        ))}
      </span>
      <span className="flex absolute inset-0 top-full">
        {[...text].map((char, i) => (
          <span
            key={i}
            className="inline-block transition-transform duration-400 ease-out group-hover:-translate-y-full text-accent"
            style={{
              transitionDelay: `${i * 12}ms`,
              whiteSpace: char === ' ' ? 'pre' : undefined
            }}
          >
            {char}
          </span>
        ))}
      </span>
    </span>
  );
}

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
      className="relative min-h-[100dvh] flex flex-col justify-end items-center px-4 sm:px-6 md:px-12 pb-16 sm:pb-20 pt-28 sm:pt-36 overflow-hidden bg-[#FAF9F5]"
    >
      {/* Background Architectural Media Frame with Cinematic Sharpness */}
      <motion.div
        style={{ scale: mediaScale }}
        className="absolute inset-0 z-0 overflow-hidden bg-[#FAF9F5]"
      >
        {/* 1. Signature High-Resolution Interior Photograph (Always visible & crisp) */}
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

        {/* 2. Optional Ambient Video (Fades in seamlessly if available and playing) */}
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

        {/* 3. Subtle Ambient Contrast Film (12% black) to make white marble & lighting pop */}
        <div className="absolute inset-0 bg-black/15 pointer-events-none" />

        {/* 4. Top Hairline Gradient for Floating Pill Header Clarity */}
        <div className="absolute top-0 left-0 right-0 h-40 bg-gradient-to-b from-[#FAF9F5]/70 via-[#FAF9F5]/20 to-transparent pointer-events-none" />

        {/* 5. Bottom Seamless Gradient Merging Into Marble White Section Below */}
        <div className="absolute bottom-0 left-0 right-0 h-64 md:h-80 bg-gradient-to-t from-[#FAF9F5] via-[#FAF9F5]/75 via-40% to-transparent pointer-events-none" />
      </motion.div>

      {/* Hero Content Overlay */}
      <motion.div
        style={{ y: textTranslateY, opacity: contentOpacity }}
        className="relative z-10 w-full max-w-5xl mx-auto flex flex-col items-center text-center space-y-4 sm:space-y-6 md:space-y-8"
      >
        {/* Pill Tag */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.15 }}
          className="pill-tag shadow-md bg-white/95 backdrop-blur-md text-[10px] sm:text-xs"
        >
          <span className="w-1.5 h-1.5 rounded-full bg-accent animate-pulse" />
          <span className="inline sm:hidden">Atelier Vérité Studio</span>
          <span className="hidden sm:inline">Atelier Vérité &bull; Architecture & Interior Design Studio</span>
        </motion.div>

        {/* Massive Bodoni Moda Display Title in Deep Charcoal with Luminous Definition */}
        <motion.h1
          initial={{ opacity: 0, y: 35 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 1, delay: 0.35, ease: [0.16, 1, 0.3, 1] }}
          className="font-serif text-5xl sm:text-7xl md:text-8xl lg:text-[9.5rem] font-normal text-[#1C1B19] tracking-tight leading-[0.92] drop-shadow-[0_4px_24px_rgba(255,255,255,0.85)] break-words w-full"
        >
          {title}
        </motion.h1>

        {/* Subtitle in Plus Jakarta Sans with High Contrast */}
        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.55 }}
          className="text-[#2B2925] text-xs sm:text-base md:text-xl font-light max-w-2xl leading-relaxed drop-shadow-[0_2px_8px_rgba(255,255,255,0.7)] px-2"
        >
          {subtitle}
        </motion.p>

        {/* Action Buttons with Split-Text Roll */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.75 }}
          className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 pt-2 w-full sm:w-auto max-w-xs sm:max-w-none"
        >
          <Link
            to={primaryButton.href}
            className="group inline-flex items-center justify-center space-x-3 bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white px-6 sm:px-8 py-3.5 sm:py-4 rounded-full text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-semibold transition-all duration-300 shadow-2xl text-center"
          >
            <SplitTextRoll text={primaryButton.text} />
            <ArrowUpRight className="w-4 h-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
          </Link>

          <Link
            to={secondaryButton.href}
            className="group inline-flex items-center justify-center space-x-3 bg-white/95 hover:bg-white text-[#1C1B19] border border-[#E2DDD5] px-6 sm:px-8 py-3.5 sm:py-4 rounded-full text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-medium backdrop-blur-md transition-all duration-300 shadow-lg text-center"
          >
            <Compass className="w-4 h-4 text-accent" />
            <SplitTextRoll text={secondaryButton.text} />
          </Link>
        </motion.div>
      </motion.div>

      {/* Bottom Scroll Cue */}
      <div className="absolute bottom-3 sm:bottom-6 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center space-y-1 sm:space-y-2 opacity-70 pointer-events-none">
        <span className="text-[9px] uppercase tracking-[0.3em] text-[#1C1B19] font-medium">Scroll to Explore</span>
        <div className="w-[1px] h-6 sm:h-8 bg-gradient-to-b from-accent to-transparent" />
      </div>
    </section>
  );
}
