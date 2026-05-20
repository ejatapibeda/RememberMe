<template>
    <div class="container mx-auto px-4 py-12 flex flex-col items-center min-h-screen">
        <h1 class="font-kaushan text-4xl text-gray-800 mb-10">Detail Tugas</h1>

        <div v-if="tugasStore.isLoading" class="text-xl font-kaushan animate-pulse">Memuat...</div>

        <div v-else-if="tugas" class="relative w-full max-w-2xl transform transition-all hover:scale-[1.01]">
            <div 
                class="rounded-[40px] shadow-xl p-10 pt-16 min-h-[350px] relative flex flex-col justify-between border border-white/50 backdrop-blur-sm"
                :class="currentTheme.bg"
            >
                <div 
                    class="absolute -top-4 left-8 px-6 py-3 rounded-2xl flex items-center gap-3 shadow-lg border border-white/60 transition-all duration-500"
                    :style="{ backgroundColor: tugas.prioritas === 'ya' ? '#FFDF5E' : 'rgba(255, 255, 255, 0.8)' }"
                >
                    <CalendarDaysIcon class="w-6 h-6 text-gray-800" />
                    <span class="font-sans font-bold text-gray-700">
                        {{ formatDateTime(tugas.tanggal) }}
                    </span>
                    <span v-if="tugas.prioritas === 'ya'" class="text-xs animate-bounce">⭐</span>
                </div>

                <div class="absolute top-8 right-10">
                    <span class="bg-white/40 px-4 py-1 rounded-full font-dancing text-2xl text-gray-800 shadow-sm border border-white/20">
                        {{ tugas.kategori?.nama_kategori || 'Kategori' }}
                    </span>
                </div>

                <div class="mt-10 mb-12 px-2">
                    <p class="text-gray-800 text-3xl leading-relaxed font-kaushan text-center italic" :class="currentTheme.text">
                        {{ tugas.tugas }}
                    </p>
                </div>

                <div class="flex justify-center gap-4">
                    <button 
                        @click="router.push({ name: 'edit-tugas', params: { id: tugas.id } })"
                        class="bg-white/80 hover:bg-white text-gray-800 font-bold px-10 py-3 rounded-full transition-all shadow-md active:scale-95 flex items-center gap-2"
                    >
                        <span>✏️</span> Edit
                    </button>
                    <button 
                        @click="handleDeleteRequest"
                        class="bg-red-500/80 hover:bg-red-500 text-white font-bold px-10 py-3 rounded-full transition-all shadow-md active:scale-95 flex items-center gap-2"
                    >
                        <span>🗑️</span> Hapus
                    </button>
                </div>
            </div>
        </div>
        
        <div v-else class="text-red-500 font-kaushan text-xl mt-10 bg-white p-4 rounded-xl shadow">
            Tugas tidak ditemukan.
        </div>

        <AlertModal 
            :show="alertConfig.show"
            :type="alertConfig.type"
            :title="alertConfig.title"
            :message="alertConfig.message"
            :show-cancel="alertConfig.showCancel"
            @confirm="handleAlertConfirm"
            @cancel="handleAlertCancel"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { CalendarDaysIcon } from '@heroicons/vue/24/outline';
import AlertModal from "../components/AlertModal.vue"; 
import { useTugasStore } from '@/Stores/Tugas';

// 1. Inisialisasi Router & Store
const route = useRoute();
const router = useRouter();
const tugasStore = useTugasStore();

// 2. State Management
const tugas = ref(null);
const currentTheme = ref({ bg: 'bg-blue-100', text: 'text-blue-900' });

// Konfigurasi Alert/Modal
const alertConfig = reactive({
    show: false,
    type: 'success',
    title: '',
    message: '',
    isConfirming: false,
    showCancel: false
});

// Daftar palet warna pastel Sky Blue
const themes = [
    { bg: 'bg-[#E0F2FE]', text: 'text-blue-900' }, // Sky Blue 100
    { bg: 'bg-[#CCEAFF]', text: 'text-sky-900' },  // Sky Blue 50
    { bg: 'bg-[#E0FCFF]', text: 'text-cyan-900' }, // Cyan
    { bg: 'bg-[#ECFEFF]', text: 'text-teal-900' }, // Teal
    { bg: 'bg-[#EEF2FF]', text: 'text-indigo-900' }, // Indigo
    { bg: 'bg-[#F5F3FF]', text: 'text-violet-900' }, // Violet
];

// 3. Lifecycle Hooks (Digabung menjadi satu)
onMounted(async () => {
    // Pilih tema warna secara acak
    currentTheme.value = themes[Math.floor(Math.random() * themes.length)];

    // Ambil data tugas berdasarkan ID dari URL
    const id = route.params.id;
    if (id) {
        const result = await tugasStore.showTugas(id);
        tugas.value = result; 
    }
});

// 4. Helper Functions
const formatDateTime = (date) => {
    if (!date) return "";
    const d = new Date(date);
    return d.toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric' 
    }).replace(/\//g, '-');
};

// 5. Logic Hapus & Modal
const handleDeleteRequest = () => {
    alertConfig.type = 'error';
    alertConfig.title = 'Konfirmasi';
    alertConfig.message = 'Apakah Anda yakin ingin menghapus tugas ini?';
    alertConfig.isConfirming = true;
    alertConfig.showCancel = true;
    alertConfig.show = true;
};

const handleAlertCancel = () => {
    alertConfig.show = false;
    alertConfig.isConfirming = false;
    alertConfig.showCancel = false;
};

const handleAlertConfirm = async () => {
    // Jika dalam mode tanya/konfirmasi
    if (alertConfig.isConfirming) {
        alertConfig.show = false;
        alertConfig.isConfirming = false;
        alertConfig.showCancel = false;

        const success = await tugasStore.deleteTugas(tugas.value.id);
        
        // Tampilkan modal hasil eksekusi
        if (success) {
            alertConfig.type = 'success';
            alertConfig.title = 'Terhapus!';
            alertConfig.message = 'Tugas berhasil dihapus dari daftar.';
            alertConfig.show = true;
        } else {
            alertConfig.type = 'error';
            alertConfig.title = 'Gagal!';
            alertConfig.message = 'Terjadi kesalahan saat menghapus tugas.';
            alertConfig.show = true;
        }
    } else {
        // Jika modal hasil sukses diklik OK, arahkan kembali ke list tugas
        alertConfig.show = false;
        if (alertConfig.type === 'success') {
            router.push('/tugas');
        }
    }
};
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Dancing+Script:wght@600;700&family=Kaushan+Script&display=swap');

    .font-kaushan {
        font-family: 'Segoe Print', cursive;
        /* font-family: 'Kaushan Script', cursive; */
    }

    .font-dancing {
        font-family: 'Sanchez', cursive;
        /* font-family: 'Dancing Script', cursive; */
    }

    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
</style>