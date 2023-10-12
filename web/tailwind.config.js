/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'media',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      backgroundImage: (theme) => ({
        'backgroundImage': "url('src/assets/img/BeachFromAbove.jpeg')",
        'LogoLight': "url('src/assets/LogoLight.svg')",
        'LogoDark': "url('src/assets/LogoDark.svg')",
        'HomeIcon': "url('src/assets/icons/Home.svg')",
        'ActiveIcon': "url('src/assets/icons/Active.svg')",
        'HistoryIcon': "url('src/assets/icons/History.svg')",
        'SettingsIcon': "url('src/assets/icons/Settings.svg')",
      }),
    },
    colors: {
      // extend default colors of Tailwind CSS
      ...require('tailwindcss/colors'),

      // Aggiungi i tuoi colori personalizzati
      primary: '#005361',
      hover: '#003B45',
      secondary: '#6C757D',
      success: '#28A745',
      error: '#DC3545',
    },
  },
  variants: {
    extend: {
      backgroundImage: ['dark'],
    },
  },
  plugins: [],
}

