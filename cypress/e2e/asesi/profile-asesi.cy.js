describe('Halaman Profil Asesi', () => {
  beforeEach(() => {
    cy.loginAsAsesi();
    cy.visit('/profile_asesi'); // Sesuai dengan route('profile_asesi.edit')
  });

  context('Update Profile Data', () => {
    it('Harus bisa mengedit data pribadi, mengelola file, dan menyimpan perubahan', () => {
      const newKebangsaan = `WNI-${Date.now()}`;

      // 1. Masuk ke mode edit
      cy.contains('button', 'Edit Data').click();
      cy.contains('h2', 'Data Pribadi').should('be.visible');

      // 2. Ubah data teks
      cy.contains('label', 'Kebangsaan').parent().find('input').clear().type(newKebangsaan);

      // 3. Hapus file yang sudah ada (misalnya KTP)
      cy.contains('label', 'Foto KTP').parent().within(() => {
        cy.get('button[aria-label="Remove file"]').click();
      });

      // 4. Unggah file baru (misalnya Pas Foto)
      cy.contains('label', 'Pasfoto terbaru').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.png');

      // 5. Simpan perubahan
      cy.contains('form', 'Nama Lengkap Sesuai KTP').contains('button', 'Save').click();

      // 6. Verifikasi
      // a. Kembali ke mode tampilan
      cy.contains('h2', 'Data Pribadi').should('be.visible');
      cy.contains('button', 'Edit Data').should('be.visible');
      // b. Pesan sukses muncul
      cy.contains('Profil berhasil diperbarui').should('be.visible');
      // c. Data teks terupdate
      cy.contains('dt', 'Kebangsaan').next('dd').should('contain.text', newKebangsaan);
      // d. File yang diunggah baru muncul
      cy.contains('a', 'dummy.png').should('be.visible');
    });
  });

  context('Update Password', () => {
    it('Harus bisa memperbarui kata sandi dengan kredensial yang benar', () => {
      // Ganti 'password' dengan kata sandi asli dari user asesi Anda
      const currentPassword = 'password';
      const newPassword = `newPassword${Date.now()}`;

      cy.get('form').contains('label', 'Current Password').parent().find('input').type(currentPassword);
      cy.get('form').contains('label', 'New Password').parent().find('input').type(newPassword);
      cy.get('form').contains('label', 'Confirm Password').parent().find('input').type(newPassword);

      cy.get('form').contains('button', 'Save').click();

      // Verifikasi pesan sukses muncul
      cy.contains('p', 'Saved.').should('be.visible');
    });

    it('Harus menampilkan error jika kata sandi saat ini salah', () => {
      cy.get('form').contains('label', 'Current Password').parent().find('input').type('password-salah');
      cy.get('form').contains('label', 'New Password').parent().find('input').type('password-baru');
      cy.get('form').contains('label', 'Confirm Password').parent().find('input').type('password-baru');

      cy.get('form').contains('button', 'Save').click();

      // Verifikasi pesan error muncul
      cy.contains('The provided password does not match your current password.').should('be.visible');
    });
  });

  context('Notification Permission', () => {
    it('Harus bisa mengaktifkan dan menonaktifkan notifikasi', () => {
      // Intercept request untuk mencegah panggilan nyata ke Firebase
      cy.intercept('POST', '/fcm/token').as('saveToken');
      cy.intercept('DELETE', '/fcm/token').as('removeToken');

      // Cari tombol di dalam komponen FCMPermission
      cy.get('div').contains('h3', 'Notifikasi').parent().within(() => {
        // Klik tombol untuk mengubah state
        cy.get('button').click();

        // Cek state setelah klik pertama (misalnya, menjadi 'Aktifkan')
        cy.contains('button', 'Aktifkan Notifikasi').should('be.visible');

        // Klik lagi untuk mengembalikannya
        cy.get('button').click();
        cy.contains('button', 'Nonaktifkan Notifikasi').should('be.visible');
      });
    });
  });
});