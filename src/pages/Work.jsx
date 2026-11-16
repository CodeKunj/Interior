import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Compass, ArrowUpRight, Filter, Sparkles, Bed, Bath, Maximize2 } from 'lucide-react';
import { getProjects } from '../services/api';

const CATEGORIES = ['All', 'Residential', 'Commercial', 'Turnkey', 'Renovation'];

export default function Work() {
  const [selectedCategory, setSelectedCategory] = useState('All');
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadPortfolio() {
      setLoading(true);
      try {
        const data = await getProjects({ category: selectedCategory });
        setProjects(data);
      } catch (err) {
        console.error('Failed to load portfolio:', err);
      } finally {
        setLoading(false);
      }
    }
    loadPortfolio();
  }, [selectedCategory]);

  return (
    <div className="pt-28 sm:pt-36 pb-20 sm:pb-32 space-y-12 sm:space-y-16 bg-[#FAF9F5] min-h-screen text-[#1C1B19]">
      {/* Portfolio Header */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="max-w-3xl space-y-3 sm:space-y-4">
          <div className="pill-tag bg-white shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-accent" />
            <span>Curated Sanctuaries & Archives</span>
          </div>

          <h1 className="font-serif text-3xl xs:text-4xl sm:text-6xl md:text-7xl font-normal text-[#1C1B19] leading-tight">
            Selected Works
          </h1>

          <p className="text-stone text-xs sm:text-base md:text-lg font-light leading-relaxed pt-1 sm:pt-2">
            A survey of residential estates, historic restorations, and experiential commercial salons. Every case study includes full architectural specifications and interactive 3D digital twins.
          </p>
        </div>

        {/* Category Filter Bar */}
        <div className="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-[#EAE6DF] flex items-center gap-2 sm:gap-3 overflow-x-auto pb-2 scrollbar-none sm:flex-wrap">
          <span className="text-[11px] sm:text-xs uppercase tracking-wider text-stone flex items-center space-x-1.5 mr-2 whitespace-nowrap flex-shrink-0">
            <Filter className="w-3.5 h-3.5 text-accent" />
            <span>Category:</span>
          </span>

          {CATEGORIES.map((cat) => {
            const isActive = selectedCategory === cat;
            return (
              <button
                key={cat}
                onClick={() => setSelectedCategory(cat)}
                className={`text-[11px] sm:text-xs uppercase tracking-[0.14em] sm:tracking-[0.16em] px-4 sm:px-5 py-2 sm:py-2.5 rounded-full border transition-all duration-300 font-medium whitespace-nowrap flex-shrink-0 ${
                  isActive
                    ? 'bg-[#1C1B19] text-[#FAF9F5] border-[#1C1B19] shadow-md'
                    : 'bg-white text-stone border-[#E2DDD5] hover:border-accent hover:text-[#1C1B19]'
                }`}
              >
                {cat}
              </button>
            );
          })}
        </div>
      </section>

      {/* Editorial Projects Grid */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        {loading ? (
          <div className="py-28 text-center text-stone text-xs tracking-widest uppercase">
            Loading architectural portfolio...
          </div>
        ) : projects.length === 0 ? (
          <div className="py-24 text-center text-stone border border-[#E5E0D8] bg-white rounded-2xl p-8 shadow-sm">
            <p className="font-serif text-2xl text-[#1C1B19] mb-2">No projects found in this category.</p>
            <p className="text-xs text-stone">Please select "All" to review our complete architectural repertoire.</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {projects.map((project) => (
              <article
                key={project.id || project.slug}
                className="group flex flex-col justify-between rounded-2xl border border-[#E5E0D8] bg-white hover:border-accent/50 transition-all duration-500 overflow-hidden shadow-[0_8px_25px_rgba(28,27,25,0.05)] hover:shadow-2xl"
              >
                {/* Cover Photography Frame */}
                <div className="relative aspect-[4/3] overflow-hidden bg-[#FAF9F5]">
                  <Link to={`/work/${project.slug}`} tabIndex={-1}>
                    <img
                      src={project.cover_image}
                      alt={project.title}
                      loading="lazy"
                      className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-60" />
                  </Link>

                  {/* 3D Virtual Tour Badge */}
                  {project.has_3d_tour && (
                    <div className="absolute top-4 left-4 bg-white/95 backdrop-blur-md text-[#1C1B19] text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full border border-[#E2DDD5] flex items-center space-x-1.5 shadow-sm font-medium">
                      <Compass className="w-3.5 h-3.5 text-accent" />
                      <span>3D Tour</span>
                    </div>
                  )}

                  {/* Category Pill */}
                  <div className="absolute bottom-3 left-4 bg-white/90 backdrop-blur-md text-[#1C1B19] text-[10px] uppercase tracking-wider px-2.5 py-0.5 rounded-full border border-white/40 shadow-sm font-medium">
                    {project.category}
                  </div>
                </div>

                {/* Card Editorial Info */}
                <div className="p-6 space-y-4 flex-1 flex flex-col justify-between">
                  <div>
                    <span className="text-[11px] uppercase tracking-wider text-stone block mb-1">
                      {project.location}
                    </span>
                    <h2 className="font-serif text-2xl text-[#1C1B19] group-hover:text-accent transition-colors">
                      <Link to={`/work/${project.slug}`}>{project.title}</Link>
                    </h2>
                    {project.subtitle && (
                      <p className="text-stone text-xs mt-2 font-light line-clamp-2 leading-relaxed">
                        {project.subtitle}
                      </p>
                    )}
                  </div>

                  {/* Footnote Specs */}
                  <div className="pt-4 border-t border-[#EAE6DF] flex items-center justify-between text-xs text-stone">
                    <div className="flex items-center space-x-3">
                      <span className="flex items-center space-x-1">
                        <Bed className="w-3 h-3 text-accent" />
                        <span>Suite</span>
                      </span>
                      <span className="flex items-center space-x-1">
                        <Maximize2 className="w-3 h-3 text-accent" />
                        <span>{project.area_sqft_formatted || `${project.area_sqft} sq. ft.`}</span>
                      </span>
                    </div>

                    <Link
                      to={`/work/${project.slug}`}
                      className="inline-flex items-center space-x-1 text-[#1C1B19] group-hover:text-accent uppercase tracking-wider text-[11px] font-semibold"
                    >
                      <span>Explore</span>
                      <ArrowUpRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              </article>
            ))}
          </div>
        )}
      </section>

      {/* Consultation Banner */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12 pt-4 sm:pt-8">
        <div className="rounded-2xl border border-[#E5E0D8] bg-white p-6 sm:p-8 md:p-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-xl">
          <div className="space-y-2">
            <h3 className="font-serif text-xl sm:text-2xl md:text-3xl text-[#1C1B19]">
              Commissioning a private residence or commercial space?
            </h3>
            <p className="text-stone text-xs md:text-sm font-light">
              Schedule an exploratory consultation with our principal design director.
            </p>
          </div>
          <Link
            to="/contact"
            className="inline-flex items-center justify-center space-x-2 bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white transition-colors px-6 py-3.5 rounded-full text-xs uppercase tracking-widest font-semibold whitespace-nowrap shadow-md w-full sm:w-auto"
          >
            <span>Inquire Now</span>
            <ArrowUpRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      </section>
    </div>
  );
}
