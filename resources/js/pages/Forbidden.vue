<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const countdown = ref(5)

onMounted(() => {
    const timer = setInterval(() => {
        countdown.value--
        if (countdown.value <= 0) {
            clearInterval(timer)
            router.visit('/')
        }
    }, 1000)
})
</script>

<template>
    <div class="min-h-screen flex items-center justify-center px-6"
        style="background: linear-gradient(135deg, #d4eef1 0%, #fdf8ef 50%, #fee5a1 100%)">

        <!-- Card -->
        <div class="bg-white rounded-3xl border-2 border-[#8bc5cd] shadow-xl max-w-md w-full overflow-hidden">

            <!-- Top bar -->
            <div class="h-2 bg-[#b96b1c]"></div>

            <div class="p-10 text-center">

                <!-- Icon -->
                <div class="relative inline-block mb-6">
                    <div class="w-24 h-24 bg-[#fee5a1] border-4 border-[#b96b1c] rounded-full flex items-center justify-center mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#b96b1c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <!-- Pulse ring -->
                    <span class="absolute inset-0 rounded-full border-4 border-[#b96b1c]/30 animate-ping"></span>
                </div>

                <!-- Text -->
                <p class="text-[11px] font-bold text-[#b96b1c] tracking-[3px] uppercase mb-2">403 Forbidden</p>
                <h1 class="text-3xl font-extrabold text-gray-900 mb-3 tracking-tight" style="font-family:'Sora',sans-serif;">
                    Area Terbatas
                </h1>
                <p class="text-gray-500 text-sm leading-relaxed mb-2">
                    Halaman ini hanya dapat diakses oleh <span class="font-bold text-[#5a9aa4]">Administrator</span>.
                    Kamu tidak memiliki izin untuk masuk ke area ini.
                </p>

                <!-- Countdown -->
                <div class="bg-[#d4eef1] border border-[#8bc5cd] rounded-2xl px-5 py-3 mb-2 w-full">
                    <p class="text-xs text-[#5a9aa4] font-semibold text-center">
                        Otomatis kembali ke home dalam
                        <span class="text-xl font-extrabold text-[#b96b1c] mx-1" style="font-family:'Sora',sans-serif;">{{ countdown }}</span>
                        detik
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-3 mt-7">
                    <Link href="/"
                        class="block w-full bg-[#b96b1c] hover:bg-[#8a4e12] text-white font-bold py-3.5 rounded-xl no-underline transition-all text-center active:scale-[0.99]">
                        🏠 Kembali ke Home
                    </Link>
                    <Link href="/shop"
                        class="block w-full bg-white hover:bg-[#d4eef1] text-[#5a9aa4] font-bold py-3 rounded-xl no-underline transition-all text-center border-2 border-[#8bc5cd] hover:border-[#5a9aa4]">
                        🛍️ Pergi ke Shop
                    </Link>
                </div>
            </div>
        </div>

        <!-- Decorative dots -->
        <div class="fixed inset-0 pointer-events-none -z-10"
            style="background-image: radial-gradient(circle, #8bc5cd 1px, transparent 1px); background-size: 28px 28px; opacity: 0.15;">
        </div>
    </div>
</template>
