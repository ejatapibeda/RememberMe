<template>
  <div class="py-2">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="font-kaushan text-4xl md:text-5xl text-gray-800">Tugas Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola dan filter semua tugasmu di satu tempat.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <router-link to="/Create_Tugas">
          <GradientButton variant="primary">
            <PlusCircleIcon class="w-5 h-5" /> Buat Tugas
          </GradientButton>
        </router-link>
      </div>
    </div>

    <!-- Filter bar -->
    <GlassCard padding="md" rounded="rounded-3xl" class="mb-6">
      <div class="flex flex-wrap gap-3 items-center">
        <div class="flex-1 min-w-[200px] relative">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input v-model="searchQuery" type="text" placeholder="Cari tugas..."
            class="w-full pl-10 pr-4 py-2.5 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm" />
        </div>
        <input type="date" v-model="filterDate.start"
          class="px-3 py-2.5 bg-white/80 border border-gray-200 rounded-2xl text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        <span class="text-gray-400 text-sm">→</span>
        <input type="date" v-model="filterDate.end"
          class="px-3 py-2.5 bg-white/80 border border-gray-200 rounded-2xl text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        <button v-if="hasFilters" @click="resetFilters" class="text-xs text-rose-500 hover:underline font-semibold">
          Reset
        </button>
      </div>

      <div v-if="kategoriList.length" class="mt-4 flex flex-wrap gap-2">
        <button v-for="cat in kategoriList" :key="cat.id" @click="toggleCategory(cat.id)"
          :class="[
            'px-3 py-1 rounded-full text-xs font-semibold transition-all border',
            selectedCategories.includes(cat.id)
              ? 'bg-blue-600 text-white border-blue-600'
              : 'bg-white/80 text-gray-600 border-gray-200 hover:bg-blue-50'
          ]">
          {{ cat.nama_kategori }}
        </button>
      </div>
    </GlassCard>

    <div v-if="isLoading" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="grouped.length > 0" class="space-y-8">
      <section v-for="group in grouped" :key="group.key">
        <div class="flex items-center gap-3 mb-4">
          <h2 class="font-kaushan text-2xl text-gray-700">{{ group.label }}</h2>
          <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 font-bold">
            {{ group.items.length }}
          </span>
          <div class="flex-1 h-px bg-gradient-to-r from-blue-200 to-transparent"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="task in group.items" :key="task.id"
            class="group relative bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 shadow-lg shadow-blue-100/40 overflow-hidden transition-all hover:-translate-y-1 hover:shadow-2xl">
            <div class="absolute top-0 left-0 right-0 h-1.5" :class="accentBar(task)"></div>

            <router-link :to="`/Show_Tugas/${task.id}`" class="block p-5 pt-7">
              <div class="flex items-start justify-between gap-2 mb-2">
                <CountdownBadge :target="task.tanggal" />
                <span v-if="task.prioritas === 'ya'"
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                  <FireIcon class="w-3 h-3" /> PRIORITAS
                </span>
              </div>

              <h3 class="font-bold text-gray-800 text-lg leading-snug font-poppins"
                :class="{ 'line-through text-gray-400': task.is_done }">
                {{ task.tugas }}
              </h3>
              <p class="text-xs text-gray-500 mt-1">
                {{ task.kategori?.nama_kategori || 'Tanpa Kategori' }}
              </p>
              <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-3">
                <CalendarDaysIcon class="w-3.5 h-3.5" />
                {{ formatDateTime(task.tanggal) }}
              </div>
            </router-link>

            <div class="px-5 pb-4 flex items-center justify-between gap-2 border-t border-gray-100 pt-3">
              <button @click.stop="toggleCheck(task)"
                class="flex items-center gap-1.5 text-xs font-semibold transition-colors"
                :class="task.is_done ? 'text-emerald-600' : 'text-gray-500 hover:text-blue-600'">
                <span class="w-5 h-5 rounded-md flex items-center justify-center"
                  :class="task.is_done ? 'bg-emerald-500 text-white' : 'border-2 border-gray-300'">
                  <CheckIcon v-if="task.is_done" class="w-3 h-3" />
                </span>
                {{ task.is_done ? 'Selesai' : 'Tandai' }}
              </button>
              <router-link :to="`/Edit_tugas/${task.id}`"
                class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                Edit →
              </router-link>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div v-else class="text-center py-20 opacity-60">
      <div class="font-kaushan text-3xl text-gray-400">Belum ada tugas...</div>
      <p class="text-sm text-gray-400 mt-2">Coba ubah filter atau buat tugas baru.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
  XMarkIcon, PlusCircleIcon, CalendarDaysIcon, CheckIcon, MagnifyingGlassIcon, FireIcon,
} from '@heroicons/vue/24/solid';
import { useTugasStore } from '@/Stores/Tugas';
import { useKategoriStore } from '@/Stores/Kategori';
import { storeToRefs } from 'pinia';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import CountdownBadge from '@/components/CountdownBadge.vue';

