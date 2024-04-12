/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    presets: [
        require('./vendor/tallstackui/tallstackui/tailwind.config.js')
    ],

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/tallstackui/tallstackui/src/**/*.php',
        './vendor/tallstackui/tallstackui/src/View/Components/*.php',
        './vendor/tallstackui/tallstackui/src/View/Components/**/*.php',
        './vendor/tallstackui/tallstackui/src/resources/views/components/**/*.php',
    ],

    theme: {
        fontSize: {
            xs: ['0.75rem', { lineHeight: '1rem' }],
            sm: ['0.875rem', { lineHeight: '1.5rem' }],
            base: ['1rem', { lineHeight: '1.75rem' }],
            lg: ['1.125rem', { lineHeight: '2rem' }],
            xl: ['1.25rem', { lineHeight: '2rem' }],
            '2xl': ['1.5rem', { lineHeight: '2rem' }],
            '3xl': ['2rem', { lineHeight: '2.5rem' }],
            '4xl': ['2.5rem', { lineHeight: '3.5rem' }],
            '5xl': ['3rem', { lineHeight: '3.5rem' }],
            '6xl': ['3.75rem', { lineHeight: '1' }],
            '7xl': ['4.5rem', { lineHeight: '1.1' }],
            '8xl': ['6rem', { lineHeight: '1' }],
            '9xl': ['8rem', { lineHeight: '1' }],
        },
        extend: {
            borderRadius: {
                '4xl': '2rem',
            },
            fontFamily: {
                heading: 'Poppins',
                body: 'Poppins',
            },
            maxWidth: {
                '2xl': '40rem',
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@headlessui/tailwindcss'),
    ],
};
