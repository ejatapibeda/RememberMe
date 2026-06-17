<template>
  <div>
    <div class="py-2 lg:py-4">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">

        <!-- Left hero -->
        <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-6">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/70 backdrop-blur border border-white/50 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold text-gray-700 tracking-wide">REMINDER AKTIF</span>
          </div>
          <h1 class="font-kaushan text-5xl md:text-6xl lg:text-7xl leading-[1.05] bg-gradient-to-br from-blue-600 via-indigo-600 to-cyan-500 bg-clip-text text-transparent drop-shadow-sm">
            Never Forget Again!
          </h1>
          <p class="text-gray-600 text-base md:text-lg max-w-md font-poppins">
            Kelola tugas-mu dengan reminder otomatis di web & Telegram. Tetap fokus, tetap on-track. ✨
          </p>

          <div class="flex items-center gap-3">
            <router-link to="/Create_Tugas">
              <GradientButton size="lg" variant="primary">
                <PlusIcon class="w-5 h-5" />
                Tambah Tugas
              </GradientButton>
            </router-link>
            <router-link to="/profil">
              <GradientButton size="lg" variant="secondary">
                <PaperAirplaneIcon class="w-4 h-4" />
                Hubungkan Telegram
              </GradientButton>
            </router-link>
          </div>

          <!-- mini stats -->
          <div class="grid grid-cols-3 gap-3 pt-4">
            <GlassCard padding="sm" rounded="rounded-2xl">
              <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Aktif</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.active }}</p>
            </GlassCard>
            <GlassCard padding="sm" rounded="rounded-2xl">
              <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Hari Ini</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.today }}</p>
            </GlassCard>
            <GlassCard padding="sm" rounded="rounded-2xl">
              <p class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">Lewat</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.overdue }}</p>
            </GlassCard>
          </div>
        </div>

        <!-- Right list -->
        <div class="lg:col-span-7">
          <div class="flex items-center justify-between mb-6">
            <h2 class="font-kaushan text-3xl md:text-4xl text-gray-800">Tugas Aktif</h2>
            <div v-if="isLoading" class="animate-spin h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full"></div>
          </div>

          <!-- filter chips -->
          <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
            <button
              v-for="opt in filters"
              :key="opt.key"
              @click="activeFilter = opt.key"
              :class="[
                'px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all border',
                activeFilter === opt.key
                  ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-200'
                  : 'bg-white/70 text-gray-600 border-white/60 hover:bg-white'
              ]"
            >
              {{ opt.label }}
              <span v-if="opt.count !== null" class="ml-1 opacity-70">{{ opt.count }}</span>
            </button>
          </div>

          <TransitionGroup
            v-if="filteredTasks.length > 0"
            name="list"
            tag="div"
            class="flex flex-col gap-4"
          >
            <div
              v-for="task in filteredTasks"
              :key="task.id"
              class="group relative bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 shadow-xl shadow-blue-100/50 overflow-hidden transition-all hover:-translate-y-1 hover:shadow-2xl"
            >
              <!-- side accent -->
              <div
                class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-3xl"
                :class="accentBar(task)"
              ></div>

              <div class="pl-6 pr-5 py-5 flex items-start gap-4">
                <!-- category icon -->
                <div
                  class="w-12 h-12 flex-shrink-0 rounded-2xl flex items-center justify-center text-xl bg-gradient-to-br shadow-sm"
                  :class="iconGradient(task)"
                >
                  {{ kategoriEmoji(task) }}
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <CountdownBadge :target="task.tanggal" />
                    <span
                      v-if="task.prioritas === 'ya'"
                      class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 ring-1 ring-amber-200"
                    >
                      <FireIcon class="w-3 h-3" /> PRIORITAS
                    </span>
                    <span class="text-xs font-semibold text-gray-500 truncate">
                      {{ task.kategori?.nama_kategori || 'Tanpa Kategori' }}
                    </span>
                  </div>

                  <h3 class="font-bold text-gray-800 text-lg leading-snug truncate font-poppins">
                    {{ task.tugas }}
                  </h3>

                  <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                    <span class="inline-flex items-center gap-1">
                      <CalendarDaysIcon class="w-3.5 h-3.5" />
                      {{ formatDateTime(task.tanggal) }}
                    </span>
                  </div>
                </div>

                <button
                  @click="toggleCheck(task)"
                  class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center transition-all active:scale-90"
                  :class="task.is_done
                    ? 'bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-md'
                    : task.its_over
                    ? 'bg-rose-50 border-2 border-rose-300 text-rose-500'
                    : 'bg-white border-2 border-gray-300 text-transparent hover:border-blue-400 hover:text-blue-400'"
                  :title="task.is_done ? 'Selesai' : 'Tandai selesai'"
                >
                  <CheckIcon v-if="task.is_done" class="w-6 h-6" />
                  <XMarkIcon v-else-if="task.its_over" class="w-6 h-6" />
                  <CheckIcon v-else class="w-6 h-6" />
                </button>
              </div>
            </div>
          </TransitionGroup>

          <div
            v-else-if="!isLoading"
            class="bg-white/60 backdrop-blur-xl border-2 border-dashed border-blue-200 rounded-3xl py-14 px-8 text-center flex flex-col items-center gap-4"
          >
            <div class="relative">
              <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center text-4xl shadow-inner">
                ✨
              </div>
              <div class="absolute -top-1 -right-1 w-5 h-5 bg-yellow-400 rounded-full animate-ping"></div>
            </div>
            <div class="space-y-1">
              <p class="font-kaushan text-3xl text-gray-700">Belum ada tugas nih...</p>
              <p class="text-gray-500 text-sm italic">"Jangan biarkan hari ini berlalu tanpa rencana!"</p>
            </div>
            <router-link to="/Create_Tugas">
              <GradientButton size="lg" variant="primary">
                <PlusIcon class="w-5 h-5" />
                Buat Tugas Sekarang
              </GradientButton>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import {
  CalendarDaysIcon, CheckIcon, XMarkIcon, PlusIcon, FireIcon, PaperAirplaneIcon,
} from '@heroicons/vue/24/solid';
import { useTugasStore } from '@/Stores/Tugas';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import CountdownBadge from '@/components/CountdownBadge.vue';

