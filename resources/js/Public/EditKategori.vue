<template>
  <div class="max-w-md mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/kategori" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>
    <h1 class="font-kaushan text-4xl text-gray-800 mb-6">Edit Kategori</h1>

    <GlassCard padding="lg">
      <div class="space-y-5">
        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Nama Kategori</label>
          <input v-model="form.nama_kategori" type="text"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm" />
          <p v-if="errors.nama_kategori" class="text-xs text-rose-500 mt-1 italic">{{ errors.nama_kategori[0] }}</p>
        </div>

        <div class="flex gap-2">
          <GradientButton variant="primary" :loading="isLoading" @click="submit">Simpan Perubahan</GradientButton>
          <router-link to="/kategori">
            <GradientButton variant="ghost">Batal</GradientButton>
          </router-link>
        </div>
      </div>
    </GlassCard>

    <AlertModal :show="alert.show" :title="alert.title" :message="alert.message" :type="alert.type" @confirm="closeAlert" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import AlertModal from '@/components/AlertModal.vue';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import { useKategoriStore } from '@/Stores/Kategori';

const route = useRoute();
const router = useRouter();
const kategoriStore = useKategoriStore();
const { isLoading, errors } = storeToRefs(kategoriStore);

const form = ref({ nama_kategori: '' });
const alert = ref({ show: false, title: '', message: '', type: 'success' });
const id = route.params.id;

onMounted(async () => {
  const data = await kategoriStore.fetchKategoriById(id);
  if (data) form.value.nama_kategori = data.nama_kategori;
});

const submit = async () => {
  const ok = await kategoriStore.updateKategori(id, form.value);
  alert.value = {
    show: true,
    title: ok ? 'Tersimpan' : 'Gagal',
    message: ok ? 'Kategori berhasil diperbarui.' : 'Tidak bisa menyimpan.',
    type: ok ? 'success' : 'error',
  };
};

const closeAlert = () => {
  const ok = alert.value.type === 'success';
  alert.value.show = false;
  if (ok) router.push('/kategori');
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
