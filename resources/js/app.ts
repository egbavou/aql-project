import './bootstrap';
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import App from '@/App.vue'
import router from '@/router'
import vuetify from '@/plugins/vuetify'
import '@/assets/css/styles.css'
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';
import * as Sentry from "@sentry/vue";

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const app = createApp(App)
Sentry.init({
    app,
    dsn: "https://2a28a6c183919109af75d1c6079623bf@o4508986450640896.ingest.de.sentry.io/4508986996359248"
});
app.use(ToastPlugin);
app.use(pinia)
app.use(router)
app.use(vuetify)
app.mount('#app')
