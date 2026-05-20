<template>
    <div class="min-h-screen flex flex-col items-center justify-center  py-10 px-4">
        <h1 class="font-kaushan text-4xl text-black mb-8">Buat Tugas</h1>

        <div class="w-full max-w-md bg-[#D0C9FF] rounded-[40px] p-8 shadow-xl flex flex-col items-center gap-5">
            <div class="w-full">
                <label class="block text-sm font-bold text-gray-700 mb-1 px-1">Batas Waktu</label>
                <input type="datetime-local" v-model="form.date"
                    class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-400 text-gray-700 shadow-sm bg-white" />
            </div>

            <div class="w-full">
                <label class="block text-sm font-bold text-gray-700 mb-1 px-1">Kategori</label>
                <select v-model="form.id_kategori"
                    class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-400 text-gray-700 shadow-sm bg-white">
                    <option value="" disabled selected>
                        {{ loadingKategori ? 'Memuat Kategori...' : 'Pilih Kategori' }}
                    </option>
                    <option v-for="cat in kategoriList" :key="cat.id" :value="cat.id">
                        {{ cat.nama_kategori }}
                    </option>
                </select>
                <div v-if="!loadingKategori && kategoriList.length === 0" class="mt-2 text-center">
                    <router-link to="/Create_Kategori" class="text-xs font-bold text-purple-600 underline">
                        + Buat Kategori Baru
                    </router-link>
                </div>
            </div>

            <div class="w-full">
                <label class="block text-sm font-bold text-gray-700 mb-1 px-1">Catatan</label>
                <textarea v-model="form.task" placeholder="Apa tugasnya?" rows="4"
                    class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-400 text-gray-700 placeholder-gray-400 shadow-sm resize-none"></textarea>
            </div>

            <div @click="form.isPriority = !form.isPriority"
                :class="[form.isPriority ? 'bg-green-200' : 'bg-green-50', 'flex items-center gap-3 px-5 py-2 rounded-full cursor-pointer shadow-sm transition-all select-none border border-green-200']">
                <div class="w-5 h-5 bg-white rounded-md flex items-center justify-center border border-gray-200">
                    <div v-if="form.isPriority" class="w-3 h-3 bg-green-500 rounded-sm"></div>
                </div>
                <span class="text-gray-700 font-bold text-sm">Prioritas</span>
            </div>

            <button @click="submitTask" :disabled="loadingTugas"
                class="mt-4 flex items-center gap-2 bg-[#FFD6D6] px-10 py-3 rounded-full shadow-lg hover:bg-pink-200 hover:scale-105 active:scale-95 transition-all disabled:opacity-50">
                <PlusCircleIcon class="w-6 h-6 text-black" />
                <span class="font-kaushan text-2xl text-black">Buat Tugas</span>
            </button>
        </div>
    </div>

    <AlertModal 
        :show="alert.show" 
        :title="alert.title" 
        :message="alert.message" 
        :type="alert.type"
        @confirm="closeAlert" 
    />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { PlusCircleIcon } from '@heroicons/vue/24/outline';

import AlertModal from '../components/AlertModal.vue'; // Import komponen
import { useKategoriStore } from '@/Stores/Kategori';
import { useTugasStore } from '@/Stores/Tugas';

const router = useRouter();
const kategoriStore = useKategoriStore();
const tugasStore = useTugasStore();

const { kategoriList, isLoading: loadingKategori } = storeToRefs(kategoriStore);
const { isLoading: loadingTugas } = storeToRefs(tugasStore);

const form = ref({ date: '', id_kategori: '', task: '', isPriority: false });

// State untuk AlertModal
const alert = ref({ show: false, title: '', message: '', type: 'success' });

onMounted(() => kategoriStore.fetchKategori());

const submitTask = async () => {
    // Validasi input
    if (!form.value.id_kategori || !form.value.task || !form.value.date) {
        alert.value = {
            show: true,
            title: 'Ups!',
            message: 'Semua kolom wajib diisi ya!',
            type: 'error'
        };
        return;
    }

    const result = await tugasStore.createTugas({
        id_kategori: form.value.id_kategori,
        tanggal: form.value.date,
        tugas: form.value.task,
        prioritas: form.value.isPriority ? 'ya' : 'tidak'
    });

    if (result.success) {
        alert.value = {
            show: true,
            title: 'Berhasil!',
            message: 'Tugas kamu sudah tersimpan.',
            type: 'success'
        };
    } else {
        alert.value = {
            show: true,
            title: 'Gagal!',
            message: result.message || 'Terjadi gangguan pada server.',
            type: 'error'
        };
    }
};

const closeAlert = () => {
    const wasSuccess = alert.value.type === 'success';
    alert.value.show = false;
    if (wasSuccess) router.push('/tugas'); // Redirect jika sukses
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&family=Poppins:wght@400;600;700&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>