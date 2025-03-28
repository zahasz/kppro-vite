const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f1f5fb',
                    100: '#dbe4f2',
                    200: '#bccce7',
                    300: '#93add7',
                    400: '#6585c1',
                    500: '#4668ac',
                    600: '#354f91',
                    700: '#2c4076',
                    800: '#273660',
                    900: '#1f2a49',
                    950: '#131b36',
                },
                dark: {
                    50: '#f5f7fa',
                    100: '#ebeef3',
                    200: '#d8dde7',
                    300: '#b0b9c9',
                    400: '#8895af',
                    500: '#677795',
                    600: '#525f7a',
                    700: '#3e4a63',
                    800: '#2e3850',
                    900: '#1e263c',
                    950: '#121828',
                },
                accent: {
                    blue: '#3361cc',
                    cyan: '#3894a3',
                    teal: '#358f80',
                    green: '#38a169',
                    amber: '#d69e2e',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
