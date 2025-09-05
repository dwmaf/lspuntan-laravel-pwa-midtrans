// Import the functions you need from the SDKs you need
importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js");

// Konfigurasi Firebase Anda (sama seperti sebelumnya)
const firebaseConfig = {
  apiKey: "AIzaSyBDeSpw3IIkOEPoFAPAfyJBjoLvRwgMaFg",
  authDomain: "snappie-c0775.firebaseapp.com",
  projectId: "snappie-c0775",
  storageBucket: "snappie-c0775.appspot.com",
  messagingSenderId: "66231578373",
  appId: "1:66231578373:web:04712f38005e7e6b45116c",
  measurementId: "G-D85LN9BBGZ"
};

// Inisialisasi Firebase menggunakan versi compat
firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// Handle notifikasi saat aplikasi di background
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/logo-lsp.png' // Ganti dengan path logo Anda
    };

    // Tampilkan notifikasi ke pengguna
    self.registration.showNotification(notificationTitle, notificationOptions);
});