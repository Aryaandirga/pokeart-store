<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    orders: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status || '')

watch(status, () => applyFilter())

function applyFilter() {
    router.get('/admin/orders', {
        search: search.value,
        status: status.value,
    }, { preserveState: true, replace: true })
}

function updateStatus(order, newStatus) {
    router.patch(`/admin/orders/${order.id}/status`, { status: newStatus }, { preserveState: true })
}

const statusConfig = {
    pending:    { label: 'Menunggu',  bg: 'bg-yellow-50', text: 'text-yellow-600', border: 'border-yellow-200' },
    paid:       { label: 'Lunas',     bg: 'bg-blue-50',   text: 'text-blue-600',   border: 'border-blue-200'   },
    processing: { label: 'Diproses',  bg: 'bg-purple-50', text: 'text-purple-600', border: 'border-purple-200' },
    shipped:    { label: 'Dikirim',   bg: 'bg-indigo-50', text: 'text-indigo-600', border: 'border-indigo-200' },
    delivered:  { label: 'Selesai',   bg: 'bg-green-50',  text: 'text-green-600',  border: 'border-green-200'  },
    cancelled:  { label: 'Batal',     bg: 'bg-red-50',    text: 'text-red-500',    border: 'border-red-200'    },
}

function getStatus(s) {
    return statusConfig[s] ?? { label: s, bg: 'bg-gray-50', text: 'text-gray-500', border: 'border-gray-200' }
}

const statusOptions = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled']
</script>

<template>
    <div>
        <!-- Header -->
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Manajemen</p>
                <h1 class="text-2xl font-extrabold text-gray-900" style="font-family:'Sora',sans-serif;">Orders</h1>
                <p class="text-sm text-gray-400 mt-1">Kelola pesanan dari website.</p>
            </div>
            <span class="text-sm text-gray-400">
                <span class="font-bold text-[#b96b1c]">{{ orders.total }}</span> order
            </span>
        </div>

        <!-- Filter -->
        <div class="flex gap-3 mb-6">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="6"/><path stroke-linecap="round" d="M20 20l-3-3"/>
                </svg>
                <input v-model="search" @keyup.enter="applyFilter" type="text"
                    placeholder="Cari order / customer..."
                    class="border-2 border-gray-200 rounded-xl pl-9 pr-4 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#5a9aa4] transition-all w-72 bg-white" />
            </div>
            <select v-model="status"
                class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#5a9aa4] bg-white transition-all">
                <option value="">Semua Status</option>
                <option v-for="s in statusOptions" :key="s" :value="s">{{ getStatus(s).label }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white border-2 border-[#8bc5cd]/40 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-100 bg-gradient-to-r from-[#d4eef1]/30 to-white">
                        <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Order</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Customer</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Alamat & HP</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Item</th>
                        <th class="text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Total</th>
                        <th class="text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Status</th>
                        <th class="text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Update</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="orders.data.length === 0">
                        <td colspan="7" class="text-center py-16 text-gray-400 text-sm">
                            <div class="text-4xl mb-3">📋</div>
                            Belum ada order.
                        </td>
                    </tr>
                    <tr v-for="order in orders.data" :key="order.id"
                        class="border-b border-gray-50 hover:bg-[#d4eef1]/20 transition-colors">

                        <!-- Order -->
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900 text-xs">{{ order.order_number }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                {{ new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                            </p>
                        </td>

                        <!-- Customer -->
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900 text-sm">{{ order.user?.name }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ order.user?.email }}</p>
                        </td>

                        <!-- Alamat & HP -->
                        <td class="px-5 py-4 max-w-[200px]">
                            <div v-if="order.address">
                                <p class="text-xs font-semibold text-[#5a9aa4]">{{ order.address.phone }}</p>
                                <p class="text-[10px] text-gray-500 mt-0.5 line-clamp-2 leading-relaxed">
                                    {{ order.address.address }}, {{ order.address.city }}
                                </p>
                            </div>
                            <p v-else class="text-[10px] text-gray-300">-</p>
                        </td>

                        <!-- Item -->
                        <td class="px-5 py-4">
                            <p class="text-gray-700 font-semibold text-xs">{{ order.items?.length }} item</p>
                            <p class="text-[10px] text-gray-400 line-clamp-1 mt-0.5">
                                {{ order.items?.map(i => i.product?.name).join(', ') }}
                            </p>
                        </td>

                        <!-- Total -->
                        <td class="px-5 py-4 text-right">
                            <p class="font-extrabold text-[#b96b1c] text-sm">Rp {{ Number(order.total).toLocaleString('id-ID') }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ order.payment?.payment_method || '-' }}</p>
                        </td>

                        <!-- Status -->
                        <td class="px-5 py-4 text-center">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border"
                                :class="[getStatus(order.status).bg, getStatus(order.status).text, getStatus(order.status).border]">
                                {{ getStatus(order.status).label }}
                            </span>
                        </td>

                        <!-- Update -->
                        <td class="px-5 py-4 text-center">
                            <select
                                :value="order.status"
                                @change="updateStatus(order, $event.target.value)"
                                class="border-2 border-gray-200 rounded-xl px-2 py-1.5 text-xs text-gray-700 focus:outline-none focus:border-[#5a9aa4] bg-white transition-all cursor-pointer"
                            >
                                <option v-for="s in statusOptions" :key="s" :value="s">{{ getStatus(s).label }}</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="orders.last_page > 1" class="flex items-center justify-center gap-2 p-4 border-t border-gray-100">
                <Link v-for="link in orders.links" :key="link.label" :href="link.url || '#'"
                    class="px-3.5 py-2 rounded-xl text-sm no-underline transition-all font-medium"
                    :class="link.active
                        ? 'bg-[#5a9aa4] text-white font-bold shadow-sm'
                        : link.url
                            ? 'bg-white text-gray-600 border border-[#8bc5cd]/60 hover:border-[#5a9aa4] hover:text-[#5a9aa4]'
                            : 'text-gray-300 cursor-not-allowed'"
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>
