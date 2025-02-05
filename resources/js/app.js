import { createApp } from "vue";
import { createStore } from "vuex";
import { router } from "./router";
import axios from "axios";
import App from "./App.vue";
import auth from "./store/modules/auth";

// Axios konfiqurasiyası
axios.defaults.baseURL = "/api";
axios.defaults.headers.common["Accept"] = "application/json";

// Token varsa əlavə et
const token = localStorage.getItem("token");
if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

// Store yaradılması
const store = createStore({
  modules: {
    auth,
  },
});

// Root komponentin yaradılması
createApp(App).use(router).use(store).mount("#app");
