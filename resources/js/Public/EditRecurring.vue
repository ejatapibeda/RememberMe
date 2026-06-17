<template>
  <div class="max-w-xl mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/recurring" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>
    <h1 class="font-kaushan text-4xl text-gray-800 mb-6">Edit Rutinitas</h1>

    <div v-if="isLoading && !form.title" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <GlassCard v-else padding="lg">
      <div class="space-y-5">
        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Judul</label>
          <input v-model="form.title" type="text"
            class="mt-1 w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm" />
          <p v-if="errors.title" class="text-xs text-rose-500 mt-1 italic">{{ errors.title[0] }}</p>
        </div>

        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Catatan (opsional)</label>
          <textarea v-model="form.body" rows="3"
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
          <GradientButton variant="primary" :loading="isLoading" @click="submit">Simpan Perubahan</GradientButton>
          <GradientButton variant="danger" @click="confirmDelete">Hapus</GradientButton>
          <router-link to="/recurring">
            <GradientButton variant="ghost">Batal</GradientButton>
          </router-link>
        </div>
      </div>
    </GlassCard>

    <AlertModal :show="alert.show" :type="alert.type" :title="alert.title" :message="alert.message"
      :showCancel="alert.showCancel" @confirm="onConfirm" @cancel="alert.show = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import AlertModal from '@/components/AlertModal.vue';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import { useRecurringReminderStore } from '@/Stores/recurringReminderStore';

const route = useRoute();
const router = useRouter();
const store = useRecurringReminderStore();
const { isLoading, errors } = storeToRefs(store);

const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
const form = ref({ title: '', body: '', days: [], time: '07:00' });
const alert = reactive({ show: false, type: 'success', title: '', message: '', showCancel: false, action: null });
const id = route.params.id;

onMounted(async () => {
  const data = await store.show(id);
  if (data) {
    form.value.title = data.title;
    form.value.body = data.body || '';
    form.value.days = Array.isArray(data.days) ? [...data.days] : [];
    form.value.time = data.time ? data.time.slice(0, 5) : '07:00';
  }
});

const toggleDay = (index) => {
  const i = form.value.days.indexOf(index);
  if (i >= 0) form.value.days.splice(i, 1);
  else form.value.days.push(index);
};

const submit = async () => {
  if (form.value.days.length === 0) {
    alert.type = 'error'; alert.title = 'Ups!'; alert.message = 'Pilih minimal 1 hari.'; alert.show = true; return;
  }
  const r = await store.update(id, {
    title: form.value.title,
    body: form.value.body,
    days: form.value.days,
    time: form.value.time,
  });
  if (r.success) {
    alert.type = 'success'; alert.title = 'Tersimpan'; alert.message = 'Rutinitas berhasil diperbarui.'; alert.show = true; alert.action = 'back';
  } else {
    alert.type = 'error'; alert.title = 'Gagal'; alert.message = r.message || 'Tidak bisa menyimpan.'; alert.show = true;
  }
};

const confirmDelete = () => {
  alert.type = 'error'; alert.title = 'Hapus rutinitas?'; alert.message = 'Rutinitas akan dihapus permanen.';
  alert.showCancel = true; alert.action = 'delete'; alert.show = true;
};

const onConfirm = async () => {
  if (alert.action === 'delete') {
    await store.remove(id);
    alert.show = false;
    router.push('/recurring');
  } else if (alert.action === 'back') {
    alert.show = false;
    router.push('/recurring');
  } else {
    alert.show = false;
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
