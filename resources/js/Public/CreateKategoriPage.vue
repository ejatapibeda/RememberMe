<template>
  <div class="max-w-md mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/kategori" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>
    <h1 class="font-kaushan text-4xl text-gray-800 mb-6">Buat Kategori</h1>

    <GlassCard padding="lg">
      <div class="space-y-5">
        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Nama Kategori</label>
          <input v-model="form.nama_kategori" type="text" placeholder="contoh: Sekolah, Kantor, Hobi"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm" />
          <p v-if="errors.nama_kategori" class="text-xs text-rose-500 mt-1 italic">{{ errors.nama_kategori[0] }}</p>
        </div>

        <div class="flex gap-2">
          <GradientButton variant="primary" :loading="isLoading" @click="submit">
            <PlusCircleIcon class="w-5 h-5" /> Simpan
          </GradientButton>
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { PlusCircleIcon } from '@heroicons/vue/24/outline';
import AlertModal from '@/components/AlertModal.vue';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import { useKategoriStore } from '@/Stores/Kategori';

const router = useRouter();
const kategoriStore = useKategoriStore();
const { isLoading, errors } = storeToRefs(kategoriStore);

const form = ref({ nama_kategori: '' });
const alert = ref({ show: false, title: '', message: '', type: 'success' });

const submit = async () => {
  if (!form.value.nama_kategori) {
    alert.value = { show: true, title: 'Ups!', message: 'Nama kategori wajib diisi.', type: 'error' };
    return;
  }
  const ok = await kategoriStore.createKategori(form.value);
  alert.value = {
    show: true,
    title: ok ? 'Berhasil!' : 'Gagal!',
    message: ok ? 'Kategori berhasil dibuat.' : 'Tidak bisa menyimpan kategori.',
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
