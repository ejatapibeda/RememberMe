<template>
  <div class="py-2">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
      <div>
        <h1 class="font-kaushan text-4xl md:text-5xl text-gray-800">Kategori</h1>
        <p class="text-sm text-gray-500 mt-1">Kelompokkan tugasmu agar lebih rapi.</p>
      </div>
      <router-link to="/Create_kategori">
        <GradientButton variant="primary">
          <PlusCircleIcon class="w-5 h-5" /> Tambah Kategori
        </GradientButton>
      </router-link>
    </div>

    <div v-if="isLoading" class="text-center py-10">
      <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="kategoriList.length === 0" class="text-center py-20 opacity-60">
      <p class="font-kaushan text-3xl text-gray-400">Belum ada kategori...</p>
      <p class="text-sm text-gray-400 mt-2">Mulai buat kategori pertamamu sekarang.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      <div v-for="(cat, index) in kategoriList" :key="cat.id"
        class="group relative bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 shadow-lg shadow-blue-100/40 overflow-hidden transition-all hover:-translate-y-1 hover:shadow-2xl">
        <div class="h-2" :class="gradient(index)"></div>
        <div class="p-5">
          <div class="flex items-start gap-3 mb-2">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl bg-gradient-to-br shadow-sm"
              :class="gradient(index)">
              {{ emoji(cat.nama_kategori) }}
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-gray-800 truncate font-poppins">{{ cat.nama_kategori }}</h3>
              <p class="text-xs text-gray-500 mt-0.5">ID #{{ cat.id }}</p>
            </div>
          </div>

          <div class="flex items-center gap-2 pt-3 border-t border-gray-100 mt-3">
            <router-link :to="`/Show_Kategori/${cat.id}`"
              class="flex-1 text-center text-xs font-semibold py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
              Detail
            </router-link>
            <router-link :to="`/Edit_Kategori/${cat.id}`"
              class="flex-1 text-center text-xs font-semibold py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 transition">
              Edit
            </router-link>
            <button @click="confirmDelete(cat)"
              class="flex-1 text-xs font-semibold py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 transition">
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <AlertModal :show="alert.show" :type="alert.type" :title="alert.title" :message="alert.message"
      :showCancel="alert.showCancel" @confirm="onConfirm" @cancel="alert.show = false" />
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { storeToRefs } from 'pinia';
import { PlusCircleIcon } from '@heroicons/vue/24/solid';
import { useKategoriStore } from '@/Stores/Kategori';
import GradientButton from '@/components/GradientButton.vue';
import AlertModal from '@/components/AlertModal.vue';

const kategoriStore = useKategoriStore();
const { kategoriList, isLoading } = storeToRefs(kategoriStore);

onMounted(() => kategoriStore.fetchKategori());

const alert = reactive({ show: false, type: 'success', title: '', message: '', showCancel: false });
const pendingDelete = ref(null);

const confirmDelete = (cat) => {
  pendingDelete.value = cat;
  alert.type = 'error';
  alert.title = 'Hapus kategori?';
  alert.message = `"${cat.nama_kategori}" akan dihapus permanen.`;
  alert.showCancel = true;
  alert.show = true;
};

const onConfirm = async () => {
  if (pendingDelete.value) {
    await kategoriStore.deleteKategori(pendingDelete.value.id);
    pendingDelete.value = null;
  }
  alert.show = false;
};

const gradients = [
  'from-blue-200 to-cyan-200',
  'from-pink-200 to-rose-200',
  'from-emerald-200 to-teal-200',
  'from-amber-200 to-orange-200',
  'from-violet-200 to-fuchsia-200',
  'from-sky-200 to-indigo-200',
];
const gradient = (i) => gradients[i % gradients.length];

const emojis = ['📌', '📚', '💼', '🏠', '🎯', '🛒', '💡', '🎨', '🏃', '🍔', '✨', '🚀'];
const emoji = (name = '') => {
  let h = 0;
  for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) >>> 0;
  return emojis[h % emojis.length];
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
