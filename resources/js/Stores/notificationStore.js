import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/axios";

export const useNotificationStore = defineStore("notifications", () => {
    const items = ref([]);
    const unreadCount = ref(0);
    const isLoading = ref(false);
    const lastSeenIds = ref(new Set());
    let pollHandle = null;
    let toastListeners = [];

    const onToast = (cb) => {
        toastListeners.push(cb);
        return () => {
            toastListeners = toastListeners.filter((c) => c !== cb);
        };
    };

    const emitToast = (notif) => {
        toastListeners.forEach((cb) => {
            try { cb(notif); } catch (e) { /* noop */ }
        });
    };

    const fetch = async ({ silent = false } = {}) => {
        if (!silent) isLoading.value = true;
        try {
            const res = await api.get("/notifications");
            const list = res.data?.data || [];
            const newOnes = list.filter(
                (n) => !n.read_at && !lastSeenIds.value.has(n.id)
            );

            // first load: just register IDs without firing toasts
            const isFirstLoad = lastSeenIds.value.size === 0 && items.value.length === 0;

            items.value = list;
            unreadCount.value = res.data?.unread_count ?? list.filter((n) => !n.read_at).length;
            list.forEach((n) => lastSeenIds.value.add(n.id));

            if (!isFirstLoad && newOnes.length > 0) {
                newOnes.forEach((n) => {
                    emitToast(n);
                    pushBrowserNotification(n);
                });
            }
            return list;
        } catch (e) {
            // silent
            return [];
        } finally {
            if (!silent) isLoading.value = false;
        }
    };

    const requestBrowserPermission = async () => {
        if (!("Notification" in window)) return false;
        if (Notification.permission === "granted") return true;
        if (Notification.permission === "denied") return false;
        const result = await Notification.requestPermission();
        return result === "granted";
    };

    const pushBrowserNotification = (notif) => {
        if (!("Notification" in window)) return;
        if (Notification.permission !== "granted") return;
        try {
            const n = new Notification(notif.title, {
                body: notif.body || "",
                tag: `rm-${notif.id}`,
                icon: "/favicon.ico",
            });
            n.onclick = () => {
                window.focus();
                n.close();
            };
        } catch (e) { /* noop */ }
    };

    const markRead = async (id) => {
        try {
            await api.post(`/notifications/${id}/read`);
            const item = items.value.find((n) => n.id === id);
            if (item && !item.read_at) {
                item.read_at = new Date().toISOString();
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            }
        } catch (e) { /* noop */ }
    };

    const markAllRead = async () => {
        try {
            await api.post(`/notifications/read-all`);
            items.value.forEach((n) => {
                if (!n.read_at) n.read_at = new Date().toISOString();
            });
            unreadCount.value = 0;
        } catch (e) { /* noop */ }
    };

    const remove = async (id) => {
        try {
            await api.delete(`/notifications/${id}`);
            items.value = items.value.filter((n) => n.id !== id);
        } catch (e) { /* noop */ }
    };

    const startPolling = (intervalMs = 30000) => {
        stopPolling();
        fetch({ silent: true });
        pollHandle = setInterval(() => fetch({ silent: true }), intervalMs);
    };

    const stopPolling = () => {
        if (pollHandle) {
            clearInterval(pollHandle);
            pollHandle = null;
        }
    };

    const reset = () => {
        items.value = [];
        unreadCount.value = 0;
        lastSeenIds.value = new Set();
        stopPolling();
    };

    const sortedItems = computed(() => items.value);

    return {
        items,
        sortedItems,
        unreadCount,
        isLoading,
        fetch,
        markRead,
        markAllRead,
        remove,
        startPolling,
        stopPolling,
        reset,
        requestBrowserPermission,
        onToast,
    };
});
