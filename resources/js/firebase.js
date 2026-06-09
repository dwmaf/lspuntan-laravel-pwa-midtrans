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

window.sendTokenToServer = function (token) {
    return axios.post('/fcm/token', {
        token: token
    })
        .then(response => {
            console.log("Token saved to server:", response.data);
            return response;
        })
        .catch(error => {
            console.error("Error sending token to server:", error);
            if (error.response) {
                console.error("Server Response:", error.response.data);
            }
            throw error;
        });
}


onMessage(messaging, (payload) => {
    console.log('Message received while app is in foreground: ', payload);
});