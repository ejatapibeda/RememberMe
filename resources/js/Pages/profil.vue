<template>
  <div>
    <div class="max-w-3xl mx-auto py-2">
      <h1 class="font-kaushan text-4xl text-gray-800 mb-6">Profil Saya</h1>

      <!-- Profile card -->
      <GlassCard padding="lg" class="mb-6">
        <div class="flex flex-col md:flex-row items-center gap-6">
          <div class="relative">
            <div class="w-24 h-24 bg-gradient-to-tr from-blue-500 via-indigo-500 to-cyan-400 rounded-3xl flex items-center justify-center p-1 shadow-xl">
              <div class="w-full h-full bg-white rounded-[20px] flex items-center justify-center">
                <span class="text-3xl font-bold bg-gradient-to-br from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                  {{ (form.name || 'U').charAt(0).toUpperCase() }}
                </span>
              </div>
            </div>
            <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-emerald-500 rounded-xl border-4 border-white"></div>
          </div>
          <div class="flex-1 w-full space-y-4">
            <div>
              <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Nama Lengkap</label>
              <input v-model="form.name" type="text"
                class="mt-1 w-full px-4 py-3 bg-white/70 border border-white/60 focus:bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all rounded-2xl text-gray-700 font-medium" />
            </div>
            <div>
              <label class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Email</label>
              <input v-model="form.email" type="email"
                class="mt-1 w-full px-4 py-3 bg-white/70 border border-white/60 focus:bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all rounded-2xl text-gray-700 font-medium" />
            </div>
            <div class="flex gap-2 pt-2">
              <GradientButton variant="primary" :loading="authStore.isLoading" @click="handleSave">
                Simpan Perubahan
              </GradientButton>
              <GradientButton variant="ghost" @click="handleCancel">Batal</GradientButton>
            </div>
          </div>
        </div>
      </GlassCard>

      <!-- Telegram section -->
      <GlassCard padding="lg">
        <div class="flex items-start justify-between flex-wrap gap-4 mb-5">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white shadow-lg shadow-blue-200">
              <PaperAirplaneIcon class="w-6 h-6" />
            </div>
            <div>
              <h2 class="font-bold text-gray-800 text-lg">Notifikasi Telegram</h2>
              <p class="text-xs text-gray-500">Reminder otomatis dikirim ke Telegram-mu</p>
            </div>
          </div>
          <div v-if="status?.linked"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            TERHUBUNG
          </div>
          <div v-else class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
            BELUM TERHUBUNG
          </div>
        </div>

        <!-- Connected state -->
        <div v-if="status?.linked" class="space-y-4">
          <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-4 flex items-center justify-between flex-wrap gap-3">
            <div>
              <p class="text-sm font-bold text-emerald-800">
                {{ status.telegram_username ? '@' + status.telegram_username : 'Akun Telegram terhubung' }}
              </p>
              <p class="text-xs text-emerald-700">
                {{ status.has_custom_bot ? 'Mode bot pribadi aktif' : 'Menggunakan bot @' + (status.bot_username || 'kwanremindbot') }}
              </p>
            </div>
            <div class="flex gap-2">
              <GradientButton size="sm" variant="secondary" :loading="testing" @click="onTest">
                <BoltIcon class="w-4 h-4" /> Test
              </GradientButton>
              <GradientButton size="sm" variant="danger" @click="onUnlink">Putuskan</GradientButton>
            </div>
          </div>
        </div>

        <!-- Not connected -->
        <div v-else class="space-y-4">
          <div v-if="!pendingCode" class="space-y-3">
            <p class="text-sm text-gray-600">
              Hubungkan akun Telegram-mu untuk menerima reminder. Klik tombol di bawah, lalu kirim kode ke bot.
            </p>
            <GradientButton variant="primary" :loading="generating" @click="onGenerate">
              <PaperAirplaneIcon class="w-4 h-4" /> Hubungkan via Bot Aplikasi
            </GradientButton>
          </div>

          <div v-else class="bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-200 rounded-2xl p-5 space-y-4">
            <div>
              <p class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-2">Kode Pairing</p>
              <div class="flex items-center gap-2">
                <code class="flex-1 px-4 py-3 bg-white rounded-xl text-2xl font-mono font-bold text-center tracking-widest text-blue-700 border border-blue-200 select-all">
                  {{ pendingCode }}
                </code>
                <button @click="copyCode" class="px-4 py-3 bg-white border border-blue-200 rounded-xl text-blue-600 hover:bg-blue-50">
                  <DocumentDuplicateIcon class="w-5 h-5" />
                </button>
              </div>
              <p v-if="copied" class="text-[11px] text-emerald-600 mt-1">Disalin!</p>
            </div>

            <ol class="space-y-2 text-sm text-gray-700">
              <li class="flex items-start gap-2">
                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                <span>Buka Telegram dan cari bot <b>@{{ status?.bot_username || 'kwanremindbot' }}</b></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                <span>Kirim perintah: <code class="bg-white px-1.5 py-0.5 rounded text-blue-700">/start {{ pendingCode }}</code></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                <span>Halaman ini akan otomatis ter-update saat berhasil ✨</span>
              </li>
            </ol>

            <div class="flex flex-wrap gap-2 pt-1">
              <a v-if="deepLink" :href="deepLink" target="_blank" rel="noopener">
                <GradientButton variant="primary" size="sm">
                  <PaperAirplaneIcon class="w-4 h-4" /> Buka @{{ status?.bot_username || 'kwanremindbot' }}
                </GradientButton>
              </a>
              <GradientButton variant="ghost" size="sm" @click="onGenerate" :loading="generating">
                <ArrowPathIcon class="w-4 h-4" /> Kode Baru
              </GradientButton>
            </div>
            <p class="text-[11px] text-gray-500">Kode kadaluarsa dalam 15 menit. Halaman akan auto-cek tiap 3 detik.</p>
          </div>
        </div>

        <!-- Custom bot toggle -->
        <div class="mt-6 pt-6 border-t border-gray-100">
          <button @click="showCustom = !showCustom"
            class="flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-colors">
            <ChevronRightIcon :class="['w-4 h-4 transition-transform', showCustom ? 'rotate-90' : '']" />
            Mode Lanjutan: pakai bot Telegram saya sendiri
          </button>

          <div v-if="showCustom" class="mt-4 space-y-3 bg-gray-50/70 border border-gray-200 rounded-2xl p-4">
            <p class="text-xs text-gray-600">
              Buat bot via <a href="https://t.me/BotFather" target="_blank" class="text-blue-600 underline">@BotFather</a>,
              ambil token dan chat ID kamu, lalu simpan di sini.
            </p>
            <div>
              <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Bot Token</label>
              <input v-model="customForm.token" type="text" placeholder="123456:ABC..."
                class="mt-1 w-full px-4 py-3 bg-white border border-gray-200 focus:ring-2 focus:ring-blue-400 focus:outline-none rounded-2xl text-sm font-mono" />
            </div>
            <div>
              <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Chat ID</label>
              <input v-model="customForm.chat_id" type="text" placeholder="123456789"
                class="mt-1 w-full px-4 py-3 bg-white border border-gray-200 focus:ring-2 focus:ring-blue-400 focus:outline-none rounded-2xl text-sm font-mono" />
            </div>
            <div class="flex gap-2">
              <GradientButton variant="primary" size="sm" :loading="savingCustom" @click="onSaveCustom">
                Simpan & Hubungkan
              </GradientButton>
              <GradientButton v-if="status?.has_custom_bot" variant="secondary" size="sm" @click="onRemoveCustom">
                Matikan Mode Lanjutan
              </GradientButton>
            </div>
          </div>
        </div>
      </GlassCard>
    </div>

    <AlertModal :show="alert.show" :type="alert.type" :title="alert.title" :message="alert.message"
      @confirm="alert.show = false" />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, computed } from 'vue';