const tugasStore = useTugasStore();
const kategoriStore = useKategoriStore();
const { tugasList, isLoading } = storeToRefs(tugasStore);
const { kategoriList } = storeToRefs(kategoriStore);

const searchQuery = ref('');
const selectedCategories = ref([]);
const filterDate = ref({ start: '', end: '' });

onMounted(async () => {
  await Promise.all([tugasStore.fetchTugas(), kategoriStore.fetchKategori()]);
});

const hasFilters = computed(() =>
  searchQuery.value || selectedCategories.value.length || filterDate.value.start || filterDate.value.end
);

const resetFilters = () => {
  searchQuery.value = '';
  selectedCategories.value = [];
  filterDate.value = { start: '', end: '' };
};
const toggleCategory = (id) => {
  const i = selectedCategories.value.indexOf(id);
  if (i >= 0) selectedCategories.value.splice(i, 1);
  else selectedCategories.value.push(id);
};

const formatDateTime = (s) => s
  ? new Date(s).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
  : '';

const filteredList = computed(() => {
  let list = Array.isArray(tugasList.value) ? [...tugasList.value] : [];
  if (searchQuery.value) {
    list = list.filter(t => t.tugas?.toLowerCase().includes(searchQuery.value.toLowerCase()));
  }
  if (selectedCategories.value.length) {
    list = list.filter(t => selectedCategories.value.includes(t.id_kategori));
  }
  if (filterDate.value.start && filterDate.value.end) {
    const s = new Date(filterDate.value.start);
    const e = new Date(filterDate.value.end);
    e.setHours(23, 59, 59);
    list = list.filter(t => {
      const d = new Date(t.tanggal);
      return d >= s && d <= e;
    });
  }
  return list.sort((a, b) => {
    if (a.is_done !== b.is_done) return a.is_done ? 1 : -1;
    if (a.prioritas === 'ya' && b.prioritas !== 'ya') return -1;
    if (a.prioritas !== 'ya' && b.prioritas === 'ya') return 1;
    return new Date(a.tanggal) - new Date(b.tanggal);
  });
});

const grouped = computed(() => {
  const today = new Date(); today.setHours(0,0,0,0);
  const tomorrow = new Date(today); tomorrow.setDate(tomorrow.getDate() + 1);
  const weekEnd = new Date(today); weekEnd.setDate(weekEnd.getDate() + 7);

  const groups = {
    overdue: { key: 'overdue', label: '⚠️ Lewat Deadline', items: [] },
    today: { key: 'today', label: '🎯 Hari Ini', items: [] },
    week: { key: 'week', label: '📅 Minggu Ini', items: [] },
    later: { key: 'later', label: '🌅 Mendatang', items: [] },
    done: { key: 'done', label: '✅ Selesai', items: [] },
  };

  filteredList.value.forEach(t => {
    if (t.is_done) return groups.done.items.push(t);
    const d = new Date(t.tanggal);
    if (t.its_over || d < new Date()) return groups.overdue.items.push(t);
    if (d < tomorrow) return groups.today.items.push(t);
    if (d < weekEnd) return groups.week.items.push(t);
    return groups.later.items.push(t);
  });

  return Object.values(groups).filter(g => g.items.length > 0);
});

const toggleCheck = async (task) => {
  const orig = task.is_done;
  task.is_done = !task.is_done;
  const ok = await tugasStore.updateStatus(task.id, task.is_done);
  if (!ok) { task.is_done = orig; alert('Gagal memperbarui status tugas.'); }
};

const accentBar = (task) => {
  if (task.is_done) return 'bg-gradient-to-r from-emerald-400 to-teal-500';
  if (task.its_over) return 'bg-gradient-to-r from-rose-400 to-red-500';
  const ms = new Date(task.tanggal).getTime() - Date.now();
  if (ms < 60 * 60 * 1000) return 'bg-gradient-to-r from-amber-400 to-orange-500';
  if (ms < 24 * 60 * 60 * 1000) return 'bg-gradient-to-r from-sky-400 to-blue-500';
  return 'bg-gradient-to-r from-violet-400 to-fuchsia-500';
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
