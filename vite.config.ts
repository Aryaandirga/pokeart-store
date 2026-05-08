import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.ts',
                'resources/js/pages/Home.vue',
                'resources/js/pages/Shop.vue',
                'resources/js/pages/ShopDetail.vue',
                'resources/js/pages/Cart.vue',
                'resources/js/pages/Checkout.vue',
                'resources/js/pages/CheckoutSuccsess.vue',
                'resources/js/pages/Contact.vue',
                'resources/js/pages/Wishlist.vue',
                'resources/js/pages/MyOrders.vue',
                'resources/js/pages/MyOrderDetail.vue',
                'resources/js/pages/Profile.vue',
                'resources/js/pages/Tracking.vue',
                'resources/js/pages/Forbidden.vue',
                'resources/js/pages/admin/Dashboard.vue',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia(),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wayfinder({
            formVariants: true,
        }),
    ],
});