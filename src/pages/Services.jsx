import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight, Check, Plus, Minus, Sparkles } from 'lucide-react';
import { MOCK_SERVICES, MOCK_PROCESS_STEPS, MOCK_FAQS } from '../services/mockData';

export default function Services() {
  const [openFaqIndex, setOpenFaqIndex] = useState(0);

  const toggleFaq = (index) => {
    setOpenFaqIndex(openFaqIndex === index ? -1 : index);
  };

  return (
    <div className="pt-28 sm:pt-36 pb-20 sm:pb-32 space-y-16 sm:space-y-28 md:space-y-36 bg-transparent min-h-screen text-[#1C1B19] relative">
      {/* 1. Header Section */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="max-w-3xl space-y-3 sm:space-y-4">
          <div className="pill-tag bg-white shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-accent" />
            <span>Studio Disciplines & Methodology</span>
          </div>

          <h1 className="font-serif text-3xl xs:text-4xl sm:text-6xl md:text-7xl font-normal text-[#1C1B19] leading-[1.08] break-words">
            Services & Architectural Process
          </h1>

          <p className="text-stone text-xs sm:text-base md:text-lg font-light leading-relaxed pt-1 sm:pt-2">
            We provide comprehensive architectural stewardship from initial concept modeling and 3D digital twins through to quarry-direct material sourcing and bespoke turnkey execution.
          </p>
        </div>
      </section>

      {/* 2. Detailed Service Offerings */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12 space-y-14 sm:space-y-24">
        {MOCK_SERVICES.map((service, index) => {
          const isEven = index % 2 === 0;

          return (
            <div
              key={service.id}
              className={`grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center ${
                isEven ? '' : 'lg:flex-row-reverse'
              }`}
            >
              {/* Service Imagery */}
              <div
                className={`lg:col-span-6 overflow-hidden rounded-2xl border border-[#E5E0D8] bg-[#FAF9F5] aspect-[4/3] shadow-xl ${
                  isEven ? '' : 'lg:order-2'
                }`}
              >
                <img
                  src={service.image}
                  alt={service.title}
                  className="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                />
              </div>

              {/* Service Description & Deliverables */}
              <div className={`lg:col-span-6 space-y-6 ${isEven ? '' : 'lg:order-1'}`}>
                <span className="font-mono text-xs text-accent">0{index + 1}</span>
                <div className="space-y-2">
                  <h2 className="font-serif text-3xl sm:text-4xl text-[#1C1B19]">
                    {service.title}
                  </h2>
                  <p className="text-xs uppercase tracking-[0.2em] text-accent font-medium">
                    {service.subtitle}
                  </p>
                </div>

                <p className="text-stone text-sm md:text-base leading-relaxed font-light">
                  {service.description}
                </p>

                {/* Key Deliverables List */}
                <div className="pt-4 border-t border-[#EAE6DF] space-y-3">
                  <span className="text-[11px] uppercase tracking-wider text-[#1C1B19] font-semibold block">
                    Key Atelier Deliverables:
                  </span>
                  <ul className="space-y-2 text-xs md:text-sm text-stone font-light">
                    {service.deliverables.map((item, dIdx) => (
                      <li key={dIdx} className="flex items-start space-x-2.5">
                        <Check className="w-4 h-4 text-accent flex-shrink-0 mt-0.5" />
                        <span>{item}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </div>
          );
        })}
      </section>

      {/* 3. Numbered 4-Step Design Process Timeline (Soft Travertine Strip) */}
      <section className="py-16 sm:py-24 md:py-32 px-4 sm:px-6 md:px-12 bg-[#F5F3EE]/80 border-y border-[#EAE6DF]/70 relative">
        <div className="max-w-6xl mx-auto space-y-12 sm:space-y-16">
          <div className="max-w-2xl space-y-3 sm:space-y-4">
            <div className="pill-tag bg-white shadow-sm">
              <Sparkles className="w-3.5 h-3.5 text-accent" />
              <span>Architectural Roadmap</span>
            </div>

            <h2 className="font-serif text-3xl sm:text-5xl font-normal text-[#1C1B19]">
              Our 4-Step Design Process
            </h2>

            <p className="text-stone text-xs sm:text-sm font-light">
              How we guide your project from first dialogue to physical turnkey handover with absolute transparency.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
            {MOCK_PROCESS_STEPS.map((step) => (
              <div
                key={step.step}
                className="rounded-2xl border border-[#E5E0D8] bg-white p-5 sm:p-8 flex flex-col justify-between relative group hover:border-accent/60 transition-all duration-300 shadow-sm hover:shadow-xl"
              >
                <div className="space-y-4">
                  <div className="flex items-baseline justify-between border-b border-[#EAE6DF] pb-4">
                    <span className="font-serif text-4xl text-[#1C1B19] font-normal">{step.step}</span>
                    <span className="text-[10px] uppercase tracking-wider bg-[#FAF9F5] text-accent px-2.5 py-1 rounded-full border border-[#E2DDD5] font-medium">
                      {step.phase}
                    </span>
                  </div>

                  <h3 className="font-serif text-2xl text-[#1C1B19] pt-2 group-hover:text-accent transition-colors">
                    {step.title}
                  </h3>

                  <p className="text-stone text-xs leading-relaxed font-light">
                    {step.description}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 4. Interactive FAQ Accordion */}
      <section className="max-w-5xl mx-auto px-6 md:px-12 space-y-12">
        <div className="text-center space-y-4 max-w-2xl mx-auto">
          <div className="pill-tag bg-white shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-accent" />
            <span>Clarity & Transparency</span>
          </div>

          <h2 className="font-serif text-4xl sm:text-5xl font-normal text-[#1C1B19]">
            Frequently Asked Questions
          </h2>

          <p className="text-stone text-sm font-light">
            Answers regarding project timelines, commercial structures, fee percentages, and our remote 3D digital twin collaboration.
          </p>
        </div>

        <div className="border-t border-[#EAE6DF] divide-y divide-[#EAE6DF]">
          {MOCK_FAQS.map((faq, index) => {
            const isOpen = openFaqIndex === index;

            return (
              <div key={index} className="py-6">
                <button
                  onClick={() => toggleFaq(index)}
                  className="w-full flex items-center justify-between text-left group focus:outline-none"
                >
                  <span className="font-serif text-xl md:text-2xl text-[#1C1B19] group-hover:text-accent transition-colors pr-6">
                    {faq.question}
                  </span>
                  <div className="p-2 rounded-full border border-[#E2DDD5] bg-white text-[#1C1B19] flex-shrink-0 group-hover:border-accent shadow-sm">
                    {isOpen ? <Minus className="w-3.5 h-3.5 text-accent" /> : <Plus className="w-3.5 h-3.5" />}
                  </div>
                </button>

                {isOpen && (
                  <div className="mt-4 text-stone text-xs md:text-sm leading-relaxed font-light max-w-3xl">
                    <p>{faq.answer}</p>
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </section>

      {/* 5. Consultation Call to Action */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 md:px-12">
        <div className="rounded-2xl border border-[#E5E0D8] bg-white p-6 sm:p-10 md:p-16 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 sm:gap-8 shadow-xl">
          <div className="space-y-2 sm:space-y-3 max-w-2xl">
            <span className="text-[10px] sm:text-[11px] uppercase tracking-[0.2em] sm:tracking-[0.25em] text-accent font-semibold block">
              Private Client Consultation
            </span>
            <h3 className="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1B19]">
              Discuss your architectural vision with our partners.
            </h3>
            <p className="text-stone text-xs md:text-sm font-light">
              We review prospective residential and commercial commissions worldwide.
            </p>
          </div>

          <Link
            to="/contact"
            className="inline-flex items-center justify-center space-x-2 bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white px-6 sm:px-8 py-3.5 sm:py-4 rounded-full text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-semibold transition-all shadow-md whitespace-nowrap w-full sm:w-auto"
          >
            <span>Inquire About Services</span>
            <ArrowUpRight className="w-4 h-4" />
          </Link>
        </div>
      </section>
    </div>
  );
}
