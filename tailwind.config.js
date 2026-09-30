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
        ink: "#111111",
        paper: "#ffffff",
        muted: "#6b7280",
        gold: { DEFAULT: "#c9a227", soft: "#e6c65c" }
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"],
        display: ["Inter", "sans-serif"]
      },
      maxWidth: { shell: "1280px" }
    }
  },
  plugins: []
};
