<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        title: 'Buat Akun Baru',
        description: 'Daftar untuk mulai belanja kartu Pokémon',
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Daftar" />

    <form @submit.prevent="submit" class="flex flex-col gap-5">
        <div class="grid gap-4">
            <div class="grid gap-1.5">
                <Label for="name" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                    Nama Lengkap <span class="text-[#b96b1c]">*</span>
                </Label>
                <Input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    placeholder="Nama lengkap kamu"
                    class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-[#5a9aa4] focus:ring-2 focus:ring-[#8bc5cd]/30 transition-all"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-1.5">
                <Label for="email" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                    Email <span class="text-[#b96b1c]">*</span>
                </Label>
                <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-[#5a9aa4] focus:ring-2 focus:ring-[#8bc5cd]/30 transition-all"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                    Password <span class="text-[#b96b1c]">*</span>
                </Label>
                <PasswordInput
                    id="password"
                    v-model="form.password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-[#5a9aa4] focus:ring-2 focus:ring-[#8bc5cd]/30 transition-all"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password_confirmation" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                    Konfirmasi Password <span class="text-[#b96b1c]">*</span>
                </Label>
                <PasswordInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-[#5a9aa4] focus:ring-2 focus:ring-[#8bc5cd]/30 transition-all"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                :tabindex="5"
                :disabled="form.processing"
                class="w-full bg-[#b96b1c] hover:bg-[#8a4e12] text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm active:scale-[0.99] mt-1"
            >
                <Spinner v-if="form.processing" class="w-4 h-4" />
                {{ form.processing ? 'Mendaftar...' : 'Buat Akun' }}
            </Button>
        </div>
    </form>
</template>