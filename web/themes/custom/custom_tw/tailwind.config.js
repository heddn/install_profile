module.exports = {
  content: [
    '../../../../config/**/component_library.variant.*.yml',
    '../../../../config/**/layout_library.layout.*.yml',
    'templates/**/*.twig',
    'templates/**/**/*.twig',
    'js/messages.js',
  ],
  theme: {
    customForms: (theme) => ({
      default: {
        'input, textarea, multiselect, select': {
          borderRadius: theme('borderRadius.none'),
        },
      },
    }),
    extend: {
      backgroundImage: (theme) => ({
        'messages-status': "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='%234ade80'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' /%3E%3C/svg%3E\")",
        'messages-warning': "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='%23facc15'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01' /%3E%3C/svg%3E\")",
        'messages-error': "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='%23f87185'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /%3E%3C/svg%3E\")",
      }),
      height: {
        '17': '4.25rem',
        '18': '4.5rem',
        '22': '5.5rem',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
