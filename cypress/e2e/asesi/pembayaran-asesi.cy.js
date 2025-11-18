describe('Pembayaran Sertifikasi oleh Asesi', () => {
  const sertificationId = 1;
  const asesiId = 1; // Asumsikan ID asesi yang login adalah 1

  beforeEach(() => {
    cy.loginAsAsesi();
    cy.visit(`/asesi/sertifikasi/${sertificationId}/pembayaran/${asesiId}`);
  });

  it('Harus bisa mengunggah bukti pembayaran untuk pertama kali', () => {
    // Asumsi: Asesi ini belum pernah mengunggah bukti bayar.
    
    // 1. Verifikasi bahwa instruksi pembayaran dan form terlihat
    cy.contains('div', 'Biaya Sertifikasi:').should('be.visible');
    cy.contains('label', 'Unggah Bukti Pembayaran').should('be.visible');

    // 2. Unggah file bukti pembayaran
    cy.get('div').contains('label', 'Unggah Bukti Pembayaran').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.jpg');

    // 3. Klik tombol submit
    cy.contains('button', 'Submit Bukti Pembayaran').click();

    // 4. Verifikasi
    // a. Pesan sukses muncul
    cy.contains('Berhasil upload bukti pembayaran, admin akan memverifikasinya.').should('be.visible');
    // b. Halaman menampilkan file yang baru diunggah sebagai "existing file"
    cy.get('a[href*="/storage/"]').should('be.visible').and('contain.text', 'dummy.jpg');
  });

  it('Harus bisa mengganti bukti pembayaran yang sudah ada', () => {
    // Asumsi: Asesi ini sudah pernah mengunggah bukti bayar sebelumnya.

    // 1. Verifikasi bahwa file yang ada sudah ditampilkan
    cy.get('a[href*="/storage/"]').should('be.visible');

    // 2. Hapus file yang lama
    cy.get('div').contains('label', 'Unggah Bukti Pembayaran').parent().within(() => {
        cy.get('button[aria-label="Remove file"]').click();
    });

    // 3. Unggah file yang baru
    cy.get('div').contains('label', 'Unggah Bukti Pembayaran').parent().find('input[type="file"]').selectFile('cypress/fixtures/dummy.png');

    // 4. Klik tombol submit
    cy.contains('button', 'Submit Bukti Pembayaran').click();

    // 5. Verifikasi
    // a. Pesan sukses muncul
    cy.contains('Berhasil upload bukti pembayaran, admin akan memverifikasinya.').should('be.visible');
    // b. File yang baru sekarang ditampilkan
    cy.get('a[href*="/storage/"]').should('be.visible').and('contain.text', 'dummy.png');
  });

  it('Harus menampilkan halaman lunas jika pembayaran sudah terverifikasi', () => {
    // Asumsi: Status transaksi asesi ini di database adalah 'bukti_pembayaran_terverifikasi'.

    // 1. Verifikasi bahwa tampilan "Lunas" muncul
    cy.contains('h3', 'Bukti Pembayaran Terverifikasi').should('be.visible');

    // 2. Verifikasi bahwa form unggah tidak ada
    cy.contains('label', 'Unggah Bukti Pembayaran').should('not.exist');
  });
});