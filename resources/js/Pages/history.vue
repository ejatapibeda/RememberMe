<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="font-kaushan text-4xl text-center mb-10 text-gray-800">History Tugas</h1>

    <div v-if="isLoading" class="text-center font-poppins text-gray-500">Memuat riwayat...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="(task, index) in tugasList" :key="task.id"
        :class="[
          getColor(index).bg, 
          'relative w-full h-64 rounded-[40px] shadow-lg p-6 flex flex-col justify-center transform transition hover:scale-105 duration-300'
        ]">
        
        <div class="absolute top-0 left-0 z-10">
          <div class="backdrop-blur-sm px-5 py-3 rounded-br-[35px] flex items-center gap-3 shadow-sm transition-colors"
               :style="{ backgroundColor: task.prioritas === 'ya' ? '#FFD83A' : 'rgba(255, 255, 255, 0.9)' }">
            <CalendarDaysIcon class="w-5 h-5" :class="task.prioritas === 'ya' ? 'text-black' : 'text-gray-600'" />
            <span class="font-bold text-xs" :class="task.prioritas === 'ya' ? 'text-black' : 'text-gray-700'">
              {{ formatDateTime(task.tanggal) }}
            </span>
          </div>
        </div>

        <div class="absolute top-5 right-6 z-20">
          <CheckIcon v-if="task.is_done" class="w-12 h-12 text-blue-600 font-bold" />
          
          <XMarkIcon v-else-if="task.its_over" class="w-12 h-12 text-red-600 font-bold" />
          
          <div v-else class="w-12 h-12 border-2 border-dashed border-gray-400 rounded-full opacity-20"></div>
        </div>

        <div class="text-center" :class="{ 'opacity-40 grayscale': task.its_over && !task.is_done }">
          <h2 class="font-kaushan text-4xl mb-2 leading-tight" :class="getColor(index).text">
            {{ task.tugas }}
          </h2>
          <span class="font-dancing text-2xl opacity-70 block">
            {{ task.kategori?.nama_kategori || 'Tanpa Kategori' }}
          </span>
        </div>
      </div>
    </div>

    <div v-if="!isLoading && tugasList.length === 0" class="text-center py-20 opacity-30 font-kaushan text-3xl">
      Belum ada riwayat tugas...
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useTugasStore } from '@/Stores/Tugas';
import { CalendarDaysIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/solid';

const tugasStore = useTugasStore();
const { tugasList, isLoading } = storeToRefs(tugasStore);

const colorPalette = [
  { bg: 'bg-green-100', text: 'text-green-800' },
  { bg: 'bg-pink-100', text: 'text-pink-800' },
  { bg: 'bg-blue-100', text: 'text-blue-800' },
  { bg: 'bg-yellow-100', text: 'text-yellow-800' }
];

const getColor = (index) => colorPalette[index % colorPalette.length];

// Fungsi memformat Tanggal & Jam
const formatDateTime = (dateTimeString) => {
    if (!dateTimeString) return "";
    const date = new Date(dateTimeString);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).replace(/\//g, '-').replace(',', '');
};

onMounted(async () => {
  await tugasStore.fetchTugas();
});
</script>