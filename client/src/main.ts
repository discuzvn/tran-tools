import { createApp } from "vue";
import { createRouter, createWebHashHistory } from "vue-router";
import App from "./App.vue";
import "virtual:windi.css";
import "virtual:windi-devtools";
import Translation from "./views/Translation.vue";
import Home from "./views/Home.vue";

const routes = [
  { path: "/", component: Home },
  { path: "/translations/:file", component: Translation },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
});

const app = createApp(App);

app.use(router);

app.mount("#app");
