import "./bootstrap";
// import "trix";
// import "trix/dist/trix.css";
import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";

// import $ from 'jquery';
// window.$ = window.jQuery = $;

// import 'summernote/dist/summernote-lite.min.js';
// import 'summernote/dist/summernote-lite.min.css';

// import Quill from 'quill';
// import 'quill/dist/quill.snow.css'; // Tema Snow (populer)
// window.Quill = Quill;

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

import Alpine from "alpinejs";
window.Alpine = Alpine;

// Jalankan skrip setelah seluruh halaman dimuat
document.addEventListener("DOMContentLoaded", () => {
    // Cari semua container editor di halaman (bisa lebih dari satu)
    const editorWrappers = document.querySelectorAll(
        ".rich-text-editor-wrapper"
    );
    // Loop setiap editor yang ditemukan
    editorWrappers.forEach((wrapper) => {
        // Ambil elemen area editor (tempat TipTap akan di-mount)
        const editorContentEl = wrapper.querySelector(".editor-content");
        // Ambil input hidden untuk menyimpan HTML hasil edit
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        // Ambil toolbar (tempat tombol-tombol editor)
        const toolbar = wrapper.querySelector(".toolbar");
        // Ambil semua tombol di toolbar
        const buttons = toolbar.querySelectorAll("button");
        // Jika area editor atau input hidden tidak ditemukan, skip editor ini
        if (!editorContentEl || !hiddenInput) return;
        // Inisialisasi TipTap Editor
        const editor = new Editor({
            element: editorContentEl, // Mount editor ke element ini
            extensions: [
                StarterKit.configure({
                    bulletList: false,
                    orderedList: false
                }), // Aktifkan fitur dasar (bold, italic, underline, list)
            ],
            editorProps: {
                attributes: {
                    class: " focus:outline-none", // Hilangkan outline saat fokus, soalnya bakal muncul sendiri nanti dia
                },
            },
            // Isi awal editor diambil dari value input hidden
            content: hiddenInput.value,

            // Callback: setiap kali isi editor berubah (misal: user mengetik), perbarui nilai input hidden, ini yg bakal dikirim ke server
            onUpdate: ({ editor }) => {
                // Simpan HTML terbaru ke input hidden (agar bisa dikirim ke server)
                hiddenInput.value = editor.getHTML();
            },

            // Setiap kali ada transaksi (ketik, klik, dll), update tampilan tombol
            onTransaction: () => {
                // Mapping dari nama command tombol ke nama extension TipTap
                const commandMap = {
                    toggleBold: "bold",
                    toggleItalic: "italic",
                    toggleUnderline: "underline",
                    // toggleBulletList: "bulletList",
                };
                // Loop semua tombol di toolbar
                buttons.forEach((button) => {
                    const command = button.dataset.command;
                    // Untuk command 'unsetAllMarks', tidak ada state aktif
                    if (command === "unsetAllMarks") return;

                    // Cek apakah command ini aktif di editor
                    const tiptapName = commandMap[command];
                    if (tiptapName && editor.isActive(tiptapName)) {
                        console.log("triggered");
                        console.log(command);

                        button.classList.add("bg-gray-200", "dark:bg-gray-700");
                    } else {
                        button.classList.remove(
                            "bg-gray-200",
                            "dark:bg-gray-700"
                        );
                    }
                });
            },
        });

        // Tambahkan event listener untuk setiap tombol di toolbar
        buttons.forEach((button) => {
            button.addEventListener("click", () => {
                const command = button.dataset.command; // Ambil nama command
                // Jalankan command TipTap sesuai tombol yang diklik (misal: toggleBold)
                editor.chain().focus()[command]().run();
            });
        });
    });
});

Alpine.start();
