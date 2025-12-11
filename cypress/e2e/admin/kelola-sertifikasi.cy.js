describe('Manajemen Sertifikasi', () => {
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/kelolasertifikasi');
  });

  it('Harus bisa membuat sertifikasi baru dengan mengisi semua field', () => {
    const tukName = `TUK Uji Cypress ${Date.now()}`;

    
    cy.contains('button', 'Mulai Sertifikasi').click();
    cy.contains('h2', 'Mulai Sertifikasi').should('be.visible');

    
    cy.get('form').find('select').select(1);

    
    cy.wait(200);
    
    cy.get('.multiselect__tags').click();
    cy.get('.multiselect__content-wrapper').find('li').first().click();

    
    cy.get('form').find('input[type="date"]').eq(0).type('2028-11-01'); 
    cy.get('form').find('input[type="date"]').eq(1).type('2028-11-30'); 
    cy.get('#tanggal_bayar_ditutup').find('input').type('2028-12-05'); 
    cy.get('#biaya_sertifikasi').find('input').type('1500000');
    cy.get('#tuk').find('input').type(tukName);

    
    cy.contains('button', 'Mulai').click();

    
    cy.contains('h2', 'Mulai Sertifikasi').should('be.visible');
    cy.get('#tuk').find('input').should('have.value', ''); 
  });

  it('Harus menampilkan daftar sertifikasi yang berlangsung dan bisa navigasi ke detail', () => {
    
    cy.contains('button', 'Sertifikasi Berlangsung').click();

    
    cy.get('div[class*="bg-white p-6"]').should('have.length.at.least', 1);

    
    cy.get('div[class*="bg-white p-6"]').first().within(() => {
      cy.contains('a', 'Detail').click();
    });

    
    cy.url().should('include', '/admin/sertifikasi/');
    cy.contains('h3', 'Detail Sertifikasi').should('be.visible');
  });

  it('Harus bisa memfilter riwayat sertifikasi berdasarkan skema dan meresetnya', () => {
    
    cy.contains('button', 'Riwayat Sertifikasi').click();

    
    cy.get('div').contains('label', 'Skema').parent().find('select > option').eq(1)
      .invoke('text')
      .then((skemaName) => {
        
        cy.get('div').contains('label', 'Skema').parent().find('select').select(1);

        
        cy.get('div[class*="bg-white p-6"]').each(($card) => {
          cy.wrap($card).should('contain.text', skemaName.trim());
        });

        
        cy.contains('button', 'Reset').click();

        
        cy.get('div').contains('label', 'Skema').parent().find('select').should('have.value', '');
      });
  });
});