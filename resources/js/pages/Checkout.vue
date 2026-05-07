<script setup>
import axios from 'axios'
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { imageUrl } from '@/composables/useImageUrl.js'

const props = defineProps({
    cart: Object,
    subtotal: Number,
    addresses: Array,
    clientKey: String,
})

const page         = usePage()
const user         = computed(() => page.props.auth?.user)
const selectedAddr = ref(props.addresses[0]?.id || null)
const useNewAddr   = ref(props.addresses.length === 0)
const processing   = ref(false)
const notes        = ref('')
const checkoutDone = ref(false)
const checkoutOrderNumber = ref('')

function showCheckoutSuccess(orderNumber) {
    checkoutDone.value = true
    checkoutOrderNumber.value = orderNumber
    // Auto hilang setelah 5 detik
    setTimeout(() => { checkoutDone.value = false }, 5000)
}

// ── Biteship ──────────────────────────────────────────────────────────────────
const areaSearch    = ref('')
const areaResults   = ref([])
const areaSearching = ref(false)
const selectedArea  = ref(null)
const shippingRates = ref([])
const loadingRates  = ref(false)
const selectedRate  = ref(null)
const ratesError    = ref('')

let areaDebounce = null

function onAreaInput(val) {
    if (areaDebounce) clearTimeout(areaDebounce)
    if (!val || val.length < 3) {
        areaResults.value = []
        return
    }
    areaDebounce = setTimeout(() => searchArea(val), 400)
}

async function searchArea(q) {
    areaSearching.value = true
    try {
        const res = await axios.get('/shipping/areas', { params: { q } })
        console.log('Area search response:', res.data)
        // Biteship area format: res.data.areas
        const areas = res.data.areas ?? res.data.data ?? []
        if (areas.length > 0) {
            areaResults.value = areas
        } else {
            // Fallback: buat area dummy dari input user supaya bisa lanjut pilih kurir
            areaResults.value = [{
                id:   'FALLBACK_' + q.toUpperCase().replace(/\s+/g, '_'),
                name: q.charAt(0).toUpperCase() + q.slice(1),
                administrative_division_level_1_name: 'Indonesia',
                country_name: 'Indonesia',
            }]
        }
        console.log('Area results:', areaResults.value)
    } catch (err) {
        console.error('Area search error:', err)
        // Fallback jika API error
        areaResults.value = [{
            id:   'FALLBACK_' + q.toUpperCase().replace(/\s+/g, '_'),
            name: q.charAt(0).toUpperCase() + q.slice(1),
            administrative_division_level_1_name: 'Indonesia',
            country_name: 'Indonesia',
        }]
    } finally {
        areaSearching.value = false
    }
}

function selectArea(area) {
    selectedArea.value = area
    areaSearch.value   = area.name
    areaResults.value  = []
    fetchRates(area.id)
}

async function fetchRates(areaId) {
    loadingRates.value  = true
    shippingRates.value = []
    selectedRate.value  = null
    ratesError.value    = ''

    try {
        const res = await axios.post('/shipping/rates', {
            destination_area_id: areaId,
        })

        shippingRates.value = res.data.rates ?? []

        if (res.data.fallback) {
            ratesError.value = '⚠️ Menampilkan tarif estimasi. Ongkir final dikonfirmasi saat proses.'
        }

        if (shippingRates.value.length === 0) {
            ratesError.value = 'Tidak ada layanan pengiriman tersedia ke area ini.'
        }
    } catch (e) {
        ratesError.value = 'Gagal memuat tarif pengiriman. Coba lagi.'
    } finally {
        loadingRates.value = false
    }
}

const shippingCost = computed(() => selectedRate.value?.price ?? 0)
const total        = computed(() => props.subtotal + shippingCost.value)

// ── Form alamat baru ──────────────────────────────────────────────────────────
const newAddr = ref({
    recipient_name: user.value?.name || '',
    phone:          '',
    address:        '',
    city:           '',
    province:       '',
    postal_code:    '',
})

