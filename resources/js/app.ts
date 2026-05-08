import { createInertiaApp } from '@inertiajs/vue3';
import axios from 'axios';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import StoreLayout from '@/layouts/StoreLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import AdminLayout from '@/layouts/AdminLayout.vue';

// Global axios config — kirim cookies dan CSRF token di semua request
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
        return pages[`./pages/${name}.vue`]
    },
    layout: (name) => {
    switch (true) {
        case name === 'Welcome':
            return null;
        case name.startsWith('auth/'):
            return AuthLayout;
        case name.startsWith('settings/'):
            return [AppLayout, SettingsLayout];
        case name.startsWith('admin/'):      // ← update ini
            return AdminLayout;
        case name === 'Dashboard':
            return AppLayout;
        default:
            return StoreLayout;
    }
},
    progress: {
        color: '#3B6DB5',
    },
});

initializeTheme();
initializeFlashToast();