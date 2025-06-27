import './bootstrap';
import "trix";
import "trix/dist/trix.css";

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

import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.start();
