import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/Stores/authStore";

import Home from "../Pages/home.vue";
import Login from "../Auth/Login.vue";
import LupaPassword from "../Auth/ForgotPassword.vue";
import ResetPassword from "../Auth/ResetPassword.vue";
import Register from "../Auth/Register.vue";
import history from "../Pages/history.vue";
import Profil from "../Pages/profil.vue";

import CreateKategoriPage from "../Public/CreateKategoriPage.vue";
import Kategori from "../Pages/Kategori.vue";
import ShowKategori from "../Public/ShowKategori.vue";
import EditKategori from "../Public/EditKategori.vue";

import CreateTugasPage from "../Public/CreateTugasPage.vue";
import tugas from "../Pages/tugas.vue";
import ShowTugas from "../Public/ShowTugas.vue";
import EditTugas from "../Public/EditTugas.vue";

const routes = [
    { path: "/", name: "home", component: Home },
    { path: "/profil", name: "profil", component: Profil },
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: { requiresGuest: true },
    },
    { path: "/lupa_password", name: "lupa_password", component: LupaPassword },
    { path: "/reset-password", name: "reset-password", component: ResetPassword },
    {
        path: "/registrasi",
        name: "register",
        component: Register,
        meta: { requiresGuest: true },
    },
    { path: "/tugas", name: "tugas", component: tugas, meta: { requiresAuth: true } },
    { path: "/Show_Tugas/:id", name: "show-tugas", component: ShowTugas, meta: { requiresAuth: true } },
    { path: "/Edit_tugas/:id", name: "edit-tugas", component: EditTugas, meta: { requiresAuth: true } },
    { path: "/Create_Tugas", name: "create-tugas", component: CreateTugasPage, meta: { requiresAuth: true } },
    { path: "/kategori", name: "kategori", component: Kategori, meta: { requiresAuth: true } },
    { path: "/Create_kategori", name: "create-kategori", component: CreateKategoriPage, meta: { requiresAuth: true } },
    { path: "/Edit_Kategori/:id", name: "edit-kategori", component: EditKategori, meta: { requiresAuth: true } },
    { path: "/Show_Kategori/:id", name: "show_kategori", component: ShowKategori, meta: { requiresAuth: true } },
    { path: "/history", name: "history", component: history, meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: "login" });
    } else if (to.meta.requiresGuest && isAuthenticated) {
        next({ name: "home" });
    } else {
        next();
    }
});

export default router;
