import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/axios"; // Pastikan path ini benar

export const useKategoriStore = defineStore("kategori", () => {

    // ---------------- STATE ----------------
    const kategoriList = ref([]);      // Untuk menampung daftar semua kategori
    const currentKategori = ref(null); // Untuk menampung 1 kategori (saat edit)
    const errors = ref({});            // Menampung error validasi
    const isLoading = ref(false);      // Loading spinner
    const successMessage = ref("");    // Pesan sukses

    // ---------------- ACTIONS ----------------


    
    // 1. GET ALL (Ambil semua kategori)
const fetchKategori = async () => {
    isLoading.value = true;
    try {
        const response = await api.get("/get-kategori");
        
        // Perhatikan ini: response.data (dari axios) .data (dari JSON Laravel)
        if (response.data && response.data.success) {
            kategoriList.value = response.data.data; 
        } else {
            // Fallback jika Laravel kirim array langsung
            kategoriList.value = response.data;
        }
    } catch (error) {
        console.error("Gagal mengambil kategori:", error);
    } finally {
        isLoading.value = false;
    }
};
    // 2. CREATE (Tambah kategori baru)
    const createKategori = async (formData) => {
        isLoading.value = true;
        errors.value = {}; // Reset error
        successMessage.value = "";

        try {
            const response = await api.post("/kategori", formData);
            
            // Refresh data list agar yang baru langsung muncul
            await fetchKategori(); 

            successMessage.value = "Kategori berhasil dibuat!";
            return true;

        } catch (error) {
            if (error.response?.status === 422) {
                // Error Validasi Laravel
                errors.value = error.response.data.errors;
            } else {
                console.error(error);
                errors.value = { general: ["Gagal menyimpan data."] };
            }
            return false;
        } finally {
            isLoading.value = false;
        }
    };

    // 3. SHOW (Ambil 1 data untuk diedit)
    const fetchKategoriById = async (id) => {
        isLoading.value = true;
        currentKategori.value = null;
        
        try {
            const response = await api.get(`/kategori/${id}`);
            if(response.data.success) {
                currentKategori.value = response.data.data;
            }
            return response.data.data;
        } catch (error) {
            console.error("Kategori tidak ditemukan:", error);
            return null;
        } finally {
            isLoading.value = false;
        }
    };

    // 4. UPDATE (Simpan perubahan)
    const updateKategori = async (id, formData) => {
        isLoading.value = true;
        errors.value = {};
        successMessage.value = "";

        try {
            // Method PUT biasanya digunakan untuk update
            await api.put(`/kategori/${id}`, formData);
            
            // Refresh list
            await fetchKategori();
            
            successMessage.value = "Kategori berhasil diperbarui!";
            return true;

        } catch (error) {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                errors.value = { general: ["Gagal mengupdate data."] };
            }
            return false;
        } finally {
            isLoading.value = false;
        }
    };

    // 5. DELETE (Hapus kategori)
    const deleteKategori = async (id) => {
        isLoading.value = true;
        try {
            await api.delete(`/kategori/${id}`);
            
            // Hapus item dari list lokal supaya tidak perlu fetch ulang (opsional, biar cepat)
            kategoriList.value = kategoriList.value.filter(k => k.id !== id);
            
            successMessage.value = "Kategori berhasil dihapus.";
            return true;

        } catch (error) {
            console.error("Gagal menghapus:", error);
            alert("Gagal menghapus kategori.");
            return false;
        } finally {
            isLoading.value = false;
        }
    };

    // Reset state (berguna saat pindah halaman)
    const resetState = () => {
        errors.value = {};
        successMessage.value = "";
        currentKategori.value = null;
    };

    return {
        // STATE
        kategoriList,
        currentKategori,
        errors,
        isLoading,
        successMessage,

        // ACTIONS
        fetchKategori,
        fetchKategoriById,
        createKategori,
        updateKategori,
        deleteKategori,
        resetState
    };

});