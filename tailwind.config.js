/** @type {import('tailwindcss').Config} */
export default {
  // Direktori yang dipindai Tailwind untuk class
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      // ─── Palet Warna Design System SiMON ───────────────────────
      colors: {
        primary: {
          DEFAULT: "#1E5631",
          50:  "#EDF6EF",
          100: "#D2E9D8",
          200: "#A6D3B1",
          300: "#79BC8A",
          400: "#4B9F63",
          500: "#1E5631",
          600: "#194728",
          700: "#13381F",
          800: "#0D2916",
          900: "#071A0D",
        },
        secondary: {
          DEFAULT: "#15803D",
          50:  "#E7F7EC",
          100: "#C3EDD0",
          200: "#93DFAA",
          300: "#63D184",
          400: "#33BE5E",
          500: "#15803D",
          600: "#116631",
          700: "#0D4D25",
          800: "#08331A",
          900: "#041A0D",
        },
        accent: "#F59E0B",
        success: "#10B981",
        warning: "#F97316",
        danger:  "#EF4444",
        purple:  "#7C3AED",
        muted:   "#64748B",
      },

      // ─── Tipografi ───────────────────────────────────────────────
      fontFamily: {
        heading: ['"Plus Jakarta Sans"', "Inter", "sans-serif"],
        body:    ["Inter", "sans-serif"],
      },

      // ─── Border Radius ───────────────────────────────────────────
      borderRadius: {
        "2xl": "1rem",
        "3xl": "1.5rem",
      },

      // ─── Box Shadow ──────────────────────────────────────────────
      boxShadow: {
        card:    "0 2px 8px rgba(30,86,49,0.08), 0 1px 3px rgba(30,86,49,0.06)",
        "card-hover": "0 8px 24px rgba(30,86,49,0.14), 0 2px 6px rgba(30,86,49,0.08)",
        sidebar: "4px 0 16px rgba(30,86,49,0.12)",
      },

      // ─── Animasi Kustom ──────────────────────────────────────────
      keyframes: {
        // Slide masuk dari atas (untuk toast/notifikasi)
        "slide-down": {
          "0%":   { transform: "translateY(-100%)", opacity: "0" },
          "100%": { transform: "translateY(0)",     opacity: "1" },
        },
        // Slide masuk dari kanan (untuk sidebar mobile)
        "slide-in": {
          "0%":   { transform: "translateX(100%)", opacity: "0" },
          "100%": { transform: "translateX(0)",    opacity: "1" },
        },
        // Fade in sederhana
        "fade-in": {
          "0%":   { opacity: "0" },
          "100%": { opacity: "1" },
        },
        // Scale naik dari kecil (untuk modal/card)
        "scale-up": {
          "0%":   { transform: "scale(0.95)", opacity: "0" },
          "100%": { transform: "scale(1)",    opacity: "1" },
        },
        // Pulse untuk badge/notifikasi penting
        "pulse-soft": {
          "0%, 100%": { opacity: "1" },
          "50%":      { opacity: "0.6" },
        },
        // Progress bar loading
        "progress": {
          "0%":   { width: "0%" },
          "100%": { width: "var(--progress-width, 100%)" },
        },
        // Shimmer loading skeleton
        "shimmer": {
          "0%":   { backgroundPosition: "-200% 0" },
          "100%": { backgroundPosition: "200% 0" },
        },
      },
      animation: {
        "slide-down":  "slide-down 0.3s ease-out",
        "slide-in":    "slide-in 0.25s ease-out",
        "fade-in":     "fade-in 0.25s ease-out",
        "scale-up":    "scale-up 0.2s ease-out",
        "pulse-soft":  "pulse-soft 2s ease-in-out infinite",
        "progress":    "progress 1s ease-out forwards",
        "shimmer":     "shimmer 1.5s linear infinite",
      },

      // ─── Background Gradient Meshes ──────────────────────────────
      backgroundImage: {
        "header-gradient":  "linear-gradient(135deg, #1E5631 0%, #15803D 100%)",
        "sp1-gradient":     "linear-gradient(135deg, #F59E0B 0%, #F97316 100%)",
        "sp2-gradient":     "linear-gradient(135deg, #F97316 0%, #EF4444 100%)",
        "sp3-gradient":     "linear-gradient(135deg, #EF4444 0%, #7C3AED 100%)",
        "success-gradient": "linear-gradient(135deg, #10B981 0%, #15803D 100%)",
        "sidebar-gradient": "linear-gradient(180deg, #1E5631 0%, #163F26 100%)",
      },

      // ─── Transition ──────────────────────────────────────────────
      transitionDuration: {
        250: "250ms",
      },
    },
  },
  plugins: [
    require("@tailwindcss/forms"),
    require("@tailwindcss/typography"),
  ],
};
