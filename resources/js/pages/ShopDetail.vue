<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, computed } from 'vue'
import { imageUrl } from '@/composables/useImageUrl.js'

const page = usePage()

const props = defineProps({
    product: Object,
    related: Array,
})

const qty       = ref(1)
const mainImage = ref(props.product.image)
const added     = ref(false)

function increment() {
    if (qty.value < props.product.stock) qty.value++
}
function decrement() {
    if (qty.value > 1) qty.value--
}

function addToCart() {
    router.post('/cart', {
        product_id: props.product.id,
        quantity: qty.value,
    }, {
        preserveState: true,
        onSuccess: () => {
            added.value = true
            setTimeout(() => { added.value = false }, 2000)
        },
    })
}

// Carousel desktop
const carouselIndex = ref(0)
const itemsPerView  = 4

const carouselMax = computed(() =>
    Math.max(0, (props.related?.length ?? 0) - itemsPerView)
)

function carouselPrev() {
    if (carouselIndex.value > 0) carouselIndex.value--
}
function carouselNext() {
    if (carouselIndex.value < carouselMax.value) carouselIndex.value++
}

// Touch/swipe mobile
const touchStartX = ref(0)

function onTouchStart(e) {
    touchStartX.value = e.touches[0].clientX
}
function onTouchEnd(e) {
    const diff = touchStartX.value - e.changedTouches[0].clientX
    if (diff > 50) carouselNext()
    if (diff < -50) carouselPrev()
}

const totalPrice = computed(() =>
    Number(props.product.price) * qty.value
)

// Wishlist — baca dari shared props supaya persist saat refresh
const wishlisted = computed(() => {
    const ids = page.props.wishlistedIds || []
    return ids.includes(props.product.id)
})

