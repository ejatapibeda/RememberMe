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

html, body {
  margin: 0;
  min-height: 100%;
  background:
    radial-gradient(1200px 600px at 10% -10%, rgba(96, 165, 250, 0.18), transparent 60%),
    radial-gradient(900px 500px at 110% 10%, rgba(244, 114, 182, 0.14), transparent 60%),
    radial-gradient(900px 700px at 50% 110%, rgba(251, 191, 36, 0.12), transparent 60%),
    linear-gradient(180deg, #F2F7FF 0%, #EFF6FF 100%);
  background-attachment: fixed;
}
</style>