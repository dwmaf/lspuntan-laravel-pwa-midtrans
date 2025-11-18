describe('Halaman Pengumuman Asesi', () => {
  const sertificationId = 1;
  const asesiId = 1; // Asumsikan ID asesi yang login adalah 1

  beforeEach(() => {
    cy.loginAsAsesi();
  });

  it('Harus bisa melihat daftar pengumuman, membaca detail, dan kembali ke daftar', () => {
    // 1. Kunjungi halaman tanpa parameter
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}`);

    // 2. Verifikasi mode daftar aktif
    cy.contains('h2', 'Pengumuman').should('be.visible');
    cy.get('div.py-3').should('have.length.at.least', 1); // Pastikan ada setidaknya 1 kartu pengumuman

    // 3. Klik "Baca Selengkapnya" pada kartu pertama
    cy.get('div.py-3').first().within(() => {
      cy.contains('button', 'Baca Selengkapnya').click();
    });

    // 4. Verifikasi mode detail aktif
    cy.contains('button', 'Kembali ke Daftar').should('be.visible');
    // Pastikan konten penuh ditampilkan (bukan yang dipotong)
    cy.get('.prose').invoke('text').then((text) => {
      expect(text.length).to.be.greaterThan(150); // Asumsi konten lebih panjang dari 150 karakter
    });

    // 5. Kembali ke daftar
    cy.contains('button', 'Kembali ke Daftar').click();

    // 6. Verifikasi kembali di mode daftar
    cy.contains('button', 'Baca Selengkapnya').should('be.visible');
    cy.contains('button', 'Kembali ke Daftar').should('not.exist');
  });

  it('Harus bisa membuka detail pengumuman secara langsung via query parameter (simulasi notifikasi)', () => {
    const targetNewsId = 1; // Asumsikan ada pengumuman dengan ID=1

    // 1. Kunjungi halaman dengan query parameter
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}?news_id=${targetNewsId}`);

    // 2. Verifikasi halaman langsung memuat mode detail
    cy.contains('button', 'Kembali ke Daftar').should('be.visible');
    cy.contains('button', 'Baca Selengkapnya').should('not.exist');

    // 3. Verifikasi bahwa konten yang ditampilkan adalah untuk pengumuman yang benar
    // (Ini adalah verifikasi opsional tapi bagus untuk dilakukan jika Anda tahu kontennya)
    // cy.contains('h5', 'Judul Pengumuman Spesifik').should('be.visible');
  });

  it('Harus menampilkan pesan jika tidak ada pengumuman', () => {
    // Untuk tes ini, kita perlu mensimulasikan respons API kosong.
    // Ini adalah skenario yang lebih canggih menggunakan cy.intercept().

    // 1. Intercept request dan kembalikan array kosong untuk 'pengumumans'
    cy.intercept('GET', `/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}`, (req) => {
      req.reply((res) => {
        // Modifikasi props yang dikirim ke Inertia
        res.body.props.pengumumans = [];
        res.send(res.body);
      });
    }).as('getEmptyAnnouncements');

    // 2. Kunjungi halaman
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pengumuman/${asesiId}`);

    // 3. Tunggu intercept selesai
    cy.wait('@getEmptyAnnouncements');

    // 4. Verifikasi pesan "Belum ada pengumuman" muncul
    cy.contains('p', 'Belum ada pengumuman apapun.').should('be.visible');
  });
});