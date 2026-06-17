import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/axios"; // Pastikan path ke axios.js benar
import { useNotificationStore } from "@/Stores/notificationStore";

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

            // Start notification polling
            try {
                useNotificationStore().startPolling();
            } catch (e) { /* noop */ }

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
        try { useNotificationStore().reset(); } catch (e) { /* noop */ }
        // Tidak perlu hapus header manual karena interceptor akan membaca token = null
    };

    // ---------------- TELEGRAM ----------------
    const getTelegramStatus = async () => {
        try {
            const res = await api.get("/telegram/status");
            return res.data;
        } catch (e) {
            return null;
        }
    };

    const generateTelegramCode = async () => {
        try {
            const res = await api.post("/telegram/link-code");
            return res.data;
        } catch (e) {
            return { success: false, message: "Gagal membuat kode" };
        }
    };

    const unlinkTelegram = async () => {
        try {
            const res = await api.post("/telegram/unlink");
            return res.data;
        } catch (e) {
            return { success: false };
        }
    };

    const saveCustomBot = async (payload) => {
        try {
            const res = await api.post("/telegram/custom-bot", payload);
            return res.data;
        } catch (e) {
            return {
                success: false,
                message: e.response?.data?.message || "Gagal menyimpan bot pribadi",
            };
        }
    };

    const removeCustomBot = async () => {
        try {
            const res = await api.delete("/telegram/custom-bot");
            return res.data;
        } catch (e) {
            return { success: false };
        }
    };

    const testTelegram = async () => {
        try {
            const res = await api.post("/telegram/test");
            return res.data;
        } catch (e) {
            return {
                success: false,
                message: e.response?.data?.message || "Gagal mengirim pesan test",
            };
        }
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
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
        return {
            success: false,
            message: error.response?.data?.message || "Gagal memperbarui profil",
        };
    } finally {
        isLoading.value = false;
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
        // telegram
        getTelegramStatus,
        generateTelegramCode,
        unlinkTelegram,
        saveCustomBot,
        removeCustomBot,
        testTelegram,
    };
});