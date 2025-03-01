import './bootstrap';
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import App from '@/App.vue'
import router from '@/router'
import vuetify from '@/plugins/vuetify'
import '@/assets/css/styles.css'
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const app = createApp(App)

app.use(ToastPlugin);
app.use(pinia)
app.use(router)
app.use(vuetify)

app.mount('#app')
