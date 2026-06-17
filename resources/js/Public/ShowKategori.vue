<template>
  <div class="max-w-2xl mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/kategori" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>

    <div v-if="isLoading" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <GlassCard v-else-if="kategori" padding="lg">
      <div class="flex items-start gap-4 mb-4">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl bg-gradient-to-br from-blue-200 to-cyan-200 shadow">
          📂
        </div>
        <div class="flex-1 min-w-0">
          <h1 class="font-kaushan text-3xl text-gray-800">{{ kategori.nama_kategori }}</h1>
          <p class="text-xs text-gray-500 mt-1">ID #{{ kategori.id }}</p>
        </div>
      </div>

      <div class="flex gap-2 mt-4 flex-wrap">
        <router-link :to="`/Edit_Kategori/${kategori.id}`">
          <GradientButton variant="primary">Edit</GradientButton>
        </router-link>
      </div>
    </GlassCard>

    <div v-else class="text-center py-20 opacity-60">
      <p class="font-kaushan text-3xl text-gray-400">Kategori tidak ditemukan</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useKategoriStore } from '@/Stores/Kategori';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';

const route = useRoute();
const kategoriStore = useKategoriStore();
const kategori = ref(null);
const isLoading = ref(true);

onMounted(async () => {
  kategori.value = await kategoriStore.fetchKategoriById(route.params.id);
  isLoading.value = false;
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
