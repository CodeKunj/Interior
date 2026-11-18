import React, { useState, useEffect } from 'react';
import { getProjects } from '../services/api';
import HeroExpand from '../components/HeroExpand';
import AboutParallax from '../components/AboutParallax';
import FeaturesAttributeCards from '../components/FeaturesAttributeCards';
import FeaturesMediaGrid from '../components/FeaturesMediaGrid';
import FeaturesRevealCardsBento from '../components/FeaturesRevealCardsBento';
import ContactSplitForm from '../components/ContactSplitForm';
import { Quote, ChevronLeft, ChevronRight } from 'lucide-react';
import { MOCK_TESTIMONIALS } from '../services/mockData';
import marbleTexture from '../assets/textures/marble-calacatta.webp';

export default function Home() {
  const [featuredProjects, setFeaturedProjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTestimonial, setActiveTestimonial] = useState(0);

  useEffect(() => {
    async function loadData() {
      try {
        const data = await getProjects({ featured: true });
        setFeaturedProjects(data);
      } catch (err) {
        console.error('Error loading featured projects:', err);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  const nextTestimonial = () => {
    setActiveTestimonial((prev) => (prev + 1) % MOCK_TESTIMONIALS.length);
  };

  const prevTestimonial = () => {
    setActiveTestimonial((prev) => (prev - 1 + MOCK_TESTIMONIALS.length) % MOCK_TESTIMONIALS.length);
  };

  return (
    <div className="text-[#1C1B19] space-y-0 relative">
      {/* 1. Hero Expand Section (Video Expand Signature) */}
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
          projects={featuredProjects.length > 0 ? featuredProjects : [
            {
              id: 1,
              slug: 'the-tribeca-loft',
              title: 'The Grand Suite (Tribeca Loft)',
              subtitle: 'Panoramic, Terrace, Italian Travertine',
              location: 'Tribeca, New York',
              badge: 'Guest Favourite',
              area_sqft: 1450,
              has_3d_tour: true,
              cover_image: 'https://storage.googleapis.com/webild/default/templates/hotel/rooms/grandsuite.webp'
            },
            {
              id: 2,
              slug: 'villa-serenita-amalfi',
              title: 'Ocean Pavilion (Villa Serenità)',
              subtitle: 'Oceanfront, Serene, Honed Limestone',
              location: 'Amalfi Coast, Italy',
              badge: null,
              area_sqft: 980,
              has_3d_tour: true,
              cover_image: 'https://storage.googleapis.com/webild/default/templates/hotel/rooms/oceanpavillion.webp'
            },
            {
              id: 3,
              slug: 'kyoto-sanctuary-residence',
              title: 'Garden Villa (Kyoto Sanctuary)',
              subtitle: 'Garden, Pool, Hinoki Woodcraft',
              location: 'Higashiyama, Kyoto',
              badge: 'Most Popular',
              area_sqft: 2200,
              has_3d_tour: true,
              cover_image: 'https://storage.googleapis.com/webild/default/templates/hotel/rooms/gardenvilla.webp'
            }
          ]}
        />
      </div>

      {/* 4. Disciplines & Amenities Media Grid */}
      <div id="experience">
        <FeaturesMediaGrid
          tag="Amenities & Practice"
          title="World-Class Spatial Experiences"
          description="Every detail has been considered to create moments of extraordinary comfort, acoustic balance, and tactile indulgence."
        />
      </div>

      {/* 5. The Architectural Journeys (Bento Cards) */}
      <div id="journeys">
        <FeaturesRevealCardsBento
          tag="Curated Journeys"
          title="The Journeys"
          description="Exclusive architectural journeys and transformations curated for RiTiVerse patrons — across New York, Amalfi, Kyoto, London, and Paris."
        />
      </div>

      {/* 6. Client Testimonial Editorial Strip (Soft Travertine / Pure White) */}
      <section className="py-16 sm:py-24 px-4 sm:px-6 md:px-12 bg-transparent">
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
              "{MOCK_TESTIMONIALS[activeTestimonial].quote}"
            </blockquote>

            <div className="flex flex-col sm:flex-row sm:items-center justify-between pt-5 sm:pt-6 border-t border-[#EAE6DF] gap-4">
              <div>
                <strong className="text-[#1C1B19] font-medium text-xs sm:text-sm block">
                  {MOCK_TESTIMONIALS[activeTestimonial].author}
                </strong>
                <span className="text-[11px] sm:text-xs text-stone">
                  {MOCK_TESTIMONIALS[activeTestimonial].role} &bull; {MOCK_TESTIMONIALS[activeTestimonial].project}
                </span>
              </div>

              {/* Slider Controls */}
              <div className="flex items-center space-x-3 self-end sm:self-auto">
                <button
                  onClick={prevTestimonial}
                  aria-label="Previous testimonial"
                  className="p-2 sm:p-2.5 rounded-full border border-[#E2DDD5] text-[#1C1B19] hover:bg-[#FAF9F5] transition-colors"
                >
                  <ChevronLeft className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
                <span className="text-xs text-stone font-mono">
                  0{activeTestimonial + 1} / 0{MOCK_TESTIMONIALS.length}
                </span>
                <button
                  onClick={nextTestimonial}
                  aria-label="Next testimonial"
                  className="p-2 sm:p-2.5 rounded-full border border-[#E2DDD5] text-[#1C1B19] hover:bg-[#FAF9F5] transition-colors"
                >
                  <ChevronRight className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

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
