import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight, Compass, SplitSquareVertical, MapPin, Calendar, Maximize2, Layers } from 'lucide-react';
import BeforeAfterSlider from '../components/BeforeAfterSlider';

const JOURNEYS_DATA = [
  {
    id: 'the-tribeca-loft',
    title: 'The Tribeca Loft Revival',
    tag: 'Industrial Heritage',
    location: 'Tribeca, New York',
    area: '4,200 sq. ft.',
    duration: '14 Months Turnkey',
    heroImage: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
    overview: 'A full-floor conversion within a protected 19th-century cast-iron warehouse. Stripping away decades of ad-hoc drywall to reveal historical timber pillars, softened with monolithic Roman travertine and micro-perforated acoustic linen walls.',
    materials: ['Roman Travertine Islands', 'Smoked French Oak', 'Acoustic Linen Drapery', 'Aged Unlacquered Brass'],
    has3DTour: true,
    slug: 'the-tribeca-loft'
  },
  {
    id: 'villa-serenita-amalfi',
    title: 'Amalfi Clifftop Pergola & Infinity Sanctuary',
    tag: 'Turnkey Coastal',
    location: 'Amalfi Coast, Italy',
    area: '6,800 sq. ft.',
    duration: '18 Months Turnkey',
    heroImage: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    overview: 'Perched over the Mediterranean, this multi-tiered clifftop villa required structural stabilization and bespoke marine-grade bronze pivot apertures. Designed to maximize coastal breezes and golden afternoon reflection across honed limestone terraces.',
    materials: ['Honed Vicenza Limestone', 'Naval Bronze Portals', 'Rough-Hewn Tufa Stone', 'Bleached Chestnut'],
    has3DTour: true,
    slug: 'villa-serenita-amalfi'
  },
  {
    id: 'kyoto-sanctuary-residence',
    title: 'Kyoto Hinoki & Moss Garden Sanctuary',
    tag: 'Wabi-Sabi Minimalism',
    location: 'Higashiyama, Kyoto',
    area: '3,400 sq. ft.',
    duration: '12 Months Turnkey',
    heroImage: 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
    overview: 'Created in close collaboration with Kyoto master carpenters (Miyadaiku), balancing traditional Ma negative space with hidden geothermal environmental systems. Hand-carved hinoki timber joinery and raw washi acoustic screens.',
    materials: ['Japanese Hinoki Cedar', 'Kurotani Washi Screens', 'Charred Yakisugi Oak', 'River-Honed Basalt'],
    has3DTour: true,
    slug: 'kyoto-sanctuary-residence'
  },
  {
    id: 'the-mayfair-heritage-salon',
    title: 'Mayfair Georgian Drawing Room Conservation',
    tag: 'Historic Restoration',
    location: 'Mayfair, London',
    area: '5,100 sq. ft.',
    duration: '16 Months Turnkey',
    heroImage: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    overview: 'Painstaking restoration of 18th-century plaster friezes and Calacatta Viola fireplaces, seamlessly paired with contemporary concealed kitchen joinery and custom bronze exhibition vitrines.',
    materials: ['Calacatta Viola Marble', 'Cornice Plaster Reliefs', 'Burr Walnut Paneling', 'Smoked Mirror Glass'],
    has3DTour: false,
    slug: 'the-mayfair-heritage-salon'
  },
  {
    id: 'lumina-flagship-ateliers',
    title: 'Lumina Flagship Parfumerie, Paris',
    tag: 'Sensory Commercial',
    location: '1er Arrondissement, Paris',
    area: '2,900 sq. ft.',
    duration: '8 Months Turnkey',
    heroImage: 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
    overview: 'An experiential fragrance salon featuring a monumental curving counter carved from solid Navona travertine in central Paris. Tuned acoustics and concealed scent diffusion chambers create an ethereal oasis.',
    materials: ['Solid Navona Travertine', 'Champagne Anodized Aluminum', 'Velvet Acoustic Plaster', 'Backlit Alabaster'],
    has3DTour: false,
    slug: 'lumina-flagship-ateliers'
  },
  {
    id: 'bel-air-promontory-villa',
    title: 'Bel Air Motorized Canyon Glass Pavilion',
    tag: 'Modernist Turnkey',
    location: 'Bel Air, California',
    area: '8,200 sq. ft.',
    duration: '22 Months Turnkey',
    heroImage: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
    overview: 'Blurring the boundary between interior luxury and the dramatic California canyon landscape through 14-foot motorized glass pocket portals, cantilevered infinity reflections, and seamless micro-cement flooring.',
    materials: ['Micro-Cement Seamless Floors', 'Thermally Broken Glass Portals', 'Smoked Eucalyptus Millwork', 'Honed Basalt'],
    has3DTour: true,
    slug: 'bel-air-promontory-villa'
  }
];

