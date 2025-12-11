describe('Pembayaran Sertifikasi oleh Asesi', () => {
  const sertificationId = 1;
  const asesiId = 1; 

  beforeEach(() => {
    cy.loginAsAsesi();
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pembayaran/${asesiId}`);
  });

  it('Harus bisa mengunggah bukti pembayaran untuk pertama kali', () => {
    
    
    cy.contains('div', 'Biaya Sertifikasi:').should('be.visible');
    cy.contains('label', 'Unggah Bukti Pembayaran').should('be.visible');

    
    cy.get('div').contains('label', 'Unggah Bukti Pembayaran').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');

    
    cy.contains('button', 'Submit Bukti Pembayaran').click();

    
    cy.contains('Berhasil upload bukti pembayaran, admin akan memverifikasinya.').should('be.visible');
    
    cy.get('a[href*="/storage/"]').should('be.visible').and('contain.text', 'dummy.jpg');
  });

  it('Harus bisa mengganti bukti pembayaran yang sudah ada', () => {
    

    
    cy.get('a[href*="/storage/"]').should('be.visible');

    
    cy.get('div').contains('label', 'Unggah Bukti Pembayaran').parent().within(() => {
        cy.get('button[aria-label="Remove file"]').click();
    });

    
    cy.get('div').contains('label', 'Unggah Bukti Pembayaran').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.png');

    
    cy.contains('button', 'Submit Bukti Pembayaran').click();

    
    cy.contains('Berhasil upload bukti pembayaran, admin akan memverifikasinya.').should('be.visible');
    
    cy.get('a[href*="/storage/"]').should('be.visible').and('contain.text', 'dummy.png');
  });

  it('Harus menampilkan halaman lunas jika pembayaran sudah terverifikasi', () => {
    
    cy.contains('h3', 'Bukti Pembayaran Terverifikasi').should('be.visible');

    
    cy.contains('label', 'Unggah Bukti Pembayaran').should('not.exist');
  });
});