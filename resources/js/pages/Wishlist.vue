<script setup>
import { Link, router } from '@inertiajs/vue3'
import { imageUrl } from '@/composables/useImageUrl.js'

const props = defineProps({ wishlists: Array })

function remove(productId) {
    router.post(`/wishlist/${productId}`, {}, { preserveScroll: true, preserveState: true })
}
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef] flex flex-col">
        <div class="flex-1 max-w-7xl mx-auto w-full px-4 md:px-6 py-8 md:py-10">

            <!-- Header -->
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Koleksi Saya</p>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight" style="font-family:'Sora',sans-serif;">
                        Wishlist
                    </h1>
                </div>
                <span v-if="wishlists.length > 0" class="text-sm text-gray-400">
                    <span class="font-bold text-[#b96b1c]">{{ wishlists.length }}</span> produk
                </span>
            </div>

            <!-- Empty -->
            <div v-if="wishlists.length === 0"
                class="text-center py-24 bg-white rounded-2xl border-2 border-dashed border-[#8bc5cd]">
                <div class="text-6xl mb-4">🤍</div>
                <p class="text-gray-700 font-bold text-lg mb-1">Wishlist masih kosong</p>
                <p class="text-gray-400 text-sm mb-6">Simpan kartu favoritmu di sini!</p>
                <Link href="/shop"
                    class="bg-[#b96b1c] hover:bg-[#8a4e12] text-white text-sm font-bold px-8 py-3 rounded-xl no-underline transition-colors inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Jelajahi Shop
                </Link>
            </div>

            <!-- Grid -->
            <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-5">
                <div
                    v-for="item in wishlists"
                    :key="item.id"
                    class="group bg-white border-2 border-[#8bc5cd]/50 rounded-2xl overflow-hidden hover:border-[#b96b1c] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
                >
                    <Link :href="`/shop/${item.product.slug}`" class="no-underline block">
                        <div class="aspect-square bg-[#d4eef1] flex items-center justify-center overflow-hidden relative">
                            <img
                                v-if="item.product.image"
                                :src="imageUrl(item.product.image)"
                                :alt="item.product.name"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#8bc5cd] opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-[#5a9aa4]/0 group-hover:bg-[#5a9aa4]/10 transition-all duration-300 flex items-end justify-center pb-3 opacity-0 group-hover:opacity-100">
                                <span class="bg-[#b96b1c] text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-md">
                                    Lihat Detail →
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-[10px] text-[#5a9aa4] uppercase tracking-wider mb-1 font-bold">{{ item.product.category?.name }}</p>
                            <p class="text-sm font-semibold text-gray-900 line-clamp-2 mb-2 leading-tight">{{ item.product.name }}</p>
                            <p class="text-base font-extrabold text-[#b96b1c]">
                                Rp {{ Number(item.product.price).toLocaleString('id-ID') }}
                            </p>
                        </div>
                    </Link>
                    <div class="px-4 pb-4 flex gap-2">
                        <Link :href="`/shop/${item.product.slug}`"
                            class="flex-1 text-xs font-bold text-white bg-[#5a9aa4] hover:bg-[#4a8a94] py-2 rounded-xl transition-colors no-underline text-center">
                            Beli Sekarang
                        </Link>
                        <button
                            @click="remove(item.product.id)"
                            class="w-9 h-9 flex items-center justify-center text-gray-300 hover:text-red-400 hover:bg-red-50 border border-gray-200 hover:border-red-200 rounded-xl transition-all"
                            title="Hapus dari wishlist"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
