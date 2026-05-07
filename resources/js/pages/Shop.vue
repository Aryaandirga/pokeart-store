<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { imageUrl } from '@/composables/useImageUrl.js'

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    wishlistedIds: Array,
})

const search         = ref(props.filters?.search || '')
const sort           = ref(props.filters?.sort || 'latest')
const category       = ref(props.filters?.category || '')
const hoveredProduct = ref(null)
const sortOpen       = ref(false)
const mobileFilterOpen = ref(false)

const sortOptions = [
    { value: 'latest',     label: 'Terbaru' },
    { value: 'price_asc',  label: 'Harga: Rendah → Tinggi' },
    { value: 'price_desc', label: 'Harga: Tinggi → Rendah' },
    { value: 'name_asc',   label: 'Nama: A–Z' },
]

watch([sort, category], () => applyFilter())

function applyFilter() {
    mobileFilterOpen.value = false
    router.get('/shop', {
        search:   search.value,
        sort:     sort.value,
        category: category.value,
    }, { preserveState: true, replace: true })
}

function onSearch()    { applyFilter() }
function clearCategory() { category.value = '' }
function clearAll()    { category.value = ''; search.value = ''; sort.value = 'latest'; applyFilter() }

const activeCategory = computed(() =>
    props.categories?.find(c => String(c.id) === String(category.value))?.name || 'Semua Kategori'
)

// Track wishlist secara lokal
const localWishlisted = ref(new Set(props.wishlistedIds || []))

watch(() => props.wishlistedIds, (newIds) => {
    localWishlisted.value = new Set(newIds || [])
})

function toggleWishlist(product) {
    if (localWishlisted.value.has(product.id)) {
        localWishlisted.value.delete(product.id)
    } else {
        localWishlisted.value.add(product.id)
    }
    router.post(`/wishlist/${product.id}`, {}, {
        preserveScroll: true,
    })
}

