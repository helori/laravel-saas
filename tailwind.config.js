const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

module.exports = {
    theme: {
        /*screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            //xl: '1280px',
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
        },
        extend: {
            fontFamily: {
                sans: ['Inter', 'Nunito', ...defaultTheme.fontFamily.sans],
            },
        }
    },
    purge: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: false, // or 'media' or 'class'
    variants: {
        backgroundColor: ['hover', 'focus'],
    },
    plugins: [
        require('@tailwindcss/forms'), 
        require('@tailwindcss/typography')
    ]
}
