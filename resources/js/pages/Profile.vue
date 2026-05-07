<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page    = usePage()
const success = computed(() => page.props.flash?.success)
const props   = defineProps({ user: Object })

const editMode      = ref(false)
const focused       = ref(null)
const avatarInput   = ref(null)
const avatarPreview = ref(
    props.user.avatar ? `/storage/${props.user.avatar}` : null
)

const form = useForm({
    name:       props.user.name       || '',
    email:      props.user.email      || '',
    phone:      props.user.phone      || '',
    birth_date: props.user.birth_date ? props.user.birth_date.substring(0, 10) : '',
    gender:     props.user.gender     || '',
    address:    props.user.address    || '',
})

function submit() {
    form.patch('/profile/update', {
        onSuccess: () => { editMode.value = false },
    })
}

function cancelEdit() {
    form.reset()
    form.name       = props.user.name       || ''
    form.email      = props.user.email      || ''
    form.phone      = props.user.phone      || ''
    form.birth_date = props.user.birth_date ? props.user.birth_date.substring(0, 10) : ''
    form.gender     = props.user.gender     || ''
    form.address    = props.user.address    || ''
    editMode.value  = false
}

function triggerAvatarUpload() { avatarInput.value?.click() }

function onAvatarChange(e) {
    const file = e.target.files[0]
    if (!file) return
    avatarPreview.value = URL.createObjectURL(file)
    const data = new FormData()
    data.append('avatar', file)
    router.post('/profile/avatar', data, { forceFormData: true, preserveScroll: true })
}

const initials = computed(() =>
    props.user.name
        ? props.user.name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
        : '?'
)

const genderLabel = computed(() => {
    const map = { male: 'Laki-laki', female: 'Perempuan', other: 'Lainnya' }
    return map[props.user.gender] || '-'
})

const birthDateFormatted = computed(() => {
    if (!props.user.birth_date) return '-'
    const d = new Date(props.user.birth_date)
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
})
</script>

