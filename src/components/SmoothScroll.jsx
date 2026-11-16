import React, { useEffect } from 'react';
import Lenis from 'lenis';

/**
 * SmoothScroll
 * Integrates Lenis smooth scrolling for a silky boutique hotel browsing experience.
 */
export default function SmoothScroll({ children }) {
  useEffect(() => {
    // On touch devices and mobile screens (< 1024px), use silky-smooth native 120Hz inertia scrolling
    // to avoid scroll hijacking, jank, or interfering with touch gestures & sliders
    const isTouch = typeof window !== 'undefined' && ('ontouchstart' in window || navigator.maxTouchPoints > 0);
    if (isTouch && window.innerWidth < 1024) {
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
