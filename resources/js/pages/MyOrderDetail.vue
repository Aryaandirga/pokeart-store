<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({ order: Object })

const statusConfig = {
    pending:    { label: 'Menunggu Pembayaran', bg: 'bg-yellow-50',  text: 'text-yellow-600',  border: 'border-yellow-200',  bar: 'bg-yellow-400',  icon: '⏳', step: 1 },
    paid:       { label: 'Pembayaran Diterima', bg: 'bg-blue-50',    text: 'text-blue-600',    border: 'border-blue-200',    bar: 'bg-blue-400',    icon: '✅', step: 2 },
    processing: { label: 'Sedang Diproses',     bg: 'bg-purple-50',  text: 'text-purple-600',  border: 'border-purple-200',  bar: 'bg-purple-400',  icon: '⚙️', step: 3 },
    shipped:    { label: 'Dalam Pengiriman',     bg: 'bg-indigo-50',  text: 'text-indigo-600',  border: 'border-indigo-200',  bar: 'bg-indigo-400',  icon: '🚚', step: 4 },
    delivered:  { label: 'Pesanan Selesai',      bg: 'bg-green-50',   text: 'text-green-600',   border: 'border-green-200',   bar: 'bg-green-400',   icon: '🎉', step: 5 },
    cancelled:  { label: 'Dibatalkan',           bg: 'bg-red-50',     text: 'text-red-500',     border: 'border-red-200',     bar: 'bg-red-400',     icon: '❌', step: 0 },
}

const status = computed(() => statusConfig[props.order.status] ?? {
    label: props.order.status, bg: 'bg-gray-50', text: 'text-gray-500', border: 'border-gray-200', bar: 'bg-gray-400', icon: '•', step: 0
})

const steps = [
    { key: 'pending',    label: 'Pesanan Dibuat',     icon: '📋' },
    { key: 'paid',       label: 'Pembayaran',          icon: '💳' },
    { key: 'processing', label: 'Diproses',            icon: '⚙️' },
    { key: 'shipped',    label: 'Dikirim',             icon: '🚚' },
    { key: 'delivered',  label: 'Selesai',             icon: '🎉' },
]

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    })
}

function formatDateShort(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    })
}

