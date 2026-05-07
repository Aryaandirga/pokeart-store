<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const navItems = [
    { name: 'Dashboard', href: '/admin', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Produk',    href: '/admin/products', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Orders',   href: '/admin/orders',   icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
]

const isActive = (href) => {
    if (href === '/admin') return page.url === '/admin'
    return page.url.startsWith(href)
}

function logout() { router.post('/logout') }
</script>

<template>
    <div class="min-h-screen flex" style="background: #f8fafc;">

        <!-- Sidebar -->
        <aside class="w-60 flex flex-col fixed h-full z-20 border-r border-gray-100" style="background: #fff;">

            <!-- Logo -->
            <div class="px-6 py-5 border-b border-gray-100">
                <Link href="/" class="no-underline flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-[#fee5a1] border-2 border-[#b96b1c] rounded-lg flex items-center justify-center">
                        <img src="/images/logo-white-genggar.png" alt="" class="h-5 w-auto" onerror="this.style.display='none'" />
                    </div>
                    <div>
                        <p class="text-sm font-extrabold text-gray-900" style="font-family:'Sora',sans-serif;">
                            <span class="text-[#5a9aa4]">Poké</span><span class="text-[#b96b1c]">Arth</span>
                        </p>
                        <p class="text-[9px] text-gray-400 tracking-widest uppercase -mt-0.5">Admin Panel</p>
                    </div>
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-5 flex flex-col gap-1">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-2">Menu</p>
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all no-underline font-medium"
                    :class="isActive(item.href)
                        ? 'bg-[#d4eef1] text-[#5a9aa4] font-bold'
                        : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'"
                >
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                        :class="isActive(item.href) ? 'bg-[#5a9aa4]' : 'bg-gray-100'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                            :class="isActive(item.href) ? 'text-white' : 'text-gray-400'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon"/>
                        </svg>
                    </div>
                    {{ item.name }}
                </Link>
            </nav>

            <!-- Bottom -->
            <div class="px-3 py-4 border-t border-gray-100 flex flex-col gap-1">
                <Link href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:bg-gray-50 transition-colors no-underline font-medium">
                    <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </div>
                    Lihat Website
                </Link>
                <button @click="logout" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-50 transition-colors font-medium">
                    <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    Logout
                </button>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 ml-60">
            <header class="bg-white border-b border-gray-100 px-8 h-14 flex items-center justify-between sticky top-0 z-10">
                <p class="text-sm text-gray-400">
                    Selamat datang, <span class="font-semibold text-gray-700">{{ user?.name }}</span>
                </p>
                <div class="w-8 h-8 rounded-full bg-[#d4eef1] border-2 border-[#8bc5cd] flex items-center justify-center text-[#5a9aa4] font-bold text-xs">
                    {{ user?.name?.charAt(0).toUpperCase() }}
                </div>
            </header>
            <main class="p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