// ── Bayar ─────────────────────────────────────────────────────────────────────
async function pay() {
    if (!selectedRate.value) {
        alert('Pilih layanan pengiriman terlebih dahulu.')
        return
    }

    processing.value = true

    const payload = {
        shipping_cost:        shippingCost.value,
        notes:                notes.value,
        courier_code:         selectedRate.value.courier_code,
        courier_service_code: selectedRate.value.courier_service_code,
        destination_area_id:  selectedArea.value?.id ?? '',
    }

    if (useNewAddr.value) {
        Object.assign(payload, newAddr.value)
    } else {
        payload.address_id = selectedAddr.value
    }

    try {
        const res = await axios.post('/checkout', payload)
        const { snap_token, order_number } = res.data

        window.snap.pay(snap_token, {
            onSuccess: () => {
                processing.value = false
                showCheckoutSuccess(order_number)
                setTimeout(() => router.visit('/orders'), 2000)
            },
            onPending: () => {
                processing.value = false
                showCheckoutSuccess(order_number)
                setTimeout(() => router.visit('/orders'), 2000)
            },
            onError:   () => { processing.value = false; alert('Pembayaran gagal.') },
            onClose:   () => { processing.value = false },
        })
    } catch (err) {
        processing.value = false
        const errors = err.response?.data?.errors
        if (errors) alert(Object.values(errors).flat().join('\n'))
        else alert('Terjadi kesalahan. Silakan coba lagi.')
    }
}
</script>

