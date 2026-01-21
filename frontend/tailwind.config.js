/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx,vue}'],
  theme: {
    extend: {
      colors: {
        'custom-blue': '#0077b6',
        'custom-dark-blue': '#023e8a',
      },
    },
  },
  plugins: [],
}
