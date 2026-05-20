<template>

    <AlertModal 
        :show="alertConfig.show" 
        :title="alertConfig.title" 
        :message="alertConfig.message"
        :type="alertConfig.type"
        @confirm="handleConfirm"
    />

    <div class="min-h-screen flex items-center justify-center  py-10 px-4">
        <div class="flex bg-white shadow-xl rounded-[40px] overflow-hidden w-full max-w-5xl">

            <div class="w-full lg:w-1/2 bg-[#FFFCE7] p-10 flex flex-col justify-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2 font-poppins">Register to Your Account</h2>
                <p class="text-gray-600 mb-8 text-sm">
                    Please enter your credentials to access your dashboard.
                </p>

                <form @submit.prevent="register" class="space-y-4">
                    <input v-model="name" type="text" placeholder="Nama Lengkap" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-400 outline-none transition-all" />

                    <input v-model="email" type="email" placeholder="Email" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-400 outline-none transition-all" />

                    <input v-model="password" type="password" placeholder="Password (Min. 6 Karakter)" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-400 outline-none transition-all" />

                    <input v-model="confirmPassword" type="password" placeholder="Konfirmasi Password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-400 outline-none transition-all" />

                    <button type="submit" :disabled="isLoading"
                        class="w-full bg-black hover:bg-gray-800 text-white py-4 rounded-xl font-bold shadow-lg transition-all active:scale-95 disabled:opacity-50 flex justify-center items-center">
                        <span v-if="isLoading" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                        <span v-else>Register</span>
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-gray-600">
                    Sudah punya akun? 
                    <router-link to="/login" class="font-bold text-blue-600 hover:underline">Login disini</router-link>
                </div>
            </div>

            <div class="hidden lg:flex w-1/2 bg-[#8EC6DF] items-center justify-center p-12">
                <div class="text-center">
                    <img src="../assets/logo.png" alt="Cat Illustration" class="w-80 object-contain drop-shadow-2xl mb-6" />
                    <h3 class="text-white text-2xl font-kaushan">Join RememberME Today!</h3>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Navbar from "../components/Navbar.vue";
import AlertModal from "../components/AlertModal.vue"; 
import { useAuthStore } from "@/Stores/authStore";
import { ref } from "vue";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const isLoading = ref(false);

// State Alert Modal Dinamis
const alertConfig = ref({
    show: false,
    title: '',
    message: '',
    type: 'success'
});

const register = async () => {
    // Validasi Password Client-side
    if (password.value !== confirmPassword.value) {
        alertConfig.value = {
            show: true,
            title: 'Ups!',
            message: 'Password dan Konfirmasi Password tidak cocok.',
            type: 'error'
        };
        return;
    }

    if (password.value.length < 6) {
        alertConfig.value = {
            show: true,
            title: 'Password Lemah',
            message: 'Password minimal harus 6 karakter.',
            type: 'error'
        };
        return;
    }

    isLoading.value = true;

    try {
        const success = await auth.register({
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: confirmPassword.value,
        });

        if (success) {
            alertConfig.value = {
                show: true,
                title: 'Berhasil!',
                message: 'Akun Anda telah dibuat. Silakan login untuk melanjutkan.',
                type: 'success'
            };
        } else {
            // Tampilkan error dari validasi server
            alertConfig.value = {
                show: true,
                title: 'Registrasi Gagal',
                message: 'Email mungkin sudah terdaftar atau format data salah.',
                type: 'error'
            };
        }
    } catch (error) {
        alertConfig.value = {
            show: true,
            title: 'Error',
            message: 'Terjadi kesalahan jaringan atau server.',
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
        router.push("/login"); // Redirect ke login hanya jika sukses
    }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>