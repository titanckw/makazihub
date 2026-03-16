import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Syne', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand - Deep Navy
                navy: {
                    100: '#E2E8F0',
                    400: '#334155',
                    500: '#1E293B',
                    600: '#0F172A',
                },
                // Accent - Emerald
                brand: {
                    100: '#D1FAE5',
                    400: '#34D399',
                    500: '#10B981',
                    600: '#059669',
                },
                // Status
                success: {
                    DEFAULT: '#16A34A',
                    bg: '#DCFCE7',
                },
                warning: {
                    DEFAULT: '#D97706',
                    bg: '#FEF3C7',
                },
                danger: {
                    DEFAULT: '#DC2626',
                    bg: '#FEE2E2',
                },
                info: {
                    DEFAULT: '#2563EB',
                    bg: '#DBEAFE',
                },
                // Neutrals
                surface: '#F8FAFC',
                card: '#FFFFFF',
                border: '#E2E8F0',
                divider: '#CBD5E1',
                // Text
                primary: '#111827',
                secondary: '#475569',
                muted: '#94A3B8',
            },
        },
    },
    plugins: [],
};
