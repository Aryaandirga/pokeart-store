<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { imageUrl } from '@/composables/useImageUrl.js'

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
})

const search   = ref(props.filters?.search || '')
const category = ref(props.filters?.category || '')
const status   = ref(props.filters?.status || '')

watch([category, status], () => applyFilter())

function applyFilter() {
    router.get('/admin/products', {
        search: search.value,
        category: category.value,
        status: status.value,
    }, { preserveState: true, replace: true })
}

function toggle(product) {
    router.patch(`/admin/products/${product.id}/toggle`, {}, { preserveState: true })
}

const uploadingId = ref(null)

function uploadImage(product, e) {
    const file = e.target.files[0]
    if (!file) return
    uploadingId.value = product.id
    const form = new FormData()
    form.append('image', file)
    router.post(`/admin/products/${product.id}/images`, form, {
        onSuccess: () => { uploadingId.value = null },
        onError:   () => { uploadingId.value = null },
    })
}

function deleteImage(product, image) {
    if (!confirm('Hapus gambar ini?')) return
    router.delete(`/admin/products/${product.id}/images/${image.id}`)
}
</script>

<template>
    <div>
        <!-- Header -->
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Manajemen</p>
                <h1 class="text-2xl font-extrabold text-gray-900" style="font-family:'Sora',sans-serif;">Produk</h1>
                <p class="text-sm text-gray-400 mt-1">Publish/unpublish & upload foto produk.</p>
            </div>
            <span class="text-sm text-gray-400">
                <span class="font-bold text-[#b96b1c]">{{ products.total }}</span> produk
            </span>
        </div>

        <!-- Filter -->
        <div class="flex gap-3 mb-6 flex-wrap">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="6"/><path stroke-linecap="round" d="M20 20l-3-3"/>
                </svg>
                <input v-model="search" @keyup.enter="applyFilter" type="text" placeholder="Cari produk..."
                    class="border-2 border-gray-200 rounded-xl pl-9 pr-4 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#5a9aa4] transition-all w-64 bg-white" />
            </div>
            <select v-model="category"
                class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#5a9aa4] bg-white transition-all">
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <select v-model="status"
                class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#5a9aa4] bg-white transition-all">
                <option value="">Semua Status</option>
                <option value="published">Published</option>
                <option value="unpublished">Draft</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white border-2 border-[#8bc5cd]/40 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-100 bg-gradient-to-r from-[#d4eef1]/30 to-white">
                        <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Produk</th>
                        <th class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Kategori</th>
                        <th class="text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Harga</th>
                        <th class="text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Stok</th>
                        <th class="text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Foto</th>
                        <th class="text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products.data" :key="product.id"
                        class="border-b border-gray-50 hover:bg-[#d4eef1]/20 transition-colors">

                        <!-- Produk -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 bg-[#d4eef1] rounded-xl overflow-hidden shrink-0 flex items-center justify-center border border-[#8bc5cd]/40">
                                    <img v-if="product.image" :src="imageUrl(product.image)" class="w-full h-full object-cover" />
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#8bc5cd]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 line-clamp-1 text-sm">{{ product.name }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ product.sku }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Kategori -->
                        <td class="px-5 py-4">
                            <span class="text-xs bg-[#d4eef1] text-[#5a9aa4] font-semibold px-2.5 py-1 rounded-full">
                                {{ product.category?.name ?? '-' }}
                            </span>
                        </td>

                        <!-- Harga -->
                        <td class="px-5 py-4 text-right">
                            <p class="font-extrabold text-[#b96b1c] text-sm">Rp {{ Number(product.price).toLocaleString('id-ID') }}</p>
                        </td>

                        <!-- Stok -->
                        <td class="px-5 py-4 text-right">
                            <span class="text-sm font-bold px-2.5 py-1 rounded-full"
                                :class="product.stock <= product.min_stock
                                    ? 'bg-red-50 text-red-500 border border-red-200'
                                    : 'bg-green-50 text-green-600'">
                                {{ product.stock }}
                            </span>
                        </td>

                        <!-- Upload Foto -->
                        <td class="px-5 py-4 text-center">
                            <label class="cursor-pointer inline-flex items-center gap-1.5 text-xs font-semibold text-[#5a9aa4] hover:text-[#4a8a94] bg-[#d4eef1] hover:bg-[#c4dfe8] px-3 py-1.5 rounded-xl transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span v-if="uploadingId === product.id">Uploading...</span>
                                <span v-else>{{ product.images?.length > 0 ? `${product.images.length} foto` : 'Upload' }}</span>
                                <input type="file" accept="image/*" class="hidden" @change="uploadImage(product, $event)" />
                            </label>
                        </td>

                        <!-- Status Toggle -->
                        <td class="px-5 py-4 text-center">
                            <button @click="toggle(product)"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border-2"
                                :class="product.is_published
                                    ? 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100'
                                    : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100'"
                            >
                                {{ product.is_published ? '✓ Published' : 'Draft' }}
                            </button>
                        </td>

                        <!-- Thumbnail preview -->
                        <td class="px-5 py-4">
                            <div v-if="product.images?.length > 0" class="flex gap-1">
                                <div v-for="img in product.images.slice(0, 3)" :key="img.id"
                                    class="relative group w-8 h-8 rounded-lg overflow-hidden border border-[#8bc5cd]/40">
                                    <img :src="imageUrl(img.image_path)" class="w-full h-full object-cover" />
                                    <button @click="deleteImage(product, img)"
                                        class="absolute inset-0 bg-red-500/0 group-hover:bg-red-500/80 flex items-center justify-center transition-all">
                                        <span class="text-white text-xs opacity-0 group-hover:opacity-100 font-bold">×</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="products.last_page > 1" class="flex items-center justify-center gap-2 p-4 border-t border-gray-100">
                <Link v-for="link in products.links" :key="link.label" :href="link.url || '#'"
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
