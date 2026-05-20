import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/axios";

export const useTugasStore = defineStore("tugas", () => {
    // ---------------- STATE ----------------
    
    const tugasList = ref([]);      // Menampung semua daftar tugas dari database
    const isLoading = ref(false);   // Loading state untuk spinner/indikator
    const errors = ref({});         // Menampung detail error validasi dari Laravel

    // ---------------- ACTIONS ----------------

    // 1. Ambil semua tugas (Fetch)
   const fetchTugas = async () => {
        isLoading.value = true;
        try {
            const response = await api.get('/get-Tugas');
            if (response.data.success) {
                tugasList.value = response.data.data;
            }
        } catch (error) {
            console.error("Gagal mengambil data:", error);
        } finally {
            isLoading.value = false;
        }
    }

    // 2. Buat tugas baru (Create)
    const createTugas = async (formData) => {
        isLoading.value = true;
        errors.value = {}; 

        try {
            const response = await api.post('/Create_Tugas', formData);
            
            if (response.data.success) {
                // Refresh daftar tugas setelah berhasil membuat yang baru
                await fetchTugas(); 
                return { success: true, message: response.data.message };
            }
        } catch (error) {
            if (error.response?.status === 422) {
                // Tangkap error validasi dari Laravel
                errors.value = error.response.data.errors;
            }
            return { 
                success: false, 
                message: error.response?.data?.message || "Gagal membuat tugas" 
            };
        } finally {
            isLoading.value = false;
        }
    };

    // 3. Update status checklist (Optional - untuk toggleCheck)
  // Tugas.js
   const updateStatus = async (id, status) => {
    try {
        // Kirim status is_done (1 atau 0) ke Laravel
        const response = await api.patch(`/update-status-tugas/${id}`, { 
            is_done: status ? 1 : 0 
        });
        return response.data.success;
    } catch (error) {
        console.error("Gagal memperbarui status tugas:", error);
        return false;
    }
};

// 4 show tugas 
const showTugas = async (id) => {
    isLoading.value = true;
    try {
        const response = await api.get(`/get-Tugas/${id}`);
        if (response.data.success) {
            return response.data.data; // Pastikan mengembalikan data objek tugas
        }
    } catch (error) {
        console.error("Gagal mengambil detail tugas:", error);
        return null; 
    } finally {
        isLoading.value = false;
    }
};

    // 5. Update data tugas (Edit)
    const updateTugas = async (id, formData) => {
        isLoading.value = true;
        errors.value = {}; 

        try {
            // Gunakan PUT atau PATCH sesuai route di Laravel
            const response = await api.put(`/Update_Tugas/${id}`, formData);
            
            if (response.data.success) {
                await fetchTugas(); // Refresh list agar data di halaman utama update
                return { success: true, message: response.data.message };
            }
        } catch (error) {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            }
            return { 
                success: false, 
                message: error.response?.data?.message || "Gagal memperbarui tugas" 
            };
        } finally {
            isLoading.value = false;
        }
    };

    // 6. Hapus tugas (Delete)
const deleteTugas = async (id) => {
    isLoading.value = true;
    try {
        const response = await api.delete(`/Delete_Tugas/${id}`);
        if (response.data.success) {
            // Update list lokal agar tidak perlu fetch ulang
            tugasList.value = tugasList.value.filter(t => t.id !== id);
            return { success: true, message: response.data.message };
        }
    } catch (error) {
        console.error("Gagal menghapus tugas:", error);
        return { success: false, message: "Gagal menghapus" };
    } finally {
        isLoading.value = false;
    }
};
    return {
        // State
        tugasList,
        isLoading,
        errors,
        // Actions
        fetchTugas,
        createTugas,
        updateStatus,
        deleteTugas,
        showTugas,  
        updateTugas
    };
});