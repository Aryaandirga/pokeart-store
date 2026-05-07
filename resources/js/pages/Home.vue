<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import { imageUrl } from '@/composables/useImageUrl.js'

// props tetap
defineProps({
    featuredProducts: Array,
    categories: Array,
})

// data kamu tetap
const marqueeItems = [
    'Scarlet & Violet', 'Paldean Fates', 'Temporal Forces',
    'Twilight Masquerade', 'Shrouded Fable', 'Stellar Crown',
    'Prismatic Evolutions', 'Surging Sparks',
]

// 🔥 TAMBAHAN PARALLAX (jangan hapus yang atas)
const cardRef = ref(null)

function handleMouseMove(e) {
    const card = cardRef.value
    if (!card) return

    const rect = card.getBoundingClientRect()
    const x = e.clientX - rect.left
    const y = e.clientY - rect.top

    const centerX = rect.width / 2
    const centerY = rect.height / 2

    const rotateX = -(y - centerY) / 20
    const rotateY = (x - centerX) / 20

    card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`
}

function resetTilt() {
    const card = cardRef.value
    if (!card) return

    card.style.transform = 'rotateX(0deg) rotateY(0deg) scale(1)'
}
</script>

<template>
    <!-- Hero Section -->
    <section class="w-full px-4 md:px-10 xl:px-20 2xl:px-32 py-10 md:py-20 flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-16">

    <!-- LEFT CONTENT -->
    <div class="w-full max-w-xl">
        
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 bg-[#d4eef1] border border-[#8bc5cd] rounded-full px-4 py-1.5 mb-5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#5a9aa4]"></span>
            <span class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase">
                New Collection 2026
            </span>
        </div>

        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 leading-[1.1] tracking-tight mb-5" style="font-family: 'Sora', sans-serif;">
            Find Your<br>
            <span class="text-[#5a9aa4]">Favourite</span><br>
            <span class="text-[#b96b1c]">Pokemon Card</span>
        </h1>

        <!-- Description -->
        <p class="text-sm md:text-base text-gray-500 leading-relaxed mb-7 max-w-md">
            Koleksi kartu Pokemon terlengkap dengan harga terbaik.
            Dari Base Set hingga koleksi terbaru Scarlet & Violet.
        </p>

        <!-- CTA -->
        <div class="flex items-center gap-3 mb-8">
            <Link
                href="/shop"
                class="bg-[#b96b1c] hover:bg-[#8a4e12] text-white text-sm font-bold px-6 py-3 rounded-xl transition-colors no-underline"
            >
                Shop Now
            </Link>

            <Link
                href="/tracking"
                class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors no-underline flex items-center gap-2"
            >
                Tracking Market
                <span class="w-7 h-7 rounded-[8px] bg-[#d4eef1] border border-[#8bc5cd] flex items-center justify-center text-[#5a9aa4] text-xs">
                    →
                </span>
            </Link>
        </div>

        <!-- Stats -->
        <div class="flex items-center divide-x divide-gray-100 border-t border-gray-100 pt-6">
            <div class="flex-1 pr-4 md:pr-6">
                <p class="text-xl md:text-2xl font-extrabold text-gray-900">500<span class="text-[#b96b1c]">+</span></p>
                <p class="text-xs text-gray-400 mt-1">Jenis Kartu</p>
            </div>

            <div class="flex-1 px-4 md:px-6">
                <p class="text-xl md:text-2xl font-extrabold text-gray-900">50<span class="text-[#b96b1c]">+</span></p>
                <p class="text-xs text-gray-400 mt-1">Set Tersedia</p>
            </div>

            <div class="flex-1 pl-4 md:pl-6">
                <p class="text-xl md:text-2xl font-extrabold text-gray-900">1k<span class="text-[#b96b1c]">+</span></p>
                <p class="text-xs text-gray-400 mt-1">Pelanggan</p>
            </div>
        </div>
    </div>

<!-- RIGHT IMAGE -->
<div class="relative hidden lg:flex items-center justify-center flex-shrink-0 [perspective:1200px]">

    <!-- Background box (dibesarkan) -->
    <div class="w-[520px] h-[520px] bg-[#d4eef1] rounded-[32px] border-2 border-[#8bc5cd] relative overflow-hidden flex items-center justify-center">
        
        <!-- Pattern -->
        <div class="absolute inset-0 opacity-30"
            style="background-image: radial-gradient(circle, #5a9aa4 1px, transparent 1px); background-size: 22px 22px;">
        </div>

        <!-- Glow (dibesarkan & lebih soft) -->
        <div class="absolute w-[420px] h-[420px] bg-[#5a9aa4]/20 rounded-full blur-3xl"></div>

        <!-- MAIN CARD (dibesarkan) -->
        <div 
            ref="cardRef"
            @mousemove="handleMouseMove"
            @mouseleave="resetTilt"
            class="relative z-10 bg-[#fdf8ef] rounded-[28px] border-2 border-[#8bc5cd] w-[320px] h-[400px] flex items-center justify-center p-5 shadow-xl transition-transform duration-300 will-change-transform"
        >
            <div class="w-full h-full overflow-hidden rounded-xl border border-[#e5e7eb] bg-[#fee5a1] group relative">
                <img 
                    src="https://images.pokemoncard.io/images/sv7/sv7-148_hiresopt.jpg" 
                    alt="Produk" 
                    class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-110"
                    loading="lazy"
                />
            </div>
        </div>

        <!-- FLOATING CARD LEFT (dibesarkan + posisi lebih keluar) -->
        <div class="absolute left-6 top-16 w-[130px] h-[180px] bg-white rounded-xl border border-gray-200 shadow-lg flex items-center justify-center animate-float-slow rotate-[-6deg]">
            <img 
                src="https://images.pokemoncard.io/images/me2pt5/me2pt5-125_hiresopt.jpg"
                class="w-full h-full object-contain p-2"
            />
        </div>

        <!-- FLOATING CARD RIGHT -->
        <div class="absolute right-6 bottom-16 w-[130px] h-[180px] bg-white rounded-xl border border-gray-200 shadow-lg flex items-center justify-center animate-float rotate-[6deg]">
            <img 
                src="https://images.pokemoncard.io/images/sv2/sv2-50.png"
                class="w-full h-full object-contain p-2"
            />
        </div>

        <!-- Badge top -->
        <div class="absolute top-6 right-6 bg-white rounded-[14px] px-4 py-2.5 border border-[#8bc5cd] shadow-sm z-20">
            <p class="text-[11px] font-bold text-gray-900">⭐ Top Rated</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Squirtle</p>
        </div>

        <!-- Badge bottom -->
        <div class="absolute bottom-6 left-6 bg-white rounded-[14px] px-4 py-2.5 border border-[#8bc5cd] shadow-sm z-20">
            <p class="text-[10px] text-gray-400">Harga Mulai</p>
            <p class="text-[16px] font-extrabold text-[#b96b1c]">Rp 25.000</p>
        </div>
    </div>
</div>
</section>

    <!-- Marquee Banner -->
    <div class="bg-[#b96b1c] py-6 md:py-10 overflow-hidden">
        <div class="flex gap-0 animate-marquee whitespace-nowrap">
            <template v-for="i in 3" :key="i">
                <span
                    v-for="item in marqueeItems"
                    :key="`${i}-${item}`"
                    class="text-[11px] font-bold text-[#fee5a1] tracking-[2.5px] uppercase mx-5"
                >
                    {{ item }} <span class="text-white/30 mx-3">·</span>
                </span>
            </template>
        </div>
    </div>

    <!-- Sisanya (Categories, Products, CTA) tetap sama, hanya update warna -->
    <!-- Categories -->
    <!-- ================= CATEGORIES ================= -->
<section class="w-full px-4 md:px-10 xl:px-20 2xl:px-32 py-12 md:py-16">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 md:mb-10">
        <div>
            <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-2">Browse</p>
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Kategori</h2>
        </div>

        <Link href="/shop" class="text-sm font-medium text-gray-400 hover:text-gray-900 transition-colors no-underline">
            Lihat semua →
        </Link>
    </div>

    <!-- Content -->
    <div v-if="categories?.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 md:gap-5">
        <Link
            v-for="cat in categories"
            :key="cat.id"
            :href="`/shop?category=${cat.id}`"
            class="bg-[#d4eef1] hover:bg-[#5a9aa4] group rounded-xl p-4 md:p-5 transition-all duration-200 no-underline hover:shadow-sm"
        >
            <p class="text-sm font-bold text-gray-900 group-hover:text-white transition-colors">
                {{ cat.name }}
            </p>
            <p class="text-xs text-gray-500 group-hover:text-sky-100 transition-colors mt-1">
                {{ cat.products_count }} produk
            </p>
        </Link>
    </div>

    <!-- Skeleton -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 md:gap-5">
        <div v-for="i in 4" :key="i" class="bg-gray-50 rounded-xl p-5 animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
        </div>
    </div>
</section>


<!-- ================= FEATURED PRODUCTS ================= -->
<section class="w-full px-4 md:px-10 xl:px-20 2xl:px-32 pb-16 md:pb-20">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 md:mb-10">
        <div>
            <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-2">Terbaru</p>
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Produk Unggulan</h2>
        </div>

        <Link href="/shop" class="text-sm font-medium text-gray-400 hover:text-gray-900 transition-colors no-underline">
            Lihat semua →
        </Link>
    </div>

    <!-- Products -->
    <div v-if="featuredProducts?.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 md:gap-6">
        <Link
            v-for="product in featuredProducts"
            :key="product.id"
            :href="`/shop/${product.slug}`"
            class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-md hover:border-[#8bc5cd] transition-all duration-200 no-underline"
        >
            <!-- Image -->
            <div class="aspect-square bg-[#d4eef1] flex items-center justify-center overflow-hidden">
                <img
                    v-if="product.image"
                    :src="imageUrl(product.image)"
                    :alt="product.name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    loading="lazy"
                />
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#8bc5cd] opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Info -->
            <div class="p-4">
                <p class="text-[10px] text-[#8bc5cd] uppercase tracking-wider mb-1 font-semibold">
                    {{ product.category?.name }}
                </p>
                <p class="text-sm font-semibold text-gray-900 leading-tight mb-2 line-clamp-2">
                    {{ product.name }}
                </p>
                <p class="text-sm font-bold text-[#b96b1c]">
                    Rp {{ Number(product.price).toLocaleString('id-ID') }}
                </p>
            </div>
        </Link>
    </div>

    <!-- Empty -->
    <div v-else class="text-center py-16 bg-[#d4eef1]/30 rounded-2xl">
        <p class="text-gray-400 text-sm">Belum ada produk yang dipublish.</p>
    </div>
</section>


<!-- ================= CTA ================= -->
<section class="w-full px-4 md:px-10 xl:px-20 2xl:px-32 pb-12 md:pb-20">
    
    <div class="bg-[#5a9aa4] rounded-2xl px-5 md:px-10 py-8 md:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 border-b-4 border-[#b96b1c]">

        <!-- Text -->
        <div class="max-w-lg">
            <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-2">
                Pantau Harga Pasar Pokemon
            </h2>
            <p class="text-sky-100 text-sm">
                Lihat tren harga kartu Pokemon terkini sebelum membeli atau menjual.
            </p>
        </div>

        <!-- Button -->
        <Link
            href="/tracking"
            class="flex-shrink-0 bg-[#fee5a1] text-[#8a4e12] text-sm font-bold px-6 py-3 rounded-xl hover:bg-[#f5c84a] transition-colors no-underline border border-[#f5c84a]"
        >
            Cek Tracking Market →
        </Link>
    </div>

</section>
</template>

<style scoped>
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-33.333%); }
}
.animate-marquee {
    animation: marquee 20s linear infinite;
}
</style>