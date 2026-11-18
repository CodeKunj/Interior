import React, { useState, useEffect } from 'react';
import { getProjects, getTestimonials } from '../services/api';
import HeroExpand from '../components/HeroExpand';
import AboutParallax from '../components/AboutParallax';
import FeaturesAttributeCards from '../components/FeaturesAttributeCards';
import FeaturesMediaGrid from '../components/FeaturesMediaGrid';
import FeaturesRevealCardsBento from '../components/FeaturesRevealCardsBento';
import ContactSplitForm from '../components/ContactSplitForm';
import { Quote, ChevronLeft, ChevronRight } from 'lucide-react';
import marbleTexture from '../assets/textures/marble-calacatta.webp';

export default function Home() {
  const [featuredProjects, setFeaturedProjects] = useState([]);
  const [testimonials, setTestimonials] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTestimonial, setActiveTestimonial] = useState(0);

  useEffect(() => {
    let isMounted = true;
    async function loadData() {
      try {
        const [projectsData, testimonialsData] = await Promise.all([
          getProjects({ featured: true }),
          getTestimonials()
        ]);
        if (isMounted) {
          setFeaturedProjects(projectsData);
          setTestimonials(testimonialsData);
        }
      } catch (err) {
        console.error('Error loading home page data:', err);
      } finally {
        if (isMounted) setLoading(false);
      }
    }
    loadData();

    return () => {
      isMounted = false;
    };
  }, []);

  const nextTestimonial = () => {
    if (testimonials.length === 0) return;
    setActiveTestimonial((prev) => (prev + 1) % testimonials.length);
  };

  const prevTestimonial = () => {
    if (testimonials.length === 0) return;
    setActiveTestimonial((prev) => (prev - 1 + testimonials.length) % testimonials.length);
  };

  const currentTestimonial = testimonials[activeTestimonial];

  return (
    <div className="text-[#1C1B19] space-y-0 relative">
      {/* 1. Hero Expand Section */}
      <div id="hero">
        <HeroExpand
          title="RiTiVerse"
          subtitle="An upscale interior architecture studio sculpted in marble white, daylight, and quiet luxury."
          imageSrc="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=2400&q=90"
          videoSrc="https://storage.googleapis.com/webild/default/templates/hotel/hero.mp4"
          primaryButton={{ text: 'Browse Sanctuaries', href: '/work' }}
          secondaryButton={{ text: 'Watch 3D Tour', href: '/work/the-tribeca-loft' }}
        />
      </div>

      {/* 2. Parallax About Section */}
      <div id="about">
        <AboutParallax
          tag="About Atelier"
          title="A Sanctuary for Wellness & Spatial Rejuvenation"
          description="From private meditation courtyards to turnkey coastal estates, our sanctuaries are designed to enhance your well-being and foster a sense of architectural harmony."
          frontImageSrc="https://storage.googleapis.com/webild/default/templates/hotel/about/magnific_recreate-this-in-higher-q_te7wx9evnr.webp"
          backImageSrc="https://storage.googleapis.com/webild/default/templates/hotel/amenities/pool.jpg"
          badge="Est. 2011"
        />
      </div>

      {/* 3. Rooms & Suites Attribute Cards */}
      <div id="rooms">
        <FeaturesAttributeCards
          tag="Rooms & Suites"
          title="Featured Sanctuaries"
          description="Each room is a sanctuary of refined comfort, designed with meticulous attention to detail and honest materiality."
          projects={featuredProjects}
        />
      </div>

      {/* 4. Asymmetric Media Grid (Studio Disciplines) */}
      <div id="disciplines">
        <FeaturesMediaGrid
          tag="Studio Disciplines"
          title="Curated Architectural Disciplines"
          description="A multi-disciplinary approach ensuring absolute fidelity from structural masonry to bespoke joinery."
        />
      </div>

      {/* 5. Transformations Bento Reveal Cards */}
      <div id="journeys">
        <FeaturesRevealCardsBento
          tag="Chronicles & Journeys"
          title="Architectural Transformations"
          description="Hover across our documented case studies to see structural interventions transformed into serene private sanctuaries."
        />
      </div>

      {/* 6. Client Testimonials Section */}
      {currentTestimonial && (
        <section id="testimonials" className="py-16 sm:py-24 md:py-36 px-4 sm:px-6 md:px-12 bg-transparent relative">
          <div className="max-w-4xl mx-auto rounded-2xl border border-[#E5E0D8]/90 bg-white/95 backdrop-blur-md p-6 sm:p-10 md:p-16 relative shadow-xl overflow-hidden">
            {/* Calacatta Marble Plaque Texture */}
            <div
              className="absolute inset-0 bg-cover bg-center opacity-[0.28] mix-blend-multiply pointer-events-none"
              style={{ backgroundImage: `url(${marbleTexture})` }}
            />
            <Quote className="w-8 h-8 sm:w-12 sm:h-12 text-accent/20 absolute top-5 left-5 sm:top-8 sm:left-8 z-10" />

            <div className="relative z-10 space-y-4 sm:space-y-6">
              <div className="pill-tag bg-[#FAF9F5] shadow-sm">
                <span>Guest & Client Perspectives</span>
              </div>

              <blockquote className="font-serif text-xl sm:text-3xl md:text-4xl text-[#1C1B19] leading-snug font-normal">
                "{currentTestimonial.quote}"
              </blockquote>

              <div className="flex flex-col sm:flex-row sm:items-center justify-between pt-5 sm:pt-6 border-t border-[#EAE6DF] gap-4">
                <div>
                  <strong className="text-[#1C1B19] font-medium text-xs sm:text-sm block">
                    {currentTestimonial.author}
                  </strong>
                  <span className="text-[11px] sm:text-xs text-[#5C5852] font-medium">
                    {currentTestimonial.role} &bull; {currentTestimonial.project}
                  </span>
                </div>

                {/* Slider Controls */}
                {testimonials.length > 1 && (
                  <div className="flex items-center space-x-3 self-end sm:self-auto">
                    <button
                      onClick={prevTestimonial}
                      aria-label="Previous testimonial"
                      className="p-2 sm:p-2.5 rounded-full border border-[#E2DDD5] text-[#1C1B19] hover:bg-[#FAF9F5] transition-colors"
                    >
                      <ChevronLeft className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                    </button>
                    <span className="text-xs text-[#5C5852] font-mono font-medium">
                      0{activeTestimonial + 1} / 0{testimonials.length}
                    </span>
                    <button
                      onClick={nextTestimonial}
                      aria-label="Next testimonial"
                      className="p-2 sm:p-2.5 rounded-full border border-[#E2DDD5] text-[#1C1B19] hover:bg-[#FAF9F5] transition-colors"
                    >
                      <ChevronRight className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                    </button>
                  </div>
                )}
              </div>
            </div>
          </div>
        </section>
      )}

      {/* 7. Split Contact & Consultation Form */}
      <div id="contact">
        <ContactSplitForm
          tag="Private Commissions"
          title="Book Your Design Consultation"
          description="Let us help you sculpt the perfect private sanctuary. Our senior partners are available to assist with design reviews and turnkey commissions worldwide."
          imageSrc="https://storage.googleapis.com/webild/default/templates/hotel/contact/bookyourstay.webp"
        />
      </div>
    </div>
  );
}
