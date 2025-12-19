describe('Manajemen Instruksi Pembayaran', () => {
  const sertificationId = 11;

  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit(`/admin/sertifikasi/${sertificationId}/payment-desc/index`);
  });

  const createInstruction = (content, biaya, deadline) => {
    cy.contains('button', 'Buat Instruksi').click();
    cy.contains('h3', 'Buat Rincian Instruksi Pembayaran').should('be.visible');
    cy.contains('button', 'Batal').click();
    cy.contains('h3', 'Buat Rincian Instruksi Pembayaran').should('not.exist');
    cy.contains('button', 'Buat Instruksi').should('be.visible').click();
    cy.contains('h3', 'Buat Rincian Instruksi Pembayaran').should('be.visible');
    cy.get('textarea').clear().type(content);
    cy.get('div').contains('label', 'Biaya Sertifikasi').parent().find('input').clear().type(biaya);
    cy.get('div').contains('label', 'Tanggal Bayar Ditutup').parent().find('input').clear().type(deadline);
    cy.contains('button', 'Preview').click();
    cy.contains('h2', 'Preview Instruksi Pembayaran').should('be.visible');
    cy.contains('div', content).should('be.visible');
    cy.contains('button', 'Tutup Preview').click();
    cy.contains('button', 'Simpan').click();
    cy.contains('h3', 'Buat Rincian Instruksi Pembayaran').should('not.exist');
  };

  const editInstruction = (content, biaya, deadline) => {
    cy.contains('button', 'Edit').click();
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('be.visible');
    cy.contains('button', 'Batal').click();
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('not.exist');
    cy.contains('button', 'Edit').should('be.visible').click();
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('be.visible');
    cy.get('textarea').clear().type(content);
    cy.get('div').contains('label', 'Biaya Sertifikasi').parent().find('input').clear().type(biaya);
    cy.get('div').contains('label', 'Tanggal Bayar Ditutup').parent().find('input').clear().type(deadline);
    cy.contains('button', 'Preview').click();
    cy.contains('h2', 'Preview Instruksi Pembayaran').should('be.visible');
    cy.contains('div', content).should('be.visible');
    cy.contains('button', 'Tutup Preview').click();
    cy.contains('button', 'Simpan').click();
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('not.exist');
  };

  const deleteInstruction = () => {
    cy.contains('button', 'Edit').click();
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('be.visible');
    cy.contains('button', 'Hapus Instruksi').click();
    cy.on('window:confirm', () => true);
    cy.contains('Asesor belum memberikan instruksi pembayaran').should('be.visible');
  };

  const ensureNoInstruction = () => {
    cy.wait(500);
    if (cy.contains('button', 'Edit')) {
        deleteInstruction();
      }
  };

  it('Skenario 1: Jika belum ada instruksi - Buat, Edit, lalu Hapus', () => {
    const timestamp = new Date().toLocaleString();
    ensureNoInstruction();
    const createContent = `Instruksi pembayaran BARU dibuat oleh Cypress pada ${timestamp}. Transfer ke rekening Bank Mandiri 1234567890.`;
    const createBiaya = '100000';
    const createDeadline = '2029-12-01T23:59';
    createInstruction(createContent, createBiaya, createDeadline);
    cy.contains('div', createContent).should('be.visible');
    cy.contains('dd', '100.000').should('be.visible');
    cy.contains('dd', '1 Desember 2029 , 23:59 WIB').should('be.visible');
    const editContent = `Instruksi pembayaran DIEDIT oleh Cypress pada ${timestamp}. Transfer ke rekening BCA 0987654321.`;
    const editBiaya = '150000';
    const editDeadline = '2029-12-15T18:00';

    editInstruction(editContent, editBiaya, editDeadline);
    cy.contains('div', editContent).should('be.visible');
    cy.contains('dd', '150.000').should('be.visible');
    cy.contains('dd', '15 Desember 2029 , 18:00 WIB').should('be.visible');
    cy.contains('span', '(Diedit)').should('be.visible');
    deleteInstruction();
    cy.contains('button', 'Buat Instruksi').should('be.visible');
    cy.contains('Asesor belum memberikan instruksi pembayaran').should('be.visible');
  });

});