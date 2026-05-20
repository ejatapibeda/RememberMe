<template>
  <header class="fixed top-0 w-full z-50 px-4 py-4">
    <nav class="max-w-7xl mx-auto bg-white/30 backdrop-blur-md border border-white/20 shadow-lg rounded-[40px] px-6 py-3 flex items-center justify-between transition-all duration-300">

      <router-link to="/" class="text-xl font-bold text-gray-800 flex items-center gap-1 group">
        <span>Remember<span class="text-yellow-600">ME</span></span>
      </router-link>

      <div class="hidden md:flex items-center gap-2">
        <template v-if="!isAuthenticated">
          <router-link to="/login"
            class="px-6 py-2 rounded-full font-bold text-gray-700 hover:bg-white/50 transition-all border border-transparent hover:border-white/40">
            Login
          </router-link>
        </template>

        <template v-else>
          <div class="flex bg-gray-200/50 p-1 rounded-full border border-white/20">
            <router-link to="/tugas" active-class="bg-white shadow-sm text-blue-600"
              class="px-5 py-2 rounded-full text-sm font-medium text-gray-600 hover:text-blue-600 transition-all">
              Tugas
            </router-link>
            <router-link to="/kategori" active-class="bg-white shadow-sm text-blue-600"
              class="px-5 py-2 rounded-full text-sm font-medium text-gray-600 hover:text-blue-600 transition-all">
              Kategori
            </router-link>
            <router-link to="/history" active-class="bg-white shadow-sm text-blue-600"
              class="px-5 py-2 rounded-full text-sm font-medium text-gray-600 hover:text-blue-600 transition-all">
              History
            </router-link>
          </div>

          <div class="relative ml-2">
            <button @click="toggleDropdown"
              class="flex items-center gap-2 bg-white/80 p-1 pr-3 rounded-full shadow-sm border border-white/40 hover:bg-white transition-all">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                {{ userName.charAt(0).toUpperCase() }}
              </div>
              <svg :class="{'rotate-180': dropdownOpen}" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-4 h-4 transition-transform text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
              </svg>
            </button>

            <transition name="dropdown">
              <div v-if="dropdownOpen"
                class="absolute right-0 mt-3 min-w-[220px] bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/40 p-2 z-50">
                <div class="px-4 py-3 border-b border-gray-100">
                  <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mb-1">Akun Aktif</p>
                  <p class="text-sm font-bold text-gray-900 truncate">{{ userName }}</p>
                  <p class="text-[11px] text-gray-500 truncate">{{ userEmail }}</p>
                </div>
                <div class="py-1">
                  <router-link @click="dropdownOpen = false" to="/profil"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded-xl transition-colors">
                    <span>Profil Saya</span>
                  </router-link>
                </div>
                <button @click="logout"
                  class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                  <span class="font-medium">Logout</span>
                </button>
              </div>
            </transition>
          </div>
        </template>
      </div>

      <button @click="toggleMenu" class="md:hidden p-2 rounded-full hover:bg-gray-100 transition-colors">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path v-if="!menuOpen" d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
          <path v-else d="M6 18L18 6M6 6l12 12" stroke-linecap="round" />
        </svg>
      </button>
    </nav>

    <transition name="mobile-menu">
      <div v-if="menuOpen"
        class="md:hidden mt-2 mx-auto max-w-[95%] bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl p-4 border border-white/40 z-50">
        <div class="flex flex-col gap-2">
          <template v-if="!isAuthenticated">
            <router-link to="/login" @click="menuOpen = false" class="p-3 rounded-2xl hover:bg-blue-50 text-gray-700 font-medium transition-colors">Login</router-link>
          </template>

          <template v-else>
            <router-link to="/tugas" @click="menuOpen = false" class="p-3 rounded-2xl hover:bg-blue-50 text-gray-700 font-medium transition-colors">Tugas</router-link>
            <router-link to="/kategori" @click="menuOpen = false" class="p-3 rounded-2xl hover:bg-blue-50 text-gray-700 font-medium transition-colors">Kategori</router-link>
            <router-link to="/history" @click="menuOpen = false" class="p-3 rounded-2xl hover:bg-blue-50 text-gray-700 font-medium transition-colors">History</router-link>
            <router-link to="/profil" @click="menuOpen = false" class="p-3 rounded-2xl hover:bg-blue-50 text-gray-700 font-medium transition-colors">Profil Saya</router-link>

            <div class="h-px bg-gray-100 my-2"></div>

            <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                  {{ userName.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="font-bold text-gray-800 text-sm leading-none mb-1">{{ userName }}</p>
                  <p class="text-[11px] text-gray-500 truncate">{{ userEmail }}</p>
                </div>
              </div>
              <button @click="logout"
                class="w-full py-3 bg-red-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-red-200 active:scale-95 transition-transform">
                Logout
              </button>
            </div>
          </template>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/Stores/authStore";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";

const menuOpen = ref(false);
const dropdownOpen = ref(false);

const auth = useAuthStore();
const router = useRouter();
const { isAuthenticated } = storeToRefs(auth);

// Ambil data user secara reaktif dari localStorage
const userName = computed(() => localStorage.getItem("name") || "Pengguna");
const userEmail = computed(() => localStorage.getItem("email") || "email@example.com");

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
  if (menuOpen.value) dropdownOpen.value = false;
};

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

const logout = () => {
  auth.logout();
  menuOpen.value = false;
  dropdownOpen.value = false;
  router.push("/login");
};
</script>

<style scoped>
  .dropdown-enter-active, .dropdown-leave-active { transition: all 0.2s ease-out; }
  .dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-10px) scale(0.95); }
  .mobile-menu-enter-active, .mobile-menu-leave-active { transition: all 0.3s ease-in-out; }
  .mobile-menu-enter-from, .mobile-menu-leave-to { opacity: 0; transform: scale(0.9); }
</style>