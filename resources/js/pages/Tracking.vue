<script setup>
import { ref, computed } from "vue"
import { Line } from "vue-chartjs"
import {
    Chart as ChartJS,
    CategoryScale, LinearScale,
    PointElement, LineElement,
    Title, Tooltip, Legend, Filler,
} from "chart.js"

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler)

const EUR_TO_IDR = 17500
const USD_TO_IDR = 16200

const query        = ref("")
const results      = ref([])
const pagination   = ref({ total: 0, total_pages: 0, page: 1 })
const loading      = ref(false)
const error        = ref(null)
const currentPage  = ref(1)
const selectedCard = ref(null)
const activeTab    = ref("cardmarket")
const quickSearches = ["Gengar", "Charizard", "Pikachu VMAX", "Umbreon"]

function formatIDR(amount) {
    if (amount == null || isNaN(amount)) return "-"
    return "Rp " + Math.round(amount).toLocaleString("id-ID")
}
function eurToIDR(v) { return v != null ? v * EUR_TO_IDR : null }
function usdToIDR(v) { return v != null ? v * USD_TO_IDR : null }

// Cache
const searchCache = new Map()

// Debounce
let debounceTimer = null
function debounceSearch() {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => search(1), 500)
}

async function search(page = 1) {
    if (!query.value.trim()) return

    const cacheKey = query.value.trim().toLowerCase() + "__" + page
    if (searchCache.has(cacheKey)) {
        const cached      = searchCache.get(cacheKey)
        results.value     = cached.results
        pagination.value  = cached.pagination
        currentPage.value = page
        selectedCard.value = null
        return
    }

    loading.value      = true
    error.value        = null
    selectedCard.value = null
    currentPage.value  = page

    try {
        const params = new URLSearchParams({ q: query.value.trim(), page: String(page), limit: "20" })
        const res    = await fetch("/api/pokewallet/search?" + params)
        if (res.status === 429) {
            error.value = "Batas request tercapai (100/jam). Silakan tunggu beberapa menit lalu coba lagi."
            return
        }
        if (!res.ok) throw new Error("HTTP " + res.status)
        const data       = await res.json()
        results.value    = data.results    ?? []
        pagination.value = data.pagination ?? { total: 0, total_pages: 0, page: 1 }
        searchCache.set(cacheKey, { results: results.value, pagination: pagination.value })
    } catch (e) {
        error.value   = "Gagal memuat data. Silakan coba lagi."
        results.value = []
    } finally {
        loading.value = false
    }
}

function quickSearch(term) {
    query.value = term
    search(1)
}

function imageUrl(id) { return "/api/pokewallet/images/" + id + "?size=low" }

function cardTrend(card) {
    const p = card?.cardmarket?.prices?.[0]
    if (!p?.avg1 || !p?.avg30 || p.avg30 === 0) return null
    const pct = ((p.avg1 - p.avg30) / p.avg30) * 100
    return { pct: Math.abs(pct).toFixed(1), up: pct >= 0 }
}

function selectCard(card) {
    if (selectedCard.value?.id === card.id) { selectedCard.value = null; return }
    selectedCard.value = card
    activeTab.value    = card?.cardmarket?.prices?.length ? "cardmarket" : "tcgplayer"
}

const chartData = computed(() => {
    if (!selectedCard.value) return null
    if (activeTab.value === "cardmarket") {
        const prices = selectedCard.value?.cardmarket?.prices ?? []
        if (!prices.length) return null
        return {
            labels:   ["Avg 1 Hari", "Avg 7 Hari", "Avg 30 Hari"],
            datasets: prices.map((p, i) => {
                const colors = ["#3B6DB5", "#f59e0b", "#10b981", "#8b5cf6"]
                const c = colors[i % colors.length]
                return {
                    label:           (p.variant_type === "holo" ? "Holo" : "Normal"),
                    data:            [eurToIDR(p.avg1), eurToIDR(p.avg7), eurToIDR(p.avg30)],
                    borderColor:     c,
                    backgroundColor: c + "22",
                    tension: 0.4, fill: true, pointRadius: 5,
                }
            }),
        }
    }
    const prices = selectedCard.value?.tcgplayer?.prices ?? []
    if (!prices.length) return null
    return {
        labels:   ["Terendah", "Tengah", "Pasar", "Tertinggi"],
        datasets: prices.map((p, i) => {
            const colors = ["#f59e0b", "#3B6DB5", "#10b981", "#8b5cf6"]
            const c = colors[i % colors.length]
            return {
                label:           p.sub_type_name ?? ("Varian " + (i + 1)),
                data:            [usdToIDR(p.low_price), usdToIDR(p.mid_price), usdToIDR(p.market_price), usdToIDR(p.high_price)],
                borderColor:     c,
                backgroundColor: c + "22",
                tension: 0.4, fill: true, pointRadius: 5,
            }
        }),
    }
})