function isWishlisted(productId) {
    return localWishlisted.value.has(productId)
}
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef]">

        <!-- Page Header Banner -->
        <div class="max-w-7xl mx-auto px-4 md:px-6 pt-6 md:pt-8 pb-2">
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-2">Koleksi Kartu</p>
                    <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 tracking-tight" style="font-family:'Sora',sans-serif;">
                        Shop
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">
                        <span class="font-semibold text-[#b96b1c]">{{ products.total }}</span> produk tersedia
                        <span v-if="category" class="ml-2">· {{ activeCategory }}</span>
                    </p>
                </div>
                <div class="hidden md:flex items-center gap-2 text-xs text-gray-400">
                    <Link href="/" class="hover:text-gray-700 transition-colors no-underline">Home</Link>
                    <span>/</span>
                    <span class="text-[#5a9aa4] font-semibold">Shop</span>
                </div>
            </div>
        </div>

        <!-- Mobile Filter Bar -->
        <div class="md:hidden max-w-7xl mx-auto px-4 py-3 flex items-center gap-2">
            <button
                @click="mobileFilterOpen = !mobileFilterOpen"
                class="flex items-center gap-2 bg-white border-2 border-[#8bc5cd] rounded-xl px-4 py-2 text-sm font-bold text-[#5a9aa4] transition-all"
                :class="mobileFilterOpen ? 'bg-[#d4eef1]' : ''"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter
                <span v-if="category || sort !== 'latest' || search" class="w-2 h-2 rounded-full bg-[#b96b1c]"></span>
            </button>
            <!-- Active sort chip -->
            <div class="flex gap-2 overflow-x-auto flex-1 scrollbar-none">
                <button
                    v-for="opt in sortOptions"
                    :key="opt.value"
                    @click="sort = opt.value"
                    class="shrink-0 text-xs px-3 py-1.5 rounded-full border font-medium transition-all"
                    :class="sort === opt.value
                        ? 'bg-[#5a9aa4] text-white border-[#5a9aa4]'
                        : 'bg-white text-gray-500 border-gray-200'"
                >{{ opt.label }}</button>
            </div>
        </div>

        <!-- Mobile Filter Drawer -->
        <Transition name="slide-down">
            <div v-if="mobileFilterOpen" class="md:hidden max-w-7xl mx-auto px-4 pb-4">
                <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] p-4 shadow-md">
                    <!-- Search -->
                    <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-2">Cari Produk</p>
                    <div class="flex gap-2 mb-4">
                        <div class="relative flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="6"/><path stroke-linecap="round" d="M20 20l-3-3"/>
                            </svg>
                            <input
                                v-model="search"
                                @keyup.enter="onSearch"
                                type="text"
                                placeholder="Nama kartu..."
                                class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-2 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:outline-none focus:border-[#5a9aa4] transition-all"
                            />
                        </div>
                        <button @click="onSearch" class="bg-[#5a9aa4] text-white text-xs font-bold px-4 py-2 rounded-xl">Cari</button>
                    </div>
                    <!-- Kategori -->
                    <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-2">Kategori</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button
                            @click="clearCategory"
                            class="text-xs px-3 py-1.5 rounded-full border font-medium transition-all"
                            :class="!category ? 'bg-[#5a9aa4] text-white border-[#5a9aa4]' : 'bg-white text-gray-600 border-gray-200'"
                        >Semua</button>
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="category = String(cat.id)"
                            class="text-xs px-3 py-1.5 rounded-full border font-medium transition-all"
                            :class="category == cat.id ? 'bg-[#5a9aa4] text-white border-[#5a9aa4]' : 'bg-white text-gray-600 border-gray-200'"
                        >{{ cat.name }}</button>
                    </div>
                    <div class="flex gap-2">
                        <button @click="applyFilter" class="flex-1 bg-[#b96b1c] text-white text-sm font-bold py-2.5 rounded-xl">Terapkan</button>
                        <button v-if="search || category || sort !== 'latest'" @click="clearAll" class="text-xs text-[#b96b1c] font-semibold px-4 py-2.5 rounded-xl border border-[#b96b1c]/30">Reset</button>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-4 md:py-8">
            <div class="flex gap-7">

                <!-- ── Sidebar (desktop only) ──────────────────────────── -->
                <aside class="hidden md:block w-56 shrink-0">

                    <!-- Search -->
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] p-4 mb-4 shadow-sm">
                        <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-3">Cari Produk</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="6"/><path stroke-linecap="round" d="M20 20l-3-3"/>
                            </svg>
                            <input
                                v-model="search"
                                @keyup.enter="onSearch"
                                type="text"
                                placeholder="Nama kartu..."
                                class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:outline-none focus:border-[#5a9aa4] focus:ring-2 focus:ring-[#8bc5cd]/30 focus:bg-white transition-all"
                            />
                        </div>
                        <button
                            @click="onSearch"
                            class="w-full mt-2 bg-[#5a9aa4] hover:bg-[#4a8a94] text-white text-xs font-bold py-2 rounded-xl transition-colors"
                        >Cari</button>
                    </div>

                    <!-- Kategori -->
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] p-4 mb-4 shadow-sm">
                        <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-3">Kategori</p>
                        <div class="flex flex-col gap-1">
                            <button
                                @click="clearCategory"
                                class="text-left px-3 py-2 rounded-xl text-sm transition-all font-medium"
                                :class="!category
                                    ? 'bg-[#5a9aa4] text-white shadow-sm'
                                    : 'text-gray-600 hover:bg-[#d4eef1] hover:text-[#5a9aa4]'"
                            >Semua Kategori</button>
                            <button
                                v-for="cat in categories"
                                :key="cat.id"
                                @click="category = String(cat.id)"
                                class="text-left px-3 py-2 rounded-xl text-sm transition-all flex items-center justify-between font-medium"
                                :class="category == cat.id
                                    ? 'bg-[#5a9aa4] text-white shadow-sm'
                                    : 'text-gray-600 hover:bg-[#d4eef1] hover:text-[#5a9aa4]'"
                            >
                                <span>{{ cat.name }}</span>
                                <span class="text-[10px] opacity-60 font-normal">{{ cat.products_count }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] overflow-hidden shadow-sm">
                        <button
                            @click="sortOpen = !sortOpen"
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-[#d4eef1]/40 transition-colors"
                        >
                            <div class="text-left">
                                <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider">Urutkan</p>
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">
                                    {{ sortOptions.find(o => o.value === sort)?.label || 'Terbaru' }}
                                </p>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-[#5a9aa4] transition-transform duration-200"
                                :class="sortOpen ? 'rotate-180' : ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div v-if="sortOpen" class="border-t border-[#8bc5cd]/40 px-2 py-2 flex flex-col gap-1">
                            <button
                                v-for="opt in sortOptions"
                                :key="opt.value"
                                @click="sort = opt.value; sortOpen = false"
                                class="w-full text-left px-3 py-2 rounded-xl text-sm font-medium transition-all"
                                :class="sort === opt.value
                                    ? 'bg-[#5a9aa4] text-white font-bold'
                                    : 'text-gray-600 hover:bg-[#d4eef1] hover:text-[#5a9aa4]'"
                            >{{ opt.label }}</button>
                        </div>
                    </div>

                    <!-- Reset -->
                    <button
                        v-if="search || category || sort !== 'latest'"
                        @click="clearAll"
                        class="w-full mt-3 text-xs text-[#b96b1c] hover:text-[#8a4e12] font-semibold py-2 rounded-xl border border-[#b96b1c]/30 hover:bg-[#fee5a1]/50 transition-all"
                    >✕ Reset Filter</button>
                </aside>

                <!-- ── Product Grid ────────────────────────────────────────── -->
                <div class="flex-1 min-w-0">

                    <!-- Empty state -->
                    <div v-if="products.data.length === 0"
                        class="text-center py-16 md:py-24 bg-white/70 rounded-2xl border-2 border-dashed border-[#8bc5cd]">
                        <div class="text-5xl mb-4">🃏</div>
                        <p class="text-gray-500 font-semibold mb-1">Produk tidak ditemukan</p>
                        <p class="text-gray-400 text-sm mb-4">Coba ubah filter atau kata kunci pencarian</p>
                        <button @click="clearAll" class="text-sm text-[#5a9aa4] hover:text-[#4a8a94] font-bold border border-[#8bc5cd] px-5 py-2 rounded-xl hover:bg-[#d4eef1] transition-all">
                            Reset Semua Filter
                        </button>
                    </div>

                    <!-- Grid -->
                    <div v-else class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                        <Link
                            v-for="product in products.data"
                            :key="product.id"
                            :href="`/shop/${product.slug}`"
                            class="group relative bg-white border-2 rounded-2xl overflow-hidden transition-all duration-300 no-underline"
                            :class="hoveredProduct === product.id
                                ? 'border-[#b96b1c] shadow-xl -translate-y-1'
                                : 'border-[#8bc5cd]/50 hover:border-[#b96b1c] hover:shadow-xl hover:-translate-y-1'"
                            @mouseenter="hoveredProduct = product.id"
                            @mouseleave="hoveredProduct = null"
                        >
                            <!-- Stock badge -->
                            <div class="absolute top-2.5 left-2.5 z-10">
                                <span
                                    class="text-[9px] font-bold px-2 py-0.5 rounded-full border"
                                    :class="product.stock > 0
                                        ? 'bg-green-50 text-green-600 border-green-200'
                                        : 'bg-red-50 text-red-400 border-red-200'"
                                >{{ product.stock > 0 ? 'Tersedia' : 'Habis' }}</span>
                            </div>

                            <!-- Wishlist button -->
                            <button
                                class="absolute top-2.5 right-2.5 z-10 w-7 h-7 rounded-full backdrop-blur-sm border flex items-center justify-center transition-all duration-200"
                                :class="isWishlisted(product.id)
                                    ? 'bg-[#fee5a1] border-[#b96b1c] opacity-100'
                                    : 'bg-white/80 border-gray-200 hover:bg-[#fee5a1] hover:border-[#b96b1c] opacity-0 group-hover:opacity-100'"
                                @click.prevent="toggleWishlist(product)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-colors"
                                    :class="isWishlisted(product.id) ? 'text-[#b96b1c] fill-[#b96b1c]' : 'text-gray-400'"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>

                            <!-- Gambar -->
                            <div class="aspect-square bg-[#d4eef1] flex items-center justify-center overflow-hidden relative">
                                <img
                                    v-if="product.image"
                                    :src="imageUrl(product.image)"
                                    :alt="product.name"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                />
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#8bc5cd] opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>

                                <!-- Overlay hover -->
                                <div class="absolute inset-0 bg-[#5a9aa4]/0 group-hover:bg-[#5a9aa4]/10 transition-all duration-300 flex items-end justify-center pb-3 opacity-0 group-hover:opacity-100">
                                    <span class="bg-[#b96b1c] text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-md translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                        Lihat Detail →
                                    </span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-3 md:p-3.5">
                                <p class="text-[10px] text-[#5a9aa4] uppercase tracking-wider mb-1 font-bold">
                                    {{ product.category?.name }}
                                </p>
                                <p class="text-xs md:text-sm font-semibold text-gray-900 leading-tight mb-2 line-clamp-2">
                                    {{ product.name }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm md:text-base font-extrabold text-[#b96b1c]">
                                        Rp {{ Number(product.price).toLocaleString('id-ID') }}
                                    </p>
                                    <div class="w-7 h-7 rounded-full bg-[#fee5a1] border border-[#b96b1c]/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#b96b1c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="mt-8 md:mt-10">

                        <!-- Mobile: Prev / Page X of Y / Next -->
                        <div class="flex md:hidden items-center justify-between gap-3">
                            <Link
                                :href="products.prev_page_url || '#'"
                                class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold no-underline transition-all border-2"
                                :class="products.current_page > 1
                                    ? 'bg-white border-[#8bc5cd] text-[#5a9aa4] hover:bg-[#d4eef1]'
                                    : 'bg-white/50 border-gray-200 text-gray-300 pointer-events-none'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Prev
                            </Link>

                            <div class="flex-1 text-center">
                                <p class="text-xs text-gray-400">Halaman</p>
                                <p class="text-sm font-extrabold text-gray-900">
                                    {{ products.current_page }}
                                    <span class="text-gray-400 font-normal">/ {{ products.last_page }}</span>
                                </p>
                            </div>

                            <Link
                                :href="products.next_page_url || '#'"
                                class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold no-underline transition-all border-2"
                                :class="products.current_page < products.last_page
                                    ? 'bg-[#5a9aa4] border-[#5a9aa4] text-white hover:bg-[#4a8a94]'
                                    : 'bg-white/50 border-gray-200 text-gray-300 pointer-events-none'"
                            >
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>
                        </div>

                        <!-- Desktop: full page links -->
                        <div class="hidden md:flex items-center justify-center gap-2 flex-wrap">
                            <Link
                                v-for="link in products.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                class="px-3.5 py-2 rounded-xl text-sm no-underline transition-all font-medium"
                                :class="link.active
                                    ? 'bg-[#5a9aa4] text-white font-bold shadow-sm'
                                    : link.url
                                        ? 'bg-white text-gray-600 border border-[#8bc5cd]/60 hover:border-[#5a9aa4] hover:text-[#5a9aa4] hover:bg-[#d4eef1]'
                                        : 'text-gray-300 cursor-not-allowed bg-white/50'"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.group:hover .group-hover\:translate-y-0 {
    transform: translateY(0);
}
.slide-down-enter-active, .slide-down-leave-active {
    transition: all 0.25s ease;
}
.slide-down-enter-from, .slide-down-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { scrollbar-width: none; }
</style>
