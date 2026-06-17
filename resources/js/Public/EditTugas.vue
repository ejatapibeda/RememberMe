<template>
  <div class="max-w-2xl mx-auto py-2">
    <div class="flex items-center gap-2 mb-6">
      <router-link to="/tugas" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">← Kembali</router-link>
    </div>
    <h1 class="font-kaushan text-4xl text-gray-800 mb-6">Edit Tugas</h1>

    <div v-if="loadingTugas && !form.task" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <GlassCard v-else padding="lg">
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
            <option value="" disabled>Pilih kategori</option>
            <option v-for="cat in kategoriList" :key="cat.id" :value="cat.id">{{ cat.nama_kategori }}</option>
          </select>
        </div>

        <div>
          <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Catatan</label>
          <textarea v-model="form.task" rows="4"
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

        <div class="flex gap-2 pt-2 flex-wrap">
          <GradientButton variant="primary" :loading="loadingTugas" @click="save">Simpan Perubahan</GradientButton>
          <GradientButton variant="danger" @click="confirmDelete">Hapus</GradientButton>
          <router-link to="/tugas">
            <GradientButton variant="ghost">Batal</GradientButton>
          </router-link>
        </div>
      </div>
    </GlassCard>

    <AlertModal :show="alert.show" :title="alert.title" :message="alert.message" :type="alert.type"
      :showCancel="alert.showCancel" @confirm="onConfirm" @cancel="alert.show = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { CheckIcon } from '@heroicons/vue/24/outline';
import AlertModal from '@/components/AlertModal.vue';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import { useKategoriStore } from '@/Stores/Kategori';
import { useTugasStore } from '@/Stores/Tugas';

const route = useRoute();
const router = useRouter();
const kategoriStore = useKategoriStore();
const tugasStore = useTugasStore();
const { kategoriList } = storeToRefs(kategoriStore);
const { isLoading: loadingTugas } = storeToRefs(tugasStore);

const form = ref({ date: '', id_kategori: '', task: '', isPriority: false });
const alert = reactive({ show: false, title: '', message: '', type: 'success', showCancel: false, action: null });

const id = route.params.id;

onMounted(async () => {
  await kategoriStore.fetchKategori();
  const data = await tugasStore.showTugas(id);
  if (data) {
    form.value.date = (data.tanggal || '').replace(' ', 'T').slice(0, 16);
    form.value.id_kategori = data.id_kategori;
    form.value.task = data.tugas;
    form.value.isPriority = data.prioritas === 'ya';
  }
});

const showAlert = (type, title, message, opts = {}) => {
  alert.type = type; alert.title = title; alert.message = message;
  alert.showCancel = opts.showCancel || false;
  alert.action = opts.action || null;
  alert.show = true;
};

const save = async () => {
  if (!form.value.date || !form.value.id_kategori || !form.value.task) {
    return showAlert('error', 'Ups!', 'Semua kolom wajib diisi.');
  }
  const r = await tugasStore.updateTugas(id, {
    id_kategori: form.value.id_kategori,
    tanggal: form.value.date,
    tugas: form.value.task,
    prioritas: form.value.isPriority ? 'ya' : 'tidak',
  });
  if (r.success) showAlert('success', 'Tersimpan', 'Tugas berhasil diperbarui.', { action: 'back' });
  else showAlert('error', 'Gagal', r.message || 'Tidak bisa menyimpan.');
};

const confirmDelete = () => {
  showAlert('error', 'Hapus tugas?', 'Tugas akan dihapus permanen.', { showCancel: true, action: 'delete' });
};

const onConfirm = async () => {
  if (alert.action === 'delete') {
    const r = await tugasStore.deleteTugas(id);
    alert.show = false;
    if (r?.success) router.push('/tugas');
  } else if (alert.action === 'back') {
    alert.show = false;
    router.push('/tugas');
  } else {
    alert.show = false;
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
