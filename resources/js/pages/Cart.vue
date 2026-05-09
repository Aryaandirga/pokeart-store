<script setup>
import { Link, router } from '@inertiajs/vue3'


const props = defineProps({
    cart: Object,
    subtotal: Number,
})

const shippingCost = 25000

function updateQty(item, qty) {
    if (qty < 1) return
    router.patch(`/cart/${item.id}`, { quantity: qty }, { preserveState: true })
}

function removeItem(item) {
    router.delete(`/cart/${item.id}`, { preserveState: true })
}

function checkout() {
    router.get('/checkout')
}
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef]">

        <!-- Header Banner -->
        <div class="bg-[#5a9aa4] border-b-[3px] border-[#b96b1c]">
            <div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-8 flex items-end justify-between">
                <div>
                    <p class="text-[10px] font-bold text-[#fee5a1] tracking-[3px] uppercase mb-1">Belanja</p>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight" style="font-family:'Sora',sans-serif;">
                        Keranjang
                    </h1>
                </div>
                <p v-if="cart.items?.length" class="text-white/70 text-sm">
                    <span class="font-bold text-[#fee5a1]">{{ cart.items.length }}</span> item
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-8">

            <!-- ── Empty Cart ─────────────────────────────────────────────── -->
            <div v-if="!cart.items || cart.items.length === 0"
                class="text-center py-16 md:py-24 bg-white rounded-2xl border-2 border-dashed border-[#8bc5cd]">
                <div class="w-20 h-20 bg-[#d4eef1] rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="text-gray-700 font-bold text-lg mb-1">Keranjang masih kosong</p>
                <p class="text-gray-400 text-sm mb-6">Yuk mulai belanja kartu Pokemon favoritmu!</p>
                <Link
                    href="/shop"
                    class="bg-[#b96b1c] hover:bg-[#8a4e12] text-white text-sm font-bold px-8 py-3 rounded-xl no-underline transition-colors inline-flex items-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Mulai Belanja
                </Link>
            </div>

            <!-- ── Cart Content ───────────────────────────────────────────── -->
            <div v-else class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

                <!-- Items list -->
                <div class="flex-1 w-full flex flex-col gap-3">
                    <div
                        v-for="item in cart.items"
                        :key="item.id"
                        class="flex items-center gap-3 md:gap-4 bg-white border-2 border-[#8bc5cd]/40 rounded-2xl p-3 md:p-4 hover:border-[#5a9aa4] hover:shadow-md transition-all duration-200"
                    >
                        <!-- Image -->
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-[#d4eef1] rounded-xl overflow-hidden shrink-0 flex items-center justify-center border border-[#8bc5cd]/40">
                            <img
    v-if="item.product.image"
    :src="item.product.image?.startsWith('http') ? item.product.image : `/storage/${item.product.image}`"
    :alt="item.product.name"
    class="w-full h-full object-cover"
/>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#8bc5cd]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-[#5a9aa4] uppercase tracking-wider font-bold">{{ item.product.category?.name }}</p>
                            <p class="text-sm font-semibold text-gray-900 truncate mt-0.5">{{ item.product.name }}</p>
                            <p class="text-sm font-bold text-[#b96b1c] mt-1">
                                Rp {{ Number(item.product.price).toLocaleString('id-ID') }}
                            </p>
                        </div>

                        <!-- QTY Control — fix: tombol + dan - bekerja dengan benar -->
                        <div class="flex items-center bg-white border-2 border-[#8bc5cd] rounded-xl overflow-hidden shadow-sm">
                            <button
                                @click="updateQty(item, item.quantity - 1)"
                                class="w-9 h-9 flex items-center justify-center text-[#5a9aa4] hover:bg-[#d4eef1] transition-colors text-lg font-bold"
                            >−</button>
                            <span class="w-10 text-center text-sm font-extrabold text-gray-900 select-none">{{ item.quantity }}</span>
                            <button
                                @click="updateQty(item, item.quantity + 1)"
                                class="w-9 h-9 flex items-center justify-center text-[#5a9aa4] hover:bg-[#d4eef1] transition-colors text-lg font-bold"
                            >+</button>
                        </div>

                        <!-- Subtotal -->
                        <div class="hidden sm:block w-28 md:w-32 text-right shrink-0">
                            <p class="text-[10px] text-gray-400 mb-0.5">Subtotal</p>
                            <p class="text-sm font-extrabold text-gray-900">
                                Rp {{ Number(item.product.price * item.quantity).toLocaleString('id-ID') }}
                            </p>
                        </div>

                        <!-- Delete -->
                        <button
                            @click="removeItem(item)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-400 hover:bg-red-50 transition-all ml-1"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                    <Link href="/shop" class="text-sm text-[#5a9aa4] hover:text-[#4a8a94] font-semibold no-underline mt-2 inline-flex items-center gap-1.5 hover:gap-2.5 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Lanjut Belanja
                    </Link>
                </div>

                <!-- ── Order Summary ──────────────────────────────────────── -->
                <div class="w-full lg:w-80 lg:shrink-0 lg:sticky lg:top-24">
                    <div class="bg-white border-2 border-[#8bc5cd] rounded-2xl overflow-hidden shadow-md">

                        <!-- Summary header -->
                        <div class="bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-6 py-4">
                            <h2 class="text-base font-extrabold text-gray-900">Ringkasan Pesanan</h2>
                        </div>

                        <div class="px-6 py-5">
                            <div class="flex flex-col gap-3 mb-5">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="font-semibold text-gray-900">
                                        Rp {{ Number(subtotal).toLocaleString('id-ID') }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Ongkos Kirim</span>
                                    <span class="font-semibold text-gray-900">
                                        Rp {{ Number(shippingCost).toLocaleString('id-ID') }}
                                    </span>
                                </div>
                                <div class="border-t-2 border-[#8bc5cd]/40 pt-3 flex justify-between items-center">
                                    <span class="text-sm font-bold text-gray-900">Total</span>
                                    <span class="text-xl font-extrabold text-[#b96b1c]" style="font-family:'Sora',sans-serif;">
                                        Rp {{ Number(subtotal + shippingCost).toLocaleString('id-ID') }}
                                    </span>
                                </div>
                            </div>

                            <button
                                @click="checkout"
                                class="w-full bg-[#b96b1c] hover:bg-[#8a4e12] active:scale-[0.99] text-white text-sm font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm"
                            >
                                Lanjut ke Checkout
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            <p class="text-[10px] text-gray-400 text-center mt-3">
                                Ongkir final dihitung saat checkout
                            </p>
                        </div>
                    </div>

                    <!-- Trust badges -->
                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <div v-for="badge in [
                            { icon: '🔒', text: 'Aman & Terpercaya' },
                            { icon: '📦', text: 'Dikemas Rapi' },
                            { icon: '⚡', text: 'Proses Cepat' },
                        ]" :key="badge.text"
                            class="bg-white border border-[#8bc5cd]/40 rounded-xl p-2.5 text-center"
                        >
                            <p class="text-lg mb-1">{{ badge.icon }}</p>
                            <p class="text-[9px] text-gray-500 font-medium leading-tight">{{ badge.text }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
