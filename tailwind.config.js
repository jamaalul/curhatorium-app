import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                bricolage: ['"Bricolage Grotesque"', 'sans-serif'],
                dm: ['"DM Sans"', 'sans-serif'],
            },
            colors: {
                primary: {
                    500: '#48A6A6',
                    600: '#357979',
                },
                secondary: {
                    50: '#FEFCE8',
                    300: '#f8c932',
                    600: '#CA8A04',
                },
                base: {
                    0: '#FFFFFF',
                    50: '#FFFFFF',
                    100: '#F3F4F6',
                    200: '#E5E7EB',
                    300: '#D1D5DB',
                    500: '#6B7280',
                    900: '#222222',
                    950: '#030712',
                },
                'text-secondary': '#6B7280',
            },
        },
    },

    plugins: [forms, typography],
};
