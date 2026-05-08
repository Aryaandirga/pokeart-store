<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const cartCount = computed(() => page.props.cartCount || 0)
const mobileOpen = ref(false)
const userMenuOpen = ref(false)

// Tutup dropdown saat klik di luar
function handleClickOutside(e) {
    if (!e.target.closest('.user-menu')) {
        userMenuOpen.value = false
    }
}
onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

const navLinks = [
    { name: 'Home', href: '/' },
    { name: 'Shop', href: '/shop' },
    { name: 'Tracking Market', href: '/tracking' },
    { name: 'Contact Us', href: '/contact' },
]

const isActive = (href) => {
    if (href === '/') return page.url === '/'
    return page.url.startsWith(href)
}

const searchOpen = ref(false)
const searchQuery = ref('')

function submitSearch() {
    if (!searchQuery.value.trim()) return
    window.location.href = `/shop?search=${encodeURIComponent(searchQuery.value.trim())}`
}

function openSearch() {
    searchOpen.value = true
    setTimeout(() => document.getElementById('navbar-search-input')?.focus(), 50)
}
</script>

<template>
    <nav class="bg-[#5a9aa4] border-b-[3px] border-[#b96b1c] sticky top-0 z-50">
    <div class="w-full px-6 md:px-10 xl:px-16">
        <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2.5 no-underline group">
                    <div class="w-9 h-9 bg-[#fee5a1] border-2 border-[#b96b1c] rounded-[10px] flex items-center justify-center">
                        <img
                            src="/images/logo-white-genggar.png"
                            alt="PokéArth"
                            class="h-6 w-auto"
                        />
                    </div>
                    <span class="font-extrabold text-[18px] tracking-tight leading-none" style="font-family: 'Sora', sans-serif;">
                        <span class="text-[#fee5a1]">Poké</span><span class="text-white">Arth</span>
                    </span>
                </Link>

                <!-- Nav Links Desktop -->
                <div class="hidden md:flex items-center gap-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="text-[13.5px] font-medium px-3.5 py-1.5 rounded-[8px] transition-all no-underline"
                        :class="isActive(link.href)
                            ? 'bg-[#fee5a1] text-[#8a4e12] font-bold'
                            : 'text-white/80 hover:bg-white/10 hover:text-white'"
                    >
                        {{ link.name }}
                    </Link>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-2">
                    <!-- Search -->
                    <div class="relative">
                        <button
                            @click="openSearch"
                            class="w-9 h-9 rounded-[9px] bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="6"/>
                                <path stroke-linecap="round" d="M20 20l-3-3"/>
                            </svg>
                        </button>

                        <!-- Search Dropdown -->
                        <div
                            v-if="searchOpen"
                            class="absolute right-0 top-12 w-[calc(100vw-2rem)] max-w-xs sm:w-72 bg-white rounded-xl shadow-xl border border-[#8bc5cd] p-3 z-50"
                        >
                            <form @submit.prevent="submitSearch" class="flex gap-2">
                                <input
                                    id="navbar-search-input"
                                    v-model="searchQuery"
                                    @keydown.escape="searchOpen = false"
                                    type="text"
                                    placeholder="Cari kartu Pokemon..."
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#5a9aa4] focus:ring-2 focus:ring-[#8bc5cd]/30 transition-all"
                                />
                                <button
                                    type="submit"
                                    class="bg-[#5a9aa4] hover:bg-[#4a8a94] text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                                >Cari</button>
                            </form>
                            <p class="text-[10px] text-gray-400 mt-2 px-1">Tekan Esc untuk tutup · Enter untuk cari</p>
                        </div>

                        <!-- Overlay tutup search -->
                        <div v-if="searchOpen" @click="searchOpen = false" class="fixed inset-0 z-40"></div>
                    </div>

                    <!-- Cart -->
                    <Link href="/cart" class="relative w-9 h-9 rounded-[9px] bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#b96b1c] text-white text-[9px] font-bold rounded-full flex items-center justify-center border-[1.5px] border-[#5a9aa4]">
                            {{ cartCount }}
                        </span>
                    </Link>

                    <div class="w-px h-5 bg-white/20"></div>

                    <!-- Guest -->
                    <template v-if="!user">
                        <Link href="/login" class="text-sm font-semibold text-white border border-white/30 hover:bg-white/10 hover:border-white px-4 py-1.5 rounded-[9px] transition-all no-underline">
                            Login
                        </Link>
                        <Link href="/register" class="text-sm font-bold text-[#8a4e12] bg-[#fee5a1] hover:bg-[#f5c84a] px-4 py-1.5 rounded-[9px] border border-[#f5c84a] transition-colors no-underline">
                            Register →
                        </Link>
                    </template>

                    <!-- Logged In -->
                    <template v-else>
                        <div class="relative user-menu">
                            <button
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-[9px] hover:bg-white/10 transition-colors"
                            >
                                <div class="w-8 h-8 rounded-full bg-[#fee5a1] flex items-center justify-center text-[#8a4e12] font-bold text-xs">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div
                                v-if="userMenuOpen"
                                class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50 overflow-hidden"
                            >
                                <Link href="/profile" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-[#d4eef1] hover:text-[#5a9aa4] transition-colors no-underline font-medium">
                                    Profile
                                </Link>
                                <Link href="/orders" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-[#d4eef1] hover:text-[#5a9aa4] transition-colors no-underline font-medium">
                                    My Orders
                                </Link>
                                <Link href="/wishlist" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-[#d4eef1] hover:text-[#5a9aa4] transition-colors no-underline font-medium">
                                    Wishlist
                                </Link>
                                <div class="border-t border-gray-100 my-1"></div>
                                <Link href="/logout" method="post" as="button" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-[#b96b1c] hover:bg-orange-50 font-medium transition-colors no-underline">
                                    Logout
                                </Link>
                            </div>
                        </div>
                    </template>

                    <!-- Mobile Toggle -->
                    <button class="md:hidden w-9 h-9 rounded-[9px] bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors" @click="mobileOpen = !mobileOpen">
                        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileOpen" class="md:hidden border-t border-white/20 py-3 flex flex-col gap-1">
                <Link
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    @click="mobileOpen = false"
                    class="px-3.5 py-2.5 rounded-[9px] text-sm no-underline transition-colors font-medium"
                    :class="isActive(link.href)
                        ? 'bg-[#fee5a1] text-[#8a4e12] font-bold'
                        : 'text-white/80 hover:bg-white/10 hover:text-white'"
                >
                    {{ link.name }}
                </Link>
            </div>
        </div>
    </nav>
</template>