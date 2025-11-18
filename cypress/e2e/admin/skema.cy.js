describe('Manajemen Skema Sertifikasi', () => {
  // Sebelum setiap tes, login sebagai admin dan kunjungi halaman skema
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/skema');
  });

  it('Harus bisa membuat skema baru, mengeditnya, lalu menghapusnya', () => {
    // --- TAHAP 1: MEMBUAT SKEMA BARU ---
    const skemaName = `Skema Uji Cypress ${Date.now()}`;
    const updatedSkemaName = `${skemaName} (Updated)`;

    // Klik tombol "Tambah Skema"
    cy.contains('button', 'Tambah Skema').click();

    // Pastikan form tambah muncul
    cy.contains('h2', 'Tambah Skema Sertifikasi').should('be.visible');

    // Isi nama skema
    cy.get('input[type="text"]').type(skemaName);

    // Upload file APL.01
    // 'dummy.pdf' harus ada di folder cypress/fixtures
    cy.get('input[type="file"]').first().selectFile('cypress/fixtures/dummy.pdf');

    // Klik Simpan
    cy.contains('button', 'Simpan').click();

    // Verifikasi: Pastikan kembali ke halaman daftar dan skema baru muncul
    cy.contains('h2', 'Daftar Skema').should('be.visible');
    cy.contains('td', skemaName).should('be.visible');
    cy.contains('a', 'APL.01').should('be.visible'); // Pastikan link file ada

    // --- TAHAP 2: MENGEDIT SKEMA ---

    // Cari baris yang berisi skema baru kita, lalu cari tombol Edit di dalamnya dan klik
    cy.contains('tr', skemaName).within(() => {
      cy.contains('button', 'Edit').click();
    });

    // Pastikan form edit muncul
    cy.contains('h2', 'Edit Skema Sertifikasi').should('be.visible');

    // Hapus nama lama dan ketik nama baru
    cy.get('input[type="text"]').clear().type(updatedSkemaName);

    // Tandai file APL.01 untuk dihapus
    // Cari tombol hapus di dalam komponen file input pertama
    cy.contains('div', 'APL.01').within(() => {
        cy.get('button').click();
    });

    // Upload file APL.02 yang baru
    cy.get('input[type="file"]').last().selectFile('cypress/fixtures/dummy.docx');

    // Klik Simpan
    cy.contains('button', 'Simpan').click();

    // Verifikasi: Pastikan nama sudah terupdate dan file sudah berubah
    cy.contains('td', skemaName).should('not.exist'); // Nama lama harus hilang
    cy.contains('td', updatedSkemaName).should('be.visible'); // Nama baru harus muncul

    // Verifikasi file: APL.01 harus hilang, APL.02 harus ada
    cy.contains('tr', updatedSkemaName).within(() => {
      cy.contains('APL.01 belum ada').should('be.visible');
      cy.contains('a', 'APL.02').should('be.visible');
    });

    // --- TAHAP 3: MENGHAPUS SKEMA ---

    // Stub window.confirm agar otomatis mengklik "OK"
    cy.on('window:confirm', () => true);

    // Cari baris skema yang sudah diupdate, lalu klik tombol Hapus
    cy.contains('tr', updatedSkemaName).within(() => {
      cy.contains('button', 'Hapus').click();
    });

    // Verifikasi: Pastikan skema tersebut sudah tidak ada lagi di tabel
    cy.contains('td', updatedSkemaName).should('not.exist');
    cy.contains('Skema berhasil dihapus').should('be.visible'); // Jika Anda menampilkan notifikasi
  });
});