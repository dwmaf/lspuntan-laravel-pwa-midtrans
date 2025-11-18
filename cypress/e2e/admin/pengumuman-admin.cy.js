describe('Manajemen Pengumuman Sertifikasi', () => {
  const sertificationId = 1; // Asumsikan kita menguji sertifikasi dengan ID 1

  beforeEach(() => {
    cy.loginAsAdmin();
    // URL ini sesuai dengan route('admin.sertifikasi.assessment-announcement.index', $sert_id)
    cy.visit(`/admin/sertifikasi/${sertificationId}/assessment-announcement`);
  });

  it('Harus bisa membuat, mengedit, dan menghapus pengumuman', () => {
    const initialContent = `Pengumuman dibuat oleh Cypress pada ${new Date().toLocaleString()}.`;
    const updatedContent = `Pengumuman ini telah di-update oleh Cypress.`;

    // --- TAHAP 1: MEMBUAT PENGUMUMAN BARU ---
    cy.contains('button', 'Tambah Pengumuman').click();

    // Verifikasi form create muncul
    cy.contains('h2', 'Buat Pengumuman').should('be.visible');

    // Isi form
    cy.get('textarea').type(initialContent);
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy.pdf');

    // Simpan
    cy.contains('button', 'Simpan').click();

    // Verifikasi kembali ke list dan pengumuman baru muncul
    cy.contains('h6', initialContent).should('be.visible');
    cy.contains('a', 'dummy.pdf').should('be.visible');

    // --- TAHAP 2: MENGEDIT PENGUMUMAN ---
    // Cari pengumuman yang baru dibuat, lalu klik tombol Edit
    cy.contains('div.py-3', initialContent).within(() => {
      cy.contains('button', 'Edit').click();
    });

    // Verifikasi form edit muncul
    cy.contains('h2', 'Edit Pengumuman').should('be.visible');

    // Ubah teks
    cy.get('textarea').clear().type(updatedContent);

    // Hapus file lama
    cy.contains('div', 'dummy.pdf').parent().within(() => {
      cy.get('button').click(); // Klik tombol 'X' untuk menghapus
    });

    // Upload file baru
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy.docx');

    // Update
    cy.contains('button', 'Update').click();

    // Verifikasi kembali ke list dan perubahan tersimpan
    cy.contains('h6', initialContent).should('not.exist'); // Konten lama harus hilang
    cy.contains('h6', updatedContent).should('be.visible'); // Konten baru harus muncul
    cy.contains('a', 'dummy.pdf').should('not.exist'); // File lama harus hilang
    cy.contains('a', 'dummy.docx').should('be.visible'); // File baru harus muncul
    cy.contains('span', '(diedit)').should('be.visible'); // Tanda 'diedit' harus muncul

    // --- TAHAP 3: MENGHAPUS PENGUMUMAN ---
    // Stub window.confirm agar otomatis mengklik "OK"
    cy.on('window:confirm', () => true);

    // Cari pengumuman yang sudah diupdate, lalu klik tombol Hapus
    cy.contains('div.py-3', updatedContent).within(() => {
      cy.contains('button', 'Hapus').click();
    });

    // Verifikasi pengumuman tersebut sudah tidak ada lagi di daftar
    cy.contains('h6', updatedContent).should('not.exist');
    cy.contains('Pengumuman berhasil dihapus').should('be.visible');
  });
});