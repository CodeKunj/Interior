import React, { useState, useEffect } from 'react';
import { ArrowUpRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import { getDisciplines } from '../services/api';

const MEDIA_SPAN_LOOKUP = {
  'lg:col-span-7': 'lg:col-span-7',
  'lg:col-span-5': 'lg:col-span-5',
  'lg:col-span-4': 'lg:col-span-4',
  'lg:col-span-6': 'lg:col-span-6',
  'lg:col-span-8': 'lg:col-span-8',
  'lg:col-span-12': 'lg:col-span-12',
  '7': 'lg:col-span-7',
  '5': 'lg:col-span-5',
  '4': 'lg:col-span-4'
};

const DEFAULT_MEDIA_SPANS = [
  'lg:col-span-7',
  'lg:col-span-5',
  'lg:col-span-4',
  'lg:col-span-4',
  'lg:col-span-4'
];

function getMediaSpan(item, idx) {
  if (item && item.span && MEDIA_SPAN_LOOKUP[item.span]) {
    return MEDIA_SPAN_LOOKUP[item.span];
  }
  return DEFAULT_MEDIA_SPANS[idx % DEFAULT_MEDIA_SPANS.length];
}

export default function FeaturesMediaGrid({
  tag = 'Architectural Practice',
  title = 'World-Class Spatial Disciplines',
  description = 'Every detail has been considered to create moments of extraordinary tactile comfort, proportion, and serene luxury.',
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
    async function fetchDisciplines() {
      try {
        const data = await getDisciplines();
        if (isMounted) {
          setItems(data);
        }
      } catch (err) {
        console.error('Failed to load disciplines for media grid:', err);
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    fetchDisciplines();

    return () => {
      isMounted = false;
    };
  }, [initialItems]);

  return (
    <section id="experience" className="py-16 sm:py-24 md:py-36 px-4 sm:px-6 md:px-12 bg-transparent relative">
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

        {/* Asymmetric Media Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5 sm:gap-6">
          {items.map((item, idx) => {
            const spanClass = getMediaSpan(item, idx);

            return (
              <div
                key={item.id || idx}
                className={`group relative rounded-2xl overflow-hidden border border-[#E5E0D8] bg-[#161616] min-h-[280px] sm:min-h-[340px] md:min-h-[380px] flex flex-col justify-end p-5 sm:p-8 shadow-lg ${spanClass}`}
              >
                {/* Background Photography */}
                {item.imageSrc && (
                  <img
                    src={item.imageSrc}
                    alt={item.title}
                    loading="lazy"
                    className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                  />
                )}

                {/* Ambient Dark Gradient for text readability */}
                <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/75 via-45% to-transparent opacity-95 transition-opacity duration-300 group-hover:opacity-100" />

                {/* Text Details */}
                <div className="relative z-10 space-y-2">
                  <span className="font-mono text-xs text-[#E5C158] font-semibold tracking-wider">0{idx + 1}</span>
                  <h3 className="font-serif text-2xl md:text-3xl text-white group-hover:text-[#E5C158] transition-colors drop-shadow-md">
                    {item.title}
                  </h3>
                  <p className="text-[#F5F2EB] text-xs sm:text-sm font-normal leading-relaxed max-w-md drop-shadow-sm">
                    {item.description}
                  </p>
                </div>
              </div>
            );
          })}
        </div>

        {/* Link to Services */}
        <div className="text-center pt-2">
          <Link
            to="/services"
            className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.2em] font-medium text-[#1C1B19] hover:text-accent transition-colors border-b border-[#1C1B19]/30 pb-1"
          >
            <span>Explore All 4 Specialized Studio Disciplines</span>
            <ArrowUpRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </section>
  );
}
