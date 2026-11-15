import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import SmoothScroll from './components/SmoothScroll';
import ScrollToTop from './components/ScrollToTop';
import NavbarFullscreen from './components/NavbarFullscreen';
import FooterBrand from './components/FooterBrand';

import Home from './pages/Home';
import Work from './pages/Work';
import ProjectDetail from './pages/ProjectDetail';
import Services from './pages/Services';
import Journeys from './pages/Journeys';
import About from './pages/About';
import Contact from './pages/Contact';

export default function App() {
  return (
    <SmoothScroll>
      <div className="min-h-screen flex flex-col bg-[#FAF9F5] text-[#1C1B19] selection:bg-[#8C6D53] selection:text-white">
        {/* Scroll restoration upon page change */}
        <ScrollToTop />

        {/* Boutique Hotel Floating Pill Header with Fullscreen Menu */}
        <NavbarFullscreen />

        {/* Main page content view */}
        <main className="flex-1">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/work" element={<Work />} />
            <Route path="/work/:slug" element={<ProjectDetail />} />
            <Route path="/services" element={<Services />} />
            <Route path="/journeys" element={<Journeys />} />
            <Route path="/about" element={<About />} />
            <Route path="/contact" element={<Contact />} />
            {/* Catch-all redirect to Home */}
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </main>

        {/* Boutique Hotel Giant Brand Footer in Soft Linen */}
        <FooterBrand brand="AURION" />
      </div>
    </SmoothScroll>
  );
}
