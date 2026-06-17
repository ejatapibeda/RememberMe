<template>
  <div class="max-w-2xl mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/tugas" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>
    <h1 class="font-kaushan text-4xl text-gray-800 mb-6">Buat Tugas Baru</h1>

    <GlassCard padding="lg">
      <div class="space-y-5">
        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Batas Waktu</label>
          <input type="datetime-local" v-model="form.date"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm" />
        </div>

        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Kategori</label>
          <select v-model="form.id_kategori"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm">
            <option value="" disabled>{{ loadingKategori ? 'Memuat...' : 'Pilih kategori' }}</option>
            <option v-for="cat in kategoriList" :key="cat.id" :value="cat.id">{{ cat.nama_kategori }}</option>
          </select>
          <router-link v-if="!loadingKategori && kategoriList.length === 0" to="/Create_kategori"
            class="text-xs font-bold text-blue-600 underline mt-2 inline-block">+ Buat kategori dulu</router-link>
        </div>

        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Catatan / Tugas</label>
          <textarea v-model="form.task" rows="4" placeholder="Apa yang harus dikerjakan?"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm resize-none"></textarea>
        </div>

        <button @click="form.isPriority = !form.isPriority"
          class="inline-flex items-center gap-3 px-4 py-2.5 rounded-2xl border transition-all w-full"
          :class="form.isPriority
            ? 'bg-gradient-to-r from-amber-100 to-orange-100 border-amber-300'
            : 'bg-white/80 border-gray-200 hover:border-amber-300'">
          <span class="w-5 h-5 rounded-md flex items-center justify-center"
            :class="form.isPriority ? 'bg-amber-500 text-white' : 'border-2 border-gray-300'">
            <CheckIcon v-if="form.isPriority" class="w-3 h-3" />
          </span>
          <span class="text-sm font-semibold text-gray-700">🔥 Tandai sebagai Prioritas</span>
        </button>

        <div class="flex gap-2 pt-2">
          <GradientButton variant="primary" :loading="loadingTugas" @click="submitTask">
            <PlusCircleIcon class="w-5 h-5" /> Simpan Tugas
          </GradientButton>
          <router-link to="/tugas">
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
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { PlusCircleIcon, CheckIcon } from '@heroicons/vue/24/outline';
import AlertModal from '@/components/AlertModal.vue';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import { useKategoriStore } from '@/Stores/Kategori';
import { useTugasStore } from '@/Stores/Tugas';

const router = useRouter();
const kategoriStore = useKategoriStore();
const tugasStore = useTugasStore();
const { kategoriList, isLoading: loadingKategori } = storeToRefs(kategoriStore);
const { isLoading: loadingTugas } = storeToRefs(tugasStore);

const form = ref({ date: '', id_kategori: '', task: '', isPriority: false });
const alert = ref({ show: false, title: '', message: '', type: 'success' });

onMounted(() => kategoriStore.fetchKategori());

const submitTask = async () => {
  if (!form.value.id_kategori || !form.value.task || !form.value.date) {
    alert.value = { show: true, title: 'Ups!', message: 'Semua kolom wajib diisi.', type: 'error' };
    return;
  }
  const r = await tugasStore.createTugas({
    id_kategori: form.value.id_kategori,
    tanggal: form.value.date,
    tugas: form.value.task,
    prioritas: form.value.isPriority ? 'ya' : 'tidak',
  });
  alert.value = {
    show: true,
    title: r.success ? 'Berhasil!' : 'Gagal!',
    message: r.success ? 'Tugas kamu sudah tersimpan.' : (r.message || 'Terjadi gangguan pada server.'),
    type: r.success ? 'success' : 'error',
  };
};

const closeAlert = () => {
  const ok = alert.value.type === 'success';
  alert.value.show = false;
  if (ok) router.push('/tugas');
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&family=Poppins:wght@400;500;600;700&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
