import React, { useState } from 'react';
import { Send, CheckCircle2, AlertCircle, MessageSquare } from 'lucide-react';
import { submitInquiry } from '../services/api';
import marbleTexture from '../assets/textures/marble-calacatta.webp';

export default function ContactSplitForm({
  tag = 'Private Commissions',
  title = 'Book Your Consultation',
  description = 'Let us help you sculpt the perfect private sanctuary. Our senior partners are available for exploratory design reviews worldwide.',
  imageSrc = 'https://storage.googleapis.com/webild/default/templates/hotel/contact/bookyourstay.webp'
}) {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    area: '',
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

    if (!formData.name.trim() || formData.name.length < 2) {
      setErrorMsg('Please enter your full name.');
      return;
    }
    if (!formData.email.trim() || !formData.email.includes('@')) {
      setErrorMsg('Please enter a valid email address.');
      return;
    }
    if (!formData.phone.trim()) {
      setErrorMsg('Please provide a contact phone number.');
      return;
    }

    setLoading(true);
    try {
      const res = await submitInquiry({
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        area_sqft: formData.area || 'Not specified',
        property_type: 'Turnkey Luxury Sanctuary',
        budget_range: 'Boutique Scale',
        city: 'Private Residence',
        message: formData.message || 'Consultation requested via Boutique Hotel portal.'
      });

      if (res && res.success) {
        setSubmitted(true);
        setInquiryId(res.inquiry_id);
      } else {
        throw new Error(res.error || 'Submission failed');
      }
    } catch (err) {
      setErrorMsg(err.message || 'Error transmitting dossier. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <section id="contact" className="py-16 sm:py-24 md:py-36 px-4 sm:px-6 md:px-12 bg-transparent border-t border-[#EAE6DF]/70 relative">
      <div className="max-w-6xl mx-auto">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">
          {/* Left Column: Pure White Luxury Inquiry Card Form */}
          <div className="rounded-2xl border border-[#E5E0D8] bg-white/95 backdrop-blur-md p-5 sm:p-8 md:p-12 shadow-xl flex flex-col justify-between relative overflow-hidden">
            {/* Subtle Calacatta Marble Veining */}
            <div
              className="absolute inset-0 bg-cover bg-center opacity-[0.22] mix-blend-multiply pointer-events-none"
              style={{ backgroundImage: `url(${marbleTexture})` }}
            />
            <div className="relative z-10 space-y-5 sm:space-y-6">
              <div className="pill-tag bg-[#FAF9F5] shadow-sm">
                <span>{tag}</span>
              </div>

              <div className="space-y-2">
                <h2 className="font-serif text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-normal text-[#1C1B19]">
                  {title}
                </h2>
                <p className="text-stone text-xs sm:text-sm font-light leading-relaxed">
                  {description}
                </p>
              </div>

              {submitted ? (
                <div className="py-8 text-center space-y-4 bg-[#FAF9F5] p-6 rounded-xl border border-[#E2DDD5]">
                  <CheckCircle2 className="w-12 h-12 text-accent mx-auto" />
                  <h3 className="font-serif text-2xl text-[#1C1B19]">Dossier Transmitted</h3>
                  <p className="text-stone text-xs max-w-sm mx-auto font-light leading-relaxed">
                    Thank you. Your consultation inquiry <strong className="text-[#1C1B19] font-mono">#{inquiryId}</strong> has been logged. Our design director will contact you within 24 business hours.
                  </p>
                  <button
                    onClick={() => {
                      setSubmitted(false);
                      setFormData({ name: '', email: '', phone: '', area: '', message: '' });
                    }}
                    className="text-xs uppercase tracking-wider text-accent underline pt-2 font-medium"
                  >
                    Send Another Request
                  </button>
                </div>
              ) : (
                <form onSubmit={handleSubmit} className="space-y-4 pt-2">
                  {errorMsg && (
                    <div className="p-3 bg-red-50 border border-red-200 text-red-700 text-xs flex items-center space-x-2 rounded-xl">
                      <AlertCircle className="w-4 h-4 flex-shrink-0" />
                      <span>{errorMsg}</span>
                    </div>
                  )}

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <input
                      type="text"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      placeholder="Full Name *"
                      required
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-sm sm:text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />

                    <input
                      type="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      placeholder="Email Address *"
                      required
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-sm sm:text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <input
                      type="tel"
                      name="phone"
                      value={formData.phone}
                      onChange={handleChange}
                      placeholder="Phone Number *"
                      required
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-sm sm:text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />

                    <input
                      type="text"
                      name="area"
                      value={formData.area}
                      onChange={handleChange}
                      placeholder="Carpet Area / Preferred Dates"
                      className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl px-4 py-3 text-sm sm:text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors"
                    />
                  </div>

                  <textarea
                    name="message"
                    rows={4}
                    value={formData.message}
                    onChange={handleChange}
                    placeholder="Describe your architectural priorities, daylight aspirations, or project timeline..."
                    className="w-full bg-[#FAF9F5] border border-[#E2DDD5] rounded-xl p-4 text-sm sm:text-xs text-[#1C1B19] placeholder:text-stone-light focus:outline-none focus:border-accent transition-colors leading-relaxed"
                  />

                  <button
                    type="submit"
                    disabled={loading}
                    className="w-full bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white py-3.5 sm:py-4 rounded-xl text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-semibold flex items-center justify-center space-x-2 transition-all duration-300 shadow-md disabled:opacity-50"
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

            {/* Direct WhatsApp Pill at Bottom of Card */}
            <div className="relative z-10 pt-6 mt-6 border-t border-[#EAE6DF] flex items-center justify-between text-xs text-stone">
              <span>Prefer immediate discussion?</span>
              <a
                href="https://wa.me/12125550198"
                target="_blank"
                rel="noreferrer"
                className="inline-flex items-center space-x-1.5 text-accent hover:underline font-semibold"
              >
                <MessageSquare className="w-3.5 h-3.5" />
                <span>WhatsApp Private Client Line</span>
              </a>
            </div>
          </div>

          {/* Right Column: Full-Bleed Atmosphere Photography Frame */}
          <div className="relative rounded-2xl overflow-hidden border border-[#E5E0D8] shadow-xl min-h-[460px] bg-white">
            <img
              src={imageSrc}
              alt="Atelier Vérité Private Sanctuary"
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[#141312] via-[#141312]/30 to-transparent opacity-80" />

            <div className="absolute bottom-8 left-8 right-8 z-10 space-y-2 text-white">
              <span className="text-[10px] uppercase tracking-[0.25em] text-accent block font-medium">
                The Atelier Commitment
              </span>
              <p className="font-serif text-2xl leading-snug">
                "We accept a limited number of turnkey residences each year to ensure personal, uncompromising architectural oversight."
              </p>
              <span className="text-xs text-[#D8D3CA] block pt-1">
                Elena Vance &bull; Principal Architect & AIA Fellow
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
