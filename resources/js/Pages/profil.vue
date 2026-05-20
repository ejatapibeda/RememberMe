<template>
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white/40 backdrop-blur-xl w-full max-w-md rounded-[40px] p-10 flex flex-col items-center shadow-2xl border border-white/40 relative overflow-hidden">
      
      <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-400/20 rounded-full blur-2xl"></div>
      <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-pink-400/20 rounded-full blur-2xl"></div>

      <div class="relative mb-10 group">
        <div class="w-32 h-32 bg-gradient-to-tr from-blue-500 to-cyan-400 rounded-full flex items-center justify-center p-1 shadow-xl transform transition-transform group-hover:scale-105">
          <div class="w-full h-full bg-white rounded-full flex items-center justify-center overflow-hidden">
             <span class="text-4xl font-bold text-blue-500">{{ form.name.charAt(0).toUpperCase() }}</span>
          </div>
        </div>
      </div>

      <h2 class="font-kaushan text-3xl text-gray-800 mb-8">Profil Saya</h2>

      <div class="w-full space-y-6 mb-10">
        <div class="space-y-2">
          <label class="text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Nama Lengkap</label>
          <input 
            v-model="form.name"
            type="text" 
            placeholder="Masukkan nama" 
            class="w-full px-5 py-4 bg-white/60 border border-white/50 focus:bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all rounded-2xl shadow-sm text-gray-700 font-medium"
          />
          <p v-if="authStore.errors.name" class="text-xs text-red-500 mt-1 italic">{{ authStore.errors.name[0] }}</p>
        </div>

        <div class="space-y-2">
          <label class="text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Alamat Email</label>
          <input 
            v-model="form.email"
            type="email" 
            placeholder="nama@email.com" 
            class="w-full px-5 py-4 bg-white/60 border border-white/50 focus:bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all rounded-2xl shadow-sm text-gray-700 font-medium"
          />
          <p v-if="authStore.errors.email" class="text-xs text-red-500 mt-1 italic">{{ authStore.errors.email[0] }}</p>
        </div>
      </div>

      <div class="w-full flex flex-col gap-3">
        <button 
          @click="handleSave"
          :disabled="authStore.isLoading"
          class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-2"
        >
          <span v-if="authStore.isLoading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
          {{ authStore.isLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </button>
        
        <button 
          @click="handleCancel"
          class="w-full bg-transparent hover:bg-gray-100 text-gray-500 font-semibold py-3 rounded-2xl transition-all"
        >
          Batalkan
        </button>
      </div>
    </div>

    <AlertModal 
      :show="alert.show"
      :type="alert.type"
      :title="alert.title"
      :message="alert.message"
      @confirm="alert.show = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/Stores/authStore'
import AlertModal from '../components/AlertModal.vue';

const authStore = useAuthStore()

const form = ref({
  name: '',
  email: ''
})

const alert = reactive({
  show: false,
  type: 'success',
  title: '',
  message: ''
})

// Muat data saat halaman dibuka
onMounted(() => {
  form.value.name = localStorage.getItem("name") || ''
  form.value.email = localStorage.getItem("email") || ''
})

const handleSave = async () => {
  const result = await authStore.updateProfile(form.value)
  
  alert.type = result.success ? 'success' : 'error'
  alert.title = result.success ? 'Berhasil!' : 'Gagal!'
  alert.message = result.message
  alert.show = true
}

const handleCancel = () => {
  // Reset ke data awal (yang ada di localStorage)
  form.value.name = localStorage.getItem("name") || ''
  form.value.email = localStorage.getItem("email") || ''
}
</script>