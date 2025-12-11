describe('Manajemen Akun Pengguna', () => {
  
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/users');
  });

  it('Harus bisa mencari dan memfilter pengguna', () => {
    
    cy.get('input[placeholder="Cari nama atau email..."]').type('Admin');
    cy.wait(600); 
    cy.get('tbody tr').should('have.length.at.least', 1);
    cy.get('tbody tr').first().should('contain.text', 'Admin');

    
    cy.get('button').contains('FunnelIcon').parent().click();

    
    cy.contains('label', 'Role').parent().find('select').select('asesor');
    cy.contains('button', 'Apply Filter').click();

    
    cy.url().should('include', 'role=asesor');
    cy.get('tbody tr').each(($row) => {
      cy.wrap($row).should('contain.text', 'asesor');
    });

    
    cy.get('button').contains('FunnelIcon').parent().click();
    cy.contains('button', 'Reset Filter').click();
    cy.url().should('not.include', 'role=asesor');
    cy.get('input[placeholder="Cari nama atau email..."]').should('have.value', '');
  });

  it('Harus bisa mengedit nama pengguna', () => {
    const newName = `User Diedit Cypress ${Date.now()}`;

    
    cy.get('tbody tr').eq(1).within(() => {
      cy.contains('button', 'Edit').click();
    });

    
    cy.contains('h2', 'Manajemen Akun Pengguna').should('be.visible');

    
    cy.get('form').within(() => {
      cy.get('input').first().clear().type(newName);
      cy.contains('button', 'Simpan').click();
    });

    
    cy.contains('td', newName).should('be.visible');
  });

  it('Harus bisa melakukan Ban dan Un-ban pengguna', () => {
    
    cy.get('tbody tr').contains('button', 'Ban', { matchCase: false }).first().parents('tr').as('userRow');

    
    cy.get('@userRow').within(() => {
      cy.contains('button', 'Ban').click();
    });

    
    cy.contains('h2', 'Apakah Anda yakin ingin menangguhkan akun ini?').should('be.visible');

    
    cy.get('div[role="dialog"]').within(() => {
      cy.contains('button', 'Ya, Tangguhkan Akun').click();
    });

    
    cy.get('@userRow').should('contain.text', 'Un-ban');

    
    cy.get('@userRow').within(() => {
      cy.contains('button', 'Un-ban').click();
    });

    
    cy.contains('h2', 'Apakah Anda yakin ingin mengaktifkan kembali akun ini?').should('be.visible');

    
    cy.get('div[role="dialog"]').within(() => {
      cy.contains('button', 'Ya, Aktifkan Kembali').click();
    });

    
    cy.get('@userRow').should('contain.text', 'Ban');
  });
});