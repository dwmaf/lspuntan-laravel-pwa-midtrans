describe('Manajemen Skema Sertifikasi', () => {
  
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/skema');
  });

  it('Harus bisa membuat skema baru, mengeditnya, lalu menghapusnya', () => {
    
    const skemaName = `Skema Uji Cypress ${Date.now()}`;
    const updatedSkemaName = `${skemaName} (Updated)`;

    
    cy.contains('button', 'Tambah Skema').click();

    
    cy.contains('h2', 'Tambah Skema Sertifikasi').should('be.visible');

    
    cy.get('input[type="text"]').type(skemaName);

    
    cy.get('input[type="file"]').first().selectFile('cypress/fixtures/dummy.pdf');

    
    cy.contains('button', 'Simpan').click();

    
    cy.contains('h2', 'Daftar Skema').should('be.visible');
    cy.contains('td', skemaName).should('be.visible');
    cy.contains('a', 'APL.01').should('be.visible'); 

    cy.contains('tr', skemaName).within(() => {
      cy.contains('button', 'Edit').click();
    });


    cy.contains('h2', 'Edit Skema Sertifikasi').should('be.visible');


    cy.get('input[type="text"]').clear().type(updatedSkemaName);


    cy.contains('div', 'APL.01').within(() => {
        cy.get('button').click();
    });


    cy.get('input[type="file"]').last().selectFile('cypress/fixtures/dummy.docx');


    cy.contains('button', 'Simpan').click();


    cy.contains('td', skemaName).should('not.exist'); 
    cy.contains('td', updatedSkemaName).should('be.visible');

    
    cy.contains('tr', updatedSkemaName).within(() => {
      cy.contains('APL.01 belum ada').should('be.visible');
      cy.contains('a', 'APL.02').should('be.visible');
    });

    
    cy.on('window:confirm', () => true);

    
    cy.contains('tr', updatedSkemaName).within(() => {
      cy.contains('button', 'Hapus').click();
    });

    
    cy.contains('td', updatedSkemaName).should('not.exist');
    cy.contains('Skema berhasil dihapus').should('be.visible'); 
  });
});