describe('Manajemen Sertifikasi', () => {
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/kelolasertifikasi/index');
  });

  it('Harus bisa membuat sertifikasi baru dengan mengisi semua field', () => {
    const tukName = `TUK Uji Cypress ${Date.now()}`;
    cy.contains('button', 'Mulai Sertifikasi').click();
    cy.contains('h2', 'Mulai Sertifikasi').should('be.visible');
    cy.get('div').contains('label', 'Pilih Skema Sertifikasi').parent().find('select').then($select => {
      const optionCount = $select.find('option').length - 1;
      const randomIndex = Math.floor(Math.random() * optionCount) + 1;
      cy.wrap($select).select(randomIndex);
    });
    cy.wait(200);
    cy.get('div').contains('label', 'Tanggal Daftar Dibuka').parent().find('input').clear().type('2025-12-15');
    cy.get('div').contains('label', 'Tanggal Daftar Ditutup').parent().find('input').clear().type('2025-12-29');
    cy.get('div').contains('label', 'Tanggal Bayar Ditutup').parent().find('input').clear().type('2025-12-22T23.59');
    cy.get('div').contains('label', 'Biaya Sertifikasi').parent().find('input').clear().type('120000');
    cy.get('div').contains('label', 'Tempat Uji Sertifikasi').parent().find('input').clear().type(tukName);
    cy.get('div').contains('label', 'Pilih Asesor').parent().find('div').first().click();
    cy.get('div').contains('label', 'Pilih Asesor').parent().find('ul li').then($options => {
      const totalAsesors = $options.length;
      const randomCount = Math.floor(Math.random() * 3) + 1;
      const pickCount = Math.min(randomCount, totalAsesors);
      const indices = Array.from({ length: totalAsesors }, (_, i) => i);
      for (let i = indices.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [indices[i], indices[j]] = [indices[j], indices[i]];
      }
      for (let i = 0; i < pickCount; i++) {
        cy.wrap($options.eq(indices[i])).click();
      }
    });
    cy.get('body').click(0, 0);
    cy.contains('button', 'Mulai').click();
    cy.contains('Sertifikasi berhasil dimulai!').should('be.visible');
    cy.contains('h2', 'Detail Sertifikasi').should('be.visible');

    cy.contains('button', 'Hapus').click();
    cy.on('window:confirm', () => true);
    cy.contains('Sertifikasi berhasil dihapus').should('be.visible');

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