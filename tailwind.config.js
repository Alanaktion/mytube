module.exports = {
  purge: [
    './resources/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        ngray: {
          '100': '#f5f5f5',
          '200': '#eeeeee',
          '300': '#e0e0e0',
          '400': '#bdbdbd',
          '500': '#9e9e9e',
          '600': '#757575',
          '700': '#585858',
          '800': '#3a3a3a',
          '900': '#212121',
        },
      },
    },
    container: {
      padding: '1rem',
      center: true,
    },
  },
  variants: {},
  plugins: [
    require('@tailwindcss/ui'),
  ],
  future: {
    removeDeprecatedGapUtilities: true,
  },
};
