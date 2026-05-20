<template>
  <div class="container mx-auto px-4 py-12 flex flex-col items-center min-h-screen">
    <h1 class="font-kaushan text-4xl text-gray-800 mb-8">Password Baru</h1>
    
    <div class="w-full max-w-md bg-[#e6ffed] rounded-3xl p-8 shadow-sm border border-green-100">
      <form @submit.prevent="handleResetPassword">
        <div class="mb-5">
          <label class="block text-gray-700 font-medium mb-2">Password Baru</label>
          <input 
            v-model="form.password" 
            type="password" 
            class="w-full px-4 py-3 rounded-xl border-none focus:ring-2 focus:ring-sky-300 outline-none" 
            placeholder="Min. 6 karakter"
            required 
          />
        </div>

        <div class="mb-8">
          <label class="block text-gray-700 font-medium mb-2">Konfirmasi Password</label>
          <input 
            v-model="form.password_confirmation" 
            type="password" 
            class="w-full px-4 py-3 rounded-xl border-none focus:ring-2 focus:ring-sky-300 outline-none" 
            placeholder="Ulangi password"
            required 
          />
        </div>

        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full bg-[#87cefa] text-black font-bold py-3 rounded-full hover:bg-sky-400 transition-all shadow-md"
        >
          {{ isLoading ? 'Memproses...' : 'Simpan Password Baru' }}
        </button>
      </form>
    </div>

    <AlertModal 
      :show="alert.show" 
      :type="alert.type" 
      :title="alert.title" 
      :message="alert.message" 
      @confirm="handleAlertConfirm" 
    />
  </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/Stores/authStore'; // Import Store kamu
import { storeToRefs } from 'pinia';
import AlertModal from "../components/AlertModal.vue"; // Aktifkan ini!

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const { isLoading } = storeToRefs(authStore); // Ambil state loading dari store

const form = reactive({
  token: route.query.token || '', 
  email: route.query.email || '', 
  password: '',
  password_confirmation: ''
});

const alert = reactive({ show: false, type: 'success', title: '', message: '' });

const handleResetPassword = async () => {
  // Panggil fungsi dari store
  const result = await authStore.resetPassword(form);

  if (result.success) {
    alert.type = 'success';
    alert.title = 'Berhasil!';
    alert.message = result.message;
    alert.show = true;
  } else {
    alert.type = 'error';
    alert.title = 'Gagal!';
    alert.message = result.message;
    alert.show = true;
  }
};

const handleAlertConfirm = () => {
  alert.show = false;
  if (alert.type === 'success') {
    router.push('/login');
  }
};
</script>