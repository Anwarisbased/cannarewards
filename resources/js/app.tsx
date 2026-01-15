import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.tsx`, import.meta.glob('./Pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        // Make Ziggy's route function globally available
        // The Ziggy configuration is provided by the @routes directive in the Blade template
        window.route = (...args) => {
            // Get the Ziggy configuration from the global window object
            const ziggyConfig = window?.Ziggy || null;
            return route(...args, ziggyConfig);
        };

        return root.render(<App {...props} />);
    },
    progress: {
        color: '#4F46E5',
    },
});