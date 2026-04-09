import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import plugin from 'tailwindcss/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#93452d',
                'primary-container': '#b25d43',
                'primary-fixed': '#ffdbd1',
                'primary-fixed-dim': '#ffb59f',
                'on-primary': '#ffffff',
                'on-primary-container': '#fffcff',
                'on-primary-fixed': '#3a0a00',
                'on-primary-fixed-variant': '#78311a',

                secondary: '#725951',
                'secondary-container': '#fad8cf',
                'secondary-fixed': '#fddbd1',
                'secondary-fixed-dim': '#e0bfb6',
                'on-secondary': '#ffffff',
                'on-secondary-container': '#765d55',
                'on-secondary-fixed': '#291712',
                'on-secondary-fixed-variant': '#58413b',

                tertiary: '#755700',
                'tertiary-container': '#936f00',
                'tertiary-fixed': '#ffdf9a',
                'tertiary-fixed-dim': '#f3bf37',
                'on-tertiary': '#ffffff',
                'on-tertiary-container': '#fffbff',
                'on-tertiary-fixed': '#251a00',
                'on-tertiary-fixed-variant': '#5a4300',

                surface: '#fef8f3',
                'surface-dim': '#ded9d4',
                'surface-bright': '#fef8f3',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f8f3ee',
                'surface-container': '#f2ede8',
                'surface-container-high': '#ece7e2',
                'surface-container-highest': '#e6e2dd',
                'surface-tint': '#96472f',
                'surface-variant': '#e6e2dd',
                'on-surface': '#1d1b19',
                'on-surface-variant': '#55433e',

                error: '#ba1a1a',
                'error-container': '#ffdad6',
                'on-error': '#ffffff',
                'on-error-container': '#93000a',

                outline: '#88726d',
                'outline-variant': '#dac1ba',

                'inverse-surface': '#32302d',
                'inverse-on-surface': '#f5f0eb',
                'inverse-primary': '#ffb59f',

                background: '#fef8f3',
                'on-background': '#1d1b19',
            },
            fontFamily: {
                headline: ['Noto Serif', ...defaultTheme.fontFamily.serif],
                body: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                label: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                DEFAULT: '0.25rem',
                sm: '0.25rem',
                md: '0.5rem',
                lg: '0.75rem',
                xl: '1rem',
                '2xl': '1.5rem',
                '3xl': '2rem',
                full: '9999px',
            },
            boxShadow: {
                ambient: '0 8px 40px -5px rgba(29, 27, 25, 0.06)',
                'ambient-lg': '0 12px 48px -8px rgba(29, 27, 25, 0.08)',
            },
        },
    },

    plugins: [
        forms,
        plugin(function ({ addUtilities }) {
            addUtilities({
                '.ghost-border': {
                    border: '1px solid rgba(218, 193, 186, 0.2)',
                },
                '.glass-nav': {
                    backgroundColor: 'rgba(254, 248, 243, 0.8)',
                    backdropFilter: 'blur(20px)',
                    WebkitBackdropFilter: 'blur(20px)',
                },
                '.cta-gradient': {
                    backgroundImage: 'linear-gradient(135deg, #93452d, #b25d43)',
                },
            });
        }),
    ],
};
