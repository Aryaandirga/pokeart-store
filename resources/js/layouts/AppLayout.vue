<script setup>
import Navbar from '@/components/Navbar.vue'

defineProps({
    title: String,
})
</script>

<template>
    <div class="min-h-screen bg-white">
        <Navbar />
        <main>
            <slot />
        </main>
        <footer class="bg-[#b96b1c] mt-16">
            <div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
                <p class="text-sm font-bold text-[#fee5a1]">
                    PokéArth<span class="text-white">.</span>
                </p>
                <p class="text-xs text-white/70">© 2025 PokéArth Store. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Terms</a>
                    <a href="#" class="text-xs text-white/70 hover:text-white transition-colors">Contact</a>
                </div>
            </div>
        </footer>
    </div>
</template>