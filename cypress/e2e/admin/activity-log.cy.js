describe('Catatan Aktivitas Sistem', () => {
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/activity-logs');
  });

  it('Harus bisa mencari log berdasarkan nama causer (pengguna)', () => {
    const causerName = 'Sistem';
    cy.get('input[placeholder="Cari causer..."]').type(causerName);
    cy.wait(600);
    cy.get('tbody tr').each(($row) => {
      cy.wrap($row).should('contain.text', causerName);
    });
  });

  it('Harus bisa memfilter log berdasarkan jenis aksi "created"', () => {
    cy.get('[data-cy="filter-trigger-button"]').click();
    cy.contains('label', 'Jenis Aksi').parent().find('select').select('created');
    cy.contains('button', 'Apply Filter').click(); 
    cy.url().should('include', 'event=created');
    cy.get('tbody tr').each(($row) => {
      cy.wrap($row).should('contain.text', 'created');
    });
    cy.get('[data-cy="filter-trigger-button"]').click();
    cy.contains('button', 'Reset').click();
    cy.url().should('not.include', 'event=created');
  });

  it('Harus bisa melihat detail log dan kembali ke daftar', () => {
    cy.get('tbody tr').first().within(() => {
      cy.contains('button', 'Lihat').click();
    });
    cy.contains('h3', 'Detail Aktivitas').should('be.visible');
    cy.contains('dt', 'Dilakukan Oleh').should('be.visible');
    cy.contains('button', 'Kembali').click();
    cy.contains('h2', 'Catatan Aktivitas Sistem').should('be.visible');
    cy.get('table').should('be.visible');
  });
});