export default function Journeys() {
  const [selectedTag, setSelectedTag] = useState('All');

  const tags = ['All', 'Industrial Heritage', 'Turnkey Coastal', 'Wabi-Sabi Minimalism', 'Historic Restoration', 'Modernist Turnkey'];

  const filteredJourneys = selectedTag === 'All'
    ? JOURNEYS_DATA
    : JOURNEYS_DATA.filter(j => j.tag === selectedTag);

  return (
    <div className="pt-28 sm:pt-36 pb-20 sm:pb-32 space-y-16 sm:space-y-24 md:space-y-32 bg-transparent min-h-screen text-[#1C1B19] relative">
      {/* 1. Page Header */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="max-w-3xl space-y-3 sm:space-y-4">
          <div className="pill-tag bg-white shadow-sm">
            <span>Architectural Chronicles & Transformations</span>
          </div>

          <h1 className="font-serif text-3xl xs:text-4xl sm:text-6xl md:text-7xl font-normal text-[#1C1B19] leading-[1.08] break-words">
            The Architectural Journeys
          </h1>

          <p className="text-stone text-xs sm:text-base md:text-lg font-light leading-relaxed pt-1 sm:pt-2">
            Every sanctuary is an intimate journey of spatial rebirth. Explore our curated case studies tracing bare structural shells through to fully realized, daylight-sculpted residences across New York, Amalfi, Kyoto, London, and Paris.
          </p>
        </div>

        {/* Tag Filters */}
        <div className="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-[#EAE6DF] flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none sm:flex-wrap">
          <span className="text-[11px] sm:text-xs uppercase tracking-wider text-stone mr-2 whitespace-nowrap flex-shrink-0">Typology:</span>
          {tags.map((tag) => (
            <button
              key={tag}
              onClick={() => setSelectedTag(tag)}
              className={`text-[11px] sm:text-xs uppercase tracking-[0.14em] sm:tracking-[0.16em] px-3.5 sm:px-5 py-1.5 sm:py-2.5 rounded-full border transition-all duration-300 font-medium whitespace-nowrap flex-shrink-0 ${
                selectedTag === tag
                  ? 'bg-[#1C1B19] text-[#FAF9F5] border-[#1C1B19] shadow-md'
                  : 'bg-white text-stone border-[#E2DDD5] hover:border-accent hover:text-[#1C1B19]'
              }`}
            >
              {tag}
            </button>
          ))}
        </div>
      </section>

      {/* 2. Interactive Transformation Spotlight (Before & After) */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="rounded-2xl border border-[#E5E0D8] bg-white p-8 md:p-12 shadow-xl space-y-6">
          <div className="flex flex-col md:flex-row md:items-end justify-between border-b border-[#EAE6DF] pb-6 gap-4">
            <div className="space-y-2">
              <span className="text-[11px] uppercase tracking-[0.25em] text-accent font-semibold block">
                Transformation Spotlight
              </span>
              <h2 className="font-serif text-3xl sm:text-4xl text-[#1C1B19]">
                The Tribeca Loft: Great Room & Kitchen Transformation
              </h2>
            </div>
            <div className="pill-tag bg-[#FAF9F5] self-start md:self-auto">
              <SplitSquareVertical className="w-3.5 h-3.5 text-accent" />
              <span>Interactive Slider</span>
            </div>
          </div>

          <p className="text-stone text-xs sm:text-sm font-light max-w-3xl leading-relaxed">
            Drag the comparison divider horizontally to inspect the architectural evolution: from yellowed drywall partitions and fluorescent fixtures to soaring 12-foot timber beams and a sculptural Roman travertine monolithic kitchen.
          </p>

          <BeforeAfterSlider
            beforeImage="https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1600&q=85"
            afterImage="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85"
            beforeLabel="Prior Condition"
            afterLabel="Atelier Handover"
          />
        </div>
      </section>

      {/* 3. Detailed Journey Case Study Cards */}
      <section className="max-w-6xl mx-auto px-6 md:px-12 space-y-16">
        <div className="text-center space-y-3 max-w-2xl mx-auto">
          <span className="text-[11px] uppercase tracking-[0.28em] text-accent font-semibold block">
            Curated Transformations
          </span>
          <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl text-[#1C1B19]">
            Chronicles of Turnkey Stewardship
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
          {filteredJourneys.map((journey, idx) => (
            <article
              key={journey.id}
              className="group flex flex-col justify-between rounded-2xl border border-[#E5E0D8] bg-white overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 hover:border-accent/50"
            >
              {/* Journey Photography Frame */}
              <div className="relative aspect-[16/10] overflow-hidden bg-[#FAF9F5]">
                <img
                  src={journey.heroImage}
                  alt={journey.title}
                  loading="lazy"
                  className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-60" />

                {/* Top Badges */}
                <div className="absolute top-4 left-4 flex flex-col gap-1.5 items-start">
                  <span className="bg-white/95 backdrop-blur-md text-[#1C1B19] text-[10px] font-semibold uppercase tracking-wider px-3 py-1 rounded-full border border-white/40 shadow-sm">
                    {journey.tag}
                  </span>
                  {journey.has3DTour && (
                    <span className="bg-white/90 backdrop-blur-md text-[#1C1B19] text-[10px] uppercase tracking-wider px-2.5 py-0.5 rounded-full border border-[#E2DDD5] flex items-center space-x-1 shadow-sm font-medium">
                      <Compass className="w-3 h-3 text-accent" />
                      <span>3D Tour Available</span>
                    </span>
                  )}
                </div>

                <div className="absolute bottom-3 right-4 font-mono text-xs text-white/90 drop-shadow-md">
                  0{idx + 1}
                </div>
              </div>

              {/* Journey Body */}
              <div className="p-5 sm:p-6 md:p-8 space-y-4 sm:space-y-6 flex-1 flex flex-col justify-between">
                <div className="space-y-3">
                  <div className="flex flex-wrap items-center gap-2 sm:gap-4 text-xs text-stone">
                    <span className="flex items-center space-x-1">
                      <MapPin className="w-3.5 h-3.5 text-accent" />
                      <span>{journey.location}</span>
                    </span>
                    <span className="hidden sm:inline">&bull;</span>
                    <span className="flex items-center space-x-1">
                      <Maximize2 className="w-3.5 h-3.5 text-accent" />
                      <span>{journey.area}</span>
                    </span>
                    <span className="hidden sm:inline">&bull;</span>
                    <span className="flex items-center space-x-1">
                      <Calendar className="w-3.5 h-3.5 text-accent" />
                      <span>{journey.duration}</span>
                    </span>
                  </div>

                  <h3 className="font-serif text-xl sm:text-2xl md:text-3xl text-[#1C1B19] group-hover:text-accent transition-colors">
                    <Link to={`/work/${journey.slug}`}>{journey.title}</Link>
                  </h3>

                  <p className="text-stone text-xs md:text-sm font-light leading-relaxed">
                    {journey.overview}
                  </p>
                </div>

                {/* Materials Used Pills */}
                <div className="pt-4 border-t border-[#EAE6DF] space-y-2 sm:space-y-3">
                  <span className="text-[10px] uppercase tracking-wider text-[#1C1B19] font-semibold flex items-center space-x-1.5">
                    <Layers className="w-3 h-3 text-accent" />
                    <span>Selected Materiality:</span>
                  </span>
                  <div className="flex flex-wrap gap-1.5">
                    {journey.materials.map((m, mIdx) => (
                      <span
                        key={mIdx}
                        className="text-[10px] px-2.5 py-1 rounded-full bg-[#FAF9F5] text-stone border border-[#E2DDD5]"
                      >
                        {m}
                      </span>
                    ))}
                  </div>
                </div>

                {/* Card Action Link */}
                <div className="pt-2">
                  <Link
                    to={`/work/${journey.slug}`}
                    className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.14em] sm:tracking-[0.18em] font-semibold text-[#1C1B19] hover:text-accent transition-colors"
                  >
                    <span>Inspect Full Case Study & 3D Tour</span>
                    <ArrowUpRight className="w-4 h-4" />
                  </Link>
                </div>
              </div>
            </article>
          ))}
        </div>
      </section>

      {/* 4. Patrons Consultation CTA */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="rounded-2xl border border-[#E5E0D8] bg-white p-6 sm:p-10 md:p-16 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 sm:gap-8 shadow-xl">
          <div className="space-y-2 sm:space-y-3 max-w-2xl">
            <span className="text-[10px] sm:text-[11px] uppercase tracking-[0.2em] sm:tracking-[0.25em] text-accent font-semibold block">
              Begin Your Journey
            </span>
            <h3 className="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1B19]">
              Commission your bespoke private sanctuary.
            </h3>
            <p className="text-stone text-xs md:text-sm font-light">
              We guide discerning clients worldwide through initial discovery, 3D digital twins, and turnkey architectural delivery.
            </p>
          </div>

          <Link
            to="/contact"
            className="inline-flex items-center justify-center space-x-2 bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white px-6 sm:px-8 py-3.5 sm:py-4 rounded-full text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-semibold transition-all shadow-md whitespace-nowrap w-full sm:w-auto"
          >
            <span>Initiate Consultation Dossier</span>
            <ArrowUpRight className="w-4 h-4" />
          </Link>
        </div>
      </section>
    </div>
  );
}