const chartOptions = computed(() => ({
    responsive: true, maintainAspectRatio: false,
    plugins: {
        legend: { position: "top" },
        tooltip: { callbacks: { label: (ctx) => " " + ctx.dataset.label + ": " + formatIDR(ctx.raw) } },
    },
    scales: {
        y: { ticks: { callback: (v) => formatIDR(v) }, grid: { color: "#f0f0f0" } },
        x: { grid: { display: false } },
    },
}))

function displayPrice(card) {
    const cm  = card?.cardmarket?.prices?.[0]
    const tcg = card?.tcgplayer?.prices?.[0]
    if (cm?.avg)          return eurToIDR(cm.avg)
    if (tcg?.market_price) return usdToIDR(tcg.market_price)
    if (tcg?.mid_price)    return usdToIDR(tcg.mid_price)
    return null
}
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef]">

        <!-- Header -->
        <div class="bg-[#5a9aa4] border-b-[3px] border-[#b96b1c] sticky top-0 z-30 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <p class="text-[10px] font-bold text-[#fee5a1] tracking-[3px] uppercase mb-0.5">Pokemon TCG</p>
                    <h1 class="text-xl font-extrabold text-white tracking-tight leading-none">Pelacak Harga Kartu</h1>
                </div>
                <form @submit.prevent="search(1)" class="flex gap-2 w-full sm:w-auto sm:min-w-[340px]">
                    <input
                        v-model="query"
                        @input="debounceSearch"
                        @keydown.enter.prevent="search(1)"
                        type="text"
                        placeholder="Cari kartu Pokemon..."
                        class="flex-1 border border-white/40 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:border-[#b96b1c] focus:ring-2 focus:ring-[#b96b1c]/20 transition-all"
                    />
                    <button
                        type="submit"
                        :disabled="loading"
                        class="bg-[#b96b1c] hover:bg-[#8a4e12] disabled:opacity-60 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors"
                    >Cari</button>
                </form>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-3 flex flex-wrap gap-2">
                <span class="text-xs text-white/70 self-center">Cepat:</span>
                <button
                    v-for="term in quickSearches"
                    :key="term"
                    @click="quickSearch(term)"
                    class="text-xs px-3 py-1 rounded-full border border-white/40 text-white/90 hover:bg-white hover:text-[#5a9aa4] hover:border-white transition-all font-medium"
                >{{ term }}</button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

            <!-- Error -->
            <div v-if="error" class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                {{ error }}
            </div>

            <!-- Detail Panel (muncul di atas grid) -->
            <Transition name="slide-up">
                <div v-if="selectedCard" class="mb-6 bg-white rounded-2xl shadow-lg border-2 border-[#8bc5cd] overflow-hidden">
                    <div class="flex items-start gap-4 p-5 border-b-2 border-[#8bc5cd] bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef]">
                        <div class="w-20 shrink-0 rounded-lg overflow-hidden shadow-sm bg-[#EEF3FB]">
                            <img :src="imageUrl(selectedCard.id)" :alt="selectedCard.card_info?.name" class="w-full h-auto object-contain" loading="lazy" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-extrabold text-gray-900 leading-tight">{{ selectedCard.card_info?.name }}</h2>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ selectedCard.card_info?.set_name }}
                                <span v-if="selectedCard.card_info?.rarity" class="ml-2 text-[#3B6DB5]">· {{ selectedCard.card_info.rarity }}</span>
                                <span v-if="selectedCard.card_info?.hp" class="ml-2 text-red-400 font-semibold">· HP {{ selectedCard.card_info.hp }}</span>
                            </p>
                            <div class="flex gap-2 mt-3">
                                <button
                                    @click="activeTab = 'cardmarket'"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all"
                                    :class="activeTab === 'cardmarket' ? 'bg-[#5a9aa4] text-white shadow-sm' : 'bg-white/60 text-gray-600 hover:bg-white border border-[#8bc5cd]/40'"
                                >CardMarket (EUR)</button>
                                <button
                                    @click="activeTab = 'tcgplayer'"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all"
                                    :class="activeTab === 'tcgplayer' ? 'bg-[#b96b1c] text-white shadow-sm' : 'bg-white/60 text-gray-600 hover:bg-white border border-[#8bc5cd]/40'"
                                >TCGPlayer (USD)</button>
                            </div>
                        </div>
                        <button @click="selectedCard = null" class="text-gray-300 hover:text-gray-500 transition-colors shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 md:p-5">
                        <!-- Chart -->
                        <div v-if="chartData" class="h-56 sm:h-64 md:h-72 mb-5" style="background: rgba(238,243,251,0.3); border-radius: 12px; padding: 12px;">
                            <Line :data="chartData" :options="chartOptions" />
                        </div>
                        <div v-else class="h-32 flex items-center justify-center text-gray-400 text-sm mb-5 bg-gray-50 rounded-xl">
                            Tidak ada data harga untuk sumber ini.
                        </div>

                        <!-- Tabel harga -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-if="selectedCard.cardmarket?.prices?.length" class="bg-[#d4eef1] rounded-xl p-4 border border-[#8bc5cd]/40">
                                <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-3">CardMarket</p>
                                <div v-for="(p, i) in selectedCard.cardmarket.prices" :key="i" class="mb-3 last:mb-0">
                                    <p class="text-xs font-semibold text-gray-600 mb-1 capitalize">{{ p.variant_type === "holo" ? "Holo" : "Normal" }}</p>
                                    <div class="grid grid-cols-3 gap-1 text-xs">
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Avg 1H</p>
                                            <p class="font-bold text-gray-800">{{ formatIDR(eurToIDR(p.avg1)) }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Avg 7H</p>
                                            <p class="font-bold text-gray-800">{{ formatIDR(eurToIDR(p.avg7)) }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Avg 30H</p>
                                            <p class="font-bold text-gray-800">{{ formatIDR(eurToIDR(p.avg30)) }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Terendah</p>
                                            <p class="font-bold text-green-600">{{ formatIDR(eurToIDR(p.low)) }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 text-center col-span-2">
                                            <p class="text-gray-400 text-[10px]">Tren</p>
                                            <p class="font-bold text-[#3B6DB5]">{{ formatIDR(eurToIDR(p.trend)) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="selectedCard.tcgplayer?.prices?.length" class="bg-amber-50 rounded-xl p-4">
                                <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-3">TCGPlayer</p>
                                <div v-for="(p, i) in selectedCard.tcgplayer.prices" :key="i" class="mb-3 last:mb-0">
                                    <p class="text-xs font-semibold text-gray-600 mb-1">{{ p.sub_type_name ?? ("Varian " + (i + 1)) }}</p>
                                    <div class="grid grid-cols-2 gap-1 text-xs">
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Terendah</p>
                                            <p class="font-bold text-green-600">{{ formatIDR(usdToIDR(p.low_price)) }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Tengah</p>
                                            <p class="font-bold text-gray-800">{{ formatIDR(usdToIDR(p.mid_price)) }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Pasar</p>
                                            <p class="font-bold text-amber-600">{{ formatIDR(usdToIDR(p.market_price)) }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 text-center">
                                            <p class="text-gray-400 text-[10px]">Tertinggi</p>
                                            <p class="font-bold text-red-500">{{ formatIDR(usdToIDR(p.high_price)) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Loading skeleton -->
            <div v-if="loading" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                <div v-for="n in 18" :key="n" class="bg-white rounded-xl overflow-hidden border border-gray-100 animate-pulse">
                    <div class="aspect-[3/4] bg-gray-200"></div>
                    <div class="p-2 space-y-1.5">
                        <div class="h-2.5 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-2 bg-gray-100 rounded w-1/2"></div>
                        <div class="h-3 bg-gray-200 rounded w-2/3 mt-1"></div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else-if="!loading && results.length === 0 && !error" class="text-center py-24">
                <div class="mb-6"><img src="/images/pokecard.png" alt="Pokemon Card" class="w-28 h-28 mx-auto object-contain opacity-80"></div>
                <p class="text-gray-400 text-sm">Cari kartu Pokemon untuk melihat harga terkini.</p>
                <p class="text-gray-300 text-xs mt-1">Gunakan tombol cepat di atas atau ketik nama kartu.</p>
            </div>

            <!-- Card grid -->
            <div v-else-if="!loading && results.length > 0">
                <div class="flex items-center justify-between mb-4 px-1">
                    <p class="text-xs text-gray-400">
                        Menampilkan <span class="font-semibold text-gray-600">{{ results.length }}</span>
                        dari <span class="font-semibold text-gray-600">{{ pagination.total }}</span> kartu
                    </p>
                    <p class="text-xs text-gray-400">Halaman {{ pagination.page }} / {{ pagination.total_pages }}</p>
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                    <button
                        v-for="card in results"
                        :key="card.id"
                        @click="selectCard(card)"
                        class="group bg-white rounded-xl overflow-hidden border transition-all duration-200 text-left focus:outline-none"
                        :class="selectedCard?.id === card.id
                            ? 'border-[#b96b1c] shadow-md ring-2 ring-[#b96b1c]/20'
                            : 'border-[#8bc5cd]/40 hover:border-[#5a9aa4] hover:shadow-sm bg-white'"
                    >
                        <div class="relative aspect-[3/4] bg-[#d4eef1] overflow-hidden">
                            <img
                                :src="imageUrl(card.id)"
                                :alt="card.card_info?.name"
                                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            />
                            <div
                                v-if="cardTrend(card)"
                                class="absolute top-1.5 right-1.5 text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                                :class="cardTrend(card).up ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
                            >{{ cardTrend(card).up ? '▲' : '▼' }} {{ cardTrend(card).pct }}%</div>
                        </div>
                        <div class="p-2">
                            <p class="text-[10px] text-gray-400 truncate">{{ card.card_info?.set_name }}</p>
                            <p class="text-xs font-semibold text-gray-900 line-clamp-2 mt-0.5">{{ card.card_info?.name }}</p>
                            <p v-if="displayPrice(card)" class="text-xs font-bold text-[#b96b1c] mt-1 truncate">{{ formatIDR(displayPrice(card)) }}</p>
                            <p v-else class="text-xs text-gray-300 mt-1">-</p>
                        </div>
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.total_pages > 1" class="flex items-center justify-center gap-3 mt-8">
                    <button
                        @click="search(currentPage - 1)"
                        :disabled="currentPage <= 1 || loading"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border border-[#8bc5cd] text-[#5a9aa4] hover:bg-[#d4eef1] transition-all disabled:opacity-40 disabled:cursor-not-allowed bg-white"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Sebelumnya
                    </button>

                    <div class="flex items-center gap-1">
                        <template v-for="p in pagination.total_pages" :key="p">
                            <button
                                v-if="p === 1 || p === pagination.total_pages || Math.abs(p - currentPage) <= 1"
                                @click="search(p)"
                                class="w-8 h-8 rounded-lg text-sm font-semibold transition-all"
                                :class="p === currentPage
                                    ? 'bg-[#5a9aa4] text-white shadow-sm'
                                    : 'bg-white border border-[#8bc5cd]/60 text-gray-600 hover:border-[#5a9aa4] hover:text-[#5a9aa4]'"
                            >{{ p }}</button>
                            <span
                                v-else-if="(p === 2 && currentPage > 3) || (p === pagination.total_pages - 1 && currentPage < pagination.total_pages - 2)"
                                class="text-gray-400 text-sm px-1 select-none"
                            >...</span>
                        </template>
                    </div>

                    <button
                        @click="search(currentPage + 1)"
                        :disabled="currentPage >= pagination.total_pages || loading"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border border-[#8bc5cd] text-[#5a9aa4] hover:bg-[#d4eef1] transition-all disabled:opacity-40 disabled:cursor-not-allowed bg-white"
                    >
                        Berikutnya
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(16px); }
</style>

