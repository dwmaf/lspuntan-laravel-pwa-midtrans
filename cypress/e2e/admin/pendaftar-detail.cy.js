describe('Rincian Pendaftar (Asesi)', () => {
  const sertificationId = 1;
  const asesiId = 1;

  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit(`/admin/sertifikasi/${sertificationId}/pendaftar/${asesiId}`);
  });

  it('Harus bisa mengubah status asesi menjadi "Perlu Perbaikan Berkas" dengan catatan', () => {
    
    cy.contains('dt', 'Status Asesi').parent().find('button').click();

    
    cy.contains('h3', 'Konfirmasi Ubah Status Asesi').should('be.visible');

    
    cy.get('div[role="dialog"]').within(() => {
      cy.get('select').select('perlu_perbaikan_berkas');
      
      cy.get('textarea').should('be.visible').type('Mohon perbaiki KTP Anda.');
      cy.contains('button', 'Simpan').click();
    });

    
    cy.contains('h3', 'Konfirmasi Ubah Status Asesi').should('not.exist');
    cy.contains('span', 'perlu perbaikan berkas').should('be.visible');
  });

  it('Harus bisa mengubah status pembayaran menjadi "Terverifikasi"', () => {
    
    cy.contains('dt', 'Status Pembayaran').parent().find('button').click();

    
    cy.contains('h3', 'Konfirmasi Ubah Status Pembayaran Asesi').should('be.visible');

    
    cy.get('div[role="dialog"]').within(() => {
      cy.get('select').select('bukti_pembayaran_terverifikasi');
      cy.contains('button', 'Simpan').click();
    });

    
    cy.contains('h3', 'Konfirmasi Ubah Status Pembayaran Asesi').should('not.exist');
    cy.contains('span', 'Pembayaran Terverifikasi').should('be.visible');
  });

  it('Harus bisa mengunggah dan menyimpan data sertifikat', () => {
    const serialNumber = `SN-${Date.now()}`;
    const certNumber = `CERT-${Date.now()}`;
    const regNumber = `REG-${Date.now()}`;

    
    cy.contains('button', 'Upload Sertifikat').click();

    
    cy.contains('h3', 'Upload Sertifikat').should('be.visible');

    
    cy.get('form').within(() => {
      cy.get('input[type="text"]').eq(0).type(serialNumber);
      cy.get('input[type="text"]').eq(1).type(certNumber);
      cy.get('input[type="text"]').eq(2).type(regNumber); 
      cy.get('input[type="date"]').eq(0).type('2024-01-01');
      cy.get('input[type="date"]').eq(1).type('2027-01-01');
      cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy.pdf');
      cy.contains('button', 'Simpan').click();
    });

    
    cy.contains('h3', 'Upload Sertifikat').should('not.exist');
    cy.contains('h3', 'Rincian Pendaftar').should('be.visible');

    
    cy.contains('button', 'Ubah Data Sertifikat').should('be.visible');
    cy.contains('a', 'Lihat Sertifikat').should('be.visible');
  });
});