<template>
    <component :is="'script'" :src="`https://app.sandbox.midtrans.com/snap/snap.js`" :data-client-key="clientKey" />

    <!-- Notifikasi checkout berhasil -->
    <Transition name="fade-scale">
        <div v-if="checkoutDone" class="fixed inset-0 z-[100] flex items-center justify-center px-6"
            @click="checkoutDone = false">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
            <!-- Card -->
            <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-green-200 p-8 max-w-sm w-full text-center"
                @click.stop>
                <!-- Icon -->
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-xl font-extrabold text-gray-900 mb-1" style="font-family:'Sora',sans-serif;">
                    Checkout Berhasil! 🎉
                </h2>
                <p class="text-sm text-gray-500 mb-1">Pesanan kamu sedang diproses.</p>
                <p class="text-xs font-bold text-[#5a9aa4]">#{{ checkoutOrderNumber }}</p>
                <p class="text-xs text-gray-400 mt-4">Mengalihkan ke halaman pesanan...</p>
                <!-- Progress bar -->
                <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-400 rounded-full animate-progress"></div>
                </div>
                <button @click="checkoutDone = false"
                    class="mt-4 text-xs text-gray-400 hover:text-gray-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </Transition>

    <div class="min-h-screen bg-[#fdf8ef]">
        <div class="max-w-6xl mx-auto px-4 md:px-6 py-8 md:py-10">

            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Pembayaran</p>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight" style="font-family:'Sora',sans-serif;">Checkout</h1>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

                <!-- ── Kiri ──────────────────────────────────────────────── -->
                <div class="flex-1 flex flex-col gap-5">

                    <!-- Alamat tersimpan -->
                    <div v-if="addresses.length > 0" class="bg-white border-2 border-[#8bc5cd] rounded-2xl p-6 shadow-sm">
                        <p class="text-sm font-extrabold text-gray-900 mb-4">Alamat Pengiriman</p>
                        <div class="flex flex-col gap-3">
                            <label v-for="addr in addresses" :key="addr.id"
                                class="flex items-start gap-3 p-3.5 border-2 rounded-xl cursor-pointer transition-all"
                                :class="selectedAddr === addr.id && !useNewAddr
                                    ? 'border-[#5a9aa4] bg-[#d4eef1]/40'
                                    : 'border-gray-100 hover:border-[#8bc5cd]'"
                            >
                                <input type="radio" :value="addr.id" v-model="selectedAddr"
                                    @change="useNewAddr = false" class="mt-0.5 accent-[#5a9aa4]" />
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ addr.recipient_name }}</p>
                                    <p class="text-xs text-gray-500">{{ addr.phone }}</p>
                                    <p class="text-xs text-gray-500">{{ addr.address }}, {{ addr.city }}, {{ addr.province }} {{ addr.postal_code }}</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 border-2 rounded-xl cursor-pointer transition-all"
                                :class="useNewAddr ? 'border-[#5a9aa4] bg-[#d4eef1]/40' : 'border-gray-100 hover:border-[#8bc5cd]'">
                                <input type="radio" :checked="useNewAddr" @change="useNewAddr = true; selectedAddr = null" class="accent-[#5a9aa4]" />
                                <span class="text-sm font-medium text-gray-700">+ Gunakan alamat baru</span>
                            </label>
                        </div>
                    </div>

                    <!-- Form alamat baru -->
                    <div v-if="useNewAddr" class="bg-white border-2 border-[#8bc5cd] rounded-2xl p-6 shadow-sm">
                        <p class="text-sm font-extrabold text-gray-900 mb-4">
                            {{ addresses.length === 0 ? 'Alamat Pengiriman' : 'Alamat Baru' }}
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 md:col-span-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Nama Penerima</label>
                                <input v-model="newAddr.recipient_name" type="text" placeholder="Nama lengkap"
                                    class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] transition-all" />
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">No. WhatsApp</label>
                                <input v-model="newAddr.phone" type="text" placeholder="08xxxxxxxxxx"
                                    class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] transition-all" />
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Alamat Lengkap</label>
                                <textarea v-model="newAddr.address" rows="2" placeholder="Jalan, Nomor, RT/RW, Kelurahan, Kecamatan"
                                    class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] transition-all resize-none" />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Kota</label>
                                <input v-model="newAddr.city" type="text" placeholder="Jakarta"
                                    class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] transition-all" />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Provinsi</label>
                                <input v-model="newAddr.province" type="text" placeholder="DKI Jakarta"
                                    class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] transition-all" />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Kode Pos</label>
                                <input v-model="newAddr.postal_code" type="text" placeholder="12345"
                                    class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] transition-all" />
                            </div>
                        </div>
                    </div>

                    <!-- ── Biteship: Cari Area & Pilih Kurir ── -->
                    <div class="bg-white border-2 border-[#8bc5cd] rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <p class="text-sm font-extrabold text-gray-900">Pilih Layanan Pengiriman</p>
                            <span class="text-[10px] bg-[#d4eef1] text-[#5a9aa4] font-bold px-2 py-0.5 rounded-full">via Biteship</span>
                        </div>

                        <!-- Search area tujuan -->
                        <div class="relative mb-4">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 block">Kota / Kecamatan Tujuan</label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input
                                    v-model="areaSearch"
                                    @input="onAreaInput($event.target.value)"
                                    type="text"
                                    placeholder="Ketik nama kota atau kecamatan..."
                                    class="w-full border-2 border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] focus:ring-2 focus:ring-[#8bc5cd]/30 transition-all"
                                />
                                <div v-if="areaSearching" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg class="animate-spin w-4 h-4 text-[#5a9aa4]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Dropdown hasil pencarian -->
                            <div v-if="areaResults.length > 0"
                                class="absolute z-20 w-full mt-1 bg-white border-2 border-[#8bc5cd] rounded-xl shadow-lg overflow-hidden max-h-52 overflow-y-auto">
                                <button
                                    v-for="area in areaResults"
                                    :key="area.id"
                                    @click="selectArea(area)"
                                    class="w-full text-left px-4 py-3 text-sm hover:bg-[#d4eef1] transition-colors border-b border-gray-50 last:border-0"
                                >
                                    <p class="font-semibold text-gray-900">{{ area.name }}</p>
                                    <p class="text-xs text-gray-400">{{ area.administrative_division_level_1_name }}, {{ area.country_name }}</p>
                                </button>
                            </div>
                        </div>

                        <!-- Loading rates -->
                        <div v-if="loadingRates" class="flex items-center gap-3 py-4 text-sm text-gray-500">
                            <svg class="animate-spin w-5 h-5 text-[#5a9aa4]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            Mengambil tarif pengiriman...
                        </div>

                        <!-- Error tanpa kurir -->
                        <div v-else-if="ratesError && shippingRates.length === 0" class="text-sm text-red-500 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                            {{ ratesError }}
                        </div>

                        <!-- Daftar kurir -->
                        <div v-else-if="shippingRates.length > 0" class="flex flex-col gap-2">
                            <!-- Warning fallback di atas list -->
                            <div v-if="ratesError" class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 mb-1">
                                {{ ratesError }}
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pilih Kurir</p>
                            <label
                                v-for="rate in shippingRates"
                                :key="`${rate.courier_code}-${rate.service_code}`"
                                class="flex items-center gap-3 p-3.5 border-2 rounded-xl cursor-pointer transition-all"
                                :class="selectedRate?.service_code === rate.service_code && selectedRate?.courier_code === rate.courier_code
                                    ? 'border-[#b96b1c] bg-[#fee5a1]/30'
                                    : 'border-gray-100 hover:border-[#8bc5cd]'"
                            >
                                <input
                                    type="radio"
                                    :value="rate"
                                    v-model="selectedRate"
                                    class="accent-[#b96b1c]"
                                />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-gray-900">{{ rate.courier_name }}</p>
                                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-semibold">{{ rate.service_name }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Estimasi {{ rate.duration }} {{ rate.duration_unit === 'days' ? 'hari' : rate.duration_unit }}
                                    </p>
                                </div>
                                <p class="text-sm font-extrabold text-[#b96b1c] shrink-0">
                                    Rp {{ Number(rate.price).toLocaleString('id-ID') }}
                                </p>
                            </label>
                        </div>

                        <div v-else-if="!selectedArea" class="text-xs text-gray-400 py-2">
                            Ketik nama kota/kecamatan tujuan untuk melihat pilihan kurir.
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="bg-white border-2 border-[#8bc5cd] rounded-2xl p-6 shadow-sm">
                        <p class="text-sm font-extrabold text-gray-900 mb-3">Catatan Pesanan</p>
                        <textarea v-model="notes" rows="2" placeholder="Catatan khusus untuk penjual (opsional)"
                            class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:border-[#5a9aa4] transition-all resize-none" />
                    </div>
                </div>

                <!-- ── Kanan: Summary ─────────────────────────────────────── -->
                <div class="w-full lg:w-80 lg:shrink-0 lg:sticky lg:top-24 flex flex-col gap-4">

                    <!-- Items -->
                    <div class="bg-white border-2 border-[#8bc5cd] rounded-2xl p-5 shadow-sm">
                        <p class="text-sm font-extrabold text-gray-900 mb-4">Pesanan ({{ cart.items.length }} item)</p>
                        <div class="flex flex-col gap-3">
                            <div v-for="item in cart.items" :key="item.id" class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#d4eef1] rounded-xl overflow-hidden shrink-0">
                                    <img v-if="item.product.image" :src="imageUrl(item.product.image)" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center text-sm">🃏</div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 truncate">{{ item.product.name }}</p>
                                    <p class="text-[10px] text-gray-400">x{{ item.quantity }}</p>
                                </div>
                                <p class="text-xs font-bold text-gray-900 shrink-0">
                                    Rp {{ Number(item.product.price * item.quantity).toLocaleString('id-ID') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <div class="bg-white border-2 border-[#8bc5cd] rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-5 py-3.5">
                            <p class="text-sm font-extrabold text-gray-900">Ringkasan Pembayaran</p>
                        </div>
                        <div class="px-5 py-4 flex flex-col gap-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-semibold text-gray-900">Rp {{ Number(subtotal).toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ongkos Kirim</span>
                                <span class="font-semibold" :class="selectedRate ? 'text-gray-900' : 'text-gray-400'">
                                    {{ selectedRate ? 'Rp ' + Number(shippingCost).toLocaleString('id-ID') : 'Pilih kurir' }}
                                </span>
                            </div>
                            <div v-if="selectedRate" class="text-xs text-gray-400 -mt-1">
                                {{ selectedRate.courier_name }} {{ selectedRate.service_name }}
                            </div>
                            <div class="border-t-2 border-[#8bc5cd]/40 pt-3 flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-900">Total</span>
                                <span class="text-xl font-extrabold text-[#b96b1c]" style="font-family:'Sora',sans-serif;">
                                    Rp {{ Number(total).toLocaleString('id-ID') }}
                                </span>
                            </div>
                        </div>

                        <div class="px-5 pb-5">
                            <button
                                @click="pay"
                                :disabled="processing || !selectedRate"
                                class="w-full text-white text-sm font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm active:scale-[0.99]"
                                :class="selectedRate
                                    ? 'bg-[#b96b1c] hover:bg-[#8a4e12]'
                                    : 'bg-gray-300 cursor-not-allowed'"
                            >
                                <svg v-if="processing" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ processing ? 'Memproses...' : !selectedRate ? 'Pilih Kurir Dulu' : 'Bayar Sekarang' }}
                            </button>
                            <p class="text-[10px] text-gray-400 text-center mt-2">Pembayaran aman via Midtrans</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-down-enter-active, .fade-down-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.fade-down-enter-from, .fade-down-leave-to { opacity: 0; transform: translate(-50%, -16px); }

.fade-scale-enter-active, .fade-scale-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; }
.fade-scale-enter-from .relative.bg-white, .fade-scale-leave-to .relative.bg-white { transform: scale(0.9); }

@keyframes progress {
    from { width: 100%; }
    to   { width: 0%; }
}
.animate-progress {
    animation: progress 5s linear forwards;
}
</style>
