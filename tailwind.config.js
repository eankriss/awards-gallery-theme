module.exports = {
  content: [
    "./*.php",
    "./inc/**/*.php",
    "./template-parts/**/*.php",
    "./assets/js/**/*.js",
    "./assets/js/**/*.php",
    "./functions.php"
  ],
  theme: {
    extend: {
      colors: {
        ink: { DEFAULT: "#0A0908", 900: "#060504", 800: "#141210", 700: "#1C1A17" },
        gold: { DEFAULT: "#DA9328", dark: "#9A6B2F", light: "#E6A43F" },
        cream: { DEFAULT: "#F5F0E8", 200: "#F4F1E9", 300: "#E7E3DA" }
      },
      fontFamily: {
        sans: ["Rubik", "ui-sans-serif", "system-ui", "sans-serif"],
        script: ['"Great Vibes"', "cursive"],
        display: ['"Pinyon Script"', "cursive"],
        outfit: ["Outfit", "ui-sans-serif", "system-ui", "sans-serif"]
      },
      letterSpacing: { eyebrow: "0.35em" },
      backgroundImage: {
        "gold-gradient": "linear-gradient(106deg, rgba(218, 147, 40, 0.9) 31%, #FFC93A 100%)",
        "gold-sheen": "linear-gradient(115deg, #DC952A 0%, #F0B833 45%, #DC952A 100%)",
        "gold-line": "linear-gradient(90deg, #DA9328 16%, #FFC93A 71%)",
        "ink-sheen": "linear-gradient(180deg, #0A0908 0%, #111009 50%, #0A0908 100%)"
      },
      maxWidth: { shell: "1440px" },
      keyframes: {
        rise: { "0%": { opacity: 0, transform: "translateY(24px)" }, "100%": { opacity: 1, transform: "none" } },
        marquee: { "0%": { transform: "translateX(0)" }, "100%": { transform: "translateX(-50%)" } },
        glow: { "0%,100%": { opacity: 0.55 }, "50%": { opacity: 0.8 } },
        "sound-hint": { "0%": { opacity: 0.8, transform: "scale(1)" }, "100%": { opacity: 0, transform: "scale(1.35)" } }
      },
      animation: {
        rise: "rise .9s cubic-bezier(.2,.7,.2,1) both",
        marquee: "marquee 40s linear infinite",
        glow: "glow 6s ease-in-out infinite",
        "sound-hint": "sound-hint 1.6s cubic-bezier(0,0,.2,1) 1s 3 both"
      }
    }
  },
  plugins: []
};
