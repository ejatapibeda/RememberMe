import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/axios";

export const useRecurringReminderStore = defineStore("recurringReminders", () => {
    const items = ref([]);
    const current = ref(null);
    const isLoading = ref(false);
    const errors = ref({});

    const fetch = async () => {
        isLoading.value = true;
        errors.value = {};
        try {
            const res = await api.get("/recurring-reminders");
            items.value = res.data?.data || [];
            return items.value;
        } catch (e) {
            return [];
        } finally {
            isLoading.value = false;
        }
    };

    const show = async (id) => {
        isLoading.value = true;
        try {
            const res = await api.get(`/recurring-reminders/${id}`);
            current.value = res.data?.data || null;
            return current.value;
        } catch (e) {
            return null;
        } finally {
            isLoading.value = false;
        }
    };

    const create = async (payload) => {
        isLoading.value = true;
        errors.value = {};
        try {
            const res = await api.post("/recurring-reminders", payload);
            if (res.data?.success) {
                items.value.unshift(res.data.data);
                return { success: true, data: res.data.data };
            }
            return { success: false, message: res.data?.message };
        } catch (e) {
            if (e.response?.status === 422) {
                errors.value = e.response.data.errors;
            }
            return { success: false, message: e.response?.data?.message || "Gagal membuat rutinitas" };
        } finally {
            isLoading.value = false;
        }
    };

    const update = async (id, payload) => {
        isLoading.value = true;
        errors.value = {};
        try {
            const res = await api.put(`/recurring-reminders/${id}`, payload);
            if (res.data?.success) {
                const idx = items.value.findIndex((i) => i.id === id);
                if (idx >= 0) items.value[idx] = res.data.data;
                return { success: true, data: res.data.data };
            }
            return { success: false, message: res.data?.message };
        } catch (e) {
            if (e.response?.status === 422) {
                errors.value = e.response.data.errors;
            }
            return { success: false, message: e.response?.data?.message || "Gagal memperbarui" };
        } finally {
            isLoading.value = false;
        }
    };

    const remove = async (id) => {
        try {
            await api.delete(`/recurring-reminders/${id}`);
            items.value = items.value.filter((i) => i.id !== id);
            return { success: true };
        } catch (e) {
            return { success: false };
        }
    };

    const toggle = async (id) => {
        try {
            const res = await api.post(`/recurring-reminders/${id}/toggle`);
            const idx = items.value.findIndex((i) => i.id === id);
            if (idx >= 0 && res.data?.data) {
                items.value[idx] = res.data.data;
            }
            return { success: true, data: res.data?.data };
        } catch (e) {
            return { success: false };
        }
    };

    return {
        items,
        current,
        isLoading,
        errors,
        fetch,
        show,
        create,
        update,
        remove,
        toggle,
    };
});
