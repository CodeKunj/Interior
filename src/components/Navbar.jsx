import React, { useState, useEffect } from 'react';
import { Link, NavLink, useLocation } from 'react-router-dom';
import { Menu, X, ArrowUpRight } from 'lucide-react';

const NAV_LINKS = [
  { name: 'Home', path: '/' },
  { name: 'Work', path: '/work' },
  { name: 'Services & Process', path: '/services' },
  { name: 'About Studio', path: '/about' },
  { name: 'Contact', path: '/contact' }
];

export default function Navbar() {
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const location = useLocation();

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 40);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Close mobile menu on page transition
  useEffect(() => {
    setMobileMenuOpen(false);
  }, [location.pathname]);

  return (
    <>
      <header
        className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
          isScrolled
            ? 'bg-alabaster/95 backdrop-blur-md shadow-sm border-b border-sand py-4'
            : 'bg-transparent py-6'
        }`}
      >
        <div className="max-w-7xl mx-auto px-6 md:px-12 flex items-center justify-between">
          {/* Brand Logo */}
          <Link to="/" className="group flex flex-col">
            <span className="font-serif text-2xl md:text-3xl font-normal tracking-[0.2em] uppercase text-charcoal group-hover:text-bronze transition-colors">
              ATELIER VÉRITÉ
            </span>
            <span className="text-[10px] tracking-[0.35em] uppercase text-stone font-light -mt-0.5">
              Architecture & Interiors
            </span>
          </Link>

          {/* Desktop Navigation Links */}
          <nav className="hidden lg:flex items-center space-x-10 text-xs tracking-[0.18em] uppercase font-medium text-stone">
            {NAV_LINKS.map((link) => (
              <NavLink
                key={link.path}
                to={link.path}
                className={({ isActive }) =>
                  `transition-all duration-300 relative py-1 hover:text-charcoal ${
                    isActive
                      ? 'text-charcoal font-semibold after:content-[""] after:absolute after:bottom-0 after:left-0 after:right-0 after:h-[1px] after:bg-bronze'
                      : ''
                  }`
                }
              >
                {link.name}
              </NavLink>
            ))}
          </nav>

          {/* Desktop CTA Consultation Button */}
          <div className="hidden lg:flex items-center space-x-6">
            <Link
              to="/contact"
              className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.18em] font-medium px-5 py-2.5 border border-charcoal text-charcoal hover:bg-charcoal hover:text-white transition-all duration-300"
            >
              <span>Book Consultation</span>
              <ArrowUpRight className="w-3.5 h-3.5" />
            </Link>
          </div>

          {/* Mobile Hamburger Button */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            aria-label="Toggle navigation menu"
            className="lg:hidden p-2 text-charcoal hover:text-bronze transition-colors focus:outline-none"
          >
            {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </header>

      {/* Mobile Menu Overlay */}
      <div
        className={`fixed inset-0 z-40 bg-alabaster/98 backdrop-blur-xl transition-all duration-500 lg:hidden flex flex-col justify-between px-8 py-24 ${
          mobileMenuOpen ? 'opacity-100 pointer-events-auto translate-y-0' : 'opacity-0 pointer-events-none -translate-y-4'
        }`}
      >
        <div className="space-y-6 pt-8">
          <p className="text-[10px] uppercase tracking-[0.3em] text-stone">Navigation</p>
          <div className="flex flex-col space-y-5">
            {NAV_LINKS.map((link) => (
              <Link
                key={link.path}
                to={link.path}
                onClick={() => setMobileMenuOpen(false)}
                className={`font-serif text-3xl uppercase tracking-wider transition-colors ${
                  location.pathname === link.path ? 'text-bronze italic' : 'text-charcoal hover:text-bronze'
                }`}
              >
                {link.name}
              </Link>
            ))}
          </div>
        </div>

        <div className="border-t border-sand pt-8 space-y-6">
          <div>
            <span className="text-[10px] uppercase tracking-[0.25em] text-stone block mb-1">Direct Inquiries</span>
            <a href="mailto:concierge@ateliervérité.com" className="font-serif text-lg text-charcoal hover:text-bronze">
              concierge@ateliervérité.com
            </a>
          </div>

          <Link
            to="/contact"
            onClick={() => setMobileMenuOpen(false)}
            className="w-full inline-flex items-center justify-center space-x-2 text-xs uppercase tracking-[0.2em] font-medium py-4 bg-charcoal text-white hover:bg-bronze transition-colors"
          >
            <span>Reserve A Consultation</span>
            <ArrowUpRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </>
  );
}
