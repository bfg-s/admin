/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
      'themes/**/*.php',

  ],
  theme: {
    extend: {},
  },
  plugins: [],
    corePlugins: {
        //preflight: false, // Опционально, если у вас проблемы с глобальными стилями
    },
    screens: {
        sm: '540px', // По умолчанию, для экранов от 640px
        md: '768px',
        lg: '1024px',
        xl: '1280px',
        '2xl': '1536px',
    }
}

