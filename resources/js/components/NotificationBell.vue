<template>
  <div class="relative">
    <button
      @click="toggle"
      class="relative w-10 h-10 rounded-full bg-white/80 hover:bg-white border border-white/40 shadow-sm flex items-center justify-center transition-all"
      aria-label="Notifikasi"
    >
      <BellIcon class="w-5 h-5 text-gray-600" />
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-gradient-to-br from-rose-500 to-red-500 text-white text-[10px] font-bold flex items-center justify-center shadow-md ring-2 ring-white"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <transition name="popover">
      <div
        v-if="open"
        ref="panelEl"
        class="absolute right-0 mt-3 w-[360px] max-w-[92vw] bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/60 z-50 overflow-hidden"
      >
        <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-gray-100">
          <div>
            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Notifikasi</p>
            <p class="text-sm font-bold text-gray-800">{{ unreadCount }} belum dibaca</p>
          </div>
          <button
            v-if="unreadCount > 0"
            @click="markAll"
            class="text-xs font-semibold text-blue-600 hover:text-blue-700"
          >
            Tandai semua
          </button>
        </div>

        <div class="max-h-[420px] overflow-y-auto">
          <div v-if="items.length === 0" class="px-6 py-12 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
              <BellSlashIcon class="w-6 h-6 text-blue-300" />
            </div>
            <p class="text-sm text-gray-500">Belum ada notifikasi</p>
          </div>

          <ul v-else class="divide-y divide-gray-50">
            <li
              v-for="n in items"
              :key="n.id"
              class="group px-5 py-4 hover:bg-blue-50/50 transition-colors cursor-pointer"
              :class="{ 'bg-blue-50/30': !n.read_at }"
              @click="onClick(n)"
            >
              <div class="flex gap-3">
                <div
                  class="w-9 h-9 flex-shrink-0 rounded-xl flex items-center justify-center text-lg"
                  :class="iconBg(n.jenis)"
                >
                  {{ icon(n.jenis) }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-800 leading-tight">
                    {{ n.title }}
                  </p>
                  <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ n.body }}</p>
                  <p class="text-[10px] text-gray-400 mt-1.5">{{ formatTime(n.created_at) }}</p>
                </div>
                <span v-if="!n.read_at" class="w-2 h-2 rounded-full bg-blue-500 mt-2 flex-shrink-0"></span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </transition>

    <!-- Toast container -->
    <Teleport to="body">
      <div class="fixed top-24 right-4 z-[100] flex flex-col gap-3 pointer-events-none">
        <transition-group name="toast">
          <div
            v-for="t in toasts"
            :key="t.id"
            class="pointer-events-auto bg-white rounded-2xl shadow-2xl border border-white/60 p-4 max-w-sm flex gap-3"
          >
            <div
              class="w-10 h-10 flex-shrink-0 rounded-xl flex items-center justify-center text-xl"
              :class="iconBg(t.jenis)"
            >
              {{ icon(t.jenis) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-gray-800">{{ t.title }}</p>
              <p class="text-xs text-gray-600 mt-0.5">{{ t.body }}</p>
            </div>
            <button @click="dismissToast(t.id)" class="text-gray-400 hover:text-gray-600">
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </transition-group>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { BellIcon, BellSlashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useNotificationStore } from '@/Stores/notificationStore';

const store = useNotificationStore();
const { items, unreadCount } = storeToRefs(store);

const open = ref(false);
const panelEl = ref(null);
const toasts = ref([]);
let unsubToast = null;

const toggle = async () => {
  open.value = !open.value;
  if (open.value) {
    await store.fetch({ silent: true });
    await store.requestBrowserPermission();
  }
};

const onClickOutside = (e) => {
  if (!open.value) return;
  if (panelEl.value && !panelEl.value.contains(e.target)) {
    // also ignore the bell button click
    if (!e.target.closest('button[aria-label="Notifikasi"]')) {
      open.value = false;
    }
  }
};

onMounted(() => {
  document.addEventListener('click', onClickOutside);
  unsubToast = store.onToast((notif) => {
    showToast(notif);
  });
});
onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside);
  if (unsubToast) unsubToast();
});

const showToast = (n) => {
  toasts.value.push(n);
  setTimeout(() => dismissToast(n.id), 6000);
};
const dismissToast = (id) => {
  toasts.value = toasts.value.filter((t) => t.id !== id);
};

const onClick = async (n) => {
  if (!n.read_at) await store.markRead(n.id);
};

const markAll = async () => {
  await store.markAllRead();
};

const icon = (jenis) => ({
  '1_jam': '🔔',
  '5_menit': '⏰',
  'deadline': '🚨',
}[jenis] || '✨');

const iconBg = (jenis) => ({
  '1_jam': 'bg-sky-100 text-sky-600',
  '5_menit': 'bg-amber-100 text-amber-600',
  'deadline': 'bg-rose-100 text-rose-600',
}[jenis] || 'bg-blue-100 text-blue-600');

const formatTime = (iso) => {
  if (!iso) return '';
  const d = new Date(iso);
  const diff = (Date.now() - d.getTime()) / 1000;
  if (diff < 60) return 'baru saja';
  if (diff < 3600) return `${Math.floor(diff / 60)} mnt lalu`;
  if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
};
</script>

<style scoped>
.popover-enter-active,
.popover-leave-active { transition: all 0.2s ease-out; }
.popover-enter-from,
.popover-leave-to { opacity: 0; transform: translateY(-8px) scale(0.96); }

.toast-enter-active,
.toast-leave-active { transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
.toast-enter-from { opacity: 0; transform: translateX(40px); }
.toast-leave-to { opacity: 0; transform: translateX(40px); }

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
