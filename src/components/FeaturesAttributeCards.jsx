import React from 'react';
import { Link } from 'react-router-dom';
import { Bed, Bath, Maximize2, Compass, ArrowUpRight } from 'lucide-react';

export default function FeaturesAttributeCards({
  tag = 'Selected Works',
  title = 'Featured Sanctuaries',
  description = 'Each space is a sanctuary of refined comfort, designed with meticulous attention to architectural proportion and material tactile quality.',
  projects = []
}) {
  return (
    <section id="rooms" className="py-16 sm:py-24 md:py-36 px-4 sm:px-6 md:px-12 bg-[#F5F3EE]/80 border-y border-[#EAE6DF]/80 relative">
      <div className="max-w-6xl mx-auto space-y-12 sm:space-y-16">
        {/* Section Header */}
        <div className="flex flex-col items-center text-center space-y-3 sm:space-y-4 max-w-3xl mx-auto">
          <div className="pill-tag bg-white shadow-sm">
            <span>{tag}</span>
          </div>

          <h2 className="font-serif text-3xl sm:text-5xl md:text-6xl font-normal text-[#1C1B19] leading-tight">
            {title}
          </h2>

          <p className="text-[#3D3A35] text-xs sm:text-base font-normal max-w-2xl leading-relaxed">
            {description}
          </p>
        </div>

        {/* 3-Column Attribute Card Grid (Crisp White on Soft Travertine) */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          {projects.length === 0 ? (
            [1, 2, 3].map((n) => (
              <div
                key={n}
                className="rounded-2xl border border-[#E5E0D8] bg-white overflow-hidden shadow-sm animate-pulse min-h-[380px] flex flex-col justify-between"
              >
                <div className="aspect-[4/3] bg-[#EAE6DF]" />
                <div className="p-6 space-y-4">
                  <div className="h-6 bg-[#EAE6DF] rounded w-3/4" />
                  <div className="h-4 bg-[#EAE6DF] rounded w-1/2" />
                  <div className="pt-4 border-t border-[#EAE6DF] h-4 bg-[#EAE6DF] rounded w-full" />
                </div>
              </div>
            ))
          ) : (
            projects.map((project) => (
            <article
              key={project.id || project.slug}
              className="group flex flex-col justify-between rounded-2xl border border-[#E5E0D8] bg-white hover:border-accent/50 transition-all duration-500 overflow-hidden shadow-[0_8px_25px_rgba(28,27,25,0.05)] hover:shadow-2xl"
            >
              {/* Image Frame */}
              <div className="relative aspect-[4/3] overflow-hidden bg-[#FAF9F5]">
                <Link to={`/work/${project.slug}`} tabIndex={-1}>
                  <img
                    src={project.cover_image || project.imageSrc}
                    alt={project.title}
                    loading="lazy"
                    className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-60" />
                </Link>

                {/* Status Badges */}
                <div className="absolute top-4 left-4 flex flex-col gap-1.5 items-start">
                  {project.badge && (
                    <span className="bg-accent text-white text-[10px] font-semibold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
                      {project.badge}
                    </span>
                  )}
                  {project.has_3d_tour && (
                    <span className="bg-white/95 backdrop-blur-md text-[#1C1B19] text-[10px] uppercase tracking-wider px-2.5 py-0.5 rounded-full border border-[#E2DDD5] flex items-center space-x-1.5 shadow-sm font-semibold">
                      <Compass className="w-3 h-3 text-accent" />
                      <span>3D Tour</span>
                    </span>
                  )}
                </div>

                {/* Category / Location Pill */}
                <div className="absolute bottom-3 left-4 bg-white/95 backdrop-blur-md text-[#1C1B19] text-[10px] uppercase tracking-wider px-3 py-0.5 rounded-full border border-white/60 shadow-sm font-semibold">
                  {project.category || project.location}
                </div>
              </div>

              {/* Card Body */}
              <div className="p-6 space-y-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 className="font-serif text-2xl text-[#1C1B19] group-hover:text-accent transition-colors">
                    <Link to={`/work/${project.slug}`}>{project.title}</Link>
                  </h3>

                  <p className="text-[#5C5852] text-xs tracking-wider uppercase mt-1 font-semibold">
                    {project.subtitle || project.tags || project.location}
                  </p>
                </div>

                {/* Specs Row with Icons */}
                <div className="pt-4 border-t border-[#EAE6DF] flex flex-wrap items-center justify-between text-xs text-[#5C5852] font-medium gap-2">
                  <div className="flex items-center space-x-3 sm:space-x-4">
                    <span className="flex items-center space-x-1 sm:space-x-1.5 text-[11px] sm:text-xs">
                      <Bed className="w-3.5 h-3.5 text-accent" />
                      <span>{project.category === 'Commercial' ? 'Salon' : 'Suite'}</span>
                    </span>
                    <span className="flex items-center space-x-1 sm:space-x-1.5 text-[11px] sm:text-xs">
                      <Bath className="w-3.5 h-3.5 text-accent" />
                      <span>Ensuite</span>
                    </span>
                    <span className="flex items-center space-x-1 sm:space-x-1.5 text-[11px] sm:text-xs">
                      <Maximize2 className="w-3.5 h-3.5 text-accent" />
                      <span>{project.area_sqft ? `${project.area_sqft} sq ft` : 'Spacious'}</span>
                    </span>
                  </div>

                  <Link
                    to={`/work/${project.slug}`}
                    className="p-2 rounded-full bg-[#FAF9F5] border border-[#E2DDD5] group-hover:bg-accent group-hover:text-white group-hover:border-accent text-[#1C1B19] transition-all duration-300 shadow-sm ml-auto"
                    aria-label={`View ${project.title}`}
                  >
                    <ArrowUpRight className="w-4 h-4" />
                  </Link>
                </div>
              </div>
            </article>
          )))}
        </div>

        {/* View All Works CTA */}
        <div className="text-center pt-2 sm:pt-4">
          <Link
            to="/work"
            className="inline-flex items-center justify-center space-x-2 text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-semibold text-[#1C1B19] hover:text-white bg-white hover:bg-accent border border-[#E2DDD5] hover:border-accent px-6 sm:px-8 py-3.5 rounded-full transition-all shadow-sm w-full sm:w-auto"
          >
            <span>Explore Complete Portfolio (6 Sanctuaries)</span>
            <ArrowUpRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </section>
  );
}
