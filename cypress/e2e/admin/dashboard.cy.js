describe('Dashboard Admin', () => {
  
  beforeEach(() => {
    cy.loginAsAdmin();
    
    cy.visit('/admin/dashboard'); 
  });

  it('Harus menampilkan judul halaman dan semua kartu statistik', () => {
    
    cy.contains('h2', 'Dashboard Admin').should('be.visible');

    
    cy.contains('h3', 'Sertifikasi Berlangsung').should('be.visible');
    cy.contains('h3', 'Sertifikasi Selesai').should('be.visible');
    cy.contains('h3', 'Pendaftar Baru').should('be.visible');
    cy.contains('h3', 'Bukti Pembayaran Pending').should('be.visible');
    cy.contains('h3', 'Total Asesi').should('be.visible');
    cy.contains('h3', 'Total Lulusan').should('be.visible');
  });

  it('Harus menampilkan data sertifikasi yang berlangsung dari server', () => {
    cy.contains('h3', 'Manajemen Sertifikasi').parent().within(() => {
      
      cy.get('div[class*="p-3 border"]').should('have.length.at.least', 1);

      
      cy.get('div[class*="p-3 border"]').first().within(() => {
        
        cy.get('h3').should('not.be.empty');
        
        cy.get('p').should('contain.text', 'asesi terdaftar');
        
        cy.get('div[class*="bg-green-100"]').should('be.visible');
      });
    });
  });

  it('Harus menampilkan kartu Aktivitas Terbaru', () => {
    
    cy.contains('h3', 'Aktivitas Terbaru').should('be.visible');
    cy.contains('p', 'Penilaian baru dimulai').should('be.visible');
  });
});