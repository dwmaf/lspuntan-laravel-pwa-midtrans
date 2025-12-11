describe('Pendaftaran Sertifikasi oleh Asesi', () => {
  const sertificationId = 1; 

  beforeEach(() => {
    
    cy.loginAsAsesi();
    
    cy.visit(`/asesi/sertifikasi/apply/${sertificationId}`);
  });

  it('Harus bisa mengisi semua field wajib dan berhasil mendaftar', () => {
    
    cy.get('#kelamin').select('Laki-laki');

    
    cy.get('select[v-model="form.tujuan_sert"]').select('Sertifikasi');

    
    cy.get('div[class*="items-center gap-3"]').within(() => {
      cy.get('input[placeholder*="Struktur Data"]').type('Pemrograman Berorientasi Objek');
      cy.get('input[placeholder*="e.g., A"]').type('A');
    });

    
    cy.contains('button', 'Tambah Mata Kuliah').click();
    cy.get('div[class*="items-center gap-3"]').eq(1).within(() => {
      cy.get('input[placeholder*="Struktur Data"]').type('Basis Data');
      cy.get('input[placeholder*="e.g., A"]').type('A-');
    });

    
    cy.get('div').contains('label', 'Form APL.01').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.pdf');
    cy.get('div').contains('label', 'Form APL.02').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.pdf');
    
    cy.get('div').contains('label', 'Scan KTP').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');
    cy.get('div').contains('label', 'Pasfoto terbaru').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.png');
    cy.get('div').contains('label', 'Scan KTM').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');

    
    cy.get('div').contains('label', 'Scan Kartu Hasil Studi').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.pdf');
    
    cy.get('div').contains('label', 'Scan Surat Keterangan Magang').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.pdf');

    
    cy.contains('button', 'Daftar').click();

    
    cy.url().should('include', `/asesi/sertifikasi/applied/${sertificationId}/`);
    cy.contains('h3', 'Detail Sertifikasi Saya').should('be.visible');
    cy.contains('Berhasil daftar sertifikasi').should('be.visible');
  });
});