<template>
    <div class="min-h-screen bg-[#fdf8ef] flex flex-col">
        <div class="flex-1 max-w-3xl mx-auto w-full px-4 md:px-6 py-8 md:py-10">

            <!-- Header -->
            <div class="mb-8">
                <p class="text-[11px] font-bold text-[#5a9aa4] tracking-[3px] uppercase mb-1">Akun Saya</p>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight" style="font-family:'Sora',sans-serif;">
                    Profil Saya
                </h1>
            </div>

            <!-- Success toast -->
            <Transition name="fade-down">
                <div v-if="success" class="mb-6 bg-green-50 border-2 border-green-200 rounded-2xl px-5 py-3.5 flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-sm text-green-700 font-semibold">{{ success }}</p>
                </div>
            </Transition>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Kiri: Avatar card -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl border-2 border-[#8bc5cd] p-6 text-center shadow-sm sticky top-6">
                        <div class="relative inline-block mb-4">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-[#8bc5cd] shadow-md mx-auto">
                                <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full bg-[#d4eef1] flex items-center justify-center text-[#5a9aa4] font-extrabold text-2xl">
                                    {{ initials }}
                                </div>
                            </div>
                            <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                        </div>

                        <p class="font-extrabold text-gray-900 text-base">{{ user.name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 mb-5">{{ user.email }}</p>

                        <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="onAvatarChange" />
                        <button
                            @click="triggerAvatarUpload"
                            class="w-full flex items-center justify-center gap-2 bg-[#d4eef1] hover:bg-[#5a9aa4] text-[#5a9aa4] hover:text-white border-2 border-[#8bc5cd] hover:border-[#5a9aa4] text-xs font-bold py-2.5 rounded-xl transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            + Upload Foto
                        </button>
                        <p class="text-[10px] text-gray-400 mt-2">JPG, PNG, WebP · Maks 2MB</p>
                    </div>
                </div>

                <!-- Kanan: View / Edit -->
                <div class="md:col-span-2">
                    <Transition name="slide-mode" mode="out-in">

                        <!-- ── VIEW MODE ─────────────────────────────────── -->
                        <div v-if="!editMode" key="view" class="bg-white rounded-2xl border-2 border-[#8bc5cd] overflow-hidden shadow-sm">

                            <!-- Header view -->
                            <div class="flex items-center justify-between bg-gradient-to-r from-[#d4eef1] to-[#fdf8ef] border-b-2 border-[#8bc5cd] px-6 py-4">
                                <div>
                                    <h2 class="text-base font-extrabold text-gray-900">Informasi Pribadi</h2>
                                    <p class="text-xs text-gray-500 mt-0.5">Data diri kamu</p>
                                </div>
                                <button
                                    @click="editMode = true"
                                    class="flex items-center gap-2 bg-[#b96b1c] hover:bg-[#8a4e12] text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all active:scale-95 shadow-sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Ubah Data Diri
                                </button>
                            </div>

                            <!-- Info list -->
                            <div class="p-6 grid grid-cols-1 gap-0 divide-y divide-gray-50">
                                <div v-for="item in [
                                    { label: 'Nama Lengkap', value: user.name, icon: 'user' },
                                    { label: 'Email',        value: user.email, icon: 'email' },
                                    { label: 'No. Telepon',  value: user.phone || '-', icon: 'phone' },
                                    { label: 'Tanggal Lahir',value: birthDateFormatted, icon: 'calendar' },
                                    { label: 'Gender',       value: genderLabel, icon: 'gender' },
                                    { label: 'Alamat',       value: user.address || '-', icon: 'location' },
                                ]" :key="item.label"
                                    class="flex items-start gap-4 py-4 hover:bg-gray-50/50 transition-colors rounded-xl px-2 -mx-2"
                                >
                                    <!-- Icon -->
                                    <div class="w-8 h-8 rounded-lg bg-[#d4eef1] flex items-center justify-center shrink-0 mt-0.5">
                                        <svg v-if="item.icon === 'user'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <svg v-if="item.icon === 'email'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <svg v-if="item.icon === 'phone'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <svg v-if="item.icon === 'calendar'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <svg v-if="item.icon === 'gender'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <svg v-if="item.icon === 'location'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#5a9aa4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">{{ item.label }}</p>
                                        <p class="text-sm font-semibold text-gray-900 break-words">{{ item.value }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── EDIT MODE ─────────────────────────────────── -->
                        <div v-else key="edit" class="bg-white rounded-2xl border-2 border-[#b96b1c] overflow-hidden shadow-sm">

                            <!-- Header edit -->
                            <div class="flex items-center justify-between bg-gradient-to-r from-[#fee5a1]/60 to-[#fdf8ef] border-b-2 border-[#b96b1c]/40 px-6 py-4">
                                <div>
                                    <h2 class="text-base font-extrabold text-gray-900">Edit Data Diri</h2>
                                    <p class="text-xs text-gray-500 mt-0.5">Ubah informasi profil kamu</p>
                                </div>
                                <button
                                    @click="cancelEdit"
                                    class="flex items-center gap-1.5 text-gray-400 hover:text-gray-600 text-xs font-semibold px-3 py-2 rounded-xl border border-gray-200 hover:border-gray-300 transition-all"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Batal
                                </button>
                            </div>

                            <div class="p-6 flex flex-col gap-4">

                                <!-- Nama -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-[#b96b1c]">*</span></label>
                                    <input v-model="form.name" @focus="focused='name'" @blur="focused=null" type="text" placeholder="Nama lengkap"
                                        class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all"
                                        :class="form.errors.name ? 'border-red-300' : focused==='name' ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30' : 'border-gray-200 hover:border-[#8bc5cd]'" />
                                    <p v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Email <span class="text-[#b96b1c]">*</span></label>
                                    <input v-model="form.email" @focus="focused='email'" @blur="focused=null" type="email" placeholder="email@example.com"
                                        class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all"
                                        :class="form.errors.email ? 'border-red-300' : focused==='email' ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30' : 'border-gray-200 hover:border-[#8bc5cd]'" />
                                    <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
                                </div>

                                <!-- No. Telp + Tanggal Lahir -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">No. Telepon</label>
                                        <input v-model="form.phone" @focus="focused='phone'" @blur="focused=null" type="tel" placeholder="08xxxxxxxxxx"
                                            class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all"
                                            :class="focused==='phone' ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30' : 'border-gray-200 hover:border-[#8bc5cd]'" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                                        <input v-model="form.birth_date" @focus="focused='birth_date'" @blur="focused=null" type="date"
                                            class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 bg-gray-50 focus:bg-white focus:outline-none transition-all"
                                            :class="focused==='birth_date' ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30' : 'border-gray-200 hover:border-[#8bc5cd]'" />
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Gender</label>
                                    <div class="flex gap-3">
                                        <button v-for="opt in [
                                            { value: 'male',   label: '♂ Laki-laki' },
                                            { value: 'female', label: '♀ Perempuan' },
                                            { value: 'other',  label: '⚬ Lainnya' },
                                        ]" :key="opt.value" type="button" @click="form.gender = opt.value"
                                            class="flex-1 py-2.5 rounded-xl text-xs font-bold border-2 transition-all"
                                            :class="form.gender === opt.value
                                                ? 'bg-[#5a9aa4] text-white border-[#5a9aa4] shadow-sm'
                                                : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-[#8bc5cd] hover:bg-[#d4eef1] hover:text-[#5a9aa4]'"
                                        >{{ opt.label }}</button>
                                    </div>
                                </div>

                                <!-- Alamat -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Alamat</label>
                                    <textarea v-model="form.address" @focus="focused='address'" @blur="focused=null" rows="3" placeholder="Jalan, Kota, Provinsi, Kode Pos"
                                        class="w-full border-2 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none transition-all resize-none"
                                        :class="focused==='address' ? 'border-[#5a9aa4] ring-2 ring-[#8bc5cd]/30' : 'border-gray-200 hover:border-[#8bc5cd]'"></textarea>
                                </div>

                                <!-- Buttons -->
                                <div class="flex gap-3 mt-1">
                                    <button @click="cancelEdit"
                                        class="flex-1 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-500 hover:border-gray-300 hover:bg-gray-50 transition-all">
                                        Batal
                                    </button>
                                    <button @click="submit" :disabled="form.processing"
                                        class="flex-1 bg-[#b96b1c] hover:bg-[#8a4e12] active:scale-[0.99] disabled:opacity-50 text-white text-sm font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                                        <svg v-if="form.processing" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                        </svg>
                                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </Transition>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-down-enter-active, .fade-down-leave-active { transition: all 0.3s ease; }
.fade-down-enter-from, .fade-down-leave-to { opacity: 0; transform: translateY(-8px); }

.slide-mode-enter-active, .slide-mode-leave-active { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-mode-enter-from { opacity: 0; transform: translateX(16px); }
.slide-mode-leave-to   { opacity: 0; transform: translateX(-16px); }
</style>
