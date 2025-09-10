/** @type {import('tailwindcss').Config} */

module.exports = {
  content: [
    "./app/Reports/**/*.php",
    __dirname + "/resources/views/**/*.blade.php",
    "./packages/blinkerboy/src/Report/resources/views/**/*.blade.php",
    "./resources/views/**/*.blade.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        Inter: ["Inter", "sans-serif"],
      },
    },
  },
  plugins: [],
  // input file location
}

