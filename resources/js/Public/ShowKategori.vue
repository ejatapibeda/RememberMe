<template>
  <div class="container mx-auto px-4 py-12 flex flex-col items-center">
    <h1 class="font-kaushan text-4xl text-gray-800 mb-12">Detail Kategori</h1>

    <div v-if="isLoading" class="text-gray-500 font-poppins">Memuat data...</div>

    <div v-else-if="currentKategori" class="w-full max-w-3xl bg-[#e6ffed] rounded-2xl p-10 flex flex-col md:flex-row items-center justify-between shadow-sm">
      <h2 class="text-2xl font-medium text-gray-800 font-serif">
        {{ currentKategori.nama_kategori }}
      </h2>

      <div class="flex gap-4 mt-6 md:mt-0">
        <router-link 
          :to="`/Edit_Kategori/${currentKategori.id}`"
          class="px-8 py-1.5 bg-[#87cefa] text-black font-bold rounded-full hover:bg-sky-400 transition-all shadow-md text-center"
        >
          edit
        </router-link>
        
        <button 
          @click="triggerConfirmDelete"
          class="px-8 py-1.5 bg-[#87cefa] text-black font-bold rounded-full hover:bg-red-500 hover:text-white transition-all shadow-md"
        >
          hapus
        </button>
      </div>
    </div>

    <div v-else class="text-red-500">Kategori tidak ditemukan.</div>

    <router-link to="/kategori" class="mt-8 text-gray-400 hover:text-gray-600 flex items-center gap-2">
      <span>← Kembali</span>
    </router-link>

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
import { onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useKategoriStore } from '@/Stores/Kategori';
import { storeToRefs } from 'pinia';
import AlertModal from "../components/AlertModal.vue"; 

const route = useRoute();
const router = useRouter();
const kategoriStore = useKategoriStore();

const { currentKategori, isLoading } = storeToRefs(kategoriStore);

const alertConfig = reactive({
  show: false,
  type: 'success',
  title: '',
  message: '',
  isConfirming: false,
  showCancel: false 
});

// --- PERBAIKAN: Fungsi ini HARUS ADA agar data muncul ---
onMounted(async () => {
  const id = route.params.id;
  if (id) {
    await kategoriStore.fetchKategoriById(id);
  }
});

const triggerConfirmDelete = () => {
  alertConfig.type = 'error'; 
  alertConfig.title = 'Konfirmasi';
  alertConfig.message = 'Apakah anda yakin ingin menghapus kategori ini?';
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
  if (alertConfig.isConfirming) {
    alertConfig.show = false; 
    alertConfig.isConfirming = false;
    alertConfig.showCancel = false;

    const success = await kategoriStore.deleteKategori(route.params.id);
    
    if (success) {
      alertConfig.type = 'success';
      alertConfig.title = 'Berhasil!';
      alertConfig.message = 'Kategori telah dihapus dari sistem.';
      alertConfig.show = true;
    } else {
      alertConfig.type = 'error';
      alertConfig.title = 'Gagal!';
      alertConfig.message = 'Gagal menghapus kategori.';
      alertConfig.show = true;
    }
  } else {
    alertConfig.show = false;
    if (alertConfig.type === 'success') {
      router.push('/kategori');
    }
  }
};
</script>

<style scoped>
.font-kaushan {
  font-family: 'Kaushan Script', cursive;
}
</style>