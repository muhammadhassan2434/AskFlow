import { route } from 'ziggy-js';

export const sidebarMenu = [
    {
        label: 'Dashboard',
        href: route('dashboard'),
        key: 'dashboard',
        badge: null,
    },
    {
        label: 'Workspaces',
        href: route('workspaces.index'),
        key: 'workspaces',
        badge: null,
    },
    {
        label: 'Bots',
        href: route('bots.index'),
        key: 'bots',
        badge: null,
    },
];