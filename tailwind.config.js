const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
    mode: 'jit',
    darkMode: 'class',
    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.vue',
    ],
    theme: {
        colors: {
            transparent: defaultTheme.colors.transparent,
            current: defaultTheme.colors.current,
            black: colors.black,
            white: colors.white,
            gray: colors.coolGray,
            trueGray: {
                ... colors.trueGray,
                850: '#1d1d1d',
            },
            red: colors.red,
            green: colors.emerald,
            blue: colors.blue,
        },
        extend: {
            colors: {
            },
            spacing: {
                '3/4': '75%',
                '9/16': '56.25%',
            },
        },
        container: {
            padding: '1rem',
            center: true,
        },
    },
    variants: {
        extend: {
            backgroundOpacity: ['dark'],
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
