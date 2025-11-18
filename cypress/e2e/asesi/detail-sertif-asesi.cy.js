describe('Detail Sertifikasi Saya (Asesi)', () => {
  const sertificationId = 1;
  const asesiId = 1; // Asumsikan ID asesi yang login adalah 1

  beforeEach(() => {
    cy.loginAsAsesi();
    cy.visit(`/asesi/sertifikasi/applied/${sertificationId}/${asesiId}`);
  });

  it('Harus bisa masuk mode edit, mengubah data, dan menyimpan perubahan', () => {
    const newKebangsaan = `WNI-${Date.now()}`;

    // 1. Masuk ke mode edit
    cy.contains('button', 'Edit Data').click();
    cy.contains('h3', 'A. Data Pribadi').should('be.visible');

    // 2. Ubah data sederhana (input teks)
    cy.contains('label', 'Kebangsaan').parent().find('input').clear().type(newKebangsaan);

    // 3. Tambah mata kuliah baru
    cy.contains('button', 'Tambah Mata Kuliah').click();
    cy.get('div[class*="items-center gap-3"]').last().within(() => {
      cy.get('input[placeholder*="Struktur Data"]').type('Jaringan Komputer');
      cy.get('input[placeholder*="e.g., A"]').type('B+');
    });

    // 4. Hapus file yang sudah ada (dari MultiFileInput)
    // Asumsi ada file KHS yang sudah diupload sebelumnya
    cy.contains('label', 'Scan Kartu Hasil Studi').parent().within(() => {
        cy.get('button[aria-label="Remove file"]').first().click();
    });

    // 5. Upload file baru (ke SingleFileInput)
    cy.contains('label', 'Scan KTP').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');

    // 6. Simpan perubahan
    cy.contains('button', 'Update').click();

    // 7. Verifikasi
    // a. Kembali ke mode tampilan
    cy.contains('h3', 'A. Data Pribadi').should('not.exist');
    // b. Pesan sukses muncul
    cy.contains('Berhasil update data sertifikasi').should('be.visible');
    // c. Verifikasi data teks yang diubah
    cy.contains('dt', 'Kebangsaan').next('dd').should('contain.text', newKebangsaan);
  });

  it('Harus menampilkan notifikasi perbaikan dan mengubah status setelah update', () => {
    // Asumsi: Status asesi ini di database adalah 'perlu_perbaikan_berkas'
    
    // 1. Verifikasi notifikasi perbaikan muncul
    cy.contains('span', 'Perhatian!').should('be.visible');
    cy.contains('Admin meminta perbaikan berkas').should('be.visible');

    // 2. Masuk mode edit dan lakukan perubahan (misalnya upload file baru)
    cy.contains('button', 'Edit Data').click();
    cy.contains('label', 'Scan KTP').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.png');
    cy.contains('button', 'Update').click();

    // 3. Verifikasi
    // a. Pesan sukses muncul
    cy.contains('Berhasil update data sertifikasi').should('be.visible');
    // b. Status asesi di halaman berubah dari 'perlu perbaikan' menjadi 'menunggu verifikasi'
    cy.contains('span', 'perlu perbaikan berkas').should('not.exist');
    cy.contains('span', 'menunggu verifikasi berkas').should('be.visible');
  });
});