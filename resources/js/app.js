import "./bootstrap";
import "../css/app.css";
import "@fortawesome/fontawesome-free/css/all.min.css";

import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./Router";
import { useNotificationStore } from "@/Stores/notificationStore";

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount("#app");

// Resume notification polling if user is already logged in (page reload)
if (localStorage.getItem("authToken")) {
    try {
        useNotificationStore().startPolling();
    } catch (e) { /* noop */ }
}
