describe('Halaman Profil Asesi', () => {
  beforeEach(() => {
    cy.loginAsAsesi();
    cy.visit('/profile_asesi');
  });

  context('Update Profile Data', () => {
    it('Harus bisa mengedit data pribadi, mengelola file, dan menyimpan perubahan', () => {
      const newKebangsaan = `WNI-${Date.now()}`;

      
      cy.contains('button', 'Edit Data').click();
      cy.contains('h2', 'Data Pribadi').should('be.visible');

      
      cy.contains('label', 'Kebangsaan').parent().find('input').clear().type(newKebangsaan);

      
      cy.contains('label', 'Foto KTP').parent().within(() => {
        cy.get('button[aria-label="Remove file"]').click();
      });

      
      cy.contains('label', 'Pasfoto terbaru').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.png');

      
      cy.contains('form', 'Nama Lengkap Sesuai KTP').contains('button', 'Save').click();

      
      cy.contains('h2', 'Data Pribadi').should('be.visible');
      cy.contains('button', 'Edit Data').should('be.visible');
      
      cy.contains('Profil berhasil diperbarui').should('be.visible');
      
      cy.contains('dt', 'Kebangsaan').next('dd').should('contain.text', newKebangsaan);
      
      cy.contains('a', 'dummy.png').should('be.visible');
    });
  });

  context('Update Password', () => {
    it('Harus bisa memperbarui kata sandi dengan kredensial yang benar', () => {
      
      const currentPassword = 'password';
      const newPassword = `newPassword${Date.now()}`;

      cy.get('form').contains('label', 'Current Password').parent().find('input').type(currentPassword);
      cy.get('form').contains('label', 'New Password').parent().find('input').type(newPassword);
      cy.get('form').contains('label', 'Confirm Password').parent().find('input').type(newPassword);

      cy.get('form').contains('button', 'Save').click();


      cy.contains('p', 'Saved.').should('be.visible');
    });

    it('Harus menampilkan error jika kata sandi saat ini salah', () => {
      cy.get('form').contains('label', 'Current Password').parent().find('input').type('password-salah');
      cy.get('form').contains('label', 'New Password').parent().find('input').type('password-baru');
      cy.get('form').contains('label', 'Confirm Password').parent().find('input').type('password-baru');

      cy.get('form').contains('button', 'Save').click();


      cy.contains('The provided password does not match your current password.').should('be.visible');
    });
  });

  context('Notification Permission', () => {
    it('Harus bisa mengaktifkan dan menonaktifkan notifikasi', () => {
      
      cy.intercept('POST', '/fcm/token').as('saveToken');
      cy.intercept('DELETE', '/fcm/token').as('removeToken');

      
      cy.get('div').contains('h3', 'Notifikasi').parent().within(() => {
        
        cy.get('button').click();

        
        cy.contains('button', 'Aktifkan Notifikasi').should('be.visible');

        
        cy.get('button').click();
        cy.contains('button', 'Nonaktifkan Notifikasi').should('be.visible');
      });
    });
  });
});