describe('Halaman Login', () => {

  // Blok 'beforeEach' akan berjalan sebelum setiap tes ('it') di dalam 'describe' ini.
  // Ini bagus untuk tugas berulang seperti mengunjungi halaman.
  beforeEach(() => {
    // 1. Kunjungi halaman login.
    // Cypress secara otomatis akan menggunakan `baseUrl` dari cypress.config.js
    cy.visit('/login');
  });

  it('Harus berhasil login sebagai asesi dengan kredensial yang benar', () => {
    // Ganti dengan email dan password user 'asesi' yang ada di database Anda
    const userEmail = 'nuraini.jagaraga@example.net';
    const userPassword = '1234';

    // 2. Cari elemen input email berdasarkan ID-nya (#email) dan ketik email.
    // ID 'email' kita dapatkan dari <TextInput id="email" ...> di Login.vue
    cy.get('#email').type(userEmail);

    // 3. Cari elemen input password berdasarkan ID-nya (#password) dan ketik password.
    // ID 'password' kita dapatkan dari <input id="password" ...> di Login.vue
    cy.get('#password').type(userPassword);

    // 4. Cari tombol submit dan klik.
    // Menggunakan selector 'button[type="submit"]' adalah cara yang andal.
    cy.contains('button','Log in').click();

    // 5. Verifikasi hasilnya.
    // Setelah login berhasil, URL harus mengandung '/asesi/dashboard'.
    // Ini sesuai dengan logika di AuthenticatedSessionController.php
    cy.url().should('include', '/admin/dashboard');

    // Sebagai verifikasi tambahan, pastikan ada teks 'Dashboard' yang terlihat di halaman baru.
    cy.contains('Dashboard').should('be.visible');
  });

  it('Harus menampilkan pesan error dengan kredensial yang salah', () => {
    // 2. Gunakan email atau password yang sengaja disalahkan.
    cy.get('#email').type('email-salah@example.com');
    cy.get('#password').type('password-yang-salah.');

    // 3. Klik tombol submit.
    cy.contains('button','Log in').click();

    // 4. Verifikasi hasilnya.
    // Pastikan kita TIDAK pindah halaman. URL harus tetap mengandung '/login'.
    cy.url().should('include', '/login');

    // Cari pesan error yang ditampilkan oleh Laravel.
    // Pesan ini biasanya ada di dalam komponen <InputError>
    cy.contains('Identitas tersebut tidak cocok dengan data kami.').should('be.visible');
  });

});