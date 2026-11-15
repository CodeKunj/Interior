import React, { useState } from 'react';
import { ArrowUpRight, CheckCircle2, AlertCircle, Sparkles, Send, MessageSquare } from 'lucide-react';
import { submitInquiry } from '../services/api';

const PROPERTY_TYPES = [
  'Penthouse / Luxury Apartment',
  'Private Villa / Country Estate',
  'Historic Townhouse Renovation',
  'Commercial / Boutique Flagship',
  'Turnkey Spatial Architecture'
];

const BUDGET_RANGES = [
  '$150,000 – $350,000',
  '$350,000 – $750,000',
  '$750,000 – $1,500,000',
  '$1,500,000 – $3,000,000+'
];

export default function Contact() {
  const [formData, setFormData] = useState({
    fullName: '',
    email: '',
    phone: '',
    city: '',
    propertyType: PROPERTY_TYPES[0],
    areaSqft: '',
    budgetRange: BUDGET_RANGES[1],
    message: ''
  });

  const [loading, setLoading] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [inquiryId, setInquiryId] = useState(null);
  const [errorMsg, setErrorMsg] = useState('');

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrorMsg('');

    if (!formData.fullName.trim() || formData.fullName.length < 2) {
      setErrorMsg('Please enter your full name.');
      return;
    }
    if (!formData.email.trim() || !formData.email.includes('@')) {
      setErrorMsg('Please provide a valid email address.');
      return;
    }
    if (!formData.phone.trim()) {
      setErrorMsg('Please provide a contact phone number.');
      return;
    }
    if (!formData.message.trim()) {
      setErrorMsg('Please share a brief note about your project scope.');
      return;
    }

    setLoading(true);
    try {
      const response = await submitInquiry({
        name: formData.fullName,
        email: formData.email,
        phone: formData.phone,
        city: formData.city,
        property_type: formData.propertyType,
        area_sqft: formData.areaSqft || 'Unspecified',
        budget_range: formData.budgetRange,
        message: formData.message
      });

      if (response && response.success) {
        setSubmitted(true);
        setInquiryId(response.inquiry_id);
      } else {
        throw new Error(response.error || 'Submission failed');
      }
    } catch (err) {
      setErrorMsg(err.message || 'Unable to submit inquiry. Please try again or reach us via WhatsApp.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="pt-36 pb-32 space-y-24 md:space-y-32 bg-[#FAF9F5] min-h-screen text-[#1C1B19]">
      {/* 1. Header */}
      <section className="max-w-6xl mx-auto px-6 md:px-12">
        <div className="max-w-3xl space-y-4">
          <div className="pill-tag bg-white shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-accent" />
            <span>Client Inquiries & Private Commissions</span>
          </div>

          <h1 className="font-serif text-5xl sm:text-6xl md:text-7xl font-normal text-[#1C1B19] leading-[1.05]">
            Initiate Consultation
          </h1>

          <p className="text-stone text-base md:text-lg font-light leading-relaxed pt-2">
            Every bespoke sanctuary begins with an exploratory conversation. Share your architectural vision, property parameters, and desired timeline below.
          </p>
        </div>
      </section>

      {/* 2. Form & Direct Studio Details */}
      <section className="max-w-6xl mx-auto px-6 md:px-12">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
          {/* Inquiry Form Column */}
          <div className="lg:col-span-7 rounded-2xl border border-[#E5E0D8] bg-white p-8 md:p-12 shadow-xl">
            {submitted ? (
              <div className="py-12 text-center space-y-6">
                <div className="w-16 h-16 bg-[#FAF9F5] text-accent rounded-full flex items-center justify-center mx-auto border border-[#E2DDD5]">
                  <CheckCircle2 className="w-8 h-8" />
                </div>
                <div className="space-y-2">
                  <h2 className="font-serif text-3xl md:text-4xl text-[#1C1B19]">Consultation Requested</h2>
                  <p className="text-stone text-sm max-w-md mx-auto font-light">
                    Thank you. Your dossier <strong className="text-[#1C1B19] font-mono">#{inquiryId}</strong> has been transmitted to our senior partners. We will contact you within 24 business hours.
                  </p>
                </div>
                <button
                  onClick={() => {
                    setSubmitted(false);
                    setFormData({
                      fullName: '',
                      email: '',
                      phone: '',
                      city: '',
                      propertyType: PROPERTY_TYPES[0],
                      areaSqft: '',
                      budgetRange: BUDGET_RANGES[1],
                      message: ''
                    });
                  }}
                  className="inline-flex items-center space-x-2 text-xs uppercase tracking-[0.2em] font-medium text-accent hover:underline pt-4"
                >
                  <span>Submit Another Inquiry</span>
                </button>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="border-b border-[#EAE6DF] pb-4 mb-2">
                  <h2 className="font-serif text-2xl md:text-3xl text-[#1C1B19]">Project Consultation Dossier</h2>
                  <p className="text-xs text-stone font-light">Fields marked with * are required.</p>
                </div>

                {errorMsg && (
                  <div className="p-3 bg-red-50 border border-red-200 text-red-700 text-xs flex items-center space-x-2 rounded-xl">
                    <AlertCircle className="w-4 h-4 flex-shrink-0" />
                    <span>{errorMsg}</span>
                  </div>
                )}

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                      Full Name *
                    </label>
                    <input
                      type="text"
                      name="fullName"
                      value={formData.fullName}
                      onChange={handleChange}
                      required
                      placeholder="Lord Julian Vance"
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />
                  </div>

                  <div>
                    <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                      Email Address *
                    </label>
                    <input
                      type="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      required
                      placeholder="julian@vance.com"
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                      Phone Number *
                    </label>
                    <input
                      type="tel"
                      name="phone"
                      value={formData.phone}
                      onChange={handleChange}
                      required
                      placeholder="+1 (212) 555-0198"
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />
                  </div>

                  <div>
                    <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                      City & Country *
                    </label>
                    <input
                      type="text"
                      name="city"
                      value={formData.city}
                      onChange={handleChange}
                      placeholder="New York, USA"
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                      Property Typology
                    </label>
                    <select
                      name="propertyType"
                      value={formData.propertyType}
                      onChange={handleChange}
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-xs text-[#1C1B19] focus:outline-none focus:border-accent transition-colors"
                    >
                      {PROPERTY_TYPES.map((t) => (
                        <option key={t} value={t}>{t}</option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                      Approximate Carpet Area (sq. ft.)
                    </label>
                    <input
                      type="text"
                      name="areaSqft"
                      value={formData.areaSqft}
                      onChange={handleChange}
                      placeholder="e.g. 4,500 sq. ft."
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                    Estimated Project Budget Range
                  </label>
                  <select
                    name="budgetRange"
                    value={formData.budgetRange}
                    onChange={handleChange}
                    className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-xs text-[#1C1B19] focus:outline-none focus:border-accent transition-colors"
                  >
                    {BUDGET_RANGES.map((b) => (
                      <option key={b} value={b}>{b}</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block text-xs uppercase tracking-wider text-stone mb-1 font-medium">
                    Project Vision & Desired Timeline *
                  </label>
                  <textarea
                    name="message"
                    rows={5}
                    value={formData.message}
                    onChange={handleChange}
                    required
                    placeholder="Describe your property, acoustic priorities, daylight requirements, and anticipated completion date..."
                    className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl p-4 text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors leading-relaxed"
                  />
                </div>

                <button
                  type="submit"
                  disabled={loading}
                  className="w-full bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white py-4 rounded-xl text-xs uppercase tracking-[0.2em] font-semibold flex items-center justify-center space-x-2 transition-all duration-300 shadow-xl disabled:opacity-50"
                >
                  {loading ? (
                    <span>Transmitting Dossier...</span>
                  ) : (
                    <>
                      <span>Submit Consultation Dossier</span>
                      <Send className="w-3.5 h-3.5" />
                    </>
                  )}
                </button>
              </form>
            )}
          </div>

          {/* Direct Studio Contact & WhatsApp Sidecard */}
          <div className="lg:col-span-5 space-y-10">
            {/* WhatsApp Direct Line Card */}
            <div className="rounded-2xl border border-[#E5E0D8] bg-white p-8 space-y-4 shadow-sm">
              <div className="pill-tag bg-[#FAF9F5]">
                <Sparkles className="w-3.5 h-3.5 text-accent" />
                <span>Instant Dialogue</span>
              </div>

              <h3 className="font-serif text-2xl text-[#1C1B19]">
                WhatsApp Private Client Desk
              </h3>
              <p className="text-stone text-xs leading-relaxed font-light">
                Prefer immediate messaging? Connect directly with our studio director for quick inquiries or to schedule an exploratory video walkthrough.
              </p>
              <a
                href="https://wa.me/12125550198"
                target="_blank"
                rel="noreferrer"
                className="inline-flex items-center space-x-2 bg-[#25D366] text-white hover:bg-[#1EBE5D] transition-colors px-6 py-3.5 rounded-full text-xs uppercase tracking-wider font-semibold shadow-md"
              >
                <MessageSquare className="w-4 h-4" />
                <span>Open WhatsApp Chat</span>
              </a>
            </div>

            {/* Studio Addresses */}
            <div className="space-y-6 text-xs text-stone border-t border-[#EAE6DF] pt-8">
              <div className="space-y-1.5">
                <span className="text-[10px] uppercase tracking-wider text-accent font-semibold block">
                  New York Studio
                </span>
                <p className="text-[#1C1B19] font-medium">482 Broome Street, SoHo, New York, NY 10013</p>
                <p>+1 (212) 555-0198 &bull; ny@ateliervérité.com</p>
              </div>

              <div className="space-y-1.5">
                <span className="text-[10px] uppercase tracking-wider text-accent font-semibold block">
                  Paris Studio
                </span>
                <p className="text-[#1C1B19] font-medium">24 Rue Saint-Honoré, 75001 Paris, France</p>
                <p>+33 1 42 68 55 00 &bull; paris@ateliervérité.com</p>
              </div>

              <div className="space-y-1.5">
                <span className="text-[10px] uppercase tracking-wider text-accent font-semibold block">
                  Kyoto Studio
                </span>
                <p className="text-[#1C1B19] font-medium">Higashiyama-ku, Shirakawa, Kyoto 605-0001, Japan</p>
                <p>+81 75 561 0290 &bull; kyoto@ateliervérité.com</p>
              </div>

              <div className="pt-4 border-t border-[#EAE6DF] space-y-1">
                <span className="text-[10px] uppercase tracking-wider text-accent font-semibold block">
                  Studio Consultation Hours
                </span>
                <p className="text-[#1C1B19]">Monday — Friday: 09:00 – 18:30 (By Private Appointment)</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
