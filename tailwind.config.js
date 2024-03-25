import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    presets: [
        "./vendor/robsontenorio/mary/src/View/Components/*.php",
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
    ],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php",
        "./vendor/robsontenorio/mary/src/View/Components/*.php",
    ],
/*    safelist: [
        {pattern: /(bg|text|border|grid|table|h|w|p|py|px|pb|pt|pl|pr|m|mb|mt|mx|my|ml|mr|justify|shadow|ring|rounded|absolute|top|left|right|bottom)-./},
        {pattern: /(grid|flex|hidden)./}
    ],*/

    theme: {
        extend: {
            fontSize: {
                sm: '0.750rem',
                base: '1rem',
                xl: '1.333rem',
                '2xl': '1.777rem',
                '3xl': '2.369rem',
                '4xl': '3.158rem',
                '5xl': '4.210rem',
            },
            fontFamily: {
                heading: 'Poppins',
                body: 'Poppins',
            },
            fontWeight: {
                normal: '400',
                bold: '700',
            }
        },
    },
    plugins: [
        typography,
        require("daisyui")
    ],

    daisyui: {
        themes: [
            {
                dark: {
                    "primary": "#4648f5",
                    "secondary": "#72ebf8",
                    "accent": "#00eef3",
                    "neutral": "#08060e",
                    "base-100": "#0b0b13",
                    "base-200": "#1a1a2a",
                    "info": "#008fff",
                    "success": "#2d7c00",
                    "warning": "#b45a00",
                    "error": "#ff6682",
                },
                light: {
                    "primary": "#4648f5",
                    "secondary": "#72ebf8",
                    "accent": "#828c00",
                    "neutral": "#171226",
                    "base-100": "#ececf4",
                    "base-200": "#b9cbc2",
                    "info": "#00beff",
                    "success": "#009800",
                    "warning": "#be6400",
                    "error": "#ff4c5a",
                }
            },
        ],
        darkTheme: "dark", // name of one of the included themes for dark mode
        base: true, // applies background color and foreground color for root element by default
        styled: true, // include daisyUI colors and design decisions for all components
        utils: true, // adds responsive and modifier utility classes
        prefix: "", // prefix for daisyUI classnames (components, modifiers and responsive class names. Not colors)
        logs: true, // Shows info about daisyUI version and used config in the console when building your CSS
        themeRoot: ":root", // The element that receives theme color CSS variables
    },
};