const tugasStore = useTugasStore();
const { tugasList, isLoading } = storeToRefs(tugasStore);

onMounted(() => tugasStore.fetchTugas());

const activeFilter = ref('all');

const isDone = (t) => t.is_done === true || t.is_done === 1 || t.is_done === '1';

const baseList = computed(() => {
  const list = Array.isArray(tugasList.value) ? [...tugasList.value] : [];
  return list.filter((t) => !isDone(t));
});

const isToday = (t) => {
  const d = new Date(t.tanggal);
  const now = new Date();
  return d.toDateString() === now.toDateString();
};

const filters = computed(() => [
  { key: 'all', label: 'Semua', count: baseList.value.length },
  { key: 'priority', label: '🔥 Prioritas', count: baseList.value.filter(t => t.prioritas === 'ya').length },
  { key: 'today', label: 'Hari ini', count: baseList.value.filter(isToday).length },
  { key: 'overdue', label: 'Lewat', count: baseList.value.filter(t => t.its_over).length },
]);

const filteredTasks = computed(() => {
  let list = [...baseList.value];
  if (activeFilter.value === 'priority') list = list.filter(t => t.prioritas === 'ya');
  else if (activeFilter.value === 'today') list = list.filter(isToday);
  else if (activeFilter.value === 'overdue') list = list.filter(t => t.its_over);

  return list.sort((a, b) => {
    if (a.its_over && !b.its_over) return -1;
    if (!a.its_over && b.its_over) return 1;
    if (a.prioritas === 'ya' && b.prioritas !== 'ya') return -1;
    if (a.prioritas !== 'ya' && b.prioritas === 'ya') return 1;
    return new Date(a.tanggal) - new Date(b.tanggal);
  });
});

const stats = computed(() => ({
  active: baseList.value.length,
  today: baseList.value.filter(isToday).length,
  overdue: baseList.value.filter(t => t.its_over).length,
}));

const formatDateTime = (s) => {
  if (!s) return '';
  return new Date(s).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit',
  });
};

const toggleCheck = async (task) => {
  task.is_done = !task.is_done;
  const ok = await tugasStore.updateStatus(task.id, task.is_done);
  if (!ok) {
    task.is_done = !task.is_done;
    alert('Gagal memperbarui status tugas.');
  }
};

const accentBar = (task) => {
  if (task.its_over) return 'bg-gradient-to-b from-rose-400 to-red-500';
  const ms = new Date(task.tanggal).getTime() - Date.now();
  if (ms < 60 * 60 * 1000) return 'bg-gradient-to-b from-amber-400 to-orange-500';
  if (ms < 24 * 60 * 60 * 1000) return 'bg-gradient-to-b from-sky-400 to-blue-500';
  return 'bg-gradient-to-b from-emerald-400 to-teal-500';
};

const gradients = [
  'from-blue-100 to-cyan-100',
  'from-pink-100 to-rose-100',
  'from-emerald-100 to-teal-100',
  'from-amber-100 to-orange-100',
  'from-violet-100 to-fuchsia-100',
];
const iconGradient = (task) => gradients[(task.id || 0) % gradients.length];

const emojiMap = ['📌', '📚', '💼', '🏠', '🎯', '🛒', '💡', '🎨', '🏃', '🍔'];
const kategoriEmoji = (task) => {
  const name = task.kategori?.nama_kategori || '';
  let h = 0;
  for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) >>> 0;
  return emojiMap[h % emojiMap.length];
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Patrick+Hand&family=Kaushan+Script&display=swap');
.font-poppins { font-family: 'Poppins', sans-serif; }
.font-kaushan { font-family: 'Kaushan Script', cursive; }

.list-enter-active, .list-leave-active { transition: all 0.45s cubic-bezier(0.4, 0, 0.2, 1); }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateY(20px) scale(0.97); }
.list-move { transition: transform 0.5s ease; }
</style>
