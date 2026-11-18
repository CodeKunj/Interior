import React from 'react';
import { ArrowUpRight } from 'lucide-react';
import { Link } from 'react-router-dom';

const DEFAULT_ITEMS = [
  {
    title: 'Turnkey Architectural Stewardship',
    description: 'White-glove design-and-build oversight from bare shell demolition to final linen drapery and art curation.',
    imageSrc: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85',
    span: 'lg:col-span-7'
  },
  {
    title: 'Interactive 3D Virtual Twins',
    description: 'Explore your future residence via browser or VR before construction begins with photorealistic daylight simulations.',
    imageSrc: 'https://storage.googleapis.com/webild/default/templates/hotel/amenities/spa.webp',
    span: 'lg:col-span-5'
  },
  {
    title: 'Quarry-Direct Roman Travertine',
    description: 'Hand-selected unlacquered stone blocks carved by generational Italian artisans for sculptural monolithic islands.',
    imageSrc: 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=85',
    span: 'lg:col-span-4'
  },
  {
    title: 'Acoustic Insulation & Belgian Linens',
    description: 'Micro-perforated acoustic paneling and heavyweight raw linen drapery that absorb reverberation for absolute serenity.',
    imageSrc: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
    span: 'lg:col-span-4'
  },
  {
    title: 'Bespoke Heirloom Millwork',
    description: 'Smoked oak, Japanese hinoki joinery, and custom furniture pieces tailored to the exact proportions of your home.',
    imageSrc: 'https://storage.googleapis.com/webild/default/templates/hotel/amenities/pool.jpg',
    span: 'lg:col-span-4'
  }
];

export default function FeaturesMediaGrid({
  tag = 'Architectural Practice',
  title = 'World-Class Spatial Disciplines',
  description = 'Every detail has been considered to create moments of extraordinary tactile comfort, proportion, and serene luxury.',
  items = DEFAULT_ITEMS
}) {
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
          {items.map((item, idx) => (
            <div
              key={idx}
              className={`group relative rounded-2xl overflow-hidden border border-[#E5E0D8] bg-[#161616] min-h-[280px] sm:min-h-[340px] md:min-h-[380px] flex flex-col justify-end p-5 sm:p-8 shadow-lg ${
                item.span || 'lg:col-span-4'
              }`}
            >
              {/* Background Photography */}
              <img
                src={item.imageSrc}
                alt={item.title}
                loading="lazy"
                className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
              />

              {/* Ambient Dark Gradient for text readability */}
              <div className="absolute inset-0 bg-gradient-to-t from-[#141312] via-[#141312]/60 to-transparent opacity-90 transition-opacity duration-300 group-hover:opacity-85" />

              {/* Text Details */}
              <div className="relative z-10 space-y-2">
                <span className="font-mono text-xs text-accent">0{idx + 1}</span>
                <h3 className="font-serif text-2xl md:text-3xl text-white group-hover:text-accent transition-colors">
                  {item.title}
                </h3>
                <p className="text-[#E0DBD3] text-xs sm:text-sm font-light leading-relaxed max-w-md">
                  {item.description}
                </p>
              </div>
            </div>
          ))}
        </div>

        {/* Link to Services */}
        <div className="text-center pt-2">
          <Link
            to="/services"
            className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.2em] font-medium text-[#1C1B19] hover:text-accent transition-colors border-b border-[#1C1B19]/30 pb-1"
          >
            <span>Learn More About Our 4-Step Process</span>
            <ArrowUpRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </section>
  );
}
