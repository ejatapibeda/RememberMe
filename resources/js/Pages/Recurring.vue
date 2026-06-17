<template>
  <div class="py-2">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
      <div>
        <h1 class="font-kaushan text-4xl md:text-5xl text-gray-800">Rutinitas</h1>
        <p class="text-sm text-gray-500 mt-1">Reminder harian/mingguan yang berulang setiap hari dan jam tertentu.</p>
      </div>
      <router-link to="/recurring/create">
        <GradientButton variant="primary">
          <PlusCircleIcon class="w-5 h-5" /> Tambah Rutinitas
        </GradientButton>
      </router-link>
    </div>

    <div v-if="isLoading" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="items.length === 0" class="text-center py-20 opacity-60">
      <div class="font-kaushan text-3xl text-gray-400">Belum ada rutinitas...</div>
      <p class="text-sm text-gray-400 mt-2">Buat rutinitas harian atau mingguan pertamamu.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="rem in items" :key="rem.id"
        class="group relative bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 shadow-lg shadow-blue-100/40 overflow-hidden transition-all hover:-translate-y-1 hover:shadow-2xl">
        <div class="h-1.5" :class="rem.enabled ? 'bg-gradient-to-r from-blue-400 to-cyan-500' : 'bg-gray-300'"></div>
        <div class="p-5">
          <div class="flex items-start justify-between gap-2 mb-3">
            <div class="flex items-center gap-2 flex-wrap">
              <span v-if="rem.enabled"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> AKTIF
              </span>
              <span v-else
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                NONAKTIF
              </span>
              <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">{{ rem.time }}</span>
            </div>
          </div>

          <h3 class="font-bold text-gray-800 text-lg font-poppins">{{ rem.title }}</h3>
          <p v-if="rem.body" class="text-sm text-gray-500 mt-1 line-clamp-2">{{ rem.body }}</p>

          <div class="flex flex-wrap gap-1.5 mt-3">
            <span v-for="d in dayLabels(rem.days)" :key="d"
              class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white border border-gray-200 text-gray-600">
              {{ d }}
            </span>
          </div>

          <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
            <button @click="onToggle(rem)"
              class="flex-1 text-center text-xs font-semibold py-2 rounded-xl transition"
              :class="rem.enabled ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'">
              {{ rem.enabled ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
            <router-link :to="`/recurring/edit/${rem.id}`"
              class="flex-1 text-center text-xs font-semibold py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
              Edit
            </router-link>
            <button @click="confirmDelete(rem)"
              class="flex-1 text-xs font-semibold py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 transition">
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <AlertModal :show="alert.show" :type="alert.type" :title="alert.title" :message="alert.message"
      :showCancel="alert.showCancel" @confirm="onConfirmDelete" @cancel="alert.show = false" />
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { storeToRefs } from 'pinia';
import { PlusCircleIcon } from '@heroicons/vue/24/outline';
import { useRecurringReminderStore } from '@/Stores/recurringReminderStore';
import GradientButton from '@/components/GradientButton.vue';
import AlertModal from '@/components/AlertModal.vue';

const store = useRecurringReminderStore();
const { items, isLoading } = storeToRefs(store);

onMounted(() => store.fetch());

const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
const dayLabels = (days) => (Array.isArray(days) ? days.map((d) => dayNames[d] ?? d) : []);

const alert = reactive({ show: false, type: 'error', title: '', message: '', showCancel: false });
const pendingDelete = ref(null);

const onToggle = async (rem) => {
  await store.toggle(rem.id);
};

const confirmDelete = (rem) => {
  pendingDelete.value = rem;
  alert.title = 'Hapus rutinitas?';
  alert.message = `"${rem.title}" akan dihapus permanen.`;
  alert.showCancel = true;
  alert.show = true;
};

const onConfirmDelete = async () => {
  if (pendingDelete.value) {
    await store.remove(pendingDelete.value.id);
    pendingDelete.value = null;
  }
  alert.show = false;
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
.font-poppins { font-family: 'Poppins', sans-serif; }
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
