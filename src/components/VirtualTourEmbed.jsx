import React, { useState, useRef } from 'react';
import { Maximize2, Minimize2, ExternalLink, Compass, Eye, Info } from 'lucide-react';

/**
 * VirtualTourEmbed
 * Responsive 16:9 embedded 3D player supporting Matterport, Kuula, Sketchfab, and Spline digital twins.
 */
export default function VirtualTourEmbed({
  tourUrl,
  tourType = 'matterport',
  projectTitle = 'Project Virtual Tour'
}) {
  const [isFullscreen, setIsFullscreen] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const containerRef = useRef(null);

  if (!tourUrl) {
    return (
      <div className="p-12 text-center rounded-2xl border border-[#E5E0D8] bg-[#FAF9F5] text-stone text-sm">
        <Compass className="w-8 h-8 text-accent mx-auto mb-3 opacity-60" />
        <p>A 3D virtual tour has not been configured for this project yet.</p>
      </div>
    );
  }

  const toggleFullscreen = () => {
    if (!containerRef.current) return;

    if (!document.fullscreenElement) {
      containerRef.current.requestFullscreen().then(() => {
        setIsFullscreen(true);
      }).catch(err => {
        console.error('Error attempting to enable fullscreen:', err.message);
      });
    } else {
      document.exitFullscreen().then(() => {
        setIsFullscreen(false);
      });
    }
  };

  const getProviderName = (type) => {
    switch (type?.toLowerCase()) {
      case 'matterport': return 'Matterport 3D Pro';
      case 'sketchfab': return 'Sketchfab Interactive 3D';
      case 'kuula': return 'Kuula 360° Panorama';
      case 'spline': return 'Spline 3D Spatial Canvas';
      default: return '3D Digital Twin';
    }
  };

  return (
    <div className="space-y-4">
      {/* Top Controls & Provider Banner */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between text-xs text-stone gap-3">
        <div className="flex items-center space-x-2">
          <span className="inline-flex items-center space-x-1.5 bg-accent/10 text-accent px-3 py-1 text-[11px] uppercase tracking-wider font-medium rounded-full border border-accent/25">
            <Eye className="w-3.5 h-3.5" />
            <span>{getProviderName(tourType)}</span>
          </span>
          <span className="hidden sm:inline text-stone/40">&bull;</span>
          <span className="text-[11px] text-stone font-light">Photorealistic Digital Twin Walkthrough</span>
        </div>

        <div className="flex items-center space-x-4">
          <a
            href={tourUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center space-x-1 hover:text-[#1C1B19] transition-colors underline underline-offset-4 text-stone"
          >
            <span>Open in Dedicated Window</span>
            <ExternalLink className="w-3 h-3" />
          </a>

          <button
            onClick={toggleFullscreen}
            className="inline-flex items-center space-x-1.5 px-4 py-1.5 rounded-full bg-[#1C1B19] text-[#FAF9F5] hover:bg-accent hover:text-white transition-colors text-[11px] uppercase tracking-wider font-semibold shadow-md"
          >
            {isFullscreen ? (
              <>
                <Minimize2 className="w-3.5 h-3.5" />
                <span>Exit Fullscreen</span>
              </>
            ) : (
              <>
                <Maximize2 className="w-3.5 h-3.5" />
                <span>Fullscreen 3D</span>
              </>
            )}
          </button>
        </div>
      </div>

      {/* 16:9 Responsive 3D Player Viewport */}
      <div
        ref={containerRef}
        className="relative w-full aspect-video bg-[#FAF9F5] rounded-2xl border border-[#E5E0D8] shadow-xl overflow-hidden group"
      >
        {/* Loading Spinner */}
        {isLoading && (
          <div className="absolute inset-0 flex flex-col items-center justify-center bg-[#FAF9F5] text-[#1C1B19] z-10 space-y-3">
            <div className="w-8 h-8 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
            <span className="text-xs tracking-[0.2em] uppercase font-light text-stone">
              Loading 3D Spatial Geometry...
            </span>
          </div>
        )}

        {/* 3D Embed Iframe */}
        <iframe
          src={tourUrl}
          title={`${projectTitle} 3D Virtual Tour`}
          className="w-full h-full border-0"
          allow="fullscreen; xr-spatial-tracking; vr; gyroscope; accelerometer"
          onLoad={() => setIsLoading(false)}
          loading="lazy"
        />

        {/* Floating Controls / Instructions Badge */}
        <div className="absolute bottom-4 left-4 right-4 sm:right-auto z-20 pointer-events-none">
          <div className="bg-white/95 backdrop-blur-md text-[#1C1B19] text-[10px] md:text-xs py-2 px-3.5 rounded-xl border border-[#E2DDD5] flex items-center space-x-3 shadow-lg max-w-xl">
            <Info className="w-4 h-4 text-accent flex-shrink-0" />
            <div className="leading-snug text-stone">
              <strong className="text-[#1C1B19] font-medium">Interactive Guide:</strong> Click & drag to rotate 360° &bull; Scroll to zoom &bull; Click floor discs to step through rooms.
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
