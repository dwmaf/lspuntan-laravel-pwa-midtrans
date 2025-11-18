describe('Pengumpulan Tugas Asesmen oleh Asesi', () => {
  const sertificationId = 1;
  const asesiId = 1; // Asumsikan ID asesi yang login adalah 1

  beforeEach(() => {
    // Login sebagai asesi
    cy.loginAsAsesi();
    // Kunjungi halaman asesmen
    cy.visit(`/asesi/sertifikasi/${sertificationId}/asesmen/${asesiId}`);
  });

  it('Harus bisa mengumpulkan file tugas untuk pertama kali', () => {
    // Asumsi: Asesi ini belum pernah mengumpulkan file, jadi halaman langsung dalam mode form.
    
    // 1. Verifikasi kita berada di mode pengumpulan
    cy.contains('h4', 'Kumpulkan Tugas Anda').should('be.visible');

    // 2. Unggah satu atau lebih file
    cy.get('input[type="file"]').selectFile([
      'cypress/fixtures/dummy.pdf',
      'cypress/fixtures/dummy.docx'
    ]);

    // 3. Klik tombol "Kumpulkan"
    cy.contains('button', 'Kumpulkan').click();

    // 4. Verifikasi
    // a. Pesan sukses muncul
    cy.contains('Berhasil unggah file asesmen.').should('be.visible');
    // b. Halaman beralih ke mode tampilan (form hilang)
    cy.contains('h4', 'Kumpulkan Tugas Anda').should('not.exist');
    // c. File yang baru diunggah sekarang ditampilkan
    cy.contains('h4', 'Tugas Terkumpul:').should('be.visible');
    cy.contains('a', 'dummy.pdf').should('be.visible');
    cy.contains('a', 'dummy.docx').should('be.visible');
  });

  it('Harus bisa mengedit file yang sudah dikumpulkan (menghapus dan menambah)', () => {
    // Asumsi: Asesi ini sudah pernah mengumpulkan file, jadi halaman dalam mode tampilan.

    // 1. Klik tombol untuk masuk mode edit
    cy.contains('button', 'Batalkan Pengiriman').click(); // Tombol ini berfungsi sebagai "Edit"

    // 2. Verifikasi kita masuk ke mode form
    cy.contains('h4', 'Kumpulkan Tugas Anda').should('be.visible');

    // 3. Hapus salah satu file yang ada
    // Asumsikan ada file 'dummy.pdf' yang sudah ada sebelumnya
    cy.contains('div', 'dummy.pdf').parent().within(() => {
      cy.get('button').click(); // Klik tombol 'X' untuk menghapus
    });

    // 4. Unggah file baru
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');

    // 5. Klik tombol "Kumpulkan"
    cy.contains('button', 'Kumpulkan').click();

    // 6. Verifikasi
    // a. Pesan sukses muncul
    cy.contains('Berhasil unggah file asesmen.').should('be.visible');
    // b. File yang dihapus sudah tidak ada
    cy.contains('a', 'dummy.pdf').should('not.exist');
    // c. File yang baru diunggah muncul
    cy.contains('a', 'dummy.jpg').should('be.visible');
  });
});