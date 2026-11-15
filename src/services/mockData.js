/**
 * Studio Atelier Vérité - Curated Architectural & Interior Mock Dataset
 * Matches MySQL schema exactly, providing instant local preview with working 3D Virtual Tours.
 */

export const MOCK_PROJECTS = [
  {
    id: 1,
    slug: 'the-tribeca-loft',
    title: 'The Tribeca Loft',
    subtitle: 'Curated Industrial Minimalism & Warm Alabaster Patina',
    category: 'Residential',
    location: 'Tribeca, New York',
    area_sqft: 4200,
    area_sqft_formatted: '4,200 sq. ft.',
    year: 2025,
    cover_image: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
    hero_image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=2200&q=85',
    client_brief: 'The client sought a complete spatial reimagining of a historic cast-iron warehouse floor. Key mandates included soft architectural acoustic dampening, continuous gallery-scale walls for private art exhibitions, and an effortless transition between serene private suites and high-capacity entertaining salons.',
    design_solution: 'We stripped back decades of disjointed drywall partitions to honor the original timber beams and structural cast-iron pillars. An expansive open-plan layout was organized into sculptural micro-zones using monolithic travertine islands, custom smoked-oak millwork, and full-height linen acoustic drapery. Flush architectural recessed lighting accentuates rich textural contrasts.',
    featured: true,
    has_3d_tour: true,
    tour_3d_url: 'https://my.matterport.com/show/?m=JGPnGQLBTJb&play=1&qs=1',
    tour_type: 'matterport',
    gallery: [
      {
        id: 101,
        image_url: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
        caption: 'Great room featuring curated linen modular seating and exposed Douglas fir joists'
      },
      {
        id: 102,
        image_url: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1600&q=85',
        caption: 'Monolithic Roman travertine kitchen island with integrated induction burners'
      },
      {
        id: 103,
        image_url: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=85',
        caption: 'Primary ensuite with fluted oak vanity and unlacquered aged brass tapware'
      },
      {
        id: 104,
        image_url: 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1600&q=85',
        caption: 'Intimate library salon with floor-to-ceiling smoked oak joinery'
      },
      {
        id: 105,
        image_url: 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1600&q=85',
        caption: 'Dining corridor lined with textured limewash plaster and bespoke sconces'
      }
    ],
    before_after: [
      {
        id: 201,
        title: 'Great Room & Kitchen Transformation',
        before_image: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1600&q=85',
        after_image: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1600&q=85',
        description: 'Decades of yellowed drywall and low fluorescent troffers were stripped away to reveal soaring 12-foot timber joists and industrial cast-iron columns, paired with warm alabaster finishes.'
      }
    ],
    navigation: {
      previous: null,
      next: { slug: 'villa-serenita-amalfi', title: 'Villa Serenità' }
    }
  },
  {
    id: 2,
    slug: 'villa-serenita-amalfi',
    title: 'Villa Serenità',
    subtitle: 'Sun-Drenched Terracotta & Mediterranean Modernism',
    category: 'Turnkey',
    location: 'Amalfi Coast, Italy',
    area_sqft: 6800,
    area_sqft_formatted: '6,800 sq. ft.',
    year: 2025,
    cover_image: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
    hero_image: 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=2200&q=85',
    client_brief: 'Perched high on the cliffs of Ravello, this private clifftop estate required a turnkey transformation that celebrated boundless azure sea vistas without succumbing to rustic clichés. The owners requested seamless indoor-outdoor living, bespoke climate-resilient furnishings, and a subterranean wine sanctuary.',
    design_solution: 'A soft, low-reflectance palette of hand-raked lime plaster and honed Roman travertine forms a quiet backdrop to the dramatic Mediterranean light. We designed custom bronze-framed pivot portals opening onto sprawling pergolas, paired with sculpted Bouclé lounge pieces, unlacquered brass hardware, and hand-loomed Tuscan textiles.',
    featured: true,
    has_3d_tour: true,
    tour_3d_url: 'https://sketchfab.com/models/b27f6cfb04ea4f9588eb7d65ff9e6fc9/embed?autostart=0&ui_controls=1&ui_infos=0&ui_inspector=0&ui_watermark=0',
    tour_type: 'sketchfab',
    gallery: [
      {
        id: 106,
        image_url: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
        caption: 'Main living pavilion framing panoramic Mediterranean clifftop views'
      },
      {
        id: 107,
        image_url: 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1600&q=85',
        caption: 'Bespoke terracotta outdoor dining pergola with hand-loomed cushions'
      },
      {
        id: 108,
        image_url: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
        caption: 'Guest suite with arched alcove and raw silk drapery'
      },
      {
        id: 109,
        image_url: 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85',
        caption: 'Honed limestone infinity pool deck reflecting the Amalfi sunset'
      }
    ],
    before_after: [
      {
        id: 202,
        title: 'Cliffside Veranda & Pergola Revival',
        before_image: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1600&q=85',
        after_image: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85',
        description: 'An aged, cracked concrete patio was reconstructed with honed Roman travertine slabs and custom bronze pergola framing that withstands harsh coastal saltwater air.'
      }
    ],
    navigation: {
      previous: { slug: 'the-tribeca-loft', title: 'The Tribeca Loft' },
      next: { slug: 'kyoto-sanctuary-residence', title: 'Kyoto Sanctuary Residence' }
    }
  },
  {
    id: 3,
    slug: 'kyoto-sanctuary-residence',
    title: 'Kyoto Sanctuary Residence',
    subtitle: 'Contemporary Wabi-Sabi & Hinoki Timber Craftsmanship',
    category: 'Residential',
    location: 'Higashiyama, Kyoto',
    area_sqft: 3100,
    area_sqft_formatted: '3,100 sq. ft.',
    year: 2024,
    cover_image: 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
    hero_image: 'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=2200&q=85',
    client_brief: 'Commissioned by a contemporary art patron, the brief called for an introspective residence immersed in traditional Japanese spatial philosophies of Ma (negative space) and Shakkei (borrowed scenery), equipped with discreet state-of-the-art smart environmental controls.',
    design_solution: 'Constructed in close collaboration with Kyoto master carpenters (Miyadaiku), the home features aromatic Japanese Hinoki framing, hand-troweled Juraku earth plaster walls, and custom Shoji screens reimagined with Belgian linen. A sunken granite soaking bath overlooks an intimate, enclosed moss and water garden.',
    featured: true,
    has_3d_tour: true,
    tour_3d_url: 'https://kuula.co/share/collection/7lZl5?logo=0&info=0&fs=1&vr=1&sd=1&thumbs=1',
    tour_type: 'kuula',
    gallery: [
      {
        id: 110,
        image_url: 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1600&q=85',
        caption: 'Tatami salon with custom hinoki woodwork and imported Belgian linen shoji screens'
      },
      {
        id: 111,
        image_url: 'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=1600&q=85',
        caption: 'Central courtyard zen garden reflecting soft afternoon filtered light'
      },
      {
        id: 112,
        image_url: 'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=1600&q=85',
        caption: 'Handcrafted granite onsen soaking tub facing a private moss garden'
      }
    ],
    before_after: [],
    navigation: {
      previous: { slug: 'villa-serenita-amalfi', title: 'Villa Serenità' },
      next: { slug: 'the-mayfair-heritage-salon', title: 'The Mayfair Heritage Salon' }
    }
  },
  {
    id: 4,
    slug: 'the-mayfair-heritage-salon',
    title: 'The Mayfair Heritage Salon',
    subtitle: 'Georgian Classical Grandeur Meets Bespoke Savile Row Detailing',
    category: 'Renovation',
    location: 'Mayfair, London',
    area_sqft: 5400,
    area_sqft_formatted: '5,400 sq. ft.',
    year: 2024,
    cover_image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
    hero_image: 'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=2200&q=85',
    client_brief: 'A sensitive architectural renovation of a Grade II listed Georgian townhouse. The client requested the preservation of delicate 18th-century plaster cornices and marble hearths alongside a cutting-edge contemporary kitchen and subterranean wellness suite.',
    design_solution: 'We executed painstaking conservation of historic plaster moldings while concealing modern ducted climate systems behind faux coffered friezes. Deep heritage olive and aged bone tones are complemented by fluted Calacatta Viola marble islands, ribbed glass pocket doors, and bespoke upholstery finished in British wool and saddle leather.',
    featured: false,
    has_3d_tour: true,
    tour_3d_url: 'https://my.matterport.com/show/?m=JGPnGQLBTJb&play=1&qs=1',
    tour_type: 'matterport',
    gallery: [
      {
        id: 113,
        image_url: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
        caption: 'Restored 18th-century ornate plaster ceiling and Calacatta Viola fire surround'
      },
      {
        id: 114,
        image_url: 'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1600&q=85',
        caption: 'Modern minimalist kitchen intervention within historic Georgian framing'
      }
    ],
    before_after: [
      {
        id: 203,
        title: 'Historic Drawing Room Restoration',
        before_image: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1600&q=85',
        after_image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=85',
        description: 'Flaking paint and damaged cornice friezes were painstakingly restored using heritage lime plaster casts, integrating modern recessed ambient floor uplighting.'
      }
    ],
    navigation: {
      previous: { slug: 'kyoto-sanctuary-residence', title: 'Kyoto Sanctuary Residence' },
      next: { slug: 'lumina-flagship-ateliers', title: 'Lumina Haute Parfumerie' }
    }
  },
  {
    id: 5,
    slug: 'lumina-flagship-ateliers',
    title: 'Lumina Haute Parfumerie',
    subtitle: 'Sensory Retail Environment with Sculptural Fluted Travertine',
    category: 'Commercial',
    location: 'Rue Saint-Honoré, Paris',
    area_sqft: 2800,
    area_sqft_formatted: '2,800 sq. ft.',
    year: 2025,
    cover_image: 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
    hero_image: 'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=2200&q=85',
    client_brief: 'An experiential flagship store and consultation salon for an artisanal French perfume house. The spatial program demanded high-end olfactory testing bars, intimate VIP consultation alcoves, and gallery-level acoustic privacy in central Paris.',
    design_solution: 'Centering around a monumental, curving counter carved from solid Navona travertine, the boutique surrounds visitors with warm ambient cove lighting, velvet-draped consultation chambers, and micro-perforated acoustic ceiling panels disguised as brushed alabaster.',
    featured: true,
    has_3d_tour: true,
    tour_3d_url: 'https://sketchfab.com/models/b27f6cfb04ea4f9588eb7d65ff9e6fc9/embed?autostart=0&ui_controls=1&ui_infos=0&ui_inspector=0&ui_watermark=0',
    tour_type: 'sketchfab',
    gallery: [
      {
        id: 115,
        image_url: 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1600&q=85',
        caption: 'Sculptural fluted travertine fragrance bar with hidden diffuse lighting'
      },
      {
        id: 116,
        image_url: 'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=1600&q=85',
        caption: 'Private VIP consultation alcove upholstered in heavy crushed velvet'
      }
    ],
    before_after: [],
    navigation: {
      previous: { slug: 'the-mayfair-heritage-salon', title: 'The Mayfair Heritage Salon' },
      next: { slug: 'bel-air-promontory-villa', title: 'Bel Air Promontory Estate' }
    }
  },
  {
    id: 6,
    slug: 'bel-air-promontory-villa',
    title: 'Bel Air Promontory Estate',
    subtitle: 'California Mid-Century Soul & Warm Architectural Volumes',
    category: 'Turnkey',
    location: 'Bel Air, Los Angeles',
    area_sqft: 8200,
    area_sqft_formatted: '8,200 sq. ft.',
    year: 2024,
    cover_image: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
    hero_image: 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=2200&q=85',
    client_brief: 'A sprawling hill-side turnkey residence designed for an entertainment industry executive who required both a restful retreat and a showcase for modern sculpture and automotive design.',
    design_solution: 'We blurred the threshold between the interior and the canyon landscape through 14-foot motorized glass pocket facades. Floors of hand-selected French limestone flow seamlessly onto infinity pool terraces, paired with dark American walnut wall paneling, brushed bronze accents, and low-slung Italian modular seating.',
    featured: false,
    has_3d_tour: true,
    tour_3d_url: 'https://my.matterport.com/show/?m=JGPnGQLBTJb&play=1&qs=1',
    tour_type: 'matterport',
    gallery: [
      {
        id: 117,
        image_url: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=85',
        caption: 'Open-air living pavilion with 14-foot motorized glass pocket facades'
      },
      {
        id: 118,
        image_url: 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85',
        caption: 'Cantilevered terrace dining overlooking the Los Angeles canyon skyline'
      }
    ],
    before_after: [],
    navigation: {
      previous: { slug: 'lumina-flagship-ateliers', title: 'Lumina Haute Parfumerie' },
      next: null
    }
  }
];

