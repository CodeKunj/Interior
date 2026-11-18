/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  safelist: [
    'md:col-span-4',
    'md:col-span-6',
    'md:col-span-8',
    'md:col-span-12',
    'lg:col-span-4',
    'lg:col-span-5',
    'lg:col-span-6',
    'lg:col-span-7',
    'lg:col-span-8',
    'lg:col-span-12',
  ],
  theme: {
    extend: {
      colors: {
        marble: {
          DEFAULT: '#FAF9F5', // Calacatta / Alabaster Marble White
          light: '#FDFCF9',
          dark: '#F3F1EC',   // Soft Limestone
          tint: '#EFECE6'    // Soft Linen
        },
        card: {
          DEFAULT: '#FFFFFF',
          light: '#FDFCFB',
          dark: '#F7F5F0',
          border: '#E5E0D8'
        },
        charcoal: {
          DEFAULT: '#1C1B19', // Primary Deep Text
          deep: '#121110',
          muted: '#363430'
        },
        stone: {
          DEFAULT: '#3D3A35', // High-contrast, rich warm editorial charcoal (WCAG AAA compliant on marble)
          light: '#6E6A63',
          dark: '#24221F'
        },
        accent: {
          DEFAULT: '#8C6D53', // Muted Bronze / Terracotta
          light: '#B89F88',
          gold: '#D4AF37',   // Luminous warm champagne gold for dark cards
          dark: '#6E523C',
          tint: 'rgba(140, 109, 83, 0.08)'
        },
        sand: {
          DEFAULT: '#E2DDD5', // Subtle Sand Borders
          light: '#EFECE6',
          dark: '#D5CFC5'
        }
      },
      fontFamily: {
        display: ['"Bodoni Moda"', 'Georgia', 'serif'],
        serif: ['"Bodoni Moda"', 'Georgia', 'serif'],
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
      },
      letterSpacing: {
        'ultra-wide': '0.25em',
        'super-wide': '0.35em',
      },
      transitionDuration: {
        '400': '400ms',
        '700': '700ms',
        '1000': '1000ms',
      }
    },
  },
  plugins: [],
}
