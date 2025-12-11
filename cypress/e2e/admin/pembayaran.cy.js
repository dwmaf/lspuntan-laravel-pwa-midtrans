describe('Manajemen Instruksi Pembayaran', () => {
  const sertificationId = 1;

  beforeEach(() => {
    cy.loginAsAdmin();
    
    cy.visit(`/admin/sertifikasi/${sertificationId}/payment-desc/index`);
  });

  it('Harus bisa mengedit rincian pembayaran, mempublikasikannya, dan memverifikasi perubahan', () => {
    const newContent = `Instruksi pembayaran diubah oleh Cypress pada ${new Date().toLocaleString()}. Silakan transfer ke rekening berikut.`;
    const newBiaya = '300000';
    const newDeadline = '2029-11-15T23:59';

    
    cy.contains('button', 'Edit').click();

    
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('be.visible');

    
    cy.get('textarea').clear().type(newContent);

    
    cy.get('div').contains('label', 'Biaya Sertifikasi').parent().find('input').clear().type(newBiaya);
    
    cy.contains('p', 'Rp 300.000').should('be.visible');

    
    cy.get('div').contains('label', 'Tanggal Bayar Ditutup').parent().find('input').clear().type(newDeadline);

    
    cy.get('#is_published').parent().click();

    
    cy.contains('button', 'Simpan').click();

    
    cy.contains('h3', 'Edit Rincian Instruksi Pembayaran').should('not.exist');

    
    cy.contains('div', newContent).should('be.visible');
    cy.contains('dd', '300.000').should('be.visible');
    cy.contains('dd', '15 November 2029 , 23:59 WIB').should('be.visible');

    
    cy.contains('span', 'Dipublikasikan').should('be.visible');
  });

});