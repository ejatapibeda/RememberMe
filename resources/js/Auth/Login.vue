<template>
 <AlertModal 
        :show="alertConfig.show" 
        :title="alertConfig.title" 
        :message="alertConfig.message"
        :type="alertConfig.type"
        @confirm="handleConfirm"
    />

    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-5xl bg-white shadow-xl rounded-[40px] grid grid-cols-1 md:grid-cols-2 overflow-hidden">
            
            <div class="bg-[#7EC4E3] flex flex-col items-center justify-center p-10 relative">
                <h1 class="text-5xl font-bold text-white mb-4 font-poppins">Halo!</h1>
                <p class="text-white/80 text-center px-6">Senang melihatmu kembali di RememberME.</p>
            </div>

            <div class="bg-[#FFFCE7] p-12 flex flex-col justify-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2 font-poppins">Login to Your Account</h2>
                <p class="text-gray-600 text-sm mb-8">Please enter your credentials to access your dashboard.</p>

                <form @submit.prevent="handleLogin" class="space-y-5">
                    <div class="space-y-1">
                        <input v-model="email" type="email" placeholder="Email Address" required 
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-400 outline-none transition-all" />
                    </div>
                    <div class="space-y-1">
                        <input v-model="password" type="password" placeholder="Password" required 
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-400 outline-none transition-all" />
                    </div>

                    <button type="submit" :disabled="isLoading" 
                        class="w-full bg-black text-white py-4 rounded-xl font-bold hover:bg-gray-800 transition-all active:scale-95 disabled:opacity-50 mt-4 shadow-lg">
                        <span v-if="isLoading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                        <span v-else>Log In</span>
                    </button>
                </form>

                <div class="flex items-center justify-between mt-8">
                    <router-link :to="`/lupa_password`" class="block">
                        <button class="text-sm font-semibold text-gray-500 hover:text-black transition-colors">Forgot Password?</button>
                    </router-link>
                    <router-link to="/registrasi">
                        <button class="bg-white border-2 border-black text-black px-6 py-2 rounded-xl font-bold text-sm hover:bg-black hover:text-white transition-all">
                            Register
                        </button>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Navbar from "../components/Navbar.vue"; 
import AlertModal from "../components/AlertModal.vue"; 
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/Stores/authStore";

const router = useRouter();
const auth = useAuthStore();

const email = ref("");
const password = ref("");
const isLoading = ref(false);

// State Alert Modal Dinamis
const alertConfig = ref({
    show: false,
    title: '',
    message: '',
    type: 'success'
});

const handleLogin = async () => {
    isLoading.value = true;

    try {
        const success = await auth.login({
            email: email.value,
            password: password.value,
        });

        if (success) {
            alertConfig.value = {
                show: true,
                title: 'Hey!',
                message: 'Login Berhasil! Selamat datang kembali.',
                type: 'success'
            };
        } else {
            // Tampilkan error menggunakan Modal Merah
            alertConfig.value = {
                show: true,
                title: 'Login Gagal',
                message: 'Email atau password yang kamu masukkan salah.',
                type: 'error'
            };
        }
    } catch (error) {
        alertConfig.value = {
            show: true,
            title: 'Error',
            message: 'Terjadi kesalahan pada server. Coba lagi nanti.',
            type: 'error'
        };
    } finally {
        isLoading.value = false;
    }
};

const handleConfirm = () => {
    const isSuccess = alertConfig.value.type === 'success';
    alertConfig.value.show = false;
    
    if (isSuccess) {
        router.push("/"); // Redirect ke Dashboard jika login sukses
    }
};
</script>