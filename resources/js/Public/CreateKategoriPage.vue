<template>
    <div class="min-h-screen flex flex-col items-center justify-center py-10 px-4">
        <h1 class="font-kaushan text-4xl text-black mb-8">Buat Kategori</h1>

        <div class="w-full max-w-md bg-[#D0C9FF] rounded-[40px] p-8 shadow-xl flex flex-col items-center gap-5">
            
            <div class="w-full">
                <input 
                    type="text" 
                    v-model="namaKategori" 
                    placeholder="Nama Kategori (cth: Kuliah)"
                    class="w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 text-gray-700 placeholder-gray-400 shadow-sm" 
                />
                <p v-if="store.errors?.nama_kategori" class="text-red-500 text-sm mt-1 ml-1 font-poppins">
                    {{ store.errors.nama_kategori[0] }}
                </p>
            </div>

            <button 
                @click="handleSubmit"
                :disabled="store.isLoading"
                class="mt-4 flex items-center gap-2 bg-[#FFD6D6] px-8 py-2 rounded-full shadow-md hover:bg-pink-200 hover:scale-105 transform transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                
                <svg v-if="store.isLoading" class="animate-spin h-5 w-5 text-black" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>

                <PlusCircleIcon v-else class="w-7 h-7 text-black" stroke-width="1.5" />
                
                <span class="font-kaushan text-2xl text-black pt-1">
                    {{ store.isLoading ? 'Menyimpan...' : 'Simpan Kategori' }}
                </span>
            </button>
        </div>
    </div>

    <AlertModal 
        :show="alertConfig.show" 
        :title="alertConfig.title" 
        :message="alertConfig.message"
        :type="alertConfig.type"
        @confirm="handleConfirm"
    />
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from "vue-router";
import { PlusCircleIcon } from '@heroicons/vue/24/outline';
import AlertModal from "../components/AlertModal.vue"; 
import { useKategoriStore } from "@/Stores/Kategori.js";

const router = useRouter();
const store = useKategoriStore();

// State Lokal
const namaKategori = ref("");

// Konfigurasi Alert Dinamis
const alertConfig = ref({
    show: false,
    title: '',
    message: '',
    type: 'success'
});

const handleSubmit = async () => {
    // Validasi Frontend
    if (!namaKategori.value.trim()) {
        alertConfig.value = {
            show: true,
            title: 'Ups!',
            message: 'Nama kategori tidak boleh kosong ya.',
            type: 'error'
        };
        return;
    }

    const success = await store.createKategori({ 
        nama_kategori: namaKategori.value 
    });

    if (success) {
        alertConfig.value = {
            show: true,
            title: 'Berhasil!',
            message: 'Kategori baru berhasil dibuat.',
            type: 'success'
        };
        namaKategori.value = ""; 
    } else {
        // Handle Error dari Store
        alertConfig.value = {
            show: true,
            title: 'Gagal!',
            message: store.errors?.general?.[0] || 'Terjadi kesalahan saat menyimpan.',
            type: 'error'
        };
    }
};

const handleConfirm = () => {
    const isSuccess = alertConfig.value.type === 'success';
    alertConfig.value.show = false;
    
    if (isSuccess) {
        router.push('/kategori'); // Redirect hanya jika sukses
    }
};
</script>