export const MOCK_SERVICES = [
  {
    id: 'turnkey-execution',
    title: 'Turnkey Interior Execution',
    subtitle: 'From Bare Shell to Flawless Handover',
    description: 'A holistic, white-glove design-and-build experience. We take absolute stewardship of civil alterations, MEP coordination, acoustic engineering, artisan finishes, and bespoke furniture placement.',
    deliverables: [
      'Comprehensive structural & MEP architectural documentation',
      'Dedicated on-site senior project manager & master contractor team',
      'Artisan craft curation (custom stonework, millwork, fine metals)',
      'Turnkey styling, fine art acquisition & final staging'
    ],
    image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85'
  },
  {
    id: 'spatial-planning',
    title: 'Spatial Planning & Architecture',
    subtitle: 'Proportion, Daylight, & Spatial Choreography',
    description: 'We curate human-centric layouts that prioritize intuitive movement, acoustic balance, and maximum daylight exposure, harmonizing volume and void.',
    deliverables: [
      'Micro-zoning & flow circulation studies',
      'Daylight trajectory & sightline mapping',
      'Acoustic insulation & spatial partition detailing',
      'Detailed joinery & architectural ceiling schematics'
    ],
    image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85'
  },
  {
    id: '3d-visualization',
    title: '3D Visualization & Virtual Tours',
    subtitle: 'Photorealistic Digital Twins Before Groundbreak',
    description: 'Immerse yourself inside your future sanctuary before a single brick is laid. We engineer cinema-grade CGI renders, interactive 3D digital twins, and walkthrough simulations.',
    deliverables: [
      'Ultra-high fidelity photorealistic interior renderings',
      'Matterport / Kuula 360° interactive digital twin models',
      'Lighting simulation under seasonal sun angles',
      'Material sample tactile mood boards'
    ],
    image: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=85'
  },
  {
    id: 'bespoke-furniture',
    title: 'Bespoke Furniture & Curated Objects',
    subtitle: 'Heirloom Furniture & Rare Tactile Finishes',
    description: 'Tailored solely to your home. We commission bespoke dining tables, sculptured seating, and custom lighting fixtures in collaboration with European and Japanese master craftsmen.',
    deliverables: [
      'Exclusive bespoke furniture sketches & 1:1 joinery details',
      'Direct sourcing of rare marbles, French oak, and tactile bouclés',
      'Commissioned gallery-grade sculptures and art pieces',
      'White-glove installation and archival care guidelines'
    ],
    image: 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=85'
  }
];

