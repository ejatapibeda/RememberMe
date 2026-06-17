<template>
  <div class="py-2">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
      <div>
        <h1 class="font-kaushan text-4xl md:text-5xl text-gray-800">Riwayat</h1>
        <p class="text-sm text-gray-500 mt-1">Tugas yang sudah selesai atau terlewat.</p>
      </div>
      <div class="flex gap-2 flex-wrap">
        <button v-for="opt in tabs" :key="opt.key" @click="activeTab = opt.key"
          :class="[
            'px-4 py-2 rounded-full text-xs font-bold border transition-all',
            activeTab === opt.key
              ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-200'
              : 'bg-white/70 text-gray-600 border-white/60 hover:bg-white'
          ]">
          {{ opt.label }} <span class="opacity-70 ml-1">{{ opt.count }}</span>
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="filtered.length === 0" class="text-center py-20 opacity-60">
      <p class="font-kaushan text-3xl text-gray-400">Belum ada riwayat...</p>
    </div>

    <div v-else class="relative pl-6 md:pl-10">
      <div class="absolute left-2 md:left-4 top-2 bottom-2 w-0.5 bg-gradient-to-b from-blue-300 via-cyan-300 to-transparent"></div>

      <div v-for="(group, gi) in grouped" :key="group.key" class="mb-8">
        <div class="flex items-center gap-3 mb-4 -ml-6 md:-ml-10">
          <div class="w-4 h-4 md:w-6 md:h-6 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 ring-4 ring-white shadow"></div>
          <h2 class="font-kaushan text-2xl text-gray-700">{{ group.label }}</h2>
        </div>

        <div class="space-y-3 ml-2 md:ml-4">
          <div v-for="task in group.items" :key="task.id"
            class="bg-white/80 backdrop-blur-xl rounded-2xl border border-white/60 shadow-md shadow-blue-100/30 p-4 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
              :class="task.is_done ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'">
              {{ task.is_done ? '✅' : '⌛' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap mb-0.5">
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                  :class="task.is_done ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                  {{ task.is_done ? 'Selesai' : 'Terlewat' }}
                </span>
                <span class="text-xs text-gray-500">{{ task.kategori?.nama_kategori || '—' }}</span>
              </div>
              <h3 class="font-semibold text-gray-800 truncate font-poppins">{{ task.tugas }}</h3>
              <p class="text-xs text-gray-500 mt-1">
                <CalendarDaysIcon class="w-3 h-3 inline -mt-0.5" /> {{ formatDateTime(task.tanggal) }}
              </p>
            </div>
            <button v-if="!task.is_done" @click="markDone(task)"
              class="text-xs font-semibold text-blue-600 hover:text-blue-700">
              Tandai Selesai
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { CalendarDaysIcon } from '@heroicons/vue/24/solid';
import { useTugasStore } from '@/Stores/Tugas';

const tugasStore = useTugasStore();
const { tugasList, isLoading } = storeToRefs(tugasStore);

onMounted(() => tugasStore.fetchTugas());

const activeTab = ref('all');

const isDone = (t) => t.is_done === true || t.is_done === 1 || t.is_done === '1';
const isOverdue = (t) => !isDone(t) && (t.its_over || new Date(t.tanggal) < new Date());

const baseList = computed(() => (Array.isArray(tugasList.value) ? tugasList.value : []).filter(t => isDone(t) || isOverdue(t)));

const tabs = computed(() => [
  { key: 'all', label: 'Semua', count: baseList.value.length },
  { key: 'done', label: 'Selesai', count: baseList.value.filter(isDone).length },
  { key: 'overdue', label: 'Terlewat', count: baseList.value.filter(isOverdue).length },
]);

const filtered = computed(() => {
  if (activeTab.value === 'done') return baseList.value.filter(isDone);
  if (activeTab.value === 'overdue') return baseList.value.filter(isOverdue);
  return baseList.value;
});

const grouped = computed(() => {
  const map = new Map();
  filtered.value
    .slice()
    .sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal))
    .forEach(t => {
      const d = new Date(t.tanggal);
      const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
      const label = d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
      if (!map.has(key)) map.set(key, { key, label, items: [] });
      map.get(key).items.push(t);
    });
  return Array.from(map.values());
});

const formatDateTime = (s) => s
  ? new Date(s).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
  : '';

const markDone = async (t) => {
  t.is_done = true;
  const ok = await tugasStore.updateStatus(t.id, true);
  if (!ok) t.is_done = false;
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
