import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { ArrowUpRight, MessageSquare, Compass, Phone } from 'lucide-react';

const NAV_ITEMS = [
  { name: 'Portfolio', path: '/work', subtitle: 'Curated Sanctuaries & 3D Virtual Tours' },
  { name: 'Services', path: '/services', subtitle: 'Turnkey Architectural Practice' },
  { name: 'Journeys', path: '/journeys', subtitle: 'Spatial Transformations & Case Studies' },
  { name: 'Philosophy', path: '/about', subtitle: 'Studio Heritage, Elena Vance & Awards' },
];

const OVERLAY_ITEMS = [
  { name: 'Selected Works', path: '/work', subtitle: 'Curated Sanctuaries & 3D Virtual Tours' },
  { name: 'Disciplines & Process', path: '/services', subtitle: 'Turnkey Architectural Practice' },
  { name: 'Architectural Journeys', path: '/journeys', subtitle: 'Spatial Transformations & Case Studies' },
  { name: 'Studio Philosophy', path: '/about', subtitle: 'Heritage, Elena Vance & Awards' },
  { name: 'Private Commissions', path: '/contact', subtitle: 'Initiate Consultation Dossier' },
];

export default function NavbarFullscreen() {
  const [isOpen, setIsOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);
  const location = useLocation();

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 30);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    setIsOpen(false);
  }, [location.pathname]);

  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isOpen]);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === 'Escape' && isOpen) setIsOpen(false);
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen]);

  const handleNavClick = (item) => {
    setIsOpen(false);
    if (item.hash && location.pathname === '/') {
      const el = document.getElementById(item.hash.replace('#', ''));
      if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
      }
    }
  };

  return (
    <>
      {/* Floating Pill Header Bar (Marble Glass Luxury Finish) */}
      <motion.header
        initial={{ y: -80, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.7, delay: 0.15, ease: [0.16, 1, 0.3, 1] }}
        className="fixed top-3 sm:top-5 left-0 right-0 z-50 flex justify-center px-3 sm:px-6 pointer-events-none"
      >
        <div
          className={`pointer-events-auto flex items-center justify-between w-full max-w-[1240px] px-4 sm:px-8 py-2.5 sm:py-3.5 rounded-full transition-all duration-500 border relative overflow-hidden ${
            isScrolled
              ? 'bg-[#FAF9F5]/95 backdrop-blur-xl border-[#DCD6CC] shadow-[0_12px_40px_rgba(28,27,25,0.08)] py-2 sm:py-3'
              : 'bg-white/90 backdrop-blur-md border-[#E2DDD5] shadow-[0_8px_32px_rgba(28,27,25,0.05)]'
          }`}
        >
          {/* Subtle Marble Capsule Texture */}
          <div
            className="absolute inset-0 bg-cover bg-center opacity-[0.09] mix-blend-multiply pointer-events-none rounded-full"
            style={{ backgroundImage: `url('/textures/marble-calacatta.webp')` }}
          />

          {/* 1. Left Brand Identity */}
          <Link
            to="/"
            className="flex items-center space-x-2 sm:space-x-3 group flex-shrink-0"
          >
            <span className="font-serif text-base sm:text-xl tracking-[0.2em] sm:tracking-[0.24em] uppercase text-[#1C1B19] font-medium group-hover:text-accent transition-colors leading-none">
              RiTiVerse
            </span>
            <span className="hidden sm:inline-block w-[1px] h-3.5 bg-[#1C1B19]/20" />
            <span className="hidden sm:inline-block text-[9px] tracking-[0.28em] uppercase text-accent font-medium whitespace-nowrap leading-none">
              ATELIER VÉRITÉ
            </span>
          </Link>

          {/* 2. Center Desktop Navigation (Clean, concise, never wrapping) */}
          <nav className="hidden lg:flex items-center space-x-7 xl:space-x-10 text-[11px] xl:text-xs tracking-[0.2em] uppercase font-medium text-stone flex-shrink-0">
            {NAV_ITEMS.map((item) => {
              const isActive = location.pathname === item.path;
              return (
                <Link
                  key={item.name}
                  to={item.path}
                  onClick={() => handleNavClick(item)}
                  className={`relative py-1 whitespace-nowrap transition-colors duration-300 hover:text-[#1C1B19] ${
                    isActive ? 'text-[#1C1B19] font-semibold' : 'text-stone'
                  }`}
                >
                  <span>{item.name}</span>
                  {isActive && (
                    <motion.span
                      layoutId="nav-active-dot"
                      className="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-accent"
                    />
                  )}
                </Link>
              );
            })}
          </nav>

          {/* 3. Right Action Cluster (Consultation CTA + Menu Toggle) */}
          <div className="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
            <Link
              to="/contact"
              className="inline-flex items-center space-x-1.5 sm:space-x-2 bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white h-9 sm:h-10 px-3.5 sm:px-6 rounded-full text-[10px] sm:text-[11px] uppercase tracking-[0.14em] sm:tracking-[0.18em] font-semibold transition-all duration-300 shadow-md whitespace-nowrap flex-shrink-0"
            >
              <span>
                <span className="inline sm:hidden">Inquire</span>
                <span className="hidden sm:inline">Book Consultation</span>
              </span>
              <ArrowUpRight className="w-3.5 h-3.5 flex-shrink-0" />
            </Link>

            {/* Menu Trigger Button */}
            <button
              onClick={() => setIsOpen(!isOpen)}
              type="button"
              aria-label={isOpen ? 'Close menu' : 'Open menu'}
              aria-expanded={isOpen}
              className="relative flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-[#E2DDD5] bg-white hover:bg-[#F5F3EE] text-[#1C1B19] hover:text-accent transition-all duration-300 focus:outline-none flex-shrink-0 shadow-sm"
            >
              <div className="w-4 h-3.5 relative flex flex-col justify-between">
                <span
                  className={`w-full h-[1.5px] bg-current transition-all duration-300 origin-center ${
                    isOpen ? 'rotate-45 translate-y-[6px]' : ''
                  }`}
                />
                <span
                  className={`w-full h-[1.5px] bg-current transition-all duration-300 origin-center ${
                    isOpen ? '-rotate-45 -translate-y-[6px]' : ''
                  }`}
                />
              </div>
            </button>
          </div>
        </div>
      </motion.header>

      {/* Full-Screen Boutique Hotel Navigation Overlay (Marble Alabaster Atmosphere) */}
      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.35, ease: [0.16, 1, 0.3, 1] }}
            className="fixed inset-0 z-40 bg-[#FAF9F5]/98 backdrop-blur-2xl p-5 sm:p-12 md:p-16 pt-24 sm:pt-32 text-[#1C1B19] overflow-y-auto"
          >
            <div className="min-h-full flex flex-col justify-between max-w-6xl mx-auto w-full">
              {/* Top Brand Banner inside Overlay */}
              <div className="flex items-center justify-between border-b border-[#E2DDD5] pb-5 w-full">
                <div className="flex items-center space-x-2 sm:space-x-3">
                  <span className="font-serif text-xl sm:text-2xl tracking-[0.24em] text-[#1C1B19]">RiTiVerse</span>
                  <span className="text-[10px] sm:text-xs text-stone tracking-[0.16em] uppercase">&bull; Atelier Vérité</span>
                </div>
                <div className="flex items-center gap-3">
                  <span className="hidden xs:inline-block text-[9px] sm:text-xs text-accent font-mono uppercase tracking-[0.2em]">
                    Boutique Architecture
                  </span>
                  <button
                    onClick={() => setIsOpen(false)}
                    className="p-1.5 rounded-full text-stone hover:text-[#1C1B19] hover:bg-black/5 transition-colors"
                    aria-label="Close menu"
                  >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              {/* Editorial Serif Menu Items */}
              <div className="my-auto py-6 sm:py-8 space-y-2 sm:space-y-4 max-w-4xl mx-auto w-full">
                {OVERLAY_ITEMS.map((item, index) => (
                  <div key={item.name} className="overflow-hidden">
                    <motion.div
                      initial={{ y: 40, opacity: 0 }}
                      animate={{ y: 0, opacity: 1 }}
                      transition={{ duration: 0.45, delay: index * 0.05, ease: [0.16, 1, 0.3, 1] }}
                    >
                      <Link
                        to={item.path}
                        onClick={() => handleNavClick(item)}
                        className="group flex items-baseline justify-between py-2 sm:py-2.5 text-[#1C1B19] hover:text-accent transition-colors border-b border-[#EAE6DF]"
                      >
                        <div className="flex items-baseline space-x-3 sm:space-x-8">
                          <span className="font-mono text-xs sm:text-sm text-stone group-hover:text-accent transition-colors">
                            0{index + 1}
                          </span>
                          <div>
                            <span className="font-serif text-2xl sm:text-4xl md:text-5xl font-normal leading-tight tracking-tight block text-[#1C1B19] group-hover:text-accent transition-colors">
                              {item.name}
                            </span>
                            <span className="text-[10px] sm:text-xs uppercase tracking-[0.14em] sm:tracking-[0.2em] text-stone group-hover:text-[#1C1B19] block mt-0.5 sm:mt-1 font-light line-clamp-1">
                              {item.subtitle}
                            </span>
                          </div>
                        </div>
                        <ArrowUpRight className="w-4 h-4 sm:w-6 sm:h-6 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-1 group-hover:-translate-y-1 text-accent flex-shrink-0 ml-2" />
                      </Link>
                    </motion.div>
                  </div>
                ))}
              </div>

              {/* Bottom Contact & Quick Access Strip */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 pt-5 border-t border-[#E2DDD5] text-xs text-stone w-full">
                <div>
                  <strong className="text-[#1C1B19] block font-medium uppercase tracking-wider mb-1 text-[11px]">
                    Private Client Inquiries
                  </strong>
                  <a href="mailto:concierge@ateliervérité.com" className="hover:text-accent transition-colors text-stone break-all">
                    concierge@ateliervérité.com
                  </a>
                </div>

                <div>
                  <strong className="text-[#1C1B19] block font-medium uppercase tracking-wider mb-1 text-[11px]">
                    WhatsApp Direct Desk
                  </strong>
                  <a
                    href="https://wa.me/12125550198"
                    target="_blank"
                    rel="noreferrer"
                    className="inline-flex items-center space-x-1.5 text-accent hover:underline font-medium"
                  >
                    <MessageSquare className="w-3.5 h-3.5" />
                    <span>+1 (212) 555-0198</span>
                  </a>
                </div>

                <div className="sm:text-right">
                  <span className="text-[#1C1B19] block font-medium">
                    Studios: NYC &bull; Paris &bull; Kyoto
                  </span>
                  <span className="text-[10px] text-stone">
                    &copy; {new Date().getFullYear()} RiTiVerse &bull; All Rights Reserved
                  </span>
                </div>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
