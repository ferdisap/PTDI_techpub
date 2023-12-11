/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.xsl",
  ],
  theme: {
    extend: {},
    fontFamily: {
        'tahoma': ['tahoma', 'system-ui']
    }
  },
  plugins: [],
}