import React, { useState, useRef, useCallback, useEffect } from 'react';
import { ChevronsLeftRight } from 'lucide-react';

/**
 * BeforeAfterSlider
 * Interactive draggable comparison slider for interior architectural transformations.
 */
export default function BeforeAfterSlider({
  beforeImage,
  afterImage,
  beforeLabel = 'Prior Condition',
  afterLabel = 'Atelier Handover',
  title = '',
  description = ''
}) {
  const [sliderPosition, setSliderPosition] = useState(50); // percentage 0 - 100
  const [isDragging, setIsDragging] = useState(false);
  const [containerWidth, setContainerWidth] = useState(0);
  const containerRef = useRef(null);

  useEffect(() => {
    if (!containerRef.current) return;
    const updateWidth = () => {
      if (containerRef.current) {
        setContainerWidth(containerRef.current.offsetWidth);
      }
    };
    updateWidth();
    window.addEventListener('resize', updateWidth);
    return () => window.removeEventListener('resize', updateWidth);
  }, []);

  const handleMove = useCallback((clientX) => {
    if (!containerRef.current) return;
    const rect = containerRef.current.getBoundingClientRect();
    const x = clientX - rect.left;
    const percentage = Math.max(0, Math.min(100, (x / rect.width) * 100));
    setSliderPosition(percentage);
  }, []);

  const handleMouseDown = (e) => {
    setIsDragging(true);
    handleMove(e.clientX);
  };

  const handleTouchStart = (e) => {
    setIsDragging(true);
    if (e.touches[0]) {
      handleMove(e.touches[0].clientX);
    }
  };

  useEffect(() => {
    const handleMouseUp = () => setIsDragging(false);
    const handleMouseMove = (e) => {
      if (!isDragging) return;
      handleMove(e.clientX);
    };
    const handleTouchMove = (e) => {
      if (!isDragging || !e.touches[0]) return;
      handleMove(e.touches[0].clientX);
    };

    if (isDragging) {
      window.addEventListener('mousemove', handleMouseMove);
      window.addEventListener('mouseup', handleMouseUp);
      window.addEventListener('touchmove', handleTouchMove);
      window.addEventListener('touchend', handleMouseUp);
    }

    return () => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseup', handleMouseUp);
      window.removeEventListener('touchmove', handleTouchMove);
      window.removeEventListener('touchend', handleMouseUp);
    };
  }, [isDragging, handleMove]);

  return (
    <div className="space-y-4">
      {/* Title & Description if provided */}
      {(title || description) && (
        <div className="max-w-2xl mb-4">
          {title && <h4 className="font-serif text-2xl text-[#1C1B19] mb-1">{title}</h4>}
          {description && <p className="text-[#3D3A35] text-xs md:text-sm leading-relaxed font-normal">{description}</p>}
        </div>
      )}

      {/* Main Draggable Comparison Frame */}
      <div
        ref={containerRef}
        onMouseDown={handleMouseDown}
        onTouchStart={handleTouchStart}
        style={{ touchAction: 'pan-y' }}
        className="relative w-full aspect-[4/3] md:aspect-[16/10] overflow-hidden select-none cursor-ew-resize rounded-2xl border border-[#E5E0D8] bg-[#FAF9F5] shadow-xl"
      >
        {/* Layer 1: "After" Image (Full background) */}
        <img
          src={afterImage}
          alt={afterLabel}
          className="absolute inset-0 w-full h-full object-cover pointer-events-none"
        />

        {/* Floating After Badge */}
        <div className="absolute top-3 right-3 sm:top-4 sm:right-4 z-20 pointer-events-none bg-white/95 text-[#1C1B19] text-[9px] sm:text-[10px] uppercase tracking-[0.16em] sm:tracking-[0.2em] px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-full backdrop-blur-md border border-[#E2DDD5] shadow-md font-medium">
          {afterLabel}
        </div>

        {/* Layer 2: "Before" Image (Clipped by slider position) */}
        <div
          className="absolute inset-0 overflow-hidden pointer-events-none"
          style={{ width: `${sliderPosition}%` }}
        >
          <img
            src={beforeImage}
            alt={beforeLabel}
            className="absolute inset-0 w-full h-full object-cover pointer-events-none max-w-none"
            style={{ width: containerWidth ? `${containerWidth}px` : '100%' }}
          />

          {/* Floating Before Badge */}
          <div className="absolute top-3 left-3 sm:top-4 sm:left-4 z-20 pointer-events-none bg-white/95 text-[#1C1B19] text-[9px] sm:text-[10px] uppercase tracking-[0.16em] sm:tracking-[0.2em] px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-full backdrop-blur-md border border-[#E2DDD5] shadow-md font-semibold">
            {beforeLabel}
          </div>
        </div>

        {/* Vertical Divider Line */}
        <div
          className="absolute top-0 bottom-0 z-30 pointer-events-none"
          style={{ left: `${sliderPosition}%` }}
        >
          <div className="w-[2px] h-full bg-white shadow-[0_0_12px_rgba(28,27,25,0.4)]" />

          {/* Central Handle Button */}
          <div className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white text-[#1C1B19] border border-[#DCD6CC] shadow-2xl flex items-center justify-center pointer-events-auto transition-transform hover:scale-110 active:scale-95">
            <ChevronsLeftRight className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-accent" />
          </div>
        </div>

        {/* Drag Helper Cue on Bottom */}
        <div className="absolute bottom-3 sm:bottom-4 left-1/2 -translate-x-1/2 z-20 pointer-events-none bg-white/90 text-[#1C1B19] text-[9px] sm:text-[10px] uppercase tracking-wider px-3 sm:px-4 py-1 rounded-full backdrop-blur-md border border-[#E2DDD5] shadow-sm font-medium whitespace-nowrap">
          Drag to compare
        </div>
      </div>
    </div>
  );
}
