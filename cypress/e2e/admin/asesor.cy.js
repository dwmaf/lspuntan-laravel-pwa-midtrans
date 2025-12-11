describe('Manajemen Asesor', () => {
  
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/asesor/index');
  });

  it('Harus bisa membuat asesor baru, mengeditnya, lalu menghapusnya', () => {
    
    const asesorName = `Asesor Uji Cypress ${Date.now()}`;
    const asesorEmail = `asesor-uji-${Date.now()}@example.com`;
    const updatedAsesorName = `${asesorName} (Updated)`;

    
    cy.contains('button', 'Tambah Asesor').click();

    
    cy.contains('h2', 'Tambah Asesor').should('be.visible');

    
    cy.contains('button', 'Pilih Skema').click();
    
    cy.get('div[class*="overflow-y-auto"]').find('label').first().click();
    
    cy.contains('button', 'Pilih Skema').click();

    // Isi form
    cy.get('input[type="text"]').first().type(asesorName); 
    cy.get('input[type="email"]').type(asesorEmail);
    cy.get('input[type="date"]').first().type('2028-12-31');
    cy.get('input[type="date"]').last().type('2029-12-31'); 
    cy.get('input[type="text"]').last().type('081234567890');
    
    cy.contains('button', 'Simpan').click();

    
    cy.contains('h2', 'Daftar Asesor').should('be.visible');
    cy.contains('td', asesorName).should('be.visible');
    cy.contains('td', asesorEmail).should('be.visible');
    cy.contains('tr', asesorEmail).within(() => {
      cy.contains('button', 'Edit').click();
    });

    
    cy.contains('h2', 'Edit Asesor').should('be.visible');
    cy.get('input[type="text"]').first().clear().type(updatedAsesorName);
    cy.get('input[type="date"]').first().type('2028-11-21'); 
    cy.get('input[type="date"]').last().type('2029-10-11');
    cy.get('input[type="text"]').last().type('081234562444');
    
    cy.contains('button', 'Pilih Skema').click();
    cy.get('div[class*="overflow-y-auto"]').find('label').eq(1).click();
    cy.contains('button', 'Pilih Skema').click();

    cy.contains('button', 'Simpan').click();

    
    cy.contains('td', asesorName).should('not.exist'); 
    cy.contains('td', updatedAsesorName).should('be.visible'); 

    
    cy.on('window:confirm', () => true);

    
    cy.contains('tr', asesorEmail).within(() => {
      cy.contains('button', 'Hapus').click();
    });

    
    cy.contains('td', asesorEmail).should('not.exist');
    cy.contains('Data asesor berhasil dihapus').should('be.visible');
  });
});