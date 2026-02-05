import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from "ziggy-js";
const appName = import.meta.env.VITE_APP_NAME || 'LSP UNTAN';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, props.initialPage.props.ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});



if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/serviceworker.js').then(function(registration) {
            console.log('Service Worker registered with scope:', registration.scope);
        }, function(err) {
            console.log('Service Worker registration failed:', err);
        });
    });
}

import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import axios from 'axios';

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FCM_API_KEY,
    authDomain: import.meta.env.VITE_FCM_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_FCM_PROJECT_ID,
    storageBucket: import.meta.env.VITE_FCM_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_FCM_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_FCM_APP_ID,
    measurementId: import.meta.env.VITE_FCM_MEASUREMENT_ID
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

window.getToken = getToken;
window.messaging = messaging;

window.sendTokenToServer = function(token) {
    axios.post('/fcm/token', {
        token: token
    })
    .then(response => console.log("Token saved to server:", response.data))
    .catch(error => {
        console.error("Error sending token to server:", error);
        if (error.response) {
            console.error("Server Response:", error.response.data);
        }
    });
}


onMessage(messaging, (payload) => {
    console.log('Message received while app is in foreground: ', payload);
});