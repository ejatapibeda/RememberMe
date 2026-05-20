<template>
  <div class="container mx-auto px-4 py-12 flex flex-col items-center">
    <h1 class="font-kaushan text-4xl text-gray-800 mb-12">Edit Kategori</h1>
    
    <div class="w-full max-w-xl  p-8 rounded-3xl shadow-lg border border-gray-200">
      <form @submit.prevent="handleUpdate">
        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
          <input 
            v-model="form.nama_kategori" 
            type="text" 
            class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-sky-300 transition-all" 
            required 
          />
        </div>
        <div class="flex gap-4">
          <button 
            type="submit" 
            :disabled="isLoading" 
            class="flex-1 bg-[#87cefa] text-black font-bold py-3 rounded-full hover:bg-sky-400 transition-all shadow-md"
          >
            {{ isLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
          <button 
            type="button" 
            @click="$router.back()" 
            class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-full hover:bg-gray-300 transition-all"
          >
            Batal
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
import { useKategoriStore } from '@/Stores/Kategori';
import { storeToRefs } from 'pinia';
import AlertModal from "../components/AlertModal.vue"; 

const route = useRoute();
const router = useRouter();
const kategoriStore = useKategoriStore();
const { isLoading } = storeToRefs(kategoriStore);

const form = ref({ nama_kategori: '' });

// State untuk mengatur AlertModal
const alertConfig = reactive({
  show: false,
  type: 'success',
  title: '',
  message: ''
});

onMounted(async () => {
  const data = await kategoriStore.fetchKategoriById(route.params.id);
  if (data) {
    form.value.nama_kategori = data.nama_kategori;
  }
});

const handleUpdate = async () => {
  const success = await kategoriStore.updateKategori(route.params.id, form.value);
  
  if (success) {
    alertConfig.type = 'success';
    alertConfig.title = 'Berhasil!';
    alertConfig.message = 'Kategori berhasil diperbarui.';
    alertConfig.show = true;
  } else {
    alertConfig.type = 'error';
    alertConfig.title = 'Gagal!';
    alertConfig.message = 'Terjadi kesalahan saat memperbarui kategori.';
    alertConfig.show = true;
  }
};

// Fungsi yang dijalankan saat tombol OK di modal diklik
const handleAlertConfirm = () => {
  alertConfig.show = false;
  // Jika sukses, baru kita pindah halaman
  if (alertConfig.type === 'success') {
    router.push(`/Show_Kategori/${route.params.id}`);
  }
};
</script>