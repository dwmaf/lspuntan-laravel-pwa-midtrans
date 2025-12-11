describe('Pengumpulan Tugas Asesmen oleh Asesi', () => {
  const sertificationId = 1;
  const asesiId = 1; 

  beforeEach(() => {
    
    cy.loginAsAsesi();
    
    cy.visit(`/asesi/sertifikasi/${sertificationId}/asesmen/${asesiId}`);
  });

  it('Harus bisa mengumpulkan file tugas untuk pertama kali', () => {
    
    
    
    cy.contains('h4', 'Kumpulkan Tugas Anda').should('be.visible');

    
    cy.get('input[type="file"]').selectFile([
      'cypress/fixtures/dummy.pdf',
      'cypress/fixtures/dummy.docx'
    ]);

    
    cy.contains('button', 'Kumpulkan').click();

    
    cy.contains('Berhasil unggah file asesmen.').should('be.visible');
    
    cy.contains('h4', 'Kumpulkan Tugas Anda').should('not.exist');
    
    cy.contains('h4', 'Tugas Terkumpul:').should('be.visible');
    cy.contains('a', 'dummy.pdf').should('be.visible');
    cy.contains('a', 'dummy.docx').should('be.visible');
  });

  it('Harus bisa mengedit file yang sudah dikumpulkan (menghapus dan menambah)', () => {
    
    cy.contains('button', 'Batalkan Pengiriman').click();

    
    cy.contains('h4', 'Kumpulkan Tugas Anda').should('be.visible');

    
    cy.contains('div', 'dummy.pdf').parent().within(() => {
      cy.get('button').click(); 
    });

    
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');

    
    cy.contains('button', 'Kumpulkan').click();

    
    cy.contains('Berhasil unggah file asesmen.').should('be.visible');
    
    cy.contains('a', 'dummy.pdf').should('not.exist');
    
    cy.contains('a', 'dummy.jpg').should('be.visible');
  });
});