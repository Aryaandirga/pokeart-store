<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    order: Object,
})
</script>

<template>
    <div class="max-w-2xl mx-auto px-6 py-16 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Pesanan Berhasil! 🎉</h1>
        <p class="text-gray-400 text-sm mb-8">
            Terima kasih sudah berbelanja di PokéArt. Pesananmu sedang diproses.
        </p>

        <div class="bg-white border border-gray-100 rounded-xl p-6 text-left mb-8">
            <div class="flex justify-between mb-4 pb-4 border-b border-gray-100">
                <div>
                    <p class="text-xs text-gray-400">Nomor Pesanan</p>
                    <p class="text-sm font-bold text-gray-900">{{ order.order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Status</p>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-50 text-yellow-600">
                        {{ order.status === 'paid' ? 'Lunas' : 'Menunggu Pembayaran' }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-2 mb-4">
                <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ item.product.name }} x{{ item.quantity }}</span>
                    <span class="font-semibold">Rp {{ Number(item.subtotal).toLocaleString('id-ID') }}</span>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-3 flex justify-between">
                <span class="text-sm font-bold">Total</span>
                <span class="text-sm font-extrabold text-[#3B6DB5]">
                    Rp {{ Number(order.total).toLocaleString('id-ID') }}
                </span>
            </div>
        </div>

        <div class="flex gap-3 justify-center">
            <Link href="/shop" class="border border-gray-200 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors no-underline">
                Lanjut Belanja
            </Link>
            <Link href="/orders" class="bg-[#3B6DB5] text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-[#2d5a9a] transition-colors no-underline">
                Lihat Pesanan
            </Link>
        </div>
    </div>
</template>