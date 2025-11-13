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




// import { Editor } from "@tiptap/core";
// import StarterKit from "@tiptap/starter-kit";

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

// Import FilePond
// import * as FilePond from 'filepond';
// import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

// Import FilePond styles
// import 'filepond/dist/filepond.min.css';

// Register the plugins
// FilePond.registerPlugin(
//   FilePondPluginFileValidateType,
// );

// Make FilePond globally available (opsional, tapi bisa berguna untuk akses dari script di Blade)
// window.FilePond = FilePond;


// Jalankan skrip setelah seluruh halaman dimuat
// document.addEventListener("DOMContentLoaded", () => {
//     // Cari semua container editor di halaman (bisa lebih dari satu)
//     const editorWrappers = document.querySelectorAll(
//         ".rich-text-editor-wrapper"
//     );
//     // Loop setiap editor yang ditemukan
//     editorWrappers.forEach((wrapper) => {
//         // Ambil elemen area editor (tempat TipTap akan di-mount)
//         const editorContentEl = wrapper.querySelector(".editor-content");
//         // Ambil input hidden untuk menyimpan HTML hasil edit
//         const hiddenInput = wrapper.querySelector('input[type="hidden"]');
//         // Ambil toolbar (tempat tombol-tombol editor)
//         const toolbar = wrapper.querySelector(".toolbar");
//         // Ambil semua tombol di toolbar
//         const buttons = toolbar.querySelectorAll("button");
//         // Jika area editor atau input hidden tidak ditemukan, skip editor ini
//         if (!editorContentEl || !hiddenInput) return;
//         // Inisialisasi TipTap Editor
//         const editor = new Editor({
//             element: editorContentEl, // Mount editor ke element ini
//             extensions: [
//                 StarterKit.configure({
//                     bulletList: false,
//                     orderedList: false
//                 }), // Aktifkan fitur dasar (bold, italic, underline, list)
//             ],
//             editorProps: {
//                 attributes: {
//                     class: " focus:outline-none", // Hilangkan outline saat fokus, soalnya bakal muncul sendiri nanti dia
//                 },
//             },
//             // Isi awal editor diambil dari value input hidden
//             content: hiddenInput.value,

//             // Callback: setiap kali isi editor berubah (misal: user mengetik), perbarui nilai input hidden, ini yg bakal dikirim ke server
//             onUpdate: ({ editor }) => {
//                 // Simpan HTML terbaru ke input hidden (agar bisa dikirim ke server)
//                 hiddenInput.value = editor.getHTML();
//             },

//             // Setiap kali ada transaksi (ketik, klik, dll), update tampilan tombol
//             onTransaction: () => {
//                 // Mapping dari nama command tombol ke nama extension TipTap
//                 const commandMap = {
//                     toggleBold: "bold",
//                     toggleItalic: "italic",
//                     toggleUnderline: "underline",
//                     // toggleBulletList: "bulletList",
//                 };
//                 // Loop semua tombol di toolbar
//                 buttons.forEach((button) => {
//                     const command = button.dataset.command;
//                     // Untuk command 'unsetAllMarks', tidak ada state aktif
//                     if (command === "unsetAllMarks") return;

//                     // Cek apakah command ini aktif di editor
//                     const tiptapName = commandMap[command];
//                     if (tiptapName && editor.isActive(tiptapName)) {
//                         console.log("triggered");
//                         console.log(command);

//                         button.classList.add("bg-gray-200", "dark:bg-gray-700");
//                     } else {
//                         button.classList.remove(
//                             "bg-gray-200",
//                             "dark:bg-gray-700"
//                         );
//                     }
//                 });
//             },
//         });

//         // Tambahkan event listener untuk setiap tombol di toolbar
//         buttons.forEach((button) => {
//             button.addEventListener("click", () => {
//                 const command = button.dataset.command; // Ambil nama command
//                 // Jalankan command TipTap sesuai tombol yang diklik (misal: toggleBold)
//                 editor.chain().focus()[command]().run();
//             });
//         });
//     });
// });

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