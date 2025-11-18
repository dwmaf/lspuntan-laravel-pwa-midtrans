describe('Manajemen Instruksi Pembayaran', () => {
  const sertificationId = 1; // Asumsikan kita menguji sertifikasi dengan ID 1

  beforeEach(() => {
    cy.loginAsAdmin();
    // URL ini sesuai dengan route('admin.sertifikasi.payment-desc.index', $sert_id)
    cy.visit(`/admin/sertifikasi/${sertificationId}/payment-instruction`);
  });

  it('Harus bisa mengedit rincian pembayaran, mempublikasikannya, dan memverifikasi perubahan', () => {
    const newContent = `Instruksi pembayaran diubah oleh Cypress pada ${new Date().toLocaleString()}. Silakan transfer ke rekening berikut.`;
    const newBiaya = '2500000';
    const newDeadline = '2029-11-15';

    // --- TAHAP 1: MASUK MODE EDIT ---
    cy.contains('button', 'Edit').click();

    // Verifikasi sudah masuk mode edit
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('be.visible');

    // --- TAHAP 2: MENGISI FORM ---
    // 1. Isi rincian pembayaran
    cy.get('textarea').clear().type(newContent);

    // 2. Isi biaya
    cy.get('div').contains('label', 'Biaya Sertifikasi').parent().find('input').clear().type(newBiaya);
    // Verifikasi computed property 'formattedHarga' berfungsi
    cy.contains('p', 'Rp 2.500.000').should('be.visible');

    // 3. Isi tanggal deadline
    cy.get('div').contains('label', 'Tanggal Bayar Ditutup').parent().find('input').clear().type(newDeadline);

    // 4. Aktifkan toggle "Publikasikan"
    cy.get('#is_published').parent().click();

    // --- TAHAP 3: SIMPAN DAN VERIFIKASI ---
    // Klik tombol Simpan
    cy.contains('button', 'Simpan').click();

    // Verifikasi kembali ke mode tampilan
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('not.exist');

    // Verifikasi data yang baru disimpan ditampilkan dengan benar
    cy.contains('div', newContent).should('be.visible');
    cy.contains('dd', '2.500.000').should('be.visible');
    cy.contains('dd', newDeadline).should('be.visible');

    // Verifikasi status menjadi "Dipublikasikan"
    cy.contains('span', 'Dipublikasikan').should('be.visible');
  });

  it('Harus bisa membatalkan mode edit', () => {
    // 1. Masuk mode edit
    cy.contains('button', 'Edit').click();
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('be.visible');

    // 2. Klik tombol Batal
    cy.contains('button', 'Batal').click();

    // 3. Verifikasi kembali ke mode tampilan
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('not.exist');
    cy.contains('button', 'Edit').should('be.visible');
  });
});