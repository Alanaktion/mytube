const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            boxShadow: {
                'inner-white-top': 'inset 0 1px 0 rgba(255, 255, 255, 0.15)',
            },
            colors: {
                neutral: {
                    850: '#1d1d1d',
                },
                primary: colors.sky,
            },
        },
    },
    corePlugins: {
        container: false,
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/line-clamp'),
    ],
};
