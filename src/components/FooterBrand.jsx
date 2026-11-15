import React from 'react';
import { Link } from 'react-router-dom';
import { ChevronRight, ArrowUpRight } from 'lucide-react';

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

export default function FooterBrand({ brand = 'AURION' }) {
  return (
    <footer className="w-full pt-20 pb-12 rounded-t-3xl overflow-hidden bg-[#EFECE6] text-[#1C1B19] border-t border-[#E2DDD5]">
      <div className="w-full max-w-6xl px-6 md:px-12 mx-auto flex flex-col gap-12 md:gap-16">
        {/* Massive Auto-Fill Editorial Bodoni Moda Brand Headline */}
        <div className="overflow-hidden select-none border-b border-[#E2DDD5] pb-12">
          <span className="font-serif text-5xl sm:text-7xl md:text-8xl lg:text-9xl font-normal tracking-tight text-[#1C1B19] block leading-none hover:text-accent transition-colors duration-500">
            {brand}
          </span>
          <span className="text-xs uppercase tracking-[0.35em] text-stone block mt-3 font-light">
            ATELIER VÉRITÉ ARCHITECTURE & LUXURY INTERIOR SANCTUARIES
          </span>
        </div>

        {/* 3 Navigation Columns with Chevrons */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-10">
          {FOOTER_COLUMNS.map((col, idx) => (
            <div key={idx} className="space-y-4">
              <h4 className="text-xs uppercase tracking-[0.25em] text-accent font-semibold">
                {col.title}
              </h4>
              <ul className="space-y-3 text-sm text-stone">
                {col.items.map((item, itemIdx) => (
                  <li key={itemIdx}>
                    {item.external ? (
                      <a
                        href={item.href}
                        target="_blank"
                        rel="noreferrer"
                        className="group flex items-center space-x-2 text-stone hover:text-[#1C1B19] transition-colors"
                      >
                        <ChevronRight className="w-3.5 h-3.5 text-accent opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300" />
                        <span>{item.label}</span>
                        <ArrowUpRight className="w-3 h-3 opacity-60" />
                      </a>
                    ) : (
                      <Link
                        to={item.href}
                        className="group flex items-center space-x-2 text-stone hover:text-[#1C1B19] transition-colors"
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
        <div className="pt-8 border-t border-[#E2DDD5] flex flex-col sm:flex-row items-center justify-between text-xs text-stone gap-4">
          <p>
            &copy; {new Date().getFullYear()} AURION &bull; Atelier Vérité Interiors. Calacatta Marble & Soft Editorial Aesthetics.
          </p>

          <div className="flex items-center space-x-6">
            <span className="text-stone-light">Shared cPanel / PHP 8+ PDO API</span>
            <a
              href="/admin/"
              target="_blank"
              rel="noreferrer"
              className="text-stone hover:text-accent transition-colors underline underline-offset-4 font-medium"
            >
              CMS Admin
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
}
