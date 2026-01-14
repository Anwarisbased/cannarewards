import React, { ReactNode, useEffect } from 'react';
import { usePage } from '@inertiajs/react';

interface AppLayoutProps {
    children: ReactNode;
    title?: string;
}

interface TenantConfig {
    theme?: {
        primary_color?: string;
        font_family?: string;
        radius?: string;
    };
    copy?: {
        points_label?: string;
        scan_cta?: string;
    };
    features?: {
        referrals_enabled?: boolean;
        age_gate_strict?: boolean;
    };
}

interface AuthProps {
    user?: any;
    tenant?: {
        id: string;
        config: TenantConfig;
    };
}

export default function AppLayout({ children, title = 'CannaRewards' }: AppLayoutProps) {
    const { props } = usePage<{ auth?: AuthProps }>();

    useEffect(() => {
        if (props.auth?.tenant?.config?.theme) {
            const theme = props.auth.tenant.config.theme;

            // Apply theme variables to the root element
            const root = document.documentElement;

            if (theme.primary_color) {
                root.style.setProperty('--primary', theme.primary_color);
            }

            if (theme.font_family) {
                root.style.setProperty('--font-family', theme.font_family);
            }

            if (theme.radius) {
                root.style.setProperty('--radius', theme.radius);
            }
        }
    }, [props.auth?.tenant?.config?.theme]);

    return (
        <div
            className="min-h-screen bg-background"
            style={{
                // Fallback values if CSS variables aren't set yet
                '--primary': props.auth?.tenant?.config?.theme?.primary_color || '#C6A355',
                '--font-family': props.auth?.tenant?.config?.theme?.font_family || 'Inter',
                '--radius': props.auth?.tenant?.config?.theme?.radius || '0.5rem',
            } as React.CSSProperties}
        >
            {/* Fixed Bottom Navigation Bar - The Persistent Shell */}
            <div className="fixed inset-x-0 bottom-0 z-50 border-t bg-background">
                <div className="flex justify-around items-center py-2">
                    <a href="/" className="flex flex-col items-center px-4 py-2 text-sm">
                        <span>Home</span>
                    </a>
                    <a href="/wallet" className="flex flex-col items-center px-4 py-2 text-sm">
                        <span>Wallet</span>
                    </a>
                    <a href="/scan" className="flex flex-col items-center px-4 py-2 text-sm">
                        <span>Scan</span>
                    </a>
                    <a href="/profile" className="flex flex-col items-center px-4 py-2 text-sm">
                        <span>Profile</span>
                    </a>
                </div>
            </div>

            {/* Main Content Area */}
            <main className="pb-16"> {/* Add padding to account for fixed bottom nav */}
                {children}
            </main>
        </div>
    );
}