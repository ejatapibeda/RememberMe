<template>
  <span
    :class="[
      'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm',
      tone.bg,
      tone.text,
      tone.ring,
    ]"
    :title="targetDate"
  >
    <span class="w-1.5 h-1.5 rounded-full" :class="tone.dot"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
  target: { type: [String, Number, Date], required: true },
});

const now = ref(Date.now());
let intervalId = null;

onMounted(() => {
  intervalId = setInterval(() => (now.value = Date.now()), 30 * 1000);
});
onBeforeUnmount(() => intervalId && clearInterval(intervalId));

const targetDate = computed(() => new Date(props.target).toLocaleString('id-ID'));
const diffMs = computed(() => new Date(props.target).getTime() - now.value);

const label = computed(() => {
  const ms = diffMs.value;
  const abs = Math.abs(ms);
  const minutes = Math.floor(abs / 60000);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);

  if (ms <= 0) {
    if (abs < 60000) return 'Baru saja lewat';
    if (minutes < 60) return `Lewat ${minutes} menit`;
    if (hours < 24) return `Lewat ${hours} jam`;
    return `Lewat ${days} hari`;
  }

  if (minutes < 1) return 'Kurang dari 1 menit';
  if (minutes < 60) return `${minutes} menit lagi`;
  if (hours < 24) return `${hours} jam ${minutes % 60} mnt`;
  return `${days} hari lagi`;
});

const tone = computed(() => {
  const ms = diffMs.value;
  if (ms <= 0) {
    return {
      bg: 'bg-rose-100/80',
      text: 'text-rose-700',
      ring: 'ring-1 ring-rose-200',
      dot: 'bg-rose-500 animate-pulse',
    };
  }
  if (ms < 60 * 60 * 1000) {
    return {
      bg: 'bg-amber-100/80',
      text: 'text-amber-800',
      ring: 'ring-1 ring-amber-200',
      dot: 'bg-amber-500 animate-pulse',
    };
  }
  if (ms < 24 * 60 * 60 * 1000) {
    return {
      bg: 'bg-sky-100/80',
      text: 'text-sky-700',
      ring: 'ring-1 ring-sky-200',
      dot: 'bg-sky-500',
    };
  }
  return {
    bg: 'bg-emerald-100/80',
    text: 'text-emerald-700',
    ring: 'ring-1 ring-emerald-200',
    dot: 'bg-emerald-500',
  };
});
</script>
