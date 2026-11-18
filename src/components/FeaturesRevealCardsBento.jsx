import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight } from 'lucide-react';
import { getJourneys } from '../services/api';

const SPAN_LOOKUP = {
  'md:col-span-4': 'md:col-span-4',
  'md:col-span-6': 'md:col-span-6',
  'md:col-span-8': 'md:col-span-8',
  'md:col-span-12': 'md:col-span-12',
  '4': 'md:col-span-4',
  '6': 'md:col-span-6',
  '8': 'md:col-span-8',
  '12': 'md:col-span-12'
};

const DEFAULT_BENTO_SPANS = [
  'md:col-span-4',
  'md:col-span-8',
  'md:col-span-6',
  'md:col-span-6',
  'md:col-span-4',
  'md:col-span-4',
  'md:col-span-4'
];

function getCardSpan(item, idx) {
  if (item && item.span && SPAN_LOOKUP[item.span]) {
    return SPAN_LOOKUP[item.span];
  }
  return DEFAULT_BENTO_SPANS[idx % DEFAULT_BENTO_SPANS.length];
}

export default function FeaturesRevealCardsBento({
  tag = 'Transformations',
  title = 'The Architectural Journeys',
  description = 'Exclusive case studies and spatial transformations curated for discerning patrons across North America, Europe, and Asia.',
  items: initialItems
}) {
  const [items, setItems] = useState(initialItems || []);
  const [loading, setLoading] = useState(!initialItems || initialItems.length === 0);

  useEffect(() => {
    if (initialItems && initialItems.length > 0) {
      setItems(initialItems);
      setLoading(false);
      return;
    }

    let isMounted = true;
    async function fetchJourneys() {
      try {
        const data = await getJourneys();
        if (isMounted) {
          setItems(data);
        }
      } catch (err) {
        console.error('Failed to load journeys for bento grid:', err);
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    fetchJourneys();

    return () => {
      isMounted = false;
    };
  }, [initialItems]);

  return (
    <section id="journeys" className="py-16 sm:py-24 md:py-36 px-4 sm:px-6 md:px-12 bg-[#F5F3EE]/80 border-t border-[#EAE6DF]/70 relative">
      <div className="max-w-6xl mx-auto space-y-12 sm:space-y-16">
        {/* Header */}
        <div className="flex flex-col items-center text-center space-y-3 sm:space-y-4 max-w-3xl mx-auto">
          <div className="pill-tag bg-white shadow-sm">
            <span>{tag}</span>
          </div>

          <h2 className="font-serif text-3xl sm:text-5xl md:text-6xl font-normal text-[#1C1B19] leading-tight">
            {title}
          </h2>

          <p className="text-stone text-xs sm:text-base font-light max-w-2xl leading-relaxed">
            {description}
          </p>
        </div>

        {/* Bento Grid */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-5 sm:gap-6">
          {items.map((item, idx) => {
            const spanClass = getCardSpan(item, idx);

            return (
              <Link
                key={item.id || item.slug || idx}
                to={item.href || `/work/${item.slug}`}
                className={`group relative rounded-2xl overflow-hidden border border-[#E5E0D8] bg-[#161616] min-h-[300px] sm:min-h-[360px] md:min-h-[420px] flex flex-col justify-between p-5 sm:p-8 hover:border-accent/60 transition-all duration-500 shadow-lg hover:shadow-2xl ${spanClass}`}
              >
                {/* Background Photo */}
                {item.imageSrc && (
                  <img
                    src={item.imageSrc}
                    alt={item.title}
                    loading="lazy"
                    className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                  />
                )}

                {/* Dark Gradient Overlay for typography readability */}
                <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/75 via-45% to-transparent opacity-95 transition-opacity duration-300 group-hover:opacity-100" />

                {/* Top Tag & Action Icon */}
                <div className="relative z-10 flex items-center justify-between">
                  <span className="bg-white/95 backdrop-blur-md text-[#1C1B19] text-[10px] font-semibold uppercase tracking-wider px-3 py-1 rounded-full border border-white/40 shadow-sm">
                    {item.tag}
                  </span>
                  <div className="w-8 h-8 rounded-full bg-white text-[#1C1B19] group-hover:bg-accent group-hover:text-white flex items-center justify-center backdrop-blur-md transition-all duration-300 shadow-md">
                    <ArrowUpRight className="w-4 h-4" />
                  </div>
                </div>

                {/* Bottom Text */}
                <div className="relative z-10 space-y-2 pt-12">
                  <h3 className="font-serif text-2xl sm:text-3xl text-white group-hover:text-[#E5C158] transition-colors drop-shadow-md">
                    {item.title}
                  </h3>
                  <p className="text-[#F5F2EB] text-xs sm:text-sm font-normal leading-relaxed line-clamp-3 drop-shadow-sm">
                    {item.description || item.overview}
                  </p>
                </div>
              </Link>
            );
          })}
        </div>

        {/* Explore All Journeys CTA */}
        <div className="text-center pt-4">
          <Link
            to="/journeys"
            className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.2em] font-semibold text-[#1C1B19] hover:text-white bg-white hover:bg-accent border border-[#E2DDD5] hover:border-accent px-8 py-3.5 rounded-full transition-all shadow-sm"
          >
            <span>Explore All Architectural Journeys & Case Studies</span>
            <ArrowUpRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </section>
  );
}
