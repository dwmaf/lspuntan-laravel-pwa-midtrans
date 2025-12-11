describe('Detail dan Edit Sertifikasi', () => {
  const sertificationId = 1;

  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit(`/admin/kelolasertifikasi/${sertificationId}/show`);
  });

  it('Harus bisa mengedit detail sertifikasi dan kemudian menghapusnya', () => {
    
    cy.contains('button', 'Edit').click();
    cy.contains('h2', 'Edit Sertifikasi').should('be.visible');

    
    const newTuk = `TUK Cypress Test ${Date.now()}`;
    const newDate = '2029-10-10';

    
    cy.get('select').first().select(1);

    
    cy.get('.multiselect__tags').within(() => {
      cy.get('.multiselect__tag-icon').each(($icon) => {
        cy.wrap($icon).click();
      });
    });
    
    cy.get('.multiselect__tags').click();
    cy.get('.multiselect__content-wrapper').find('li').first().click();

    
    cy.get('#tanggal_apply_ditutup').find('input').clear().type(newDate);

    
    cy.get('#tuk').find('input').clear().type(newTuk);

    
    cy.contains('button', 'Update').click();

    
    cy.contains('h2', 'Edit Sertifikasi').should('not.exist');
    cy.contains('h3', 'Detail Sertifikasi').should('be.visible');

    
    cy.contains('dt', 'TUK').next('dd').should('contain.text', newTuk);
    cy.contains('dt', 'Tanggal Pendaftaran Ditutup').next('dd').should('contain.text', '10 Oktober 2029');

    
    cy.on('window:confirm', () => true);
    cy.contains('button', 'Hapus').click();
    cy.url().should('include', '/admin/kelolasertifikasi');
    cy.contains('Sertifikasi berhasil dihapus').should('be.visible');
  });
});