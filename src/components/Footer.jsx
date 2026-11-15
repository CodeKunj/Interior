import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight, Check, Send } from 'lucide-react';

export default function Footer() {
  const [email, setEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);

  const handleSubscribe = (e) => {
    e.preventDefault();
    if (!email || !email.includes('@')) return;
    setSubscribed(true);
    setEmail('');
    setTimeout(() => setSubscribed(false), 5000);
  };

  return (
    <footer className="bg-linen border-t border-sand text-charcoal pt-24 pb-12">
      <div className="max-w-7xl mx-auto px-6 md:px-12">
        {/* Top Editorial Banner */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 pb-20 border-b border-sand">
          <div className="lg:col-span-7 space-y-6">
            <span className="text-[11px] uppercase tracking-[0.3em] text-stone block font-medium">
              Spatial Integrity &bull; Quiet Luxury
            </span>
            <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl font-normal leading-[1.1] text-charcoal">
              Crafting architectural sanctuaries rooted in natural materiality & light.
            </h2>
          </div>

          <div className="lg:col-span-5 flex flex-col justify-end space-y-6">
            <p className="text-stone text-sm leading-relaxed max-w-md">
              Receive our seasonal journal on architectural materiality, private residential commissions, and emerging spatial methodologies.
            </p>

            <form onSubmit={handleSubscribe} className="relative max-w-md">
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Enter your email address"
                required
                className="w-full bg-alabaster border border-sand px-4 py-3.5 text-xs text-charcoal placeholder:text-stone/60 focus:outline-none focus:border-bronze pr-12 transition-colors"
              />
              <button
                type="submit"
                aria-label="Subscribe to journal"
                className="absolute right-0 top-0 bottom-0 px-4 flex items-center justify-center text-charcoal hover:text-bronze transition-colors"
              >
                {subscribed ? <Check className="w-4 h-4 text-emerald-700" /> : <Send className="w-4 h-4" />}
              </button>
              {subscribed && (
                <span className="text-[11px] text-emerald-700 mt-1 block">
                  Thank you for subscribing to the Atelier Vérité Journal.
                </span>
              )}
            </form>
          </div>
        </div>

        {/* Studio Locations & Navigation */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-10 py-16 border-b border-sand text-xs">
          {/* Studios */}
          <div>
            <h3 className="text-[10px] uppercase tracking-[0.25em] text-stone font-semibold mb-4">Studios</h3>
            <div className="space-y-4 text-stone">
              <div>
                <strong className="text-charcoal block font-medium">New York</strong>
                <span>482 Broome Street, SoHo</span>
              </div>
              <div>
                <strong className="text-charcoal block font-medium">Paris</strong>
                <span>24 Rue Saint-Honoré, 1er</span>
              </div>
              <div>
                <strong className="text-charcoal block font-medium">Kyoto</strong>
                <span>Higashiyama-ku, Shirakawa</span>
              </div>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-[10px] uppercase tracking-[0.25em] text-stone font-semibold mb-4">Navigation</h3>
            <ul className="space-y-2.5">
              <li>
                <Link to="/" className="text-stone hover:text-charcoal transition-colors">Home Experience</Link>
              </li>
              <li>
                <Link to="/work" className="text-stone hover:text-charcoal transition-colors">Curated Portfolio</Link>
              </li>
              <li>
                <Link to="/services" className="text-stone hover:text-charcoal transition-colors">Services & Process</Link>
              </li>
              <li>
                <Link to="/about" className="text-stone hover:text-charcoal transition-colors">Studio & Founder</Link>
              </li>
              <li>
                <Link to="/contact" className="text-stone hover:text-charcoal transition-colors">Inquiry & Consultation</Link>
              </li>
            </ul>
          </div>

          {/* Specializations */}
          <div>
            <h3 className="text-[10px] uppercase tracking-[0.25em] text-stone font-semibold mb-4">Practices</h3>
            <ul className="space-y-2.5 text-stone">
              <li>Turnkey Residential Estates</li>
              <li>3D Virtual Digital Twins</li>
              <li>Historic Restoration</li>
              <li>High-End Retail Salons</li>
              <li>Bespoke Art & Furniture</li>
            </ul>
          </div>

          {/* Direct Connect */}
          <div>
            <h3 className="text-[10px] uppercase tracking-[0.25em] text-stone font-semibold mb-4">Connect</h3>
            <div className="space-y-3">
              <a
                href="https://wa.me/12125550198"
                target="_blank"
                rel="noreferrer"
                className="inline-flex items-center space-x-1.5 text-stone hover:text-charcoal transition-colors"
              >
                <span>WhatsApp Private Client Line</span>
                <ArrowUpRight className="w-3.5 h-3.5" />
              </a>
              <a
                href="mailto:concierge@ateliervérité.com"
                className="block text-stone hover:text-charcoal transition-colors"
              >
                concierge@ateliervérité.com
              </a>
              <div className="flex space-x-4 pt-2 text-stone">
                <a href="#instagram" className="hover:text-bronze transition-colors">Instagram</a>
                <a href="#pinterest" className="hover:text-bronze transition-colors">Pinterest</a>
                <a href="#architectural-digest" className="hover:text-bronze transition-colors">AD Pro</a>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom Credits & cPanel Admin Link */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between text-[11px] text-stone gap-4">
          <p>
            &copy; {new Date().getFullYear()} Atelier Vérité Architecture & Interior Studio. All rights reserved.
          </p>
          <div className="flex items-center space-x-6">
            <span className="text-stone/60">Boutique Hotel Editorial Standard</span>
            <a
              href="/api/admin/"
              target="_blank"
              rel="noreferrer"
              className="text-stone hover:text-bronze transition-colors underline underline-offset-4"
            >
              CMS Admin Portal
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
}