import { useAuthStore } from '@/Stores/authStore';
import {
  PaperAirplaneIcon, BoltIcon, DocumentDuplicateIcon, ArrowPathIcon, ChevronRightIcon,
} from '@heroicons/vue/24/outline';
import GlassCard from '@/components/GlassCard.vue';
import GradientButton from '@/components/GradientButton.vue';
import AlertModal from '@/components/AlertModal.vue';

const authStore = useAuthStore();

const form = ref({ name: '', email: '' });
const alert = reactive({ show: false, type: 'success', title: '', message: '' });

const status = ref(null);
const pendingCode = ref(null);
const deepLink = ref(null);
const generating = ref(false);
const testing = ref(false);
const copied = ref(false);
const showCustom = ref(false);
const savingCustom = ref(false);
const customForm = reactive({ token: '', chat_id: '' });

let pollHandle = null;

const showAlert = (type, title, message) => {
  alert.type = type; alert.title = title; alert.message = message; alert.show = true;
};

const refreshStatus = async () => {
  const res = await authStore.getTelegramStatus();
  if (res) {
    status.value = res;
    pendingCode.value = res.pending_code;
    deepLink.value = res.bot_username && res.pending_code
      ? `https://t.me/${res.bot_username}?start=${res.pending_code}`
      : null;
  }
};