function toggleWishlist() {
    const url = window.location.origin + '/wishlist/' + props.product.id
    axios.post(url, {}, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        }
    })
}
</script>
<template>
    <div class="min-h-screen bg-[#fdf8ef]">

        <!-- Breadcrumb -->
        <div class="bg-[#5a9aa4] border-b-[3px] border-[#b96b1c]">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center gap-2 text-xs text-white/70">
                <Link href="/" class="hover:text-white transition-colors no-underline">Home</Link>
                <span>/</span>
                <Link href="/shop" class="hover:text-white transition-colors no-underline">Shop</Link>
                <span>/</span>
                <span class="text-[#fee5a1] font-semibold truncate max-w-xs">{{ product.name }}</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-10">

            <!-- Product Detail -->
            <div class="flex flex-col md:flex-row gap-6 md:gap-12 mb-10 md:mb-16">

                <!-- Images -->
                <div class="w-full md:w-[460px] md:shrink-0">
                    <div class="aspect-square bg-[#d4eef1] rounded-2xl border-2 border-[#8bc5cd] overflow-hidden mb-3 flex items-center justify-center relative group">
                        <img
                            v-if="mainImage"
                            :src="imageUrl(product.image)"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-[#8bc5cd] opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div v-if="product.images?.length" class="flex gap-2">
                        <button
                            v-for="img in product.images"
                            :key="img.id"
                            @click="mainImage = img.image_path"
                            class="w-16 h-16 rounded-xl overflow-hidden border-2 transition-all"
                            :class="mainImage === img.image_path
                                ? 'border-[#b96b1c] shadow-md scale-105'
                                : 'border-[#8bc5cd]/40 hover:border-[#5a9aa4]'"
                        >
                            <img :src="imageUrl(product.image)" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-1 py-0 md:py-2">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider bg-[#d4eef1] border border-[#8bc5cd] px-3 py-1 rounded-full">
                            {{ product.category?.name }}
                        </span>
                        <span
                            class="text-[10px] font-bold px-3 py-1 rounded-full border"
                            :class="product.stock > 0
                                ? 'bg-green-50 text-green-600 border-green-200'
                                : 'bg-red-50 text-red-400 border-red-200'"
                        >
                            {{ product.stock > 0 ? `Stok: ${product.stock}` : 'Stok Habis' }}
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight mb-2" style="font-family:'Sora',sans-serif;">
                        {{ product.name }}
                    </h1>
                    <p class="text-xs text-gray-400 mb-6">SKU: {{ product.sku }}</p>

                    <div class="bg-linear-to-r from-[#d4eef1] to-[#fdf8ef] border-2 border-[#8bc5cd] rounded-2xl px-4 md:px-6 py-4 mb-6 md:mb-8 inline-block">
                        <p class="text-xs text-[#5a9aa4] font-bold uppercase tracking-wider mb-1">Harga</p>
                        <p class="text-3xl md:text-4xl font-extrabold text-[#b96b1c]" style="font-family:'Sora',sans-serif;">
                            Rp {{ Number(product.price).toLocaleString('id-ID') }}
                        </p>
                    </div>

                    <div v-if="product.description" class="mb-8 bg-white/60 rounded-xl border border-[#8bc5cd]/40 px-5 py-4">
                        <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-2">Deskripsi</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ product.description }}</p>
                    </div>

                    <div v-if="product.stock > 0">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">Jumlah</p>
                        <div class="flex items-center gap-5 mb-6">
                            <div class="flex items-center bg-white border-2 border-[#8bc5cd] rounded-xl overflow-hidden shadow-sm">
                                <button @click="decrement" :disabled="qty <= 1"
                                    class="w-11 h-11 flex items-center justify-center text-[#5a9aa4] hover:bg-[#d4eef1] disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-xl font-bold">−</button>
                                <span class="w-14 text-center text-base font-extrabold text-gray-900 select-none">{{ qty }}</span>
                                <button @click="increment" :disabled="qty >= product.stock"
                                    class="w-11 h-11 flex items-center justify-center text-[#5a9aa4] hover:bg-[#d4eef1] disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-xl font-bold">+</button>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Maks. {{ product.stock }} pcs</p>
                                <p class="text-sm font-bold text-[#b96b1c] mt-0.5">
                                    Total: Rp {{ totalPrice.toLocaleString('id-ID') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="addToCart"
                                class="flex-1 py-3.5 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-sm"
                                :class="added
                                    ? 'bg-green-500 text-white scale-[0.99]'
                                    : 'bg-[#b96b1c] hover:bg-[#8a4e12] text-white hover:shadow-md active:scale-[0.99]'"
                            >
                                <svg v-if="added" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                {{ added ? 'Ditambahkan ke Keranjang!' : 'Tambah ke Keranjang' }}
                            </button>
                            <button
    @click="toggleWishlist"
    class="flex items-center gap-2 px-5 py-3 border-2 rounded-xl font-semibold text-sm transition-all duration-200"
    :class="wishlisted
        ? 'border-red-300 bg-red-50 text-red-500'
        : 'border-gray-200 bg-white text-gray-500 hover:border-red-200 hover:bg-red-50 hover:text-red-400'"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-all duration-200"
        :class="wishlisted ? 'fill-red-500 text-red-500' : 'fill-none'"
        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
    </svg>
    {{ wishlisted ? 'Tersimpan di Wishlist' : 'Tambah ke Wishlist' }}
</button>
                        </div>
                    </div>

                    <div v-else class="bg-red-50 border-2 border-red-200 rounded-xl px-5 py-4">
                        <p class="text-sm text-red-500 font-semibold">Stok sedang habis.</p>
                        <p class="text-xs text-red-400 mt-1">Hubungi kami untuk pre-order.</p>
                    </div>
                </div>
            </div>

            <!-- Related Products Carousel -->
            <div v-if="related && related.length">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-1">Koleksi Serupa</p>
                        <h2 class="text-2xl font-extrabold text-gray-900">Produk Serupa</h2>
                    </div>

                    <!-- Tombol navigasi — desktop only -->
                    <div class="hidden md:flex gap-2">
                        <button
                            @click="carouselPrev"
                            :disabled="carouselIndex === 0"
                            class="w-10 h-10 rounded-xl border-2 flex items-center justify-center transition-all disabled:opacity-30"
                            :class="carouselIndex === 0
                                ? 'border-gray-200 text-gray-300'
                                : 'border-[#8bc5cd] text-[#5a9aa4] hover:bg-[#d4eef1]'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button
                            @click="carouselNext"
                            :disabled="carouselIndex >= carouselMax"
                            class="w-10 h-10 rounded-xl border-2 flex items-center justify-center transition-all disabled:opacity-30"
                            :class="carouselIndex >= carouselMax
                                ? 'border-gray-200 text-gray-300'
                                : 'border-[#8bc5cd] text-[#5a9aa4] hover:bg-[#d4eef1]'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Desktop: carousel dengan tombol -->
                <div class="hidden md:block overflow-hidden">
                    <div
                        class="flex gap-4 transition-transform duration-500 ease-in-out"
                        :style="{ transform: `translateX(calc(-${carouselIndex} * (25% + 4px)))` }"
                    >
                        <Link
                            v-for="item in related"
                            :key="item.id"
                            :href="`/shop/${item.slug}`"
                            class="group bg-white border-2 border-[#8bc5cd]/50 rounded-2xl overflow-hidden hover:border-[#b96b1c] hover:shadow-lg transition-all duration-300 no-underline shrink-0"
                            style="width: calc(25% - 12px)"
                        >
                            <div class="aspect-square bg-[#d4eef1] flex items-center justify-center overflow-hidden">
                                <img v-if="item.image" :src="imageUrl(item.image)" :alt="item.name"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#8bc5cd] opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="p-4">
                                <p class="text-[10px] text-[#5a9aa4] font-bold uppercase tracking-wider mb-1">{{ item.category?.name }}</p>
                                <p class="text-sm font-semibold text-gray-900 line-clamp-2 mb-2">{{ item.name }}</p>
                                <p class="text-base font-extrabold text-[#b96b1c]">Rp {{ Number(item.price).toLocaleString('id-ID') }}</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Mobile: scroll horizontal native -->
                <div class="md:hidden flex gap-3 overflow-x-auto pb-3"
                    style="-webkit-overflow-scrolling: touch; scrollbar-width: none;"
                    @touchstart="onTouchStart"
                    @touchend="onTouchEnd"
                >
                    <Link
                        v-for="item in related"
                        :key="item.id"
                        :href="`/shop/${item.slug}`"
                        class="group bg-white border-2 border-[#8bc5cd]/50 rounded-2xl overflow-hidden hover:border-[#b96b1c] transition-all no-underline shrink-0"
                        style="width: 65vw; scroll-snap-align: start;"
                    >
                        <div class="aspect-square bg-[#d4eef1] flex items-center justify-center overflow-hidden">
                            <img v-if="item.image" :src="imageUrl(item.image)" :alt="item.name"
                                class="w-full h-full object-cover" />
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#8bc5cd] opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="p-4">
                            <p class="text-[10px] text-[#5a9aa4] font-bold uppercase tracking-wider mb-1">{{ item.category?.name }}</p>
                            <p class="text-sm font-semibold text-gray-900 line-clamp-2 mb-2">{{ item.name }}</p>
                            <p class="text-base font-extrabold text-[#b96b1c]">Rp {{ Number(item.price).toLocaleString('id-ID') }}</p>
                        </div>
                    </Link>
                </div>

                <!-- Dots indicator — desktop only -->
                <div v-if="carouselMax > 0" class="hidden md:flex justify-center gap-1.5 mt-4">
                    <button
                        v-for="i in carouselMax + 1"
                        :key="i"
                        @click="carouselIndex = i - 1"
                        class="rounded-full transition-all duration-300"
                        :class="carouselIndex === i - 1
                            ? 'w-6 h-2 bg-[#b96b1c]'
                            : 'w-2 h-2 bg-[#8bc5cd] hover:bg-[#5a9aa4]'"
                    ></button>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Hide scrollbar mobile */
.md\:hidden::-webkit-scrollbar {
    display: none;
}
</style>