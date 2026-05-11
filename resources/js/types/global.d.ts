import '@inertiajs/core';
import type { Auth } from '@/types/auth';
import type { Team } from '@/types/teams';
import type { Config, Router, RouteParam, RouteParamsWithQueryOverload } from 'ziggy-js';

declare global {
    function route(): Router;
    function route(
        name: string,
        params?: RouteParamsWithQueryOverload | RouteParam,
        absolute?: boolean,
        config?: Config
    ): string;

    const Ziggy: Config;
};

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            currentTeam: Team | null;
            teams: Team[];
            [key: string]: unknown;
        };
    }
}
