<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const page = usePage();
const activeTab = computed(() =>
    page.url.startsWith('/register') ? 'register' : 'login'
);

function switchTab(tab: string) {
    if (tab === 'login') router.visit('/login', { preserveState: false });
    else router.visit('/register', { preserveState: false });
}
</script>

<template>
    <div class="min-h-screen flex" style="background: linear-gradient(135deg, #d4eef1 0%, #fdf8ef 50%, #fee5a1 100%)">

        <!-- Left panel branding (desktop only) -->
        <div class="hidden lg:flex lg:w-1/2 bg-[#5a9aa4] relative overflow-hidden flex-col items-center justify-center p-12">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 24px 24px;"></div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#b96b1c]"></div>

            <div class="relative z-10 text-center">
                <Link :href="home()" class="inline-flex flex-col items-center gap-3 mb-10 no-underline">
                    <div class="w-16 h-16 bg-[#fee5a1] border-4 border-[#b96b1c] rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="/images/logo-white-genggar.png" alt="PokéArth" class="h-10 w-auto" onerror="this.style.display='none'" />
                    </div>
                    <span class="text-3xl font-extrabold tracking-tight" style="font-family:'Sora',sans-serif;">
                        <span class="text-[#fee5a1]">Poké</span><span class="text-white">Arth</span>
                    </span>
                </Link>

                <h2 class="text-2xl font-extrabold text-white mb-3" style="font-family:'Sora',sans-serif;">
                    Koleksi Kartu Pokémon
                </h2>
                <p class="text-white/70 text-sm max-w-xs mx-auto leading-relaxed">
                    Temukan kartu favoritmu dari Base Set hingga koleksi terbaru Scarlet & Violet.
                </p>

                <div class="flex gap-8 justify-center mt-10">
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-[#fee5a1]" style="font-family:'Sora',sans-serif;">500+</p>
                        <p class="text-xs text-white/60 mt-0.5">Jenis Kartu</p>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-[#fee5a1]" style="font-family:'Sora',sans-serif;">50+</p>
                        <p class="text-xs text-white/60 mt-0.5">Set Tersedia</p>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-[#fee5a1]" style="font-family:'Sora',sans-serif;">1k+</p>
                        <p class="text-xs text-white/60 mt-0.5">Pelanggan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right panel form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-sm">

                <!-- Mobile logo -->
                <div class="flex lg:hidden justify-center mb-8">
                    <Link :href="home()" class="inline-flex flex-col items-center gap-2 no-underline">
                        <div class="w-12 h-12 bg-[#fee5a1] border-[3px] border-[#b96b1c] rounded-xl flex items-center justify-center">
                            <img src="/images/logo-white-genggar.png" alt="PokéArth" class="h-7 w-auto" onerror="this.style.display='none'" />
                        </div>
                        <span class="text-xl font-extrabold" style="font-family:'Sora',sans-serif;">
                            <span class="text-[#5a9aa4]">Poké</span><span class="text-[#b96b1c]">Arth</span>
                        </span>
                    </Link>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] shadow-lg overflow-hidden">

                    <!-- Tab switcher -->
                    <div class="flex border-b-2 border-[#8bc5cd]">
                        <button
                            @click="switchTab('login')"
                            class="flex-1 py-3.5 text-sm font-bold transition-all duration-200 relative"
                            :class="activeTab === 'login'
                                ? 'text-[#5a9aa4] bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef]'
                                : 'text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-50'"
                        >
                            Masuk
                            <div
                                class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#5a9aa4] transition-all duration-300"
                                :class="activeTab === 'login' ? 'opacity-100' : 'opacity-0'"
                            ></div>
                        </button>
                        <div class="w-px bg-[#8bc5cd]/40"></div>
                        <button
                            @click="switchTab('register')"
                            class="flex-1 py-3.5 text-sm font-bold transition-all duration-200 relative"
                            :class="activeTab === 'register'
                                ? 'text-[#5a9aa4] bg-gradient-to-r from-[#fdf8ef] to-[#d4eef1]'
                                : 'text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-50'"
                        >
                            Daftar
                            <div
                                class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#5a9aa4] transition-all duration-300"
                                :class="activeTab === 'register' ? 'opacity-100' : 'opacity-0'"
                            ></div>
                        </button>
                    </div>

                    <!-- Form content with slide animation -->
                    <div class="px-8 py-7 overflow-hidden min-h-[420px]">
                        <Transition
                            :name="activeTab === 'login' ? 'slide-left' : 'slide-right'"
                            mode="out-in"
                        >
                            <div :key="activeTab">
                                <slot />
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Back to home -->
                <p class="text-center text-xs text-gray-400 mt-5">
                    <Link :href="home()" class="text-[#5a9aa4] hover:text-[#4a8a94] font-semibold no-underline transition-colors">
                        ← Kembali ke Toko
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-left-enter-from  { opacity: 0; transform: translateX(20px); }
.slide-left-leave-to    { opacity: 0; transform: translateX(-20px); }
.slide-right-enter-from { opacity: 0; transform: translateX(-20px); }
.slide-right-leave-to   { opacity: 0; transform: translateX(20px); }
</style>
