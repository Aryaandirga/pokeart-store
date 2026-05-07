<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page    = usePage()
const success = computed(() => page.props.flash?.success)
const focused = ref(null)

const form = useForm({
    name:    '',
    email:   '',
    subject: '',
    message: '',
})

function submit() {
    form.post('/contact', {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef]">

        <!-- Header Banner -->
        <div class="bg-[#5a9aa4] border-b-[3px] border-[#b96b1c]">
            <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-10 text-center">
                <p class="text-[11px] font-bold text-[#fee5a1] tracking-[3px] uppercase mb-2">Hubungi Kami</p>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-2" style="font-family:'Sora',sans-serif;">
                    Contact Us
                </h1>
                <p class="text-white/70 text-sm max-w-md mx-auto">
                    Ada pertanyaan tentang produk, pesanan, atau ingin pre-order kartu tertentu? Kami siap membantu!
                </p>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 md:px-6 py-8 md:py-12">
            <div class="flex flex-col md:flex-row gap-6 md:gap-10 items-start">

                <!-- ── Contact Info ──────────────────────────────────────── -->
                <div class="w-full md:w-72 md:shrink-0 flex flex-col gap-4">

                    <!-- Info cards -->
                    <div
                        v-for="(item, i) in [
                            { icon: 'location', label: 'Lokasi', value: 'Jakarta, Indonesia', value2: null },
                            { icon: 'email', label: 'Email', value: 'hello@pokeart.id', value2: null },
                            { icon: 'phone', label: 'WhatsApp', value: '+62 812-3456-7890', value2: null },
                            { icon: 'clock', label: 'Jam Operasional', value: 'Senin – Sabtu', value2: '09.00 – 21.00 WIB' },
                        ]"
                        :key="i"
                        class="bg-white rounded-2xl border-2 border-[#8bc5cd] p-4 flex items-start gap-3 shadow-sm hover:shadow-md hover:border-[#5a9aa4] transition-all duration-200"
                    >
                        <div class="w-10 h-10 rounded-xl bg-[#d4eef1] border border-[#8bc5cd] flex items-center justify-center shrink-0">
                            <!-- Location -->
                            <svg v-if="item.icon === 'location'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <!-- Email -->
                            <svg v-if="item.icon === 'email'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <!-- Phone -->
                            <svg v-if="item.icon === 'phone'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <!-- Clock -->
                            <svg v-if="item.icon === 'clock'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#5a9aa4] uppercase tracking-wide mb-0.5">{{ item.label }}</p>
                            <p class="text-sm text-gray-700 font-medium">{{ item.value }}</p>
                            <p v-if="item.value2" class="text-sm text-gray-500">{{ item.value2 }}</p>
                        </div>
                    </div>

                    <!-- Sosmed -->
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-[#5a9aa4] uppercase tracking-wider mb-3">Ikuti Kami</p>
                        <div class="flex gap-2">
                            <!-- Instagram -->
                            <a href="#" class="w-10 h-10 rounded-xl bg-[#d4eef1] border border-[#8bc5cd] flex items-center justify-center text-[#5a9aa4] hover:bg-[#5a9aa4] hover:text-white hover:border-[#5a9aa4] transition-all duration-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                            <!-- TikTok -->
                            <a href="#" class="w-10 h-10 rounded-xl bg-[#d4eef1] border border-[#8bc5cd] flex items-center justify-center text-[#5a9aa4] hover:bg-[#5a9aa4] hover:text-white hover:border-[#5a9aa4] transition-all duration-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.27 8.27 0 004.84 1.55V6.79a4.85 4.85 0 01-1.07-.1z"/></svg>
                            </a>
                            <!-- Facebook -->
                            <a href="#" class="w-10 h-10 rounded-xl bg-[#d4eef1] border border-[#8bc5cd] flex items-center justify-center text-[#5a9aa4] hover:bg-[#5a9aa4] hover:text-white hover:border-[#5a9aa4] transition-all duration-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <!-- X (Twitter) -->
                            <a href="#" class="w-10 h-10 rounded-xl bg-[#d4eef1] border border-[#8bc5cd] flex items-center justify-center text-[#5a9aa4] hover:bg-[#5a9aa4] hover:text-white hover:border-[#5a9aa4] transition-all duration-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ── Form ──────────────────────────────────────────────── -->
                <div class="flex-1 bg-white rounded-2xl border-2 border-[#8bc5cd] shadow-md overflow-hidden">

                    <!-- Form header -->
                    <div class="bg-linear-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-5 md:px-8 py-4 md:py-5">
                        <h2 class="text-lg font-extrabold text-gray-900">Kirim Pesan</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Kami akan membalas dalam 1×24 jam</p>
                    </div>

                    <div class="p-5 md:p-8">
                        <!-- Success -->
                        <div v-if="success" class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-sm text-green-700 font-medium">{{ success }}</p>
                        </div>

                        <div class="flex flex-col gap-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                        Nama <span class="text-[#b96b1c]">*</span>
                                    </label>
                                    <input
                                        v-model="form.name"
                                        @focus="focused = 'name'"
                                        @blur="focused = null"
                                        type="text"
                                        placeholder="Nama kamu"
                                        class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all"
                                        :class="form.errors.name
                                            ? 'border-red-300 focus:border-red-400'
                                            : focused === 'name'
                                                ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30'
                                                : 'border-gray-200 hover:border-[#8bc5cd]'"
                                    />
                                    <p v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                        Email <span class="text-[#b96b1c]">*</span>
                                    </label>
                                    <input
                                        v-model="form.email"
                                        @focus="focused = 'email'"
                                        @blur="focused = null"
                                        type="email"
                                        placeholder="email@kamu.com"
                                        class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all"
                                        :class="form.errors.email
                                            ? 'border-red-300 focus:border-red-400'
                                            : focused === 'email'
                                                ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30'
                                                : 'border-gray-200 hover:border-[#8bc5cd]'"
                                    />
                                    <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                    Subjek <span class="text-[#b96b1c]">*</span>
                                </label>
                                <input
                                    v-model="form.subject"
                                    @focus="focused = 'subject'"
                                    @blur="focused = null"
                                    type="text"
                                    placeholder="Tentang apa?"
                                    class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all"
                                    :class="form.errors.subject
                                        ? 'border-red-300 focus:border-red-400'
                                        : focused === 'subject'
                                            ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30'
                                            : 'border-gray-200 hover:border-[#8bc5cd]'"
                                />
                                <p v-if="form.errors.subject" class="text-xs text-red-400 mt-1">{{ form.errors.subject }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                    Pesan <span class="text-[#b96b1c]">*</span>
                                </label>
                                <textarea
                                    v-model="form.message"
                                    @focus="focused = 'message'"
                                    @blur="focused = null"
                                    rows="5"
                                    placeholder="Tulis pesanmu di sini..."
                                    class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all resize-none"
                                    :class="form.errors.message
                                        ? 'border-red-300 focus:border-red-400'
                                        : focused === 'message'
                                            ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30'
                                            : 'border-gray-200 hover:border-[#8bc5cd]'"
                                ></textarea>
                                <p v-if="form.errors.message" class="text-xs text-red-400 mt-1">{{ form.errors.message }}</p>
                            </div>

                            <button
                                @click="submit"
                                :disabled="form.processing"
                                class="w-full bg-[#b96b1c] hover:bg-[#8a4e12] active:scale-[0.99] disabled:opacity-50 text-white text-sm font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm"
                            >
                                <svg v-if="form.processing" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