const paymentMethodLabel = computed(() => {
    const m = props.order.payment?.payment_method
    const map = {
        credit_card: 'Kartu Kredit',
        bank_transfer: 'Transfer Bank',
        echannel: 'Mandiri Bill',
        bca_va: 'BCA Virtual Account',
        bni_va: 'BNI Virtual Account',
        bri_va: 'BRI Virtual Account',
        gopay: 'GoPay',
        shopeepay: 'ShopeePay',
        qris: 'QRIS',
    }
    return map[m] ?? m ?? 'Online Payment'
})
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef] flex flex-col">
        <div class="flex-1 max-w-4xl mx-auto w-full px-6 py-10">

            <!-- Back + Header -->
            <div class="mb-8">
                <Link href="/orders" class="inline-flex items-center gap-1.5 text-sm text-[#5a9aa4] hover:text-[#4a8a94] font-semibold no-underline mb-4 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Pesanan
                </Link>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Detail Pesanan</p>
                        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight" style="font-family:'Sora',sans-serif;">
                            {{ order.order_number }}
                        </h1>
                        <p class="text-xs text-gray-400 mt-1">{{ formatDate(order.created_at) }}</p>
                    </div>
                    <!-- Status badge -->
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full border-2 font-bold text-sm"
                        :class="[status.bg, status.text, status.border]">
                        <span>{{ status.icon }}</span>
                        <span>{{ status.label }}</span>
                    </div>
                </div>
            </div>

            <!-- Progress tracker (tidak tampil jika cancelled) -->
            <div v-if="order.status !== 'cancelled'" class="bg-white rounded-2xl border-2 border-[#8bc5cd] p-6 mb-6 shadow-sm">
                <p class="text-xs font-bold text-[#5a9aa4] uppercase tracking-wider mb-5">Status Pesanan</p>
                <div class="flex items-center">
                    <template v-for="(step, i) in steps" :key="step.key">
                        <!-- Step -->
                        <div class="flex flex-col items-center flex-1">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-lg border-2 transition-all duration-500"
                                :class="status.step >= i + 1
                                    ? 'bg-[#5a9aa4] border-[#5a9aa4] shadow-md scale-110'
                                    : 'bg-white border-gray-200 opacity-40'"
                            >
                                <span v-if="status.step >= i + 1">{{ step.icon }}</span>
                                <span v-else class="w-2 h-2 rounded-full bg-gray-300"></span>
                            </div>
                            <p class="text-[10px] font-semibold mt-2 text-center leading-tight"
                                :class="status.step >= i + 1 ? 'text-[#5a9aa4]' : 'text-gray-300'">
                                {{ step.label }}
                            </p>
                        </div>
                        <!-- Connector line -->
                        <div v-if="i < steps.length - 1"
                            class="h-0.5 flex-1 -mt-5 transition-all duration-500"
                            :class="status.step > i + 1 ? 'bg-[#5a9aa4]' : 'bg-gray-200'"
                        ></div>
                    </template>
                </div>
            </div>

            <!-- Cancelled notice -->
            <div v-else class="bg-red-50 border-2 border-red-200 rounded-2xl p-5 mb-6 flex items-center gap-4">
                <div class="text-3xl">❌</div>
                <div>
                    <p class="font-bold text-red-600">Pesanan Dibatalkan</p>
                    <p class="text-xs text-red-400 mt-0.5">Pesanan ini telah dibatalkan. Hubungi kami jika ada pertanyaan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Kiri: Items + Alamat -->
                <div class="md:col-span-2 flex flex-col gap-5">

                    <!-- Produk -->
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-5 py-3.5">
                            <p class="text-sm font-extrabold text-gray-900">
                                Produk ({{ order.items.length }} item)
                            </p>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div v-for="item in order.items" :key="item.id" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/50 transition-colors">
                                <!-- Gambar -->
                                <div class="w-14 h-14 bg-[#d4eef1] rounded-xl overflow-hidden shrink-0 border border-[#8bc5cd]/40">
                                    <img
                                        v-if="item.product?.image"
                                        :src="`/storage/${item.product.image}`"
                                        :alt="item.product.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center text-xl">🃏</div>
                                </div>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ item.product?.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ item.quantity }} × Rp {{ Number(item.price).toLocaleString('id-ID') }}
                                    </p>
                                </div>
                                <!-- Subtotal -->
                                <p class="text-sm font-extrabold text-[#b96b1c] shrink-0">
                                    Rp {{ Number(item.subtotal).toLocaleString('id-ID') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Pengiriman -->
                    <div v-if="order.address" class="bg-white rounded-2xl border-2 border-[#8bc5cd] overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-5 py-3.5">
                            <p class="text-sm font-extrabold text-gray-900">Alamat Pengiriman</p>
                        </div>
                        <div class="px-5 py-4 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#d4eef1] flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ order.address.recipient_name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ order.address.phone }}</p>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    {{ order.address.address }},
                                    {{ order.address.city }},
                                    {{ order.address.province }}
                                    {{ order.address.postal_code }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div v-if="order.notes" class="bg-[#fee5a1]/30 rounded-2xl border-2 border-[#b96b1c]/20 px-5 py-4">
                        <p class="text-xs font-bold text-[#b96b1c] uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-sm text-gray-700">{{ order.notes }}</p>
                    </div>
                </div>

                <!-- Kanan: Ringkasan pembayaran -->
                <div class="md:col-span-1 flex flex-col gap-5">

                    <!-- Ringkasan -->
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-5 py-3.5">
                            <p class="text-sm font-extrabold text-gray-900">Ringkasan Pembayaran</p>
                        </div>
                        <div class="px-5 py-4 flex flex-col gap-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-semibold text-gray-900">Rp {{ Number(order.subtotal).toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ongkos Kirim</span>
                                <span class="font-semibold text-gray-900">Rp {{ Number(order.shipping_cost).toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="border-t-2 border-[#8bc5cd]/40 pt-3 flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-900">Total</span>
                                <span class="text-xl font-extrabold text-[#b96b1c]" style="font-family:'Sora',sans-serif;">
                                    Rp {{ Number(order.total).toLocaleString('id-ID') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Pembayaran -->
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-5 py-3.5">
                            <p class="text-sm font-extrabold text-gray-900">Info Pembayaran</p>
                        </div>
                        <div class="px-5 py-4 flex flex-col gap-3">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Metode</p>
                                <p class="text-sm font-semibold text-gray-900">{{ paymentMethodLabel }}</p>
                            </div>
                            <div v-if="order.payment?.transaction_id">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">ID Transaksi</p>
                                <p class="text-xs font-mono text-gray-600 break-all">{{ order.payment.transaction_id }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Status Pembayaran</p>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                                    :class="order.payment?.status === 'success'
                                        ? 'bg-green-50 text-green-600'
                                        : order.payment?.status === 'pending'
                                            ? 'bg-yellow-50 text-yellow-600'
                                            : 'bg-red-50 text-red-500'">
                                    {{ order.payment?.status === 'success' ? '✓ Lunas'
                                        : order.payment?.status === 'pending' ? '⏳ Menunggu'
                                        : '✗ Gagal' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Butuh bantuan -->
                    <div class="bg-[#d4eef1] rounded-2xl border-2 border-[#8bc5cd] px-5 py-4 text-center">
                        <p class="text-xs font-bold text-[#5a9aa4] mb-2">Ada pertanyaan?</p>
                        <Link
                            href="/contact"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#5a9aa4] hover:bg-[#4a8a94] px-4 py-2 rounded-xl no-underline transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Hubungi Kami
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
