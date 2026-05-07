<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Navbar from '@/components/Navbar.vue'

defineProps({
    title: String,
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
const cartCount = computed(() => page.props.cartCount || 0)

const waNumber = '6281234567890'
const waMessage = 'Halo PokéArth! Saya ingin bertanya tentang produk.'
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef] relative">
        <!-- Subtle dot pattern background -->
        <div
            class="fixed inset-0 pointer-events-none"
            style="background-image: radial-gradient(circle, #8bc5cd 1px, transparent 1px); background-size: 28px 28px; opacity: 0.18;"
        ></div>
        <Head>
            <meta name="csrf-token" :content="$page.props.csrf_token" />
        </Head>
        <Navbar />
        <main class="relative z-10 pb-16 md:pb-0">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-[#b96b1c] mt-16 relative z-10 hidden md:block">
            <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm font-bold text-[#fee5a1]">
                    PokéArth<span class="text-white">.</span>
                </p>
                <p class="text-xs text-white/70 text-center">© 2025 PokéArth Store. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Terms</a>
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Contact</a>
                </div>
            </div>
        </footer>

        <!-- Mobile Footer -->
        <footer class="bg-[#b96b1c] mt-8 relative z-10 md:hidden">
            <div class="px-6 py-6 flex flex-col items-center gap-3 text-center">
                <p class="text-sm font-bold text-[#fee5a1]">PokéArth<span class="text-white">.</span></p>
                <div class="flex gap-5">
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Terms</a>
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Contact</a>
                </div>
                <p class="text-[10px] text-white/50">© 2025 PokéArth Store. All rights reserved.</p>
            </div>
        </footer>

        <!-- WhatsApp Float Button — hidden on mobile (bottom nav takes space) -->
        <a
            :href="`https://wa.me/${waNumber}?text=${encodeURIComponent(waMessage)}`"
            target="_blank"
            rel="noopener noreferrer"
            class="fixed bottom-6 right-6 z-50 group hidden md:flex items-center gap-3"
        >
            <!-- Tooltip -->
            <div class="opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0 transition-all duration-300 pointer-events-none">
                <div class="bg-gray-900 text-white text-xs font-semibold px-3 py-2 rounded-xl whitespace-nowrap shadow-lg relative">
                    Chat dengan kami 👋
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1.5 w-2.5 h-2.5 bg-gray-900 rotate-45 rounded-sm"></div>
                </div>
            </div>
            <!-- Button -->
            <div class="relative">
                <span class="absolute inset-0 rounded-full bg-[#25D366] animate-ping opacity-30"></span>
                <div class="relative w-14 h-14 bg-[#25D366] hover:bg-[#1ebe5d] rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
            </div>
        </a>

        <!-- ── Mobile Bottom Navigation ─────────────────────────────────── -->
        <nav class="fixed bottom-0 left-0 right-0 z-50 md:hidden bg-white border-t-2 border-[#8bc5cd] shadow-lg">
            <div class="flex items-center justify-around h-16 px-2">
                <!-- Home -->
                <Link href="/" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors no-underline group"
                    :class="$page.url === '/' ? 'text-[#5a9aa4]' : 'text-gray-400 hover:text-[#5a9aa4]'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-[9px] font-bold">Home</span>
                </Link>

                <!-- Shop -->
                <Link href="/shop" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors no-underline"
                    :class="$page.url.startsWith('/shop') ? 'text-[#5a9aa4]' : 'text-gray-400 hover:text-[#5a9aa4]'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="text-[9px] font-bold">Shop</span>
                </Link>

                <!-- Cart (center, highlighted) -->
                <Link href="/cart" class="relative flex flex-col items-center gap-0.5 px-3 py-1 no-underline"
                    :class="$page.url.startsWith('/cart') ? 'text-[#b96b1c]' : 'text-gray-400 hover:text-[#b96b1c]'">
                    <div class="relative -mt-5 w-12 h-12 rounded-full bg-[#b96b1c] flex items-center justify-center shadow-lg border-4 border-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        <span v-if="cartCount > 0" class="absolute -top-1 -right-1 w-4 h-4 bg-[#fee5a1] text-[#8a4e12] text-[9px] font-bold rounded-full flex items-center justify-center border border-[#b96b1c]">
                            {{ cartCount }}
                        </span>
                    </div>
                    <span class="text-[9px] font-bold mt-1">Cart</span>
                </Link>

                <!-- Tracking -->
                <Link href="/tracking" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors no-underline"
                    :class="$page.url.startsWith('/tracking') ? 'text-[#5a9aa4]' : 'text-gray-400 hover:text-[#5a9aa4]'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="text-[9px] font-bold">Market</span>
                </Link>

                <!-- Profile -->
                <Link :href="user ? '/profile' : '/login'" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-colors no-underline"
                    :class="($page.url.startsWith('/profile') || $page.url.startsWith('/login')) ? 'text-[#5a9aa4]' : 'text-gray-400 hover:text-[#5a9aa4]'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-[9px] font-bold">{{ user ? 'Profil' : 'Login' }}</span>
                </Link>
            </div>
        </nav>
    </div>
</template>