export const MOCK_PROCESS_STEPS = [
  {
    step: '01',
    title: 'Discovery & Spatial Audit',
    phase: 'Week 1 – 2',
    description: 'We explore your lifestyle, aesthetic sensitivities, functional requirements, and the unique architectural context of the property.'
  },
  {
    step: '02',
    title: 'Concept & 3D Digital Twin',
    phase: 'Week 3 – 6',
    description: 'We translate the spatial vision into tactile mood boards, detailed floor plans, and interactive 3D virtual tour simulations.'
  },
  {
    step: '03',
    title: 'Material Sourcing & Joinery',
    phase: 'Week 7 – 10',
    description: 'Quarry-direct marble selection, custom timber milling, hand-loomed textiles, and fabrication of custom architectural fixtures.'
  },
  {
    step: '04',
    title: 'Execution & White-Glove Handover',
    phase: 'Week 11 – Completion',
    description: 'Rigorous on-site craftsmanship supervision, acoustic testing, deep detail cleaning, art placement, and the ceremonial key handover.'
  }
];

export const MOCK_TESTIMONIALS = [
  {
    quote: 'Atelier Vérité transformed our Tribeca warehouse into a serene sanctuary that feels simultaneously like a private art gallery and an effortlessly comfortable home. Their restraint and mastery of natural materials is unmatched.',
    author: 'Julian & Elena Vance',
    role: 'Private Collectors',
    project: 'The Tribeca Loft'
  },
  {
    quote: 'Being able to walk through our Amalfi villa via their 3D virtual twin before construction commenced saved us months of revisions and gave us absolute clarity. The final result exceeded our wildest aspirations.',
    author: 'Conte Marco Rossi',
    role: 'Estate Owner',
    project: 'Villa Serenità, Amalfi'
  },
  {
    quote: 'The level of craftsmanship and architectural rigor they brought to our Paris flagship established our brand identity on an international stage. Every client notes the extraordinary tactile energy of the space.',
    author: 'Céline Laurent',
    role: 'Creative Director',
    project: 'Lumina Haute Parfumerie, Paris'
  }
];

export const MOCK_FAQS = [
  {
    question: 'What is your typical project timeline for a full turnkey residence?',
    answer: 'Turnkey residential projects typically range from 4 to 9 months, depending on carpet area and structural scope. Discovery and 3D digital twin design take 4–6 weeks, followed by concurrent procurement and on-site artisan execution.'
  },
  {
    question: 'How do you charge for interior architecture and design services?',
    answer: 'We operate on a transparent design fee structured per square foot or as a percentage of the total construction cost, paired with direct pass-through wholesale trade pricing for material procurement and bespoke furniture fabrication.'
  },
  {
    question: 'Can you take on international commissions outside your primary studios?',
    answer: 'Yes. Atelier Vérité frequently designs residences throughout North America, Europe, Japan, and the Middle East. Our remote 3D digital twin methodology and vetted regional contractor networks ensure seamless overseas execution.'
  },
  {
    question: 'How do the interactive 3D virtual tours work during the design phase?',
    answer: 'Before physical demolition or construction begins, we construct an immersive 3D digital model (accessible via browser or VR headset). You can freely walk through hallways, test sightlines, view material reflections, and experience exact daylight conditions.'
  },
  {
    question: 'Do you manage site contractors and municipal permissions?',
    answer: 'Yes. In our Turnkey Execution tier, our studio coordinates all municipal permits, heritage board approvals, structural engineer sign-offs, and daily site contractor craftsmanship.'
  }
];
