import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/axios"; // Pastikan path ke axios.js benar

export const useAuthStore = defineStore("auth", () => {
    // ---------------- STATE ----------------
    const user = ref(null);
    const token = ref(localStorage.getItem("authToken") || null);
    const errors = ref({});
    const isLoading = ref(false);
    const successMessage = ref("");

    // ---------------- GETTERS ----------------
    const isAuthenticated = computed(() => !!token.value);

    // ---------------- REGISTER ----------------
    const register = async (formData) => {
        isLoading.value = true;
        errors.value = {};
        successMessage.value = "";

        try {
            const response = await api.post("/register", formData);
            successMessage.value = response.data.message;
            return true;
        } catch (error) {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                errors.value = { general: ["Terjadi kesalahan pada server."] };
            }
            return false;
        } finally {
            isLoading.value = false;
        }
    };

    // ---------------- LOGIN ----------------
    const login = async (credentials) => {
        isLoading.value = true;
        errors.value = {};

        try {
            const response = await api.post("/login", credentials);

            // Update state
            token.value = response.data.token;
            user.value = response.data.user;

            // Simpan ke LocalStorage
            localStorage.setItem("authToken", token.value);
            localStorage.setItem("name", response.data.user.name);
            localStorage.setItem("email", response.data.user.email);

            return true;
        } catch (error) {
            if (error.response?.status === 401) {
                errors.value = { general: ["Email atau password salah"] };
            } else if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                errors.value = { general: ["Terjadi kesalahan server"] };
            }
            return false;
        } finally {
            isLoading.value = false;
        }
    };

    // ---------------- LOGOUT ----------------
    const logout = () => {
        token.value = null;
        user.value = null;
        localStorage.removeItem("authToken");
        localStorage.removeItem("name");
        localStorage.removeItem("email");
        // Tidak perlu hapus header manual karena interceptor akan membaca token = null
    };

    // ---------------- UPDATE PROFILE ----------------
  const updateProfile = async (formData) => {
    isLoading.value = true;
    errors.value = {};
    try {
        // Ganti dari .put("/update-profile") menjadi .post("/profile")
        const response = await api.post("/profile", formData);
        
        user.value = response.data.user;
        localStorage.setItem("name", response.data.user.name);
        localStorage.setItem("email", response.data.user.email);
        
        return { success: true, message: "Profil berhasil diperbarui!" };
    } catch (error) {
        // ... (sisanya tetap sama)
    }
};

    // ---------------- FORGOT PASSWORD ----------------
    const forgotPassword = async (email) => {
        isLoading.value = true;
        errors.value = {};
        try {
            const response = await api.post("/forgot-password", { email });
            return { success: true, message: response.data.message };
        } catch (error) {
            let msg = error.response?.data?.message || "Terjadi kesalahan.";
            errors.value = error.response?.data?.errors || { general: [msg] };
            return { success: false, message: msg };
        } finally {
            isLoading.value = false;
        }
    };

    // ---------------- RESET PASSWORD ----------------
const resetPassword = async (formData) => {
    isLoading.value = true;
    errors.value = {};
    try {
        const response = await api.post("/reset-password", formData);
        return { success: true, message: response.data.message || "Password berhasil diperbarui!" };
    } catch (error) {
        let msg = error.response?.data?.message || "Gagal mereset password.";
        // Simpan error validasi (seperti password kurang panjang) ke state errors
        errors.value = error.response?.data?.errors || { general: [msg] };
        return { success: false, message: msg };
    } finally {
        isLoading.value = false;
    }
};
    return {
        
        user,
        token,
        errors,
        isLoading,
        successMessage,
        isAuthenticated,
        
        resetPassword,
        updateProfile,
        forgotPassword,
        register,
        login,
        logout,
    };
});