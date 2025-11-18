describe('Manajemen Sertifikasi', () => {
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/kelolasertifikasi');
  });

  it('Harus bisa membuat sertifikasi baru dengan mengisi semua field', () => {
    const tukName = `TUK Uji Cypress ${Date.now()}`;

    // 1. Pindah ke tab "Mulai Sertifikasi"
    cy.contains('button', 'Mulai Sertifikasi').click();
    cy.contains('h2', 'Mulai Sertifikasi').should('be.visible');

    // 2. Pilih Skema (pilih opsi kedua yang tersedia)
    cy.get('form').find('select').select(1);

    // 3. Interaksi dengan vue-multiselect untuk Asesor
    // Tunggu sebentar agar daftar asesor yang relevan dimuat setelah memilih skema
    cy.wait(200);
    // Buka dropdown dan pilih asesor pertama yang tersedia
    cy.get('.multiselect__tags').click();
    cy.get('.multiselect__content-wrapper').find('li').first().click();

    // 4. Isi sisa form
    cy.get('form').find('input[type="date"]').eq(0).type('2028-11-01'); // Daftar Dibuka
    cy.get('form').find('input[type="date"]').eq(1).type('2028-11-30'); // Daftar Ditutup
    cy.get('#tanggal_bayar_ditutup').find('input').type('2028-12-05'); // Bayar Ditutup
    cy.get('#biaya_sertifikasi').find('input').type('1500000');
    cy.get('#tuk').find('input').type(tukName);

    // 5. Submit form
    cy.contains('button', 'Mulai').click();

    // 6. Verifikasi: Form harus di-reset dan kita kembali ke tab "Berlangsung" (default)
    // atau tetap di tab "Mulai" dengan form kosong.
    cy.contains('h2', 'Mulai Sertifikasi').should('be.visible');
    cy.get('#tuk').find('input').should('have.value', ''); // Cek salah satu field, harusnya kosong
  });

  it('Harus menampilkan daftar sertifikasi yang berlangsung dan bisa navigasi ke detail', () => {
    // 1. Pindah ke tab "Sertifikasi Berlangsung"
    cy.contains('button', 'Sertifikasi Berlangsung').click();

    // 2. Verifikasi: Pastikan setidaknya ada satu kartu sertifikasi
    cy.get('div[class*="bg-white p-6"]').should('have.length.at.least', 1);

    // 3. Klik tombol "Detail" pada kartu pertama
    cy.get('div[class*="bg-white p-6"]').first().within(() => {
      cy.contains('a', 'Detail').click();
    });

    // 4. Verifikasi: URL harus berubah ke halaman detail
    cy.url().should('include', '/admin/sertifikasi/');
    cy.contains('h3', 'Detail Sertifikasi').should('be.visible');
  });

  it('Harus bisa memfilter riwayat sertifikasi berdasarkan skema dan meresetnya', () => {
    // 1. Pindah ke tab "Riwayat Sertifikasi"
    cy.contains('button', 'Riwayat Sertifikasi').click();

    // 2. Dapatkan nama skema dari opsi kedua di dropdown filter
    cy.get('div').contains('label', 'Skema').parent().find('select > option').eq(1)
      .invoke('text')
      .then((skemaName) => {
        // 3. Pilih skema tersebut di filter
        cy.get('div').contains('label', 'Skema').parent().find('select').select(1);

        // 4. Verifikasi: Semua kartu yang ditampilkan harus berisi nama skema yang difilter
        cy.get('div[class*="bg-white p-6"]').each(($card) => {
          cy.wrap($card).should('contain.text', skemaName.trim());
        });

        // 5. Klik tombol Reset
        cy.contains('button', 'Reset').click();

        // 6. Verifikasi: Filter harus kembali ke default
        cy.get('div').contains('label', 'Skema').parent().find('select').should('have.value', '');
      });
  });
});