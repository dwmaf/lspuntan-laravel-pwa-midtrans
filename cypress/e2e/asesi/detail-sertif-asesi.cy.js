describe('Detail Sertifikasi Saya (Asesi)', () => {
  const sertificationId = 1;
  const asesiId = 1;

  beforeEach(() => {
    cy.loginAsAsesi();
    cy.visit(`/asesi/sertifikasi/applied/${sertificationId}/${asesiId}`);
  });

  it('Harus bisa masuk mode edit, mengubah data, dan menyimpan perubahan', () => {
    const newKebangsaan = `WNI-${Date.now()}`;

    
    cy.contains('button', 'Edit Data').click();
    cy.contains('h3', 'A. Data Pribadi').should('be.visible');

    
    cy.contains('label', 'Kebangsaan').parent().find('input').clear().type(newKebangsaan);

    
    cy.contains('button', 'Tambah Mata Kuliah').click();
    cy.get('div[class*="items-center gap-3"]').last().within(() => {
      cy.get('input[placeholder*="Struktur Data"]').type('Jaringan Komputer');
      cy.get('input[placeholder*="e.g., A"]').type('B+');
    });

    
    cy.contains('label', 'Scan Kartu Hasil Studi').parent().within(() => {
        cy.get('button[aria-label="Remove file"]').first().click();
    });

    
    cy.contains('label', 'Scan KTP').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');

    
    cy.contains('button', 'Update').click();

    
    cy.contains('h3', 'A. Data Pribadi').should('not.exist');
    
    cy.contains('Berhasil update data sertifikasi').should('be.visible');
    
    cy.contains('dt', 'Kebangsaan').next('dd').should('contain.text', newKebangsaan);
  });

  it('Harus menampilkan notifikasi perbaikan dan mengubah status setelah update', () => {
    
    cy.contains('span', 'Perhatian!').should('be.visible');
    cy.contains('Admin meminta perbaikan berkas').should('be.visible');

    
    cy.contains('button', 'Edit Data').click();
    cy.contains('label', 'Scan KTP').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.png');
    cy.contains('button', 'Update').click();

    
    cy.contains('Berhasil update data sertifikasi').should('be.visible');
    
    cy.contains('span', 'perlu perbaikan berkas').should('not.exist');
    cy.contains('span', 'menunggu verifikasi berkas').should('be.visible');
  });
});