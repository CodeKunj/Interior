/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
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
          DEFAULT: '#6E6A63', // Soft Muted Text
          light: '#8F8B83',
          dark: '#4A4742'
        },
        accent: {
          DEFAULT: '#8C6D53', // Muted Bronze / Terracotta
          light: '#B89F88',
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
