<template>
  <div class="max-w-xl mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/recurring" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>
    <h1 class="font-kaushan text-4xl text-gray-800 mb-6">Tambah Rutinitas</h1>

    <GlassCard padding="lg">
      <div class="space-y-5">
        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Judul</label>
          <input v-model="form.title" type="text" placeholder="contoh: Episode Netflix Baru"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm" />
          <p v-if="errors.title" class="text-xs text-rose-500 mt-1 italic">{{ errors.title[0] }}</p>
        </div>

        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Catatan (opsional)</label>
          <textarea v-model="form.body" rows="3" placeholder="Jangan lupa nonton!"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm resize-none"></textarea>
        </div>

        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-2 block">Hari</label>
          <div class="flex flex-wrap gap-2">
            <button v-for="(label, index) in dayNames" :key="index" @click="toggleDay(index)"
              class="px-4 py-2 rounded-xl text-sm font-semibold border transition-all"
              :class="form.days.includes(index)
                ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-200'
                : 'bg-white/80 text-gray-600 border-gray-200 hover:bg-blue-50'">
              {{ label }}
            </button>
          </div>
          <p v-if="errors.days" class="text-xs text-rose-500 mt-1 italic">{{ errors.days[0] }}</p>
        </div>

        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Jam</label>
          <input v-model="form.time" type="time"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm" />
          <p v-if="errors.time" class="text-xs text-rose-500 mt-1 italic">{{ errors.time[0] }}</p>
        </div>

        <div class="flex gap-2 pt-2">
          <GradientButton variant="primary" :loading="isLoading" @click="submit">
            <PlusCircleIcon class="w-5 h-5" /> Simpan
          </GradientButton>
          <router-link to="/recurring">
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
import { useRecurringReminderStore } from '@/Stores/recurringReminderStore';

const router = useRouter();
const store = useRecurringReminderStore();
const { isLoading, errors } = storeToRefs(store);

const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

const form = ref({
  title: '',
  body: '',
  days: [],
  time: '07:00',
});

const alert = ref({ show: false, title: '', message: '', type: 'success' });

const toggleDay = (index) => {
  const i = form.value.days.indexOf(index);
  if (i >= 0) form.value.days.splice(i, 1);
  else form.value.days.push(index);
};

const submit = async () => {
  if (form.value.days.length === 0) {
    alert.value = { show: true, title: 'Ups!', message: 'Pilih minimal 1 hari.', type: 'error' };
    return;
  }
  const r = await store.create({
    title: form.value.title,
    body: form.value.body,
    days: form.value.days,
    time: form.value.time,
  });
  alert.value = {
    show: true,
    title: r.success ? 'Berhasil!' : 'Gagal!',
    message: r.success ? 'Rutinitas berhasil dibuat.' : (r.message || 'Tidak bisa menyimpan.'),
    type: r.success ? 'success' : 'error',
  };
};

const closeAlert = () => {
  const ok = alert.value.type === 'success';
  alert.value.show = false;
  if (ok) router.push('/recurring');
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