onMounted(async () => {
  form.value.name = localStorage.getItem('name') || '';
  form.value.email = localStorage.getItem('email') || '';
  await refreshStatus();
  // Poll status while waiting for pairing
  pollHandle = setInterval(async () => {
    if (pendingCode.value && !status.value?.linked) {
      await refreshStatus();
      if (status.value?.linked) {
        showAlert('success', 'Terhubung!', 'Telegram berhasil dihubungkan.');
      }
    }
  }, 3000);
});

onBeforeUnmount(() => pollHandle && clearInterval(pollHandle));

const handleSave = async () => {
  const r = await authStore.updateProfile(form.value);
  showAlert(r?.success ? 'success' : 'error', r?.success ? 'Berhasil!' : 'Gagal!',
    r?.message || 'Profil diperbarui.');
};
const handleCancel = () => {
  form.value.name = localStorage.getItem('name') || '';
  form.value.email = localStorage.getItem('email') || '';
};

const onGenerate = async () => {
  generating.value = true;
  const r = await authStore.generateTelegramCode();
  generating.value = false;
  if (r?.success) {
    pendingCode.value = r.code;
    deepLink.value = r.deep_link;
    await refreshStatus();
  } else {
    showAlert('error', 'Gagal', r?.message || 'Tidak bisa membuat kode.');
  }
};

const copyCode = async () => {
  try {
    await navigator.clipboard.writeText(pendingCode.value);
    copied.value = true;
    setTimeout(() => copied.value = false, 1500);
  } catch (e) { /* noop */ }
};

const onUnlink = async () => {
  const r = await authStore.unlinkTelegram();
  await refreshStatus();
  showAlert(r?.success ? 'success' : 'error', r?.success ? 'Diputus' : 'Gagal',
    r?.message || 'Telegram diputus.');
};

const onTest = async () => {
  testing.value = true;
  const r = await authStore.testTelegram();
  testing.value = false;
  showAlert(r?.success ? 'success' : 'error', r?.success ? 'Terkirim!' : 'Gagal',
    r?.message || '-');
};

const onSaveCustom = async () => {
  if (!customForm.token || !customForm.chat_id) {
    return showAlert('error', 'Lengkapi field', 'Token & chat ID wajib diisi.');
  }
  savingCustom.value = true;
  const r = await authStore.saveCustomBot({ token: customForm.token, chat_id: customForm.chat_id });
  savingCustom.value = false;
  await refreshStatus();
  showAlert(r?.success ? 'success' : 'error', r?.success ? 'Tersimpan' : 'Gagal',
    r?.message || '-');
  if (r?.success) { customForm.token = ''; customForm.chat_id = ''; }
};

const onRemoveCustom = async () => {
  const r = await authStore.removeCustomBot();
  await refreshStatus();
  showAlert(r?.success ? 'success' : 'error', r?.success ? 'Dimatikan' : 'Gagal',
    r?.message || '-');
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Kaushan+Script&display=swap');
.font-kaushan { font-family: 'Kaushan Script', cursive; }
</style>
