import React from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight } from 'lucide-react';
import marbleTexture from '../assets/textures/marble-calacatta.webp';

const PRESS_MENTIONS = [
  { outlet: 'Architectural Digest', quote: 'A masterclass in quiet luxury and architectural restraint.' },
  { outlet: 'Wallpaper* Magazine', quote: 'The digital twin methodology that is redefining turnkey interior architecture.' },
  { outlet: 'Elle Décor International', quote: 'Sanctuaries carved from stone, linen, and daylight.' },
  { outlet: 'Dezeen Awards', quote: 'Best Residential Transformation — The Tribeca Loft.' }
];

export default function About() {
  return (
    <div className="pt-28 sm:pt-36 pb-20 sm:pb-32 space-y-16 sm:space-y-24 md:space-y-36 bg-transparent min-h-screen text-[#1C1B19] relative">
      {/* 1. Header Section */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="max-w-3xl space-y-3 sm:space-y-4">
          <div className="pill-tag bg-white shadow-sm">
            <span>Studio Heritage & Philosophy</span>
          </div>

          <h1 className="font-serif text-3xl xs:text-4xl sm:text-6xl md:text-7xl font-normal text-[#1C1B19] leading-[1.08] break-words">
            About Atelier Vérité
          </h1>

          <p className="text-stone text-xs sm:text-base md:text-lg font-light leading-relaxed pt-1 sm:pt-2">
            Founded on the principle that the spaces we inhabit shape our internal clarity, we craft contemplative architectural environments across New York, Paris, and Kyoto.
          </p>
        </div>
      </section>

      {/* 2. Founder Portrait & Story */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 lg:gap-20 items-center">
          {/* Portrait Frame */}
          <div className="lg:col-span-5 relative">
            <div className="rounded-2xl border border-[#E5E0D8] bg-white overflow-hidden aspect-[3/4] shadow-2xl">
              <img
                src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=1200&q=85"
                alt="Elena Vance, Principal Architect & Creative Director"
                className="w-full h-full object-cover grayscale contrast-110"
              />
            </div>
            <div className="mt-4 sm:mt-0 sm:absolute sm:-bottom-6 sm:-right-6 bg-white/95 backdrop-blur-md border border-[#E2DDD5] p-4 sm:p-6 rounded-2xl shadow-xl max-w-xs relative overflow-hidden">
              <div
                className="absolute inset-0 bg-cover bg-center opacity-[0.20] mix-blend-multiply pointer-events-none"
                style={{ backgroundImage: `url(${marbleTexture})` }}
              />
              <div className="relative z-10">
                <span className="text-[10px] uppercase tracking-widest text-accent font-semibold block">
                  Principal & Founder
                </span>
                <p className="font-serif text-lg sm:text-xl text-[#1C1B19]">Elena Vance, AIA</p>
                <p className="text-[11px] text-stone font-light">Architectural Director & Scenographer</p>
              </div>
            </div>
          </div>

          {/* Narrative Story */}
          <div className="lg:col-span-7 space-y-4 sm:space-y-6 pt-2 sm:pt-0">
            <span className="text-[10px] sm:text-[11px] uppercase tracking-[0.2em] sm:tracking-[0.25em] text-accent font-semibold block">
              The Founder’s Journey
            </span>
            <h2 className="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1B19] leading-tight">
              "We do not design to impress the eye; we design to quiet the mind."
            </h2>

            <div className="text-stone text-xs sm:text-sm md:text-base leading-relaxed font-light space-y-3 sm:space-y-4">
              <p>
                Trained in architectural preservation in Milan and classical carpentry in Kyoto, Elena Vance established Atelier Vérité in 2011 to bridge the divide between timeless historical craftsmanship and cutting-edge 3D spatial simulation.
              </p>
              <p>
                Her practice rejects the ephemeral churn of design fads in favor of enduring materials that develop a rich patina over decades: honed Roman travertines, smoked French oaks, and raw Belgian linens that soften with morning sunlight.
              </p>
              <p>
                Today, Atelier Vérité operates as a tightly focused architectural collective, intentionally accepting only 8 to 10 turnkey commissions per year to maintain uncompromising on-site stewardship and material fidelity.
              </p>
            </div>

            <div className="pt-2 sm:pt-4 flex flex-wrap items-center gap-2 sm:gap-6 text-xs text-stone font-mono">
              <span className="text-accent font-medium">AD100 Designer 2024–2026</span>
              <span className="hidden sm:inline">&bull;</span>
              <span>RIBA International Fellow</span>
            </div>
          </div>
        </div>
      </section>

      {/* 3. Studio Metrics Strip (Soft Travertine Strip) */}
      <section className="py-14 sm:py-20 px-4 sm:px-6 md:px-12 bg-[#F5F3EE] border-y border-[#EAE6DF]">
        <div className="max-w-6xl mx-auto">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 lg:gap-12 text-center lg:text-left">
            <div className="space-y-1 sm:space-y-2 border-b lg:border-b-0 lg:border-r border-[#EAE6DF] pb-5 lg:pb-0 lg:pr-6">
              <span className="font-serif text-3xl sm:text-5xl md:text-6xl text-[#1C1B19] block">15+</span>
              <span className="text-[10px] sm:text-xs uppercase tracking-wider text-stone">Years of Architectural Practice</span>
            </div>
            <div className="space-y-1 sm:space-y-2 border-b lg:border-b-0 lg:border-r border-[#EAE6DF] pb-5 lg:pb-0 lg:pr-6">
              <span className="font-serif text-3xl sm:text-5xl md:text-6xl text-[#1C1B19] block">80+</span>
              <span className="text-[10px] sm:text-xs uppercase tracking-wider text-stone">Bespoke Sanctuaries Completed</span>
            </div>
            <div className="space-y-1 sm:space-y-2 border-b sm:border-b-0 lg:border-r border-[#EAE6DF] pb-5 lg:pb-0 lg:pr-6">
              <span className="font-serif text-3xl sm:text-5xl md:text-6xl text-[#1C1B19] block">3</span>
              <span className="text-[10px] sm:text-xs uppercase tracking-wider text-stone">Studios: NYC &bull; Paris &bull; Kyoto</span>
            </div>
            <div className="space-y-1 sm:space-y-2">
              <span className="font-serif text-3xl sm:text-5xl md:text-6xl text-[#1C1B19] block">12</span>
              <span className="text-[10px] sm:text-xs uppercase tracking-wider text-stone">International Architectural Honors</span>
            </div>
          </div>
        </div>
      </section>

      {/* 4. Press & Architectural Publications */}
      <section className="max-w-6xl mx-auto px-6 md:px-12 space-y-12">
        <div className="text-center space-y-3 max-w-2xl mx-auto">
          <div className="pill-tag bg-white shadow-sm">
            <span>Critical Reception</span>
          </div>

          <h2 className="font-serif text-4xl text-[#1C1B19]">
            Press & Architectural Honors
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {PRESS_MENTIONS.map((item, idx) => (
            <div
              key={idx}
              className="rounded-2xl border border-[#E5E0D8] bg-white p-8 flex flex-col justify-between space-y-6 hover:border-accent/60 transition-colors shadow-sm hover:shadow-xl"
            >
              <p className="font-serif text-lg text-[#1C1B19] italic leading-relaxed">
                "{item.quote}"
              </p>
              <div className="border-t border-[#EAE6DF] pt-4">
                <strong className="text-xs uppercase tracking-wider text-accent block font-medium">
                  {item.outlet}
                </strong>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* 5. Consultation CTA */}
      <section className="max-w-6xl mx-auto px-6 md:px-12">
        <div className="rounded-2xl border border-[#E5E0D8] bg-white p-10 md:p-16 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 shadow-xl">
          <div className="space-y-3">
            <h3 className="font-serif text-3xl md:text-4xl text-[#1C1B19]">
              Connect with our design partners.
            </h3>
            <p className="text-stone text-xs md:text-sm font-light">
              Explore your property's spatial potential with our New York or Paris team.
            </p>
          </div>
          <Link
            to="/contact"
            className="inline-flex items-center space-x-2 bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white px-8 py-4 rounded-full text-xs uppercase tracking-[0.2em] font-semibold transition-colors whitespace-nowrap shadow-md"
          >
            <span>Initiate Dialogue</span>
            <ArrowUpRight className="w-4 h-4" />
          </Link>
        </div>
      </section>
    </div>
  );
}
