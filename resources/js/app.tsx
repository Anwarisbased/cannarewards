import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.tsx`, import.meta.glob('./Pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        // Ziggy's route function should be available globally via the @routes directive in the Blade template
        // If not available globally, we'll import it from the ziggy-js package
        if (typeof window.route === 'undefined') {
            // Dynamically import ziggy-js if not available globally
            import('ziggy-js').then(({ route: ziggyRoute }) => {
                // Get Ziggy configuration from the global object (set by @routes directive)
                const ziggyConfig = typeof window.Ziggy !== 'undefined' ? window.Ziggy : undefined;

                // Make the route function available globally
                window.route = (...args) => ziggyRoute(...args, ziggyConfig);
            });
        }

        return root.render(<App {...props} />);
    },
    progress: {
        color: '#4F46E5',
    },
});