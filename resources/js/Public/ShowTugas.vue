<template>
  <div class="max-w-2xl mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/tugas" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>

    <div v-if="isLoading" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <GlassCard v-else-if="tugas" padding="lg">
      <div class="flex items-start gap-4 mb-5">
        <div
          class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-gradient-to-br from-blue-100 to-cyan-100">
          📌
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex flex-wrap gap-2 mb-2">
            <CountdownBadge :target="tugas.tanggal" />
            <span v-if="tugas.prioritas === 'ya'"
              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
              🔥 PRIORITAS
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
              :class="tugas.is_done ? 'bg-emerald-100 text-emerald-700' : tugas.its_over ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700'">
              {{ tugas.is_done ? 'SELESAI' : tugas.its_over ? 'TERLEWAT' : 'AKTIF' }}
            </span>
          </div>
          <h1 class="font-kaushan text-3xl text-gray-800 leading-tight">{{ tugas.tugas }}</h1>
          <p class="text-sm text-gray-500 mt-1">{{ tugas.kategori?.nama_kategori || 'Tanpa Kategori' }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
        <div class="bg-white/70 border border-gray-100 rounded-2xl p-4">
          <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Deadline</p>
          <p class="text-sm font-semibold text-gray-800">{{ formatDateTime(tugas.tanggal) }}</p>
        </div>
        <div class="bg-white/70 border border-gray-100 rounded-2xl p-4">
          <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Kategori</p>
          <p class="text-sm font-semibold text-gray-800">{{ tugas.kategori?.nama_kategori || '—' }}</p>
        </div>
      </div>

      <div class="flex gap-2 mt-6 flex-wrap">
        <router-link :to="`/Edit_tugas/${tugas.id}`">
          <GradientButton variant="primary">Edit</GradientButton>
        </router-link>
        <GradientButton variant="success" v-if="!tugas.is_done" @click="markDone">Tandai Selesai</GradientButton>
      </div>
    </GlassCard>

    <div v-else class="text-center py-20 opacity-60">
      <p class="font-kaushan text-3xl text-gray-400">Tugas tidak ditemukan</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useTugasStore } from '@/Stores/Tugas';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import CountdownBadge from '@/components/CountdownBadge.vue';

const route = useRoute();
const tugasStore = useTugasStore();
const tugas = ref(null);
const isLoading = ref(true);

onMounted(async () => {
  tugas.value = await tugasStore.showTugas(route.params.id);
  isLoading.value = false;
});

const formatDateTime = (s) => s
  ? new Date(s).toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
  : '';

const markDone = async () => {
  const ok = await tugasStore.updateStatus(tugas.value.id, true);
  if (ok) tugas.value.is_done = true;
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
