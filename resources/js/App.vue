<template>
  <div class="min-h-screen" >
    <Navbar />
    
    <main class="pt-28 lg:pt-36 container mx-auto px-4">
      <router-view v-slot="{ Component, route }">
        <transition name="fade" mode="out-in">
          <div :key="route.path">
            <component :is="Component" />
          </div>
        </transition>
      </router-view>
    </main>
  </div>
</template>

<script setup>
import { watch } from "vue";
import { useRoute } from "vue-router";
import Navbar from "./components/Navbar.vue";

const route = useRoute();

watch(
  () => route.name,
  (newName) => {
    document.title = newName
      ? `${capitalize(newName)} | RememberME`
      : "RememberME";
  },
  { immediate: true }
);

function capitalize(text) {
  if (!text) return "";
  return text.charAt(0).toUpperCase() + text.slice(1);
}
</script>

<style>
/* Animasi pindah halaman */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

body {
  background-color: #F0F7FF;
  margin: 0;
}
</style>