const colors = require('tailwindcss/colors');

module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.vue',
    ],
    theme: {
        colors: {
            transparent: colors.transparent,
            current: colors.current,
            black: colors.black,
            white: colors.white,
            gray: colors.gray,
            trueGray: {
                ... colors.neutral,
                850: '#1d1d1d',
            },
            red: colors.red,
            green: colors.emerald,
            blue: colors.blue,
        },
        extend: {
            spacing: {
                '3/4': '75%',
                '9/16': '56.25%',
            },
            boxShadow: {
                'inner-white-top': 'inset 0 1px 0 rgba(255, 255, 255, 0.15)',
            },
            ringWidth: {
                6: '6px',
            },
        },
        container: {
            padding: '1rem',
            center: true,
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/line-clamp'),
    ],
};
