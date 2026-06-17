<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'relative inline-flex items-center justify-center gap-2 font-semibold rounded-2xl transition-all active:scale-95 focus:outline-none focus:ring-4',
      sizeClass,
      variantClass,
      block ? 'w-full' : '',
      disabled || loading ? 'opacity-60 cursor-not-allowed' : '',
    ]"
  >
    <span
      v-if="loading"
      class="w-4 h-4 border-2 border-white/70 border-t-transparent rounded-full animate-spin"
    />
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' }, // primary|secondary|danger|ghost|success
  size: { type: String, default: 'md' }, // sm|md|lg
  block: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
});

const sizeClass = computed(() => ({
  sm: 'px-4 py-2 text-sm',
  md: 'px-6 py-3 text-sm',
  lg: 'px-8 py-4 text-base',
}[props.size] || 'px-6 py-3 text-sm'));

const variantClass = computed(() => ({
  primary: 'bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 text-white shadow-lg shadow-blue-200 hover:shadow-blue-300 focus:ring-blue-200',
  secondary: 'bg-white/80 text-gray-700 border border-gray-200 hover:bg-white shadow-sm focus:ring-gray-200',
  danger: 'bg-gradient-to-r from-rose-500 to-red-500 text-white shadow-lg shadow-red-200 hover:shadow-red-300 focus:ring-red-200',
  success: 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-200 focus:ring-emerald-200',
  ghost: 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-200',
}[props.variant] || ''));
</script>
