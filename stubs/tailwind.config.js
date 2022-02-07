const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

module.exports = {
    theme: {
        /*screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1280px',
        },*/
        colors: {
            // Build your palette here
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.coolGray,
            red: colors.rose,
            blue: colors.sky,
            green: colors.teal,
            yellow: colors.amber,
            orange: colors.orange,
        },
        minHeight: {
            '0': '0',
            '1/4': '25%',
            '1/2': '50%',
            '3/4': '75%',
            'full': '100%',
            '1/4-screen': '25vh',
            '1/2-screen': '50vh',
            '3/4-screen': '75vh',
            'screen': '100vh',
        },
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        }
    },
    purge: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'media', // or 'media' or 'class'
    variants: {
        //backgroundColor: ['hover', 'focus'],
        extend: {
            display: ['dark']
        },
    },
    plugins: [
        require('@tailwindcss/forms'), 
        require('@tailwindcss/typography')
    ]
}
