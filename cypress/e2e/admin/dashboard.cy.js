describe('Dashboard Admin', () => {
  // Sebelum setiap tes, login sebagai admin dan kunjungi halaman dashboard
  beforeEach(() => {
    cy.loginAsAdmin();
    // Pastikan route ini sesuai dengan yang ada di web.php Anda
    cy.visit('/admin/dashboard'); 
  });

  it('Harus menampilkan judul halaman dan semua kartu statistik', () => {
    // 1. Verifikasi judul utama halaman
    cy.contains('h2', 'Dashboard Admin').should('be.visible');

    // 2. Verifikasi bahwa semua judul kartu statistik ada di halaman.
    // Ini memastikan komponen-komponen dasar telah dirender.
    cy.contains('h3', 'Sertifikasi Berlangsung').should('be.visible');
    cy.contains('h3', 'Sertifikasi Selesai').should('be.visible');
    cy.contains('h3', 'Pendaftar Baru').should('be.visible');
    cy.contains('h3', 'Bukti Pembayaran Pending').should('be.visible');
    cy.contains('h3', 'Total Asesi').should('be.visible');
    cy.contains('h3', 'Total Lulusan').should('be.visible');
  });

  it('Harus menampilkan data sertifikasi yang berlangsung dari server', () => {
    // Tes ini mengasumsikan ada setidaknya satu sertifikasi dengan status 'berlangsung' di database Anda.

    // 1. Cari kartu "Manajemen Sertifikasi"
    cy.contains('h3', 'Manajemen Sertifikasi').parent().within(() => {
      // 2. Verifikasi bahwa setidaknya ada satu item sertifikasi yang dirender.
      // Ini membuktikan bahwa loop v-for pada props 'sertificationBerlangsung' berfungsi.
      cy.get('div[class*="p-3 border"]').should('have.length.at.least', 1);

      // 3. Verifikasi konten dari item pertama yang dirender.
      cy.get('div[class*="p-3 border"]').first().within(() => {
        // Pastikan ada nama skema (h3)
        cy.get('h3').should('not.be.empty');
        // Pastikan ada jumlah asesi (p)
        cy.get('p').should('contain.text', 'asesi terdaftar');
        // Pastikan ada badge status
        cy.get('div[class*="bg-green-100"]').should('be.visible');
      });
    });
  });

  it('Harus menampilkan kartu Aktivitas Terbaru', () => {
    // Saat ini, konten di kartu ini di-hardcode.
    // Tes ini hanya memverifikasi bahwa kartu dan beberapa teks statisnya muncul.
    cy.contains('h3', 'Aktivitas Terbaru').should('be.visible');
    cy.contains('p', 'Penilaian baru dimulai').should('be.visible');
  });
});