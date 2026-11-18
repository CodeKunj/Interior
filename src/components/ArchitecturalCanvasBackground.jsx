import React, { useState, useEffect, useRef } from 'react';
import { motion, useScroll, useTransform, useSpring } from 'framer-motion';
import marbleTexture from '../assets/textures/marble-calacatta.webp';

/**
 * ArchitecturalCanvasBackground
 * Bespoke creative canvas for RiTiVerse • Atelier Vérité.
 * Imbues the entire website with tactile Italian lime-plaster grain,
 * shifting ambient daylight orbs, subtle architectural drafting guidelines,
 * and classical proportion watermarks.
 */
export default function ArchitecturalCanvasBackground() {
  const [mousePos, setMousePos] = useState({ x: -1000, y: -1000 });
  const [isPointerDevice, setIsPointerDevice] = useState(false);

  // Smooth mouse coordinates with spring physics
  const springConfig = { damping: 28, stiffness: 90 };
  const mouseX = useSpring(-1000, springConfig);
  const mouseY = useSpring(-1000, springConfig);

  const { scrollY } = useScroll();
  // Faint parallax drift for architectural line watermarks
  const parallaxY = useTransform(scrollY, [0, 3000], [0, -180]);
  const archRotate = useTransform(scrollY, [0, 3000], [0, 12]);

  useEffect(() => {
    // Check if device supports fine mouse hover
    const hasPointer = window.matchMedia('(pointer: fine)').matches;
    setIsPointerDevice(hasPointer);

    if (!hasPointer) return;

    const handleMouseMove = (e) => {
      mouseX.set(e.clientX);
      mouseY.set(e.clientY);
      setMousePos({ x: e.clientX, y: e.clientY });
    };

    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    return () => window.removeEventListener('mousemove', handleMouseMove);
  }, [mouseX, mouseY]);

  return (
    <div
      aria-hidden="true"
      className="fixed inset-0 pointer-events-none z-0 overflow-hidden select-none"
    >
      {/* ------------------------------------------------------------------ */}
      {/* 1. Base Warm Alabaster to Honed Travertine Architectural Base        */}
      {/* ------------------------------------------------------------------ */}
      <div className="absolute inset-0 bg-[#FAF9F5]" />

      {/* ------------------------------------------------------------------ */}
      {/* 1.5 Authentic Italian Calacatta Marble Vein Substratum              */}
      {/* ------------------------------------------------------------------ */}
      <div
        className="absolute inset-0 opacity-[0.08] md:opacity-[0.11] md:mix-blend-multiply bg-cover bg-center pointer-events-none"
        style={{
          backgroundImage: `url(${marbleTexture})`,
        }}
      />

      {/* ------------------------------------------------------------------ */}
      {/* 2. Organic Shifting Daylight Orbs (Natural Sunlight Simulation)     */}
      {/* ------------------------------------------------------------------ */}
      {/* Mobile-Optimized Lightweight Static Gradients (Zero CPU/RAF jank) */}
      <div className="md:hidden absolute -top-16 -right-16 w-64 h-64 rounded-full bg-gradient-to-br from-[#F5E8D3]/40 to-transparent blur-[45px] pointer-events-none transform-gpu" />
      <div className="md:hidden absolute top-[35%] -left-20 w-60 h-60 rounded-full bg-gradient-to-tr from-[#EBE1D0]/35 to-transparent blur-[45px] pointer-events-none transform-gpu" />
      <div className="md:hidden absolute bottom-[15%] -right-16 w-56 h-56 rounded-full bg-gradient-to-tl from-[#D8C6B6]/30 to-transparent blur-[40px] pointer-events-none transform-gpu" />

      {/* Desktop Animated Daylight Orbs (Hardware-accelerated) */}
      <div className="hidden md:block">
        {/* Top Right: Warm Golden Hour Sunlit Glow */}
        <motion.div
          animate={{
            x: [0, 40, -30, 0],
            y: [0, -50, 20, 0],
            scale: [1, 1.15, 0.95, 1],
          }}
          transition={{
            duration: 22,
            repeat: Infinity,
            ease: 'easeInOut',
          }}
          className="absolute -top-32 -right-32 w-[850px] h-[850px] rounded-full bg-gradient-to-br from-[#F5E8D3]/50 via-[#EDE0CA]/30 to-transparent blur-[110px] transform-gpu will-change-transform"
        />

        {/* Center Left: Soft Italian Travertine & Linen Atmosphere */}
        <motion.div
          animate={{
            x: [0, -50, 30, 0],
            y: [0, 60, -40, 0],
            scale: [1, 0.9, 1.12, 1],
          }}
          transition={{
            duration: 28,
            repeat: Infinity,
            ease: 'easeInOut',
          }}
          className="absolute top-[35%] -left-48 w-[800px] h-[800px] rounded-full bg-gradient-to-tr from-[#EBE1D0]/45 via-[#E6D9C2]/25 to-transparent blur-[120px] transform-gpu will-change-transform"
        />

        {/* Bottom Right: Bronze Sanctuary Warmth */}
        <motion.div
          animate={{
            x: [0, 30, -40, 0],
            y: [0, -40, 30, 0],
            scale: [1, 1.1, 0.92, 1],
          }}
          transition={{
            duration: 25,
            repeat: Infinity,
            ease: 'easeInOut',
          }}
          className="absolute bottom-[10%] right-[-10%] w-[750px] h-[750px] rounded-full bg-gradient-to-tl from-[#D8C6B6]/35 via-[#F1E7DA]/30 to-transparent blur-[100px] transform-gpu will-change-transform"
        />
      </div>

      {/* Interactive Cursor Daylight Follower (Desktop only) */}
      {isPointerDevice && (
        <motion.div
          style={{
            x: mouseX,
            y: mouseY,
            translateX: '-50%',
            translateY: '-50%',
          }}
          className="absolute w-[500px] h-[500px] rounded-full bg-gradient-radial from-[#F5EEDB]/40 via-[#ECE2D0]/15 to-transparent blur-[80px]"
        />
      )}

      {/* ------------------------------------------------------------------ */}
      {/* 3. Architectural Drafting Guidelines (Blueprint Spatial Grid)      */}
      {/* ------------------------------------------------------------------ */}
      <div className="absolute inset-0 max-w-6xl mx-auto px-4 sm:px-6 md:px-12 flex justify-between">
        {/* Left Boundary Guideline */}
        <div className="h-full w-[1px] bg-gradient-to-b from-[#8C6D53]/[0.02] via-[#8C6D53]/[0.08] to-[#8C6D53]/[0.02] relative">
          {/* Subtle architectural vertical notations */}
          <div className="hidden xl:flex absolute top-44 -left-7 -rotate-90 origin-left items-center space-x-2 text-[9px] font-mono tracking-[0.3em] uppercase text-[#8C6D53]/40 whitespace-nowrap">
            <span>GRID AXIS 01 &bull; SCALE 1:50</span>
          </div>
          <div className="hidden xl:flex absolute top-[1400px] -left-7 -rotate-90 origin-left items-center space-x-2 text-[9px] font-mono tracking-[0.3em] uppercase text-[#8C6D53]/40 whitespace-nowrap">
            <span>MONOLITHIC TRAVERTINE &bull; ELEV 14.5M</span>
          </div>
        </div>

        {/* Quarter Guide (hidden on small screens) */}
        <div className="hidden md:block h-full w-[1px] bg-gradient-to-b from-transparent via-[#8C6D53]/[0.035] to-transparent" />

        {/* Center Proportional Axis */}
        <div className="h-full w-[1px] bg-gradient-to-b from-transparent via-[#8C6D53]/[0.045] to-transparent" />

        {/* Three-Quarter Guide (hidden on small screens) */}
        <div className="hidden md:block h-full w-[1px] bg-gradient-to-b from-transparent via-[#8C6D53]/[0.035] to-transparent" />

        {/* Right Boundary Guideline */}
        <div className="h-full w-[1px] bg-gradient-to-b from-[#8C6D53]/[0.02] via-[#8C6D53]/[0.08] to-[#8C6D53]/[0.02] relative">
          <div className="hidden xl:flex absolute top-96 -right-7 rotate-90 origin-right items-center space-x-2 text-[9px] font-mono tracking-[0.3em] uppercase text-[#8C6D53]/40 whitespace-nowrap">
            <span>SO HO &bull; LAT 40.7228&deg; N &bull; LON 74.0016&deg; W</span>
          </div>
        </div>
      </div>

      {/* Horizontal Architectural Datum Hairlines & Registration Crosshairs */}
      <div className="absolute inset-0 max-w-6xl mx-auto px-4 sm:px-6 md:px-12 pointer-events-none">
        {/* Crosshair at 180px */}
        <div className="absolute top-[180px] left-4 sm:left-6 md:left-12 -translate-x-1/2 -translate-y-1/2 text-[#8C6D53]/30 font-mono text-xs select-none">
          +
        </div>
        <div className="absolute top-[180px] right-4 sm:right-6 md:right-12 translate-x-1/2 -translate-y-1/2 text-[#8C6D53]/30 font-mono text-xs select-none">
          +
        </div>

        {/* Crosshair at 950px */}
        <div className="absolute top-[950px] left-4 sm:left-6 md:left-12 -translate-x-1/2 -translate-y-1/2 text-[#8C6D53]/25 font-mono text-xs select-none">
          +
        </div>
        <div className="absolute top-[950px] right-4 sm:right-6 md:right-12 translate-x-1/2 -translate-y-1/2 text-[#8C6D53]/25 font-mono text-xs select-none">
          +
        </div>

        {/* Crosshair at 1900px */}
        <div className="absolute top-[1900px] left-4 sm:left-6 md:left-12 -translate-x-1/2 -translate-y-1/2 text-[#8C6D53]/20 font-mono text-xs select-none">
          +
        </div>
        <div className="absolute top-[1900px] right-4 sm:right-6 md:right-12 translate-x-1/2 -translate-y-1/2 text-[#8C6D53]/20 font-mono text-xs select-none">
          +
        </div>
      </div>

      {/* ------------------------------------------------------------------ */}
      {/* 4. Sculptural Classical Arch & Geometric Line Watermarks (Desktop) */}
      {/* ------------------------------------------------------------------ */}
      <motion.div
        style={{ y: parallaxY }}
        className="hidden md:block absolute top-[480px] right-[3%] w-[620px] h-[820px] opacity-[0.035] text-[#8C6D53] pointer-events-none transform-gpu"
      >
        <svg
          viewBox="0 0 600 800"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.2"
          className="w-full h-full"
        >
          {/* Classical Roman Arch Silhouette */}
          <path d="M 100 800 L 100 350 A 200 200 0 0 1 500 350 L 500 800" />
          <path d="M 150 800 L 150 350 A 150 150 0 0 1 450 350 L 450 800" />
          <path d="M 200 800 L 200 350 A 100 100 0 0 1 400 350 L 400 800" />
          {/* Keystone & Center Proportion Axis */}
          <line x1="300" y1="120" x2="300" y2="800" strokeDasharray="4 6" />
          <circle cx="300" cy="350" r="8" />
          <circle cx="300" cy="350" r="280" strokeDasharray="2 8" />
        </svg>
      </motion.div>

      {/* Faint Floating Golden Spiral / Spatial Curve on Left */}
      <motion.div
        style={{ y: parallaxY, rotate: archRotate }}
        className="hidden lg:block absolute top-[1800px] -left-24 w-[500px] h-[500px] opacity-[0.025] text-[#8C6D53]"
      >
        <svg viewBox="0 0 500 500" fill="none" stroke="currentColor" strokeWidth="1">
          <circle cx="250" cy="250" r="240" strokeDasharray="3 5" />
          <circle cx="250" cy="250" r="180" />
          <circle cx="250" cy="250" r="110" strokeDasharray="6 6" />
          <line x1="10" y1="250" x2="490" y2="250" />
          <line x1="250" y1="10" x2="250" y2="490" />
        </svg>
      </motion.div>

      {/* ------------------------------------------------------------------ */}
      {/* 5. Tactile Italian Lime Plaster Noise Film (Desktop only)          */}
      {/* ------------------------------------------------------------------ */}
      {/* High-frequency micro-texture rendered via inline SVG (disabled on mobile for 120Hz native fps) */}
      <div
        className="hidden md:block absolute inset-0 opacity-[0.038] mix-blend-multiply bg-repeat pointer-events-none"
        style={{
          backgroundImage: `url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E")`,
        }}
      />

      {/* ------------------------------------------------------------------ */}
      {/* 6. Soft Atmospheric Vignette (Prevents harsh screen edges)          */}
      {/* ------------------------------------------------------------------ */}
      <div className="absolute inset-0 bg-radial-vignette pointer-events-none" />
    </div>
  );
}
