import { createApp } from "vue";
import routes from "./routes/index";
import { base, api } from "@/boot/axios";
import { Quasar, LocalStorage } from "quasar";
import { createRouter, createWebHistory } from "vue-router";

// Import icon libraries
// import quasarLang from "quasar/lang/es";
import "@quasar/extras/roboto-font/roboto-font.css";
import "@quasar/extras/material-icons/material-icons.css";
import "@quasar/extras/mdi-v6/mdi-v6.css";
import "@quasar/extras/fontawesome-v6/fontawesome-v6.css";

// A few examples for animations from Animate.css:
// import @quasar/extras/animate/fadeIn.css
// import @quasar/extras/animate/fadeOut.css

// Import Quasar css
import "quasar/src/css/index.sass";

/* Create App */
import App from "./App.vue";
const app = createApp(App);

/* Quasar Plugin Config */
app.use(Quasar, {
    plugins: {}, // import Quasar plugins and add here
    // lang: quasarLang,
    /*
  config: {
    brand: {
      // primary: '#e46262',
      // ... or all other brand colors
    },
    notify: {...}, // default set of options for Notify Quasar plugin
    loading: {...}, // default set of options for Loading Quasar plugin
    loadingBar: { ... }, // settings for LoadingBar Quasar plugin
    // ..and many more (check Installation card on each Quasar component/directive/plugin)
  }
  */
});

/* Vue Router Config */
const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    mode: "history", // quasar.conf.js -> build -> publicPath
    history: createWebHistory(),
    hashbang: false,
});

const isLoggedIn = async () => {
    let { data } = await api.get("auth/user");
    // console.log(data ? data : "vacio");
    if (data) {
        LocalStorage.set("user", data);
        return true;
    } else {
        LocalStorage.remove("user");
        return false;
    }
};

Router.beforeEach(async (to, from, next) => {
    let isLogged = await isLoggedIn();
    if (isLogged) {
        if (to.matched.some((record) => record.meta.guest)) {
            next({ name: "Dashboard" });
        } else next();
    } else {
        if (to.matched.some((record) => record.meta.auth)) {
            next({ name: "login", query: { redirect: to.fullPath } });
        } else next();
    }
});

app.use(Router);

/* Mount App */
app.mount("#app");
