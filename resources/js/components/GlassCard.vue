<template>
  <component
    :is="tag"
    :class="[
      'relative bg-white/60 backdrop-blur-xl border border-white/50 shadow-xl shadow-blue-100/40',
      rounded,
      paddingClass,
      hover ? 'transition-all hover:-translate-y-0.5 hover:shadow-2xl' : '',
    ]"
  >
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
  tag: { type: String, default: 'div' },
  rounded: { type: String, default: 'rounded-3xl' },
  padding: { type: String, default: 'lg' }, // none|sm|md|lg|xl
  hover: { type: Boolean, default: false },
});
const paddingClass = computed(() => ({
  none: '',
  sm: 'p-4',
  md: 'p-6',
  lg: 'p-8',
  xl: 'p-10',
}[props.padding] || 'p-8'));
</script>
