import React from 'react';
import { Link } from 'react-router-dom';
import { ChevronRight, ArrowUpRight } from 'lucide-react';
import marbleTexture from '../assets/textures/marble-calacatta.webp';

const FOOTER_COLUMNS = [
  {
    title: 'Sanctuaries & Suites',
    items: [
      { label: 'Featured Works & Suites', href: '/work' },
      { label: 'Interactive 3D Virtual Tours', href: '/work/the-tribeca-loft' },
      { label: 'Turnkey Residential Estates', href: '/work?category=Turnkey' },
      { label: 'Historic Restorations', href: '/work?category=Renovation' }
    ]
  },
  {
    title: 'Methodology & Practice',
    items: [
      { label: 'Architectural Disciplines', href: '/services' },
      { label: 'Spatial Transformations & Journeys', href: '/journeys' },
      { label: '4-Step Design Process', href: '/services' },
      { label: 'Studio Heritage & Philosophy', href: '/about' }
    ]
  },
  {
    title: 'Client Inquiries',
    items: [
      { label: 'Book Private Consultation', href: '/contact' },
      { label: 'WhatsApp Private Client Desk', href: 'https://wa.me/12125550198', external: true },
      { label: 'Studio CMS Portal', href: '/admin/', external: true },
      { label: 'New York &bull; Paris &bull; Kyoto', href: '/contact' }
    ]
  }
];

export default function FooterBrand({ brand = 'RiTiVerse' }) {
  return (
    <footer className="relative z-10 w-full pt-14 sm:pt-20 pb-10 sm:pb-12 rounded-t-2xl sm:rounded-t-3xl overflow-hidden bg-[#EFECE6] text-[#1C1B19] border-t border-[#E2DDD5]">
      {/* Authentic Monolithic Honed Marble Texture Underlay */}
      <div
        className="absolute inset-0 bg-cover bg-center opacity-[0.25] mix-blend-multiply pointer-events-none"
        style={{ backgroundImage: `url(${marbleTexture})` }}
      />
      <div className="relative z-10 w-full max-w-6xl px-4 sm:px-6 md:px-12 mx-auto flex flex-col gap-10 sm:gap-14">
        {/* Massive Auto-Fill Editorial Bodoni Moda Brand Headline */}
        <div className="overflow-hidden select-none border-b border-[#E2DDD5] pb-8 sm:pb-12">
          <span className="font-serif text-4xl xs:text-5xl sm:text-7xl md:text-8xl lg:text-9xl font-normal tracking-tight text-[#1C1B19] block leading-none hover:text-accent transition-colors duration-500 break-words">
            {brand}
          </span>
          <span className="text-[10px] sm:text-xs uppercase tracking-[0.16em] sm:tracking-[0.35em] text-[#3D3A35] block mt-3 font-medium break-words">
            ATELIER VÉRITÉ ARCHITECTURE & LUXURY INTERIOR SANCTUARIES
          </span>
        </div>

        {/* 3 Navigation Columns with Chevrons */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 sm:gap-10">
          {FOOTER_COLUMNS.map((col, idx) => (
            <div key={idx} className="space-y-3 sm:space-y-4">
              <h4 className="text-xs uppercase tracking-[0.2em] sm:tracking-[0.25em] text-accent font-semibold">
                {col.title}
              </h4>
              <ul className="space-y-2.5 sm:space-y-3 text-xs sm:text-sm text-[#2E2B27] font-medium">
                {col.items.map((item, itemIdx) => (
                  <li key={itemIdx}>
                    {item.external ? (
                      <a
                        href={item.href}
                        target="_blank"
                        rel="noreferrer"
                        className="group flex items-center space-x-2 text-[#2E2B27] hover:text-accent transition-colors py-0.5"
                      >
                        <ChevronRight className="w-3.5 h-3.5 text-accent opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300" />
                        <span>{item.label}</span>
                        <ArrowUpRight className="w-3 h-3 opacity-60" />
                      </a>
                    ) : (
                      <Link
                        to={item.href}
                        className="group flex items-center space-x-2 text-[#2E2B27] hover:text-accent transition-colors py-0.5"
                      >
                        <ChevronRight className="w-3.5 h-3.5 text-accent opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300" />
                        <span>{item.label}</span>
                      </Link>
                    )}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Bottom Credits & cPanel Admin Link */}
        <div className="pt-6 sm:pt-8 border-t border-[#E2DDD5] flex flex-col sm:flex-row items-center justify-between text-xs text-[#3D3A35] font-medium gap-4 text-center sm:text-left">
          <p className="text-[11px] sm:text-xs">
            &copy; {new Date().getFullYear()} RiTiVerse &bull; Atelier Vérité Interiors. Calacatta Marble & Soft Editorial Aesthetics.
          </p>

          <div className="flex items-center space-x-4 sm:space-x-6 text-[11px] sm:text-xs">
            <span className="text-[#5C5852]">Shared cPanel / PHP 8+ PDO API</span>
            <a
              href="/admin/"
              target="_blank"
              rel="noreferrer"
              className="text-[#2E2B27] hover:text-accent transition-colors underline underline-offset-4 font-semibold"
            >
              CMS Admin
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
}
