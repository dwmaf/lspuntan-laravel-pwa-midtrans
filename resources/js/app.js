import './bootstrap';
import "trix";
import "trix/dist/trix.css";

// import $ from 'jquery';
// window.$ = window.jQuery = $;

// import 'summernote/dist/summernote-lite.min.js';
// import 'summernote/dist/summernote-lite.min.css';

import Quill from 'quill';
import 'quill/dist/quill.snow.css'; // Tema Snow (populer)
window.Quill = Quill;

import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.start();
