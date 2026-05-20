<template>
    <div class="container mx-auto px-4 py-12 flex flex-col items-center">
        <h1 class="font-kaushan text-4xl text-gray-800 mb-10">Edit Catatan</h1>

        <div v-if="isLoading" class="flex flex-col items-center gap-4 mt-10">
            <div class="animate-spin h-10 w-10 border-4 border-blue-400 border-t-transparent rounded-full"></div>
            <p class="font-kaushan text-xl text-gray-500">Memuat data...</p>
        </div>

        <div v-else class="relative w-full max-w-2xl bg-white/40 backdrop-blur-xl rounded-[40px] p-8 md:p-12 shadow-2xl border border-white/50 overflow-hidden">
            
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-green-300/20 rounded-full blur-2xl"></div>

            <form @submit.prevent="handleUpdate" class="relative z-10 space-y-8">
                
                <div class="space-y-2">
                    <label class="block font-kaushan text-2xl text-gray-700 ml-2">Apa rencananya?</label>
                    <input v-model="form.tugas" type="text"
                        class="w-full p-5 rounded-2xl bg-white/60 border border-white focus:bg-white focus:ring-4 focus:ring-green-200 focus:outline-none transition-all shadow-sm font-sans text-lg text-gray-700"
                        placeholder="Contoh: Belajar Vue JS">
                    <p v-if="errors.tugas" class="text-red-500 text-xs mt-1 ml-2 italic">{{ errors.tugas[0] }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block font-kaushan text-2xl text-gray-700 ml-2">Kategori</label>
                        <select v-model="form.id_kategori" 
                            class="w-full p-5 rounded-2xl bg-white/60 border border-white focus:bg-white focus:ring-4 focus:ring-green-200 focus:outline-none transition-all shadow-sm font-sans text-gray-700 appearance-none">
                            <option value="" disabled>Pilih Kategori</option>
                            <option v-for="cat in kategoriList" :key="cat.id" :value="cat.id">
                                {{ cat.nama_kategori }}
                            </option>
                        </select>
                        <p v-if="errors.id_kategori" class="text-red-500 text-xs mt-1 ml-2 italic">{{ errors.id_kategori[0] }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block font-kaushan text-2xl text-gray-700 ml-2">Kapan?</label>
                        <input v-model="form.tanggal" type="datetime-local"
                            class="w-full p-5 rounded-2xl bg-white/60 border border-white focus:bg-white focus:ring-4 focus:ring-green-200 focus:outline-none transition-all shadow-sm font-sans text-gray-700">
                        <p v-if="errors.tanggal" class="text-red-500 text-xs mt-1 ml-2 italic">{{ errors.tanggal[0] }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block font-kaushan text-2xl text-gray-700 ml-2">Penting banget?</label>
                    <div class="flex p-1 bg-gray-200/50 backdrop-blur-sm w-fit rounded-full border border-white/20">
                        <button type="button" @click="form.prioritas = 'ya'"
                            :class="form.prioritas === 'ya' ? 'bg-yellow-400 text-black shadow-md' : 'text-gray-500 hover:text-gray-700'"
                            class="px-8 py-3 rounded-full font-bold transition-all flex items-center gap-2">
                            <span v-if="form.prioritas === 'ya'">⭐</span> Ya
                        </button>
                        <button type="button" @click="form.prioritas = 'tidak'"
                            :class="form.prioritas === 'tidak' ? 'bg-gray-800 text-white shadow-md' : 'text-gray-500 hover:text-gray-700'"
                            class="px-8 py-3 rounded-full font-bold transition-all">Tidak</button>
                    </div>
                </div>

                <div class="flex flex-col-reverse md:flex-row justify-end items-center gap-6 pt-6 border-t border-white/30">
                    <router-link to="/tugas" class="px-8 py-3 font-kaushan text-xl text-gray-500 hover:text-red-500 transition-colors">
                        Batal
                    </router-link>
                    <button type="submit" :disabled="tugasStore.isLoading"
                        class="w-full md:w-auto px-12 py-4 bg-gradient-to-r from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white rounded-full font-kaushan text-2xl shadow-xl shadow-blue-200 transition-all active:scale-95 disabled:grayscale">
                        {{ tugasStore.isLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>

        <AlertModal 
            :show="alertConfig.show"
            :type="alertConfig.type"
            :title="alertConfig.title"
            :message="alertConfig.message"
            @confirm="handleAlertConfirm"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import AlertModal from "../components/AlertModal.vue"; 
import { useTugasStore } from '@/Stores/Tugas';
import { useKategoriStore } from '@/Stores/Kategori';

const route = useRoute();
const router = useRouter();
const tugasStore = useTugasStore();
const kategoriStore = useKategoriStore();

const { kategoriList } = storeToRefs(kategoriStore);
const { errors } = storeToRefs(tugasStore);

const isLoading = ref(true);
const form = ref({
    tugas: '',
    id_kategori: '',
    tanggal: '',
    prioritas: 'tidak'
});

// State untuk konfigurasi AlertModal
const alertConfig = reactive({
    show: false,
    type: 'success',
    title: '',
    message: ''
});

onMounted(async () => {
    await kategoriStore.fetchKategori(); 
    
    const id = route.params.id;
    const data = await tugasStore.showTugas(id); 
    
    if (data) {
        form.value = {
            tugas: data.tugas,
            id_kategori: data.id_kategori,
            tanggal: data.tanggal ? data.tanggal.slice(0, 16) : '',
            prioritas: data.prioritas
        };
    }
    isLoading.value = false;
});

const handleUpdate = async () => {
    const id = route.params.id;
    const result = await tugasStore.updateTugas(id, form.value); 
    
    if (result.success) {
        alertConfig.type = 'success';
        alertConfig.title = 'Berhasil!';
        alertConfig.message = 'Tugas berhasil diperbarui.';
        alertConfig.show = true;
    } else {
        // Jika ada error validasi atau server
        alertConfig.type = 'error';
        alertConfig.title = 'Gagal!';
        alertConfig.message = 'Gagal memperbarui tugas. Cek kembali inputan Anda.';
        alertConfig.show = true;
    }
};

const handleAlertConfirm = () => {
    alertConfig.show = false;
    // Jika berhasil, arahkan kembali ke daftar tugas
    if (alertConfig.type === 'success') {
        router.push('/tugas');
    }
};
</script>

