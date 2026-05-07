<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ orders: Object })

const hoveredOrder = ref(null)

const statusConfig = {
    pending:    { label: 'Menunggu Pembayaran', bg: 'bg-yellow-50',  text: 'text-yellow-600',  border: 'border-yellow-200',  dot: 'bg-yellow-400',  icon: '⏳' },
    paid:       { label: 'Pembayaran Diterima', bg: 'bg-blue-50',    text: 'text-blue-600',    border: 'border-blue-200',    dot: 'bg-blue-400',    icon: '✅' },
    processing: { label: 'Sedang Diproses',     bg: 'bg-purple-50',  text: 'text-purple-600',  border: 'border-purple-200',  dot: 'bg-purple-400',  icon: '⚙️' },
    shipped:    { label: 'Dalam Pengiriman',     bg: 'bg-indigo-50',  text: 'text-indigo-600',  border: 'border-indigo-200',  dot: 'bg-indigo-400',  icon: '🚚' },
    delivered:  { label: 'Pesanan Selesai',      bg: 'bg-green-50',   text: 'text-green-600',   border: 'border-green-200',   dot: 'bg-green-400',   icon: '🎉' },
    cancelled:  { label: 'Dibatalkan',           bg: 'bg-red-50',     text: 'text-red-500',     border: 'border-red-200',     dot: 'bg-red-400',     icon: '❌' },
}

function getStatus(status) {
    return statusConfig[status] ?? { label: status, bg: 'bg-gray-50', text: 'text-gray-500', border: 'border-gray-200', dot: 'bg-gray-400', icon: '•' }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef] flex flex-col">
        <div class="flex-1 max-w-4xl mx-auto w-full px-4 md:px-6 py-8 md:py-10">

            <!-- Header -->
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Riwayat Belanja</p>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight" style="font-family:'Sora',sans-serif;">
                        Pesanan Saya
                    </h1>
                </div>
                <span v-if="orders.total > 0" class="text-sm text-gray-400">
                    <span class="font-bold text-[#b96b1c]">{{ orders.total }}</span> pesanan
                </span>
            </div>

            <!-- Empty state -->
            <div v-if="orders.data.length === 0"
                class="text-center py-24 bg-white rounded-2xl border-2 border-dashed border-[#8bc5cd]">
                <div class="text-6xl mb-4">📦</div>
                <p class="text-gray-700 font-bold text-lg mb-1">Belum ada pesanan</p>
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

            <!-- Order list -->
            <div v-else class="flex flex-col gap-4">
                <TransitionGroup name="order-list" appear>
                    <Link
                        v-for="(order, i) in orders.data"
                        :key="order.id"
                        :href="`/orders/${order.order_number}`"
                        class="group bg-white border-2 rounded-2xl overflow-hidden no-underline block transition-all duration-300"
                        :class="hoveredOrder === order.id
                            ? 'border-[#b96b1c] shadow-lg -translate-y-0.5'
                            : 'border-[#8bc5cd]/50 hover:border-[#b96b1c] hover:shadow-lg hover:-translate-y-0.5'"
                        :style="{ transitionDelay: `${i * 40}ms` }"
                        @mouseenter="hoveredOrder = order.id"
                        @mouseleave="hoveredOrder = null"
                    >
                        <!-- Top bar status color -->
                        <div class="h-1 w-full transition-all duration-300"
                            :class="getStatus(order.status).dot"
                            :style="{ opacity: hoveredOrder === order.id ? 1 : 0.5 }"
                        ></div>

                        <div class="p-5">
                        <!-- Header row -->
                        <div class="flex items-start justify-between mb-4 gap-2">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">No. Pesanan</span>
                                </div>
                                <p class="text-sm font-extrabold text-gray-900 tracking-tight">{{ order.order_number }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(order.created_at) }}</p>
                            </div>

                            <!-- Status badge -->
                            <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full border text-xs font-bold shrink-0"
                                :class="[getStatus(order.status).bg, getStatus(order.status).text, getStatus(order.status).border]">
                                <span>{{ getStatus(order.status).icon }}</span>
                                <span class="hidden sm:inline">{{ getStatus(order.status).label }}</span>
                            </div>
                        </div>

                            <!-- Products preview -->
                            <div class="flex items-center gap-2 mb-4 py-3 px-3 bg-[#fdf8ef] rounded-xl border border-[#8bc5cd]/30">
                                <!-- Product thumbnails -->
                                <div class="flex -space-x-2">
                                    <div
                                        v-for="(item, idx) in order.items.slice(0, 3)"
                                        :key="item.id"
                                        class="w-9 h-9 rounded-lg bg-[#d4eef1] border-2 border-white overflow-hidden shrink-0"
                                        :style="{ zIndex: 10 - idx }"
                                    >
                                        <img
                                            v-if="item.product?.image"
                                            :src="`/storage/${item.product.image}`"
                                            :alt="item.product.name"
                                            class="w-full h-full object-cover"
                                        />
                                        <div v-else class="w-full h-full flex items-center justify-center text-[#8bc5cd] text-xs">🃏</div>
                                    </div>
                                    <div v-if="order.items.length > 3"
                                        class="w-9 h-9 rounded-lg bg-[#d4eef1] border-2 border-white flex items-center justify-center text-[10px] font-bold text-[#5a9aa4]">
                                        +{{ order.items.length - 3 }}
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0 ml-1">
                                    <p class="text-xs font-semibold text-gray-700 line-clamp-1">
                                        {{ order.items.map(i => i.product?.name).join(', ') }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ order.items.length }} item</p>
                                </div>
                            </div>

                            <!-- Footer row -->
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Total Pembayaran</p>
                                    <p class="text-base font-extrabold text-[#b96b1c]" style="font-family:'Sora',sans-serif;">
                                        Rp {{ Number(order.total).toLocaleString('id-ID') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs font-bold text-[#5a9aa4] group-hover:text-[#b96b1c] transition-colors">
                                    Lihat Detail
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </Link>
                </TransitionGroup>

                <!-- Pagination -->
                <div v-if="orders.last_page > 1" class="flex items-center justify-center gap-2 mt-4">
                    <Link
                        v-for="link in orders.links"
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
</template>

<style scoped>
.order-list-enter-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.order-list-enter-from {
    opacity: 0;
    transform: translateY(16px);
}
</style>
