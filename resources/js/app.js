import './bootstrap';
import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import ConfirmationService from 'primevue/confirmationservice';

import App from './App.vue'; 

if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.classList.add('dark');
}

const app = createApp(App);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            // Isso diz ao PrimeVue: "Ative o modo escuro quando a tag HTML tiver a classe 'dark'"
            darkModeSelector: '.dark',
        }
    }
});
app.use(ConfirmationService);
app.mount('#app');