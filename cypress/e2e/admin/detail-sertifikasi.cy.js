describe('Detail dan Edit Sertifikasi', () => {
  const sertificationId = 1; // Asumsikan kita menguji sertifikasi dengan ID 1

  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit(`/admin/kelolasertifikasi/${sertificationId}/show`);
  });

  it('Harus bisa mengedit detail sertifikasi dan kemudian menghapusnya', () => {
    // --- TAHAP 1: MASUK MODE EDIT & VERIFIKASI FORM ---
    cy.contains('button', 'Edit').click();
    cy.contains('h2', 'Edit Sertifikasi').should('be.visible');

    // --- TAHAP 2: EDIT DATA DI FORM ---
    const newTuk = `TUK Cypress Test ${Date.now()}`;
    const newDate = '2029-10-10';

    // 1. Ubah Skema (pilih opsi kedua yang tersedia)
    cy.get('select').first().select(1); // .select(1) memilih opsi dengan index 1

    // 2. Interaksi dengan vue-multiselect untuk Asesor
    // Hapus pilihan asesor yang ada (jika ada)
    cy.get('.multiselect__tags').within(() => {
      cy.get('.multiselect__tag-icon').each(($icon) => {
        cy.wrap($icon).click();
      });
    });
    // Buka dropdown dan pilih asesor pertama yang tersedia
    cy.get('.multiselect__tags').click();
    cy.get('.multiselect__content-wrapper').find('li').first().click();

    // 3. Ubah Tanggal Daftar Ditutup
    cy.get('#tanggal_apply_ditutup').find('input').clear().type(newDate);

    // 4. Ubah TUK
    cy.get('#tuk').find('input').clear().type(newTuk);

    // 5. Klik tombol Update
    cy.contains('button', 'Update').click();

    // --- TAHAP 3: VERIFIKASI PERUBAHAN ---
    // Pastikan kembali ke mode tampilan
    cy.contains('h2', 'Edit Sertifikasi').should('not.exist');
    cy.contains('h3', 'Detail Sertifikasi').should('be.visible');

    // Verifikasi data yang baru diubah
    cy.contains('dt', 'TUK').next('dd').should('contain.text', newTuk);
    cy.contains('dt', 'Tanggal Pendaftaran Ditutup').next('dd').should('contain.text', '10 Oktober 2029');

    // --- TAHAP 4: HAPUS SERTIFIKASI ---
    // Stub window.confirm agar otomatis mengklik "OK"
    cy.on('window:confirm', () => true);

    // Klik tombol Hapus
    cy.contains('button', 'Hapus').click();

    // Verifikasi: Harus diarahkan kembali ke halaman daftar sertifikasi
    cy.url().should('include', '/admin/kelolasertifikasi');
    cy.contains('Sertifikasi berhasil dihapus').should('be.visible');
  });
});