import "./bootstrap";
// import "trix";
// import "trix/dist/trix.css";
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faGauge, faCertificate, faBook, faUser, faChalkboardTeacher, faRightFromBracket, faCalendarDays, faMoneyBill1Wave, faEye, faEyeSlash, faCircleXmark, faXmark, faLock, faTrash, faImage } from '@fortawesome/free-solid-svg-icons'

library.add(faGauge, faCertificate, faBook, faUser, faChalkboardTeacher, faRightFromBracket, faCalendarDays, faMoneyBill1Wave, faEye, faEyeSlash, faCircleXmark, faXmark, faLock, faTrash, faImage)

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from "ziggy-js";
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, props.initialPage.props.ziggy)
            .component('FontAwesomeIcon', FontAwesomeIcon)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});




// import { Editor } from "@tiptap/core";
// import StarterKit from "@tiptap/starter-kit";
// window.initRichEditor = (element, initialValue, onUpdate) => {
//     const editorWrapper = element.querySelector('.rich-text-editor-wrapper');
//     if (!editorWrapper) {
//         console.error('Rich editor wrapper not found inside the component root.');
//         return;
//     }

//     const editorContent = editorWrapper.querySelector('.editor-content');
//     const toolbar = editorWrapper.querySelector('.toolbar');

//     if (!editorContent || !toolbar) {
//         console.error('Editor content or toolbar not found.');
//         return;
//     }

//     const editor = new Editor({
//         element: editorContent,
//         extensions: [StarterKit],
//         content: initialValue,
//         onUpdate: ({ editor }) => {
//             onUpdate(editor.getHTML());
//         }
//     });
//     element.editor = editor;

//     toolbar.querySelectorAll('[data-command]').forEach(btn => {
//         btn.addEventListener('click', () => {
//             const command = btn.dataset.command;
//             if (command && typeof editor.chain().focus()[command] === 'function') {
//                 editor.chain().focus()[command]().run();
//             }
//         });
//     });
// };

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
// import $ from 'jquery';
// window.$ = window.jQuery = $;


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

// Konfigurasi Firebase Anda (ambil dari console Firebase)
const firebaseConfig = {
    apiKey: "AIzaSyBDeSpw3IIkOEPoFAPAfyJBjoLvRwgMaFg",
    authDomain: "snappie-c0775.firebaseapp.com",
    projectId: "snappie-c0775",
    storageBucket: "snappie-c0775.appspot.com",
    messagingSenderId: "66231578373",
    appId: "1:66231578373:web:04712f38005e7e6b45116c",
    measurementId: "G-D85LN9BBGZ"
};

// Inisialisasi Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

function requestPermissionAndGetToken() {
    console.log("Requesting permission for notifications...");
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            // Dapatkan token
            getToken(messaging, { vapidKey: 'BMrTLuoCHunyHVyUW3iA8b-_os4U84ESXBG-NMch2nR6gFRCaiO5xYcbU2p1S_ZFr95JSCXHvCNvNj3YWX8D75k' }) // Ganti dengan VAPID key Anda
                .then((currentToken) => {
                    if (currentToken) {
                        console.log('FCM Token:', currentToken);
                        sendTokenToServer(currentToken);
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                }).catch((err) => {
                    console.log('An error occurred while retrieving token. ', err);
                });
        } else {
            console.log('Unable to get permission to notify.');
        }
    });
}

function sendTokenToServer(token) {
    fetch("/fcm/token", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({ token: token }),
    })
    .then(response => response.json())
    .then(data => console.log("Token saved to server:", data))
    .catch(error => console.error("Error sending token to server:", error));
}

document.addEventListener('DOMContentLoaded', () => {
    const enableNotifBtn = document.getElementById('enable-notifications-btn');
    if (enableNotifBtn) {
        enableNotifBtn.addEventListener('click', () => {
            if (Notification.permission === 'default') {
                requestPermissionAndGetToken();
            }
        });
    }
});

// Handle notifikasi saat aplikasi di foreground
onMessage(messaging, (payload) => {
    console.log('Message received while app is in foreground: ', payload);
    // Tampilkan notifikasi custom di sini, misalnya menggunakan Toastr atau alert
    alert(`New Notification: ${payload.notification.title}`);
});


