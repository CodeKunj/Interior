import React, { useRef } from 'react';
import { motion, useScroll, useTransform } from 'framer-motion';
import { ArrowUpRight } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function AboutParallax({
  tag = 'About Atelier',
  title = 'A Sanctuary for Spatial Serenity & Rejuvenation',
  description = 'From private contemplative courtyards to bespoke turnkey coastal residences, every space is sculpted with honest materiality, acoustic quietude, and generous daylight.',
  frontImageSrc = 'https://storage.googleapis.com/webild/default/templates/hotel/about/magnific_recreate-this-in-higher-q_te7wx9evnr.webp',
  backImageSrc = 'https://storage.googleapis.com/webild/default/templates/hotel/amenities/pool.jpg',
  badge = 'Est. 2011'
}) {
  const containerRef = useRef(null);
  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ['start end', 'end start']
  });

  const frontY = useTransform(scrollYProgress, [0, 1], ['50px', '-50px']);
  const backY = useTransform(scrollYProgress, [0, 1], ['-30px', '30px']);
  const backScale = useTransform(scrollYProgress, [0, 1], [1, 1.06]);

  return (
    <section ref={containerRef} className="relative py-16 sm:py-24 md:py-36 px-4 sm:px-6 md:px-12 bg-transparent overflow-hidden">
      <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 lg:gap-16 items-center">
        {/* Narrative Column */}
        <div className="lg:col-span-5 space-y-4 sm:space-y-6">
          <div className="pill-tag">
            <span>{tag}</span>
          </div>

          <h2 className="font-serif text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-normal text-[#1C1B19] leading-[1.15]">
            {title}
          </h2>

          <p className="text-stone text-xs sm:text-base font-light leading-relaxed">
            {description}
          </p>

          <p className="text-stone-dark text-xs sm:text-sm font-light leading-relaxed">
            We collaborate with generational master stonemasons and Japanese timber joiners to deliver environments that age with grace, depth, and tactile patina.
          </p>

          <div className="pt-2 sm:pt-4 flex items-center space-x-6">
            <Link
              to="/about"
              className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-medium text-[#1C1B19] hover:text-accent border-b border-[#1C1B19]/30 pb-1 transition-colors"
            >
              <span>Explore Studio Philosophy</span>
              <ArrowUpRight className="w-3.5 h-3.5" />
            </Link>
          </div>
        </div>

        {/* Parallax Layered Imagery Column */}
        <div className="lg:col-span-7 relative h-[320px] xs:h-[380px] sm:h-[500px] md:h-[620px] flex items-center justify-center">
          {/* Back Image with Parallax Drift */}
          <motion.div
            style={{ y: backY, scale: backScale }}
            className="absolute top-0 right-0 w-[80%] h-[82%] rounded-2xl overflow-hidden border border-[#E5E0D8] shadow-xl bg-white transform-gpu will-change-transform"
          >
            <img
              src={backImageSrc}
              alt="Atelier Vérité Architectural Environment"
              className="w-full h-full object-cover"
            />
          </motion.div>

          {/* Front Elevated Card Image with Inverse Parallax Drift */}
          <motion.div
            style={{ y: frontY }}
            className="absolute bottom-2 sm:bottom-4 left-0 w-[64%] h-[70%] rounded-2xl overflow-hidden border border-[#E5E0D8] shadow-[0_25px_50px_rgba(28,27,25,0.12)] bg-white z-10 transform-gpu will-change-transform"
          >
            <img
              src={frontImageSrc}
              alt="Interior Sanctuaries Detail"
              className="w-full h-full object-cover"
            />

            {/* Heritage Badge */}
            {badge && (
              <div className="absolute top-3 left-3 sm:top-4 sm:left-4 bg-white/95 backdrop-blur-md px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-full border border-[#E2DDD5] text-[9px] sm:text-[10px] uppercase tracking-[0.2em] text-accent font-semibold shadow-sm">
                {badge}
              </div>
            )}
          </motion.div>
        </div>
      </div>
    </section>
  );
}
