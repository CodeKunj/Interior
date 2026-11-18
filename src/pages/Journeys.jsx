import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight, Compass, SplitSquareVertical, MapPin, Calendar, Maximize2, Layers } from 'lucide-react';
import BeforeAfterSlider from '../components/BeforeAfterSlider';
import { getJourneys } from '../services/api';

export default function Journeys() {
  const [journeys, setJourneys] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedTag, setSelectedTag] = useState('All');

  useEffect(() => {
    let isMounted = true;
    async function loadJourneys() {
      try {
        const data = await getJourneys();
        if (isMounted) {
          setJourneys(data);
        }
      } catch (err) {
        console.error('Failed to load journeys from API:', err);
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    loadJourneys();

    return () => {
      isMounted = false;
    };
  }, []);

  const tags = ['All', ...Array.from(new Set(journeys.map(j => j.tag).filter(Boolean)))];

  const filteredJourneys = selectedTag === 'All'
    ? journeys
    : journeys.filter(j => j.tag === selectedTag);

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

          <p className="text-stone text-sm sm:text-base md:text-lg font-normal leading-relaxed pt-1 sm:pt-2">
            Every sanctuary is an intimate journey of spatial rebirth. Explore our curated case studies tracing bare structural shells through to fully realized, daylight-sculpted residences across New York, Amalfi, Kyoto, London, and Paris.
          </p>
        </div>

        {/* Tag Filters */}
        {tags.length > 1 && (
          <div className="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-[#EAE6DF] flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none sm:flex-wrap">
            <span className="text-[11px] sm:text-xs uppercase tracking-wider text-[#2E2B27] font-semibold mr-2 whitespace-nowrap flex-shrink-0">Typology:</span>
            {tags.map((tag) => (
              <button
                key={tag}
                onClick={() => setSelectedTag(tag)}
                className={`text-[11px] sm:text-xs uppercase tracking-[0.14em] sm:tracking-[0.16em] px-3.5 sm:px-5 py-1.5 sm:py-2.5 rounded-full border transition-all duration-300 font-medium whitespace-nowrap flex-shrink-0 ${
                  selectedTag === tag
                    ? 'bg-[#1C1B19] text-[#FAF9F5] border-[#1C1B19] shadow-md'
                    : 'bg-white text-[#2E2B27] border-[#D5CFC5] hover:border-accent hover:text-[#1C1B19]'
                }`}
              >
                {tag}
              </button>
            ))}
          </div>
        )}
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
              key={journey.id || journey.slug || idx}
              className="group flex flex-col justify-between rounded-2xl border border-[#E5E0D8] bg-white overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 hover:border-accent/50"
            >
              {/* Journey Photography Frame */}
              <div className="relative aspect-[16/10] overflow-hidden bg-[#FAF9F5]">
                {journey.heroImage && (
                  <img
                    src={journey.heroImage}
                    alt={journey.title}
                    loading="lazy"
                    className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                  />
                )}
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
                  <div className="flex flex-wrap items-center gap-2 sm:gap-4 text-xs text-[#2E2B27] font-medium">
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
                    <Link to={journey.href || `/work/${journey.slug}`}>{journey.title}</Link>
                  </h3>

                  <p className="text-stone text-xs md:text-sm font-normal leading-relaxed">
                    {journey.overview}
                  </p>
                </div>

                {/* Materials Used Pills */}
                {Array.isArray(journey.materials) && journey.materials.length > 0 && (
                  <div className="pt-4 border-t border-[#EAE6DF] space-y-2 sm:space-y-3">
                    <span className="text-[10px] uppercase tracking-wider text-[#1C1B19] font-semibold flex items-center space-x-1.5">
                      <Layers className="w-3 h-3 text-accent" />
                      <span>Selected Materiality:</span>
                    </span>
                    <div className="flex flex-wrap gap-1.5">
                      {journey.materials.map((m, mIdx) => (
                        <span
                          key={mIdx}
                          className="text-[10px] px-2.5 py-1 rounded-full bg-[#FAF9F5] text-[#2E2B27] border border-[#D5CFC5] font-medium"
                        >
                          {m}
                        </span>
                      ))}
                    </div>
                  </div>
                )}

                {/* Card Action Link */}
                <div className="pt-2">
                  <Link
                    to={journey.href || `/work/${journey.slug}`}
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
