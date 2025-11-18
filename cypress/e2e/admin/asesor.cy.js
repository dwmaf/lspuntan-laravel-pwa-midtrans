describe('Manajemen Asesor', () => {
  // Sebelum setiap tes, login sebagai admin dan kunjungi halaman asesor
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/asesor/index');
  });

  it('Harus bisa membuat asesor baru, mengeditnya, lalu menghapusnya', () => {
    // --- TAHAP 1: MEMBUAT ASESOR BARU ---
    const asesorName = `Asesor Uji Cypress ${Date.now()}`;
    const asesorEmail = `asesor-uji-${Date.now()}@example.com`;
    const updatedAsesorName = `${asesorName} (Updated)`;

    // Klik tombol "Tambah Asesor"
    cy.contains('button', 'Tambah Asesor').click();

    // Pastikan form tambah muncul
    cy.contains('h2', 'Tambah Asesor').should('be.visible');

    // Interaksi dengan custom dropdown skema
    cy.contains('button', 'Pilih Skema').click();
    // Pilih skema pertama yang tersedia di daftar
    cy.get('div[class*="overflow-y-auto"]').find('label').first().click();
    // Tutup dropdown dengan mengklik tombolnya lagi
    cy.contains('button', 'Pilih Skema').click();

    // Isi form
    cy.get('input[type="text"]').first().type(asesorName); // Input Nama Lengkap
    cy.get('input[type="email"]').type(asesorEmail);
    cy.get('input[type="date"]').first().type('2028-12-31'); // Sertifikat Teknis
    cy.get('input[type="date"]').last().type('2029-12-31'); // Sertifikat Asesor
    cy.get('input[type="text"]').last().type('081234567890'); // No. HP

    // Klik Simpan
    cy.contains('button', 'Simpan').click();

    // Verifikasi: Pastikan kembali ke halaman daftar dan asesor baru muncul
    cy.contains('h2', 'Daftar Asesor').should('be.visible');
    cy.contains('td', asesorName).should('be.visible');
    cy.contains('td', asesorEmail).should('be.visible');

    // --- TAHAP 2: MENGEDIT ASESOR ---

    // Cari baris yang berisi asesor baru, lalu klik tombol Edit
    cy.contains('tr', asesorEmail).within(() => {
      cy.contains('button', 'Edit').click();
    });

    // Pastikan form edit muncul
    cy.contains('h2', 'Edit Asesor').should('be.visible');

    // Hapus nama lama dan ketik nama baru
    cy.get('input[type="text"]').first().clear().type(updatedAsesorName);
    cy.get('input[type="date"]').first().type('2028-11-21'); 
    cy.get('input[type="date"]').last().type('2029-10-11'); // Sertifikat Asesor
    cy.get('input[type="text"]').last().type('081234562444'); // No. HP
    // Ubah pilihan skema: buka dropdown, klik skema kedua
    cy.contains('button', 'Pilih Skema').click();
    cy.get('div[class*="overflow-y-auto"]').find('label').eq(1).click(); // .eq(1) untuk elemen kedua
    cy.contains('button', 'Pilih Skema').click();

    // Klik Simpan
    cy.contains('button', 'Simpan').click();

    // Verifikasi: Pastikan nama sudah terupdate
    cy.contains('td', asesorName).should('not.exist'); // Nama lama harus hilang
    cy.contains('td', updatedAsesorName).should('be.visible'); // Nama baru harus muncul

    // --- TAHAP 3: MENGHAPUS ASESOR ---

    // Stub window.confirm agar otomatis mengklik "OK" saat dialog konfirmasi muncul
    cy.on('window:confirm', () => true);

    // Cari baris asesor yang sudah diupdate, lalu klik tombol Hapus
    cy.contains('tr', asesorEmail).within(() => {
      cy.contains('button', 'Hapus').click();
    });

    // Verifikasi: Pastikan asesor tersebut sudah tidak ada lagi di tabel
    cy.contains('td', asesorEmail).should('not.exist');
    cy.contains('Data asesor berhasil dihapus').should('be.visible');
  });
});