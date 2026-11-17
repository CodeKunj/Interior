import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ArrowLeft, ArrowRight, Camera, SplitSquareVertical, Compass, Calendar, MapPin, Maximize, Sparkles } from 'lucide-react';
import { getProjectBySlug } from '../services/api';
import ProjectGallery from '../components/ProjectGallery';
import BeforeAfterSlider from '../components/BeforeAfterSlider';
import VirtualTourEmbed from '../components/VirtualTourEmbed';

export default function ProjectDetail() {
  const { slug } = useParams();
  const [project, setProject] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('photography');

  useEffect(() => {
    async function loadDetail() {
      setLoading(true);
      try {
        const data = await getProjectBySlug(slug);
        setProject(data);
        setActiveTab('photography');
      } catch (err) {
        console.error('Error fetching project detail:', err);
      } finally {
        setLoading(false);
      }
    }
    loadDetail();
  }, [slug]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center pt-32 pb-24 text-stone text-xs tracking-widest uppercase bg-transparent relative">
        <div className="text-center space-y-4">
          <div className="w-8 h-8 border-2 border-accent border-t-transparent rounded-full animate-spin mx-auto"></div>
          <p>Retrieving Case Study & Architectural Specifications...</p>
        </div>
      </div>
    );
  }

  if (!project) {
    return (
      <div className="min-h-screen max-w-4xl mx-auto px-6 pt-40 pb-24 text-center space-y-6 bg-transparent text-[#1C1B19] relative">
        <h1 className="font-serif text-4xl text-[#1C1B19]">Project Not Found</h1>
        <p className="text-stone text-sm">The requested case study could not be located in our archive.</p>
        <Link
          to="/work"
          className="inline-flex items-center space-x-2 bg-[#1C1B19] text-[#FAF9F5] px-6 py-3 rounded-full text-xs uppercase tracking-widest hover:bg-accent transition-colors"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Return to Portfolio</span>
        </Link>
      </div>
    );
  }

  const hasBeforeAfter = project.before_after && project.before_after.length > 0;
  const hasTour3D = Boolean(project.tour_3d_url);

  return (
    <div className="pt-28 sm:pt-36 pb-20 sm:pb-32 space-y-12 sm:space-y-20 md:space-y-28 bg-transparent min-h-screen text-[#1C1B19] relative">
      {/* 1. Project Header & Breadcrumb */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12 space-y-6 sm:space-y-8">
        <div className="flex items-center justify-between border-b border-[#EAE6DF] pb-4">
          <Link
            to="/work"
            className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] text-stone hover:text-[#1C1B19] transition-colors font-medium"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Back to Portfolio</span>
          </Link>
          <div className="pill-tag bg-white shadow-sm text-[10px] sm:text-xs">
            <Sparkles className="w-3 h-3 text-accent" />
            <span>{project.category}</span>
          </div>
        </div>

        <div className="max-w-4xl space-y-3 sm:space-y-4">
          <h1 className="font-serif text-3xl xs:text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-normal text-[#1C1B19] leading-[1.08] break-words">
            {project.title}
          </h1>
          {project.subtitle && (
            <p className="text-stone text-sm sm:text-base md:text-xl font-light leading-relaxed">
              {project.subtitle}
            </p>
          )}
        </div>

        {/* Project Key Metadata Grid */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 py-4 sm:py-6 border-y border-[#EAE6DF] text-xs">
          <div>
            <span className="text-[10px] uppercase tracking-wider text-stone flex items-center space-x-1.5 mb-1 font-medium">
              <MapPin className="w-3.5 h-3.5 text-accent" />
              <span>Location</span>
            </span>
            <strong className="text-[#1C1B19] block font-medium">{project.location}</strong>
          </div>
          <div>
            <span className="text-[10px] uppercase tracking-wider text-stone flex items-center space-x-1.5 mb-1 font-medium">
              <Maximize className="w-3.5 h-3.5 text-accent" />
              <span>Carpet Area</span>
            </span>
            <strong className="text-[#1C1B19] block font-medium">{project.area_sqft_formatted || `${project.area_sqft} sq. ft.`}</strong>
          </div>
          <div>
            <span className="text-[10px] uppercase tracking-wider text-stone flex items-center space-x-1.5 mb-1 font-medium">
              <Calendar className="w-3.5 h-3.5 text-accent" />
              <span>Completion</span>
            </span>
            <strong className="text-[#1C1B19] block font-medium">{project.year}</strong>
          </div>
          <div>
            <span className="text-[10px] uppercase tracking-wider text-stone flex items-center space-x-1.5 mb-1 font-medium">
              <Compass className="w-3.5 h-3.5 text-accent" />
              <span>3D Virtual Twin</span>
            </span>
            <strong className="text-[#1C1B19] block font-medium">
              {hasTour3D ? `${project.tour_type?.toUpperCase() || '3D'} Embedded` : 'Archived Model'}
            </strong>
          </div>
        </div>
      </section>

      {/* 2. Full-Bleed Architectural Hero Frame */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="relative aspect-[16/9] overflow-hidden rounded-2xl border border-[#E5E0D8] bg-[#FAF9F5] shadow-2xl">
          <img
            src={project.hero_image || project.cover_image}
            alt={project.title}
            className="w-full h-full object-cover"
          />
        </div>
      </section>

      {/* 3. Client Brief & Design Solution Split Block */}
      <section className="max-w-6xl mx-auto px-6 md:px-12">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
          <div className="lg:col-span-5 space-y-4">
            <span className="text-[11px] uppercase tracking-[0.25em] text-accent font-semibold block">
              The Client Brief
            </span>
            <h2 className="font-serif text-3xl sm:text-4xl text-[#1C1B19]">
              Spatial Ambition & Aspirations
            </h2>
            <div className="text-stone text-sm leading-relaxed font-light space-y-4 pt-2">
              <p>{project.client_brief}</p>
            </div>
          </div>

          <div className="lg:col-span-7 space-y-4 lg:border-l lg:border-[#EAE6DF] lg:pl-16">
            <span className="text-[11px] uppercase tracking-[0.25em] text-accent font-semibold block">
              The Design Solution
            </span>
            <h2 className="font-serif text-3xl sm:text-4xl text-[#1C1B19]">
              Materiality & Execution
            </h2>
            <div className="text-stone text-sm leading-relaxed font-light space-y-4 pt-2">
              <p>{project.design_solution}</p>
            </div>
          </div>
        </div>
      </section>

      {/* 4. GALLERY SECTION VIEW SWITCHER (CRITICAL 3D GALLERY FEATURE) */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="rounded-2xl border border-[#E5E0D8] bg-white p-5 sm:p-8 md:p-10 shadow-xl space-y-6 sm:space-y-8">
          {/* Header & View Switcher Tabs */}
          <div className="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-[#EAE6DF] gap-4 sm:gap-6">
            <div>
              <span className="text-[10px] sm:text-[11px] uppercase tracking-[0.24em] sm:tracking-[0.28em] text-accent font-semibold block mb-1">
                Visual Documentation
              </span>
              <h3 className="font-serif text-2xl sm:text-3xl text-[#1C1B19]">
                Interactive Case Study Exploration
              </h3>
            </div>

            {/* Editorial Tab Switcher Buttons */}
            <div className="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none sm:flex-wrap">
              {/* Tab 1: Photography */}
              <button
                onClick={() => setActiveTab('photography')}
                className={`inline-flex items-center space-x-1.5 sm:space-x-2 text-[11px] sm:text-xs uppercase tracking-[0.12em] sm:tracking-[0.16em] px-4 sm:px-5 py-2 sm:py-2.5 rounded-full border transition-all duration-300 whitespace-nowrap flex-shrink-0 ${
                  activeTab === 'photography'
                    ? 'bg-[#1C1B19] text-[#FAF9F5] border-[#1C1B19] font-semibold shadow-md'
                    : 'bg-[#FAF9F5] text-stone border-[#E2DDD5] hover:text-[#1C1B19] hover:border-accent'
                }`}
              >
                <Camera className="w-3.5 h-3.5 text-accent" />
                <span>
                  <span className="inline sm:hidden">Photos ({project.gallery?.length || 0})</span>
                  <span className="hidden sm:inline">Curated Photography ({project.gallery?.length || 0})</span>
                </span>
              </button>

              {/* Tab 2: Before & After (if available) */}
              {hasBeforeAfter && (
                <button
                  onClick={() => setActiveTab('before-after')}
                  className={`inline-flex items-center space-x-1.5 sm:space-x-2 text-[11px] sm:text-xs uppercase tracking-[0.12em] sm:tracking-[0.16em] px-4 sm:px-5 py-2 sm:py-2.5 rounded-full border transition-all duration-300 whitespace-nowrap flex-shrink-0 ${
                    activeTab === 'before-after'
                      ? 'bg-[#1C1B19] text-[#FAF9F5] border-[#1C1B19] font-semibold shadow-md'
                      : 'bg-[#FAF9F5] text-stone border-[#E2DDD5] hover:text-[#1C1B19] hover:border-accent'
                  }`}
                >
                  <SplitSquareVertical className="w-3.5 h-3.5 text-accent" />
                  <span>
                    <span className="inline sm:hidden">Before/After ({project.before_after.length})</span>
                    <span className="hidden sm:inline">Before & After ({project.before_after.length})</span>
                  </span>
                </button>
              )}

              {/* Tab 3: Interactive 3D Virtual Tour */}
              {hasTour3D && (
                <button
                  onClick={() => setActiveTab('tour-3d')}
                  className={`inline-flex items-center space-x-1.5 sm:space-x-2 text-[11px] sm:text-xs uppercase tracking-[0.12em] sm:tracking-[0.16em] px-4 sm:px-5 py-2 sm:py-2.5 rounded-full border transition-all duration-300 relative whitespace-nowrap flex-shrink-0 ${
                    activeTab === 'tour-3d'
                      ? 'bg-[#1C1B19] text-[#FAF9F5] border-[#1C1B19] font-semibold shadow-md'
                      : 'bg-[#FAF9F5] text-stone border-[#E2DDD5] hover:text-[#1C1B19] hover:border-accent'
                  }`}
                >
                  <Compass className="w-3.5 h-3.5 text-accent animate-pulse" />
                  <span>
                    <span className="inline sm:hidden">3D Tour</span>
                    <span className="hidden sm:inline">Interactive 3D Virtual Tour</span>
                  </span>
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-600 absolute -top-1 -right-1" />
                </button>
              )}
            </div>
          </div>

          {/* TAB 1 CONTENT: Curated Photography Grid with Lightbox */}
          {activeTab === 'photography' && (
            <div className="space-y-4">
              <p className="text-xs text-stone font-light">
                Click any photograph to launch the high-resolution architectural lightbox.
              </p>
              <ProjectGallery images={project.gallery} title={project.title} />
            </div>
          )}

          {/* TAB 2 CONTENT: Interactive Before & After Comparison Slider */}
          {activeTab === 'before-after' && (
            <div className="space-y-8">
              <p className="text-xs text-stone font-light">
                Drag the slider horizontally to inspect the architectural transformation from original condition to final Atelier finish.
              </p>
              {project.before_after.map((ba) => (
                <BeforeAfterSlider
                  key={ba.id}
                  title={ba.title}
                  description={ba.description}
                  beforeImage={ba.before_image}
                  afterImage={ba.after_image}
                  beforeLabel="Prior Condition"
                  afterLabel="Atelier Handover"
                />
              ))}
            </div>
          )}

          {/* TAB 3 CONTENT: Interactive 3D Virtual Tour */}
          {activeTab === 'tour-3d' && (
            <div className="space-y-4">
              <VirtualTourEmbed
                tourUrl={project.tour_3d_url}
                tourType={project.tour_type}
                projectTitle={project.title}
              />
            </div>
          )}
        </div>
      </section>

      {/* 5. Navigation to Next & Previous Projects */}
      <section className="max-w-6xl mx-auto px-6 md:px-12 border-t border-[#EAE6DF] pt-12">
        <div className="flex flex-col sm:flex-row items-center justify-between gap-6">
          {project.navigation?.previous ? (
            <Link
              to={`/work/${project.navigation.previous.slug}`}
              className="inline-flex items-center space-x-3 text-stone hover:text-[#1C1B19] transition-colors group"
            >
              <ArrowLeft className="w-4 h-4 transition-transform group-hover:-translate-x-1" />
              <div className="text-left">
                <span className="text-[10px] uppercase tracking-wider text-stone block">Previous Case Study</span>
                <span className="font-serif text-lg text-[#1C1B19] group-hover:text-accent transition-colors">
                  {project.navigation.previous.title}
                </span>
              </div>
            </Link>
          ) : (
            <div />
          )}

          <Link
            to="/work"
            className="text-xs uppercase tracking-[0.2em] font-medium text-stone hover:text-[#1C1B19] underline underline-offset-4"
          >
            All Projects
          </Link>

          {project.navigation?.next ? (
            <Link
              to={`/work/${project.navigation.next.slug}`}
              className="inline-flex items-center space-x-3 text-stone hover:text-[#1C1B19] transition-colors group text-right"
            >
              <div className="text-right">
                <span className="text-[10px] uppercase tracking-wider text-stone block">Next Case Study</span>
                <span className="font-serif text-lg text-[#1C1B19] group-hover:text-accent transition-colors">
                  {project.navigation.next.title}
                </span>
              </div>
              <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-1" />
            </Link>
          ) : (
            <div />
          )}
        </div>
      </section>
    </div>
  );
}
