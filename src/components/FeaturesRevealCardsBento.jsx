import React from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight, Sparkles } from 'lucide-react';

const DEFAULT_JOURNEYS = [
  {
    title: 'The Tribeca Loft Revival',
    description: 'Decades of disjointed partitions stripped away to honor historic cast-iron pillars and Douglas fir beams, paired with Roman travertine islands.',
    href: '/work/the-tribeca-loft',
    imageSrc: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=85',
    span: 'md:col-span-4',
    tag: 'Industrial Heritage'
  },
  {
    title: 'Amalfi Clifftop Pergola & Infinity Sanctuary',
    description: 'A sun-drenched clifftop estate overlooking azure Mediterranean waters, engineered with climate-resilient bronze pivot portals and honed stone.',
    href: '/work/villa-serenita-amalfi',
    imageSrc: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    span: 'md:col-span-8',
    tag: 'Turnkey Coastal'
  },
  {
    title: 'Kyoto Hinoki & Moss Garden Sanctuary',
    description: 'Constructed in close dialogue with Kyoto master carpenters (Miyadaiku), balancing traditional Ma negative space with hidden smart environmental controls.',
    href: '/work/kyoto-sanctuary-residence',
    imageSrc: 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=85',
    span: 'md:col-span-6',
    tag: 'Wabi-Sabi Minimalism'
  },
  {
    title: 'Mayfair Georgian Drawing Room Conservation',
    description: 'Painstaking restoration of 18th-century plaster friezes and Calacatta Viola fireplaces, seamlessly integrated with contemporary concealed kitchen joinery.',
    href: '/work/the-mayfair-heritage-salon',
    imageSrc: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
    span: 'md:col-span-6',
    tag: 'Historic Restoration'
  },
  {
    title: 'Lumina Flagship Parfumerie, Paris',
    description: 'An experiential fragrance salon featuring a monumental curving counter carved from solid Navona travertine in central Paris.',
    href: '/work/lumina-flagship-ateliers',
    imageSrc: 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1200&q=85',
    span: 'md:col-span-4',
    tag: 'Sensory Commercial'
  },
  {
    title: 'Bel Air Motorized Canyon Glass Pavilion',
    description: 'Blurring the boundary between interior and dramatic California canyon landscape through 14-foot motorized glass pocket facades.',
    href: '/work/bel-air-promontory-villa',
    imageSrc: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=85',
    span: 'md:col-span-4',
    tag: 'Modernist Turnkey'
  },
  {
    title: 'Interactive 3D Digital Twin Simulation',
    description: 'Every commission includes a fully navigable 3D digital twin so you can test sightlines, natural daylight trajectory, and acoustic isolation.',
    href: '/work/the-tribeca-loft',
    imageSrc: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=85',
    span: 'md:col-span-4',
    tag: 'Virtual Twin Technology'
  }
];

export default function FeaturesRevealCardsBento({
  tag = 'Transformations',
  title = 'The Architectural Journeys',
  description = 'Exclusive case studies and spatial transformations curated for discerning patrons across North America, Europe, and Asia.',
  items = DEFAULT_JOURNEYS
}) {
  return (
    <section id="journeys" className="py-24 md:py-36 px-6 md:px-12 bg-[#F5F3EE] border-t border-[#EAE6DF]">
      <div className="max-w-6xl mx-auto space-y-16">
        {/* Header */}
        <div className="flex flex-col items-center text-center space-y-4 max-w-3xl mx-auto">
          <div className="pill-tag bg-white shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-accent" />
            <span>{tag}</span>
          </div>

          <h2 className="font-serif text-3xl sm:text-5xl md:text-6xl font-normal text-[#1C1B19] leading-tight">
            {title}
          </h2>

          <p className="text-stone text-sm sm:text-base font-light max-w-2xl leading-relaxed">
            {description}
          </p>
        </div>

        {/* Bento Grid */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
          {items.map((item, idx) => (
            <Link
              key={idx}
              to={item.href}
              className={`group relative rounded-2xl overflow-hidden border border-[#E5E0D8] bg-[#161616] min-h-[360px] md:min-h-[420px] flex flex-col justify-between p-8 hover:border-accent/60 transition-all duration-500 shadow-lg hover:shadow-2xl ${
                item.span || 'md:col-span-4'
              }`}
            >
              {/* Background Photo */}
              <img
                src={item.imageSrc}
                alt={item.title}
                loading="lazy"
                className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
              />

              {/* Dark Gradient Overlay for typography readability */}
              <div className="absolute inset-0 bg-gradient-to-t from-[#141312] via-[#141312]/60 to-[#141312]/20 transition-opacity duration-300 group-hover:opacity-90" />

              {/* Top Tag & Action Icon */}
              <div className="relative z-10 flex items-center justify-between">
                <span className="bg-white/90 backdrop-blur-md text-[#1C1B19] text-[10px] font-semibold uppercase tracking-wider px-3 py-1 rounded-full border border-white/30 shadow-sm">
                  {item.tag}
                </span>
                <div className="w-8 h-8 rounded-full bg-white text-[#1C1B19] group-hover:bg-accent group-hover:text-white flex items-center justify-center backdrop-blur-md transition-all duration-300 shadow-md">
                  <ArrowUpRight className="w-4 h-4" />
                </div>
              </div>

              {/* Bottom Text */}
              <div className="relative z-10 space-y-2 pt-12">
                <h3 className="font-serif text-2xl sm:text-3xl text-white group-hover:text-accent transition-colors">
                  {item.title}
                </h3>
                <p className="text-[#E0DBD3] text-xs sm:text-sm font-light leading-relaxed line-clamp-3">
                  {item.description}
                </p>
              </div>
            </Link>
          ))}
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
