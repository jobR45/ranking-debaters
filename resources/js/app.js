import { createApp } from "vue";
import App from "./App.vue";
import router from "./router.js";
import "../css/app.css";

import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import PrimeVue from "primevue/config";
import { definePreset } from '@primeuix/themes';
import Aura from "@primeuix/themes/aura";
import ConfirmationService from "primevue/confirmationservice";

const RankingTheme = definePreset(Aura, {
    semantic: {
        primary: {
            50: '#fffbea',
            100: '#fff3c4',
            200: '#ffe588',
            300: '#ffd24d',
            400: '#eeb82b',
            500: '#c99a16',
            600: '#a77a0b',
            700: '#805b0a',
            800: '#69480f',
            900: '#583b12',
            950: '#332006'
        },
        colorScheme: {
            dark: {
                primary: {
                    color: '{primary.500}',
                    inverseColor: '{surface.950}',
                    hoverColor: '{primary.400}',
                    activeColor: '{primary.600}'
                },
                highlight: {
                    background: 'rgba(201, 154, 22, 0.16)',
                    focusBackground: 'rgba(201, 154, 22, 0.24)',
                    color: '{primary.400}',
                    focusColor: '{primary.300}'
                },
                surface: {
                    0: '#f5f5f5',
                    50: '#f5f5f5',
                    100: '#e7e7e7',
                    200: '#c8c8c8',
                    300: '#a0a0a0',
                    400: '#858585',
                    500: '#6b6b6b',
                    600: '#525252',
                    700: '#333333',
                    800: '#292929',
                    900: '#1c1c1c',
                    950: '#111111'
                }
            }
        }
    }
});


const app = createApp(App);
app.use(router);
app.use(PrimeVue, {
    theme: {
        license: "eyJpZCI6ImE1M2IyODk1LWMyMmMtNDdiYS1iOWMxLWVjYjUxMTNiMTJkYyIsInByb2R1Y3QiOiJwcmltZXVpIiwidGllciI6ImNvbW11bml0eSIsInR5cGUiOiJkZXYiLCJpYXQiOjE3OTI1NTM4MTAsImV4cCI6MTgyNDA4OTgxMH0.CslABfJrv6tLST1O0pi0mkPE60iPCja6zx0u-CSixk48DdHLKqsDivxe6bnmkKT6c98C70SW5o-aBOx-2UptCg",
        preset: RankingTheme,
        options: {
            darkModeSelector: '.app-dark'
        }
    },
});
app.use(ConfirmationService);
app.use(Vue3Toastify, {
    autoClose: 3000,
    position: "bottom-right",
    rtl: true,
});
app.mount("#app");
