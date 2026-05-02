/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/**/*.php",
    "./public/js/**/*.js",
    "./app/Helpers/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        'elite-gold': '#d4af37',
        'elite-dark': '#111111',
      },
    },
  },
  plugins: [],
}
