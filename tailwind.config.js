import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    navy: '#120F8B',
                    dark: '#0A0E2E',
                    accent: '#ffa916',
                    'accent-hover': '#ffb738',
                    canvas: '#F4F6F5',
                    border: '#E9EFEF',
                    muted: '#6C7E75',
                },
            },
        },
    },

    plugins: [forms],
};
