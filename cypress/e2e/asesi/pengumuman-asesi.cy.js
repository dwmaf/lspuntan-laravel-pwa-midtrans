describe('Halaman Pengumuman Asesi', () => {
  const sertificationId = 1;
  const asesiId = 1; 

  beforeEach(() => {
    cy.loginAsAsesi();
  });

  it('Harus bisa melihat daftar pengumuman, membaca detail, dan kembali ke daftar', () => {
    
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}`);

    
    cy.contains('h2', 'Pengumuman').should('be.visible');
    cy.get('div.py-3').should('have.length.at.least', 1);

    
    cy.get('div.py-3').first().within(() => {
      cy.contains('button', 'Baca Selengkapnya').click();
    });

    
    cy.contains('button', 'Kembali ke Daftar').should('be.visible');
    
    cy.get('.prose').invoke('text').then((text) => {
      expect(text.length).to.be.greaterThan(150); 
    });

    
    cy.contains('button', 'Kembali ke Daftar').click();

    
    cy.contains('button', 'Baca Selengkapnya').should('be.visible');
    cy.contains('button', 'Kembali ke Daftar').should('not.exist');
  });

  it('Harus bisa membuka detail pengumuman secara langsung via query parameter (simulasi notifikasi)', () => {
    const targetNewsId = 1; 

    
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}?news_id=${targetNewsId}`);

    
    cy.contains('button', 'Kembali ke Daftar').should('be.visible');
    cy.contains('button', 'Baca Selengkapnya').should('not.exist');

    
    // cy.contains('h5', 'Judul Pengumuman Spesifik').should('be.visible');
  });

  it('Harus menampilkan pesan jika tidak ada pengumuman', () => {
    
    cy.intercept('GET', `/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}`, (req) => {
      req.reply((res) => {
        
        res.body.props.pengumumans = [];
        res.send(res.body);
      });
    }).as('getEmptyAnnouncements');

    
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}`);

    
    cy.wait('@getEmptyAnnouncements');

    
    cy.contains('p', 'Belum ada pengumuman apapun.').should('be.visible');
  });
});