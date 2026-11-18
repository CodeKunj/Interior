import React, { useEffect } from 'react';
import Lenis from 'lenis';

/**
 * SmoothScroll
 * Integrates Lenis smooth scrolling for a silky boutique hotel browsing experience.
 */
export default function SmoothScroll({ children }) {
  useEffect(() => {
    // Completely disable Lenis on mobile/touch devices for silky-smooth 120Hz native touch scrolling
    const isMobileDevice =
      typeof window !== 'undefined' &&
      ('ontouchstart' in window ||
        navigator.maxTouchPoints > 0 ||
        window.matchMedia('(pointer: coarse)').matches ||
        window.innerWidth < 1024);

    if (isMobileDevice) {
      return;
    }

    const lenis = new Lenis({
      duration: 1.2,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      orientation: 'vertical',
      gestureOrientation: 'vertical',
      smoothWheel: true,
      wheelMultiplier: 1.0,
      touchMultiplier: 1.0,
    });

    function raf(time) {
      lenis.raf(time);
      requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);

    return () => {
      lenis.destroy();
    };
  }, []);

  return <>{children}</>;
}
