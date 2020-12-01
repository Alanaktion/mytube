const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
    darkMode: 'class',
    purge: [
        './resources/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                trueGray: {
                    ... colors.trueGray,
                    850: '#1d1d1d',
                },
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
