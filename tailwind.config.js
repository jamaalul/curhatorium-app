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
                    50: '#F0FDFA',
                    100: '#CCFBF1',
                    200: '#96F7E4',
                    300: '#46ECD5',
                    400: '#00D5BE',
                    500: '#00BBA7',
                    600: '#009689',
                    700: '#00786F',
                    800: '#005F5A',
                    900: '#0B4F4A',
                    950: '#022F2E',
                },
                secondary: {
                    50: '#FEFCE8',
                    100: '#FEF9C2',
                    200: '#FFF085',
                    300: '#FFDF20',
                    400: '#FDC700',
                    500: '#F0B100',
                    600: '#D08700',
                    700: '#A65F00',
                },
                base: {
                    0: '#FFFFFF',
                    50: '#FAFAFA',
                    100: '#F4F4F5',
                    200: '#E4E4E7',
                    300: '#D4D4D8',
                    400: '#A1A1AA',
                    500: '#71717A',
                    600: '#52525B',
                    700: '#3F3F46',
                    800: '#27272A',
                    900: '#18181B',
                    950: '#09090B',
                },
                warning: {
                    50: '#FFFBEB',
                    100: '#FEF3C6',
                    300: '#FFD230',
                    500: '#FE9A00',
                    600: '#E17100',
                    700: '#BB4D00',
                },
                danger: {
                    50: '#FEF2F2',
                    100: '#FFE2E2',
                    300: '#FFA2A2',
                    500: '#FB2C36',
                    600: '#E7180B',
                    700: '#C11007',
                },
                success: {
                    50: '#F0FDF4',
                    100: '#DCFCE7',
                    300: '#7BF1A8',
                    500: '#22C55E',
                    600: '#16A34A',
                    700: '#15803D',
                },
                'text-secondary': '#6B7280',
            },
        },
    },

    plugins: [forms, typography],
};
