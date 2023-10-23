/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'media',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    /* The `extend` property in the Tailwind CSS configuration file allows you to extend or add new styles
    to the existing default styles provided by Tailwind CSS. */
    extend: {
      backgroundImage: (theme) => ({
        /* The line `backgroundImage': "url('src/assets/img/BeachFromAbove.jpeg')"` is adding a custom
        background image to the Tailwind CSS configuration. It sets the background image for the
        `backgroundImage` class to the image file located at `src/assets/img/BeachFromAbove.jpeg`. */
        'backgroundImage': "url('src/assets/img/BeachFromAbove.jpeg')",
        'LogoLight': "url('src/assets/LogoLight.svg')",
        'LogoDark': "url('src/assets/LogoDark.svg')",
        'HomeIcon': "url('src/assets/icons/Home.svg')",
        'ActiveIcon': "url('src/assets/icons/Active.svg')",
        'HistoryIcon': "url('src/assets/icons/History.svg')",
        'SettingsIcon': "url('src/assets/icons/Settings.svg')",
        'MoneyIcon': "url('src/assets/icons/Money.svg')",
        'DistanceIcon': "url('src/assets/icons/Distance.svg')",
        'ArrowIconDark': "url('src/assets/icons/Arrow.svg')",
        'ArrowIcon': "url('src/assets/icons/Arrow_White.svg')",
        'ArrowDownIcon': "url('src/assets/icons/ArrowDown.svg')",
        'ArrowUpIcon': "url('src/assets/icons/ArrowUp.svg')",
        'ArrowUpDarkIcon': "url('src/assets/icons/ArrowUpDark.svg')",
        'ArrowDownDarkIcon': "url('src/assets/icons/ArrowDownDark.svg')",
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

