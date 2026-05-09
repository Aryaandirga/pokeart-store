<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Masuk ke Akun',
        description: 'Masukkan email dan password kamu untuk login',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Login" />

    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
        {{ status }}
    </div>

    <form @submit.prevent="submit" class="flex flex-col gap-5">
    <div class="grid gap-5">
        <div class="grid gap-1.5">
            <Label for="email" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                Email <span class="text-[#b96b1c]">*</span>
            </Label>
            <Input
                id="email"
                type="email"
                v-model="form.email"
                required
                autofocus
                :tabindex="1"
                autocomplete="email"
                placeholder="email@example.com"
                class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"
            />
            <InputError :message="form.errors.email" />
        </div>

        <div class="grid gap-1.5">
            <div class="flex items-center justify-between">
                <Label for="password" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                    Password <span class="text-[#b96b1c]">*</span>
                </Label>
                <Link
                    v-if="canResetPassword"
                    :href="request()"
                    :tabindex="5"
                    style="color: #5a9aa4; font-size: 0.75rem; font-weight: 600;"
                >Lupa password?</Link>
            </div>
            <PasswordInput
                id="password"
                v-model="form.password"
                name="password"
                required
                :tabindex="2"
                autocomplete="current-password"
                placeholder="Password"
                class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"
            />
            <InputError :message="form.errors.password" />
        </div>

        <div class="flex items-center gap-2.5">
            <Checkbox id="remember" v-model:checked="form.remember" :tabindex="3" />
            <Label for="remember" class="text-sm text-gray-600 cursor-pointer">Ingat saya</Label>
        </div>

        <Button
            type="submit"
            :tabindex="4"
            :disabled="form.processing"
            class="w-full bg-[#b96b1c] hover:bg-[#8a4e12] text-white font-bold py-3 rounded-xl transition-all"
        >
            <Spinner v-if="form.processing" class="w-4 h-4" />
            {{ form.processing ? 'Masuk...' : 'Masuk' }}
        </Button>
    </div>

    <div v-if="canRegister" class="text-center text-sm text-gray-500 pt-1 border-t border-gray-100">
        Belum punya akun?
        <Link href="/register" style="color: #b96b1c; font-weight: 700;">Daftar Sekarang!</Link>
    </div>
</form>
</template>
