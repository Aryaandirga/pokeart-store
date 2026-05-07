<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    lowStock: Array,
})

const statusConfig = {
    pending:    { label: 'Menunggu',  bg: 'bg-yellow-50', text: 'text-yellow-600', dot: 'bg-yellow-400' },
    paid:       { label: 'Lunas',     bg: 'bg-blue-50',   text: 'text-blue-600',   dot: 'bg-blue-400'   },
    processing: { label: 'Diproses',  bg: 'bg-purple-50', text: 'text-purple-600', dot: 'bg-purple-400' },
    shipped:    { label: 'Dikirim',   bg: 'bg-indigo-50', text: 'text-indigo-600', dot: 'bg-indigo-400' },
    delivered:  { label: 'Selesai',   bg: 'bg-green-50',  text: 'text-green-600',  dot: 'bg-green-400'  },
    cancelled:  { label: 'Batal',     bg: 'bg-red-50',    text: 'text-red-500',    dot: 'bg-red-400'    },
}

function getStatus(s) {
    return statusConfig[s] ?? { label: s, bg: 'bg-gray-50', text: 'text-gray-500', dot: 'bg-gray-400' }
}
</script>

<template>
    <div>
        <!-- Header -->
        <div class="mb-8">
            <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Overview</p>
            <h1 class="text-2xl font-extrabold text-gray-900" style="font-family:'Sora',sans-serif;">Dashboard</h1>
            <p class="text-sm text-gray-400 mt-1">Ringkasan performa toko online kamu.</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border-2 border-[#8bc5cd]/40 rounded-2xl p-5 hover:border-[#5a9aa4] transition-colors">
                <div class="w-9 h-9 bg-[#d4eef1] rounded-xl flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Total Produk</p>
                <p class="text-3xl font-extrabold text-gray-900" style="font-family:'Sora',sans-serif;">{{ stats.total_products }}</p>
                <p class="text-xs text-[#5a9aa4] mt-1 font-semibold">{{ stats.published }} dipublish</p>
            </div>

            <div class="bg-white border-2 border-[#8bc5cd]/40 rounded-2xl p-5 hover:border-[#5a9aa4] transition-colors">
                <div class="w-9 h-9 bg-yellow-50 rounded-xl flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Total Orders</p>
                <p class="text-3xl font-extrabold text-gray-900" style="font-family:'Sora',sans-serif;">{{ stats.total_orders }}</p>
                <p class="text-xs text-yellow-500 mt-1 font-semibold">{{ stats.pending_orders }} menunggu</p>
            </div>

            <div class="bg-white border-2 border-[#8bc5cd]/40 rounded-2xl p-5 hover:border-[#5a9aa4] transition-colors">
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Orders Lunas</p>
                <p class="text-3xl font-extrabold text-gray-900" style="font-family:'Sora',sans-serif;">{{ stats.paid_orders }}</p>
                <p class="text-xs text-green-500 mt-1 font-semibold">Terbayar</p>
            </div>

            <div class="bg-gradient-to-br from-[#5a9aa4] to-[#4a8a94] rounded-2xl p-5 text-white">
                <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-[10px] text-white/70 uppercase tracking-wider mb-1">Total Revenue</p>
                <p class="text-xl font-extrabold text-white leading-tight" style="font-family:'Sora',sans-serif;">
                    Rp {{ Number(stats.total_revenue).toLocaleString('id-ID') }}
                </p>
                <p class="text-xs text-white/60 mt-1">Paid & delivered</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Recent Orders -->
            <div class="bg-white border-2 border-[#8bc5cd]/40 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#d4eef1]/50 to-white">
                    <p class="text-sm font-extrabold text-gray-900">Order Terbaru</p>
                    <Link href="/admin/orders" class="text-xs text-[#5a9aa4] hover:text-[#4a8a94] font-semibold no-underline transition-colors">
                        Lihat semua →
                    </Link>
                </div>
                <div class="divide-y divide-gray-50">
                    <div v-if="recentOrders.length === 0" class="text-center py-10">
                        <p class="text-xs text-gray-400">Belum ada order.</p>
                    </div>
                    <div v-for="order in recentOrders" :key="order.id"
                        class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50/50 transition-colors">
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ order.order_number }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ order.user?.name }} · {{ order.items?.length }} item</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1"
                                :class="[getStatus(order.status).bg, getStatus(order.status).text]">
                                <span class="w-1.5 h-1.5 rounded-full" :class="getStatus(order.status).dot"></span>
                                {{ getStatus(order.status).label }}
                            </span>
                            <p class="text-xs font-bold text-gray-900 mt-1">Rp {{ Number(order.total).toLocaleString('id-ID') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-white border-2 border-[#8bc5cd]/40 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#fee5a1]/40 to-white">
                    <p class="text-sm font-extrabold text-gray-900">⚠️ Stok Menipis</p>
                    <Link href="/admin/products" class="text-xs text-[#5a9aa4] hover:text-[#4a8a94] font-semibold no-underline transition-colors">
                        Lihat semua →
                    </Link>
                </div>
                <div class="divide-y divide-gray-50">
                    <div v-if="lowStock.length === 0" class="text-center py-10">
                        <p class="text-xs text-gray-400">✅ Semua stok aman.</p>
                    </div>
                    <div v-for="product in lowStock" :key="product.id"
                        class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50/50 transition-colors">
                        <p class="text-xs font-semibold text-gray-900 line-clamp-1">{{ product.name }}</p>
                        <span class="text-xs font-bold text-red-500 bg-red-50 px-2.5 py-1 rounded-full border border-red-100">
                            Sisa {{ product.stock }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
