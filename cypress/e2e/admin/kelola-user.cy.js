describe('Manajemen Akun Pengguna', () => {
  // Sebelum setiap tes, login sebagai admin dan kunjungi halaman
  beforeEach(() => {
    cy.loginAsAdmin();
    cy.visit('/admin/users');
  });

  it('Harus bisa mencari dan memfilter pengguna', () => {
    // --- TES PENCARIAN ---
    // Asumsikan ada pengguna dengan nama 'Admin'
    cy.get('input[placeholder="Cari nama atau email..."]').type('Admin');
    cy.wait(600); // Tunggu debounce
    cy.get('tbody tr').should('have.length.at.least', 1);
    cy.get('tbody tr').first().should('contain.text', 'Admin');

    // --- TES FILTER LANJUTAN ---
    // Buka dropdown filter
    cy.get('button').contains('FunnelIcon').parent().click();

    // Filter berdasarkan role 'asesor'
    cy.contains('label', 'Role').parent().find('select').select('asesor');
    cy.contains('button', 'Apply Filter').click();

    // Verifikasi: URL dan isi tabel harus sesuai
    cy.url().should('include', 'role=asesor');
    cy.get('tbody tr').each(($row) => {
      cy.wrap($row).should('contain.text', 'asesor');
    });

    // --- TES RESET FILTER ---
    cy.get('button').contains('FunnelIcon').parent().click();
    cy.contains('button', 'Reset Filter').click();
    cy.url().should('not.include', 'role=asesor');
    cy.get('input[placeholder="Cari nama atau email..."]').should('have.value', '');
  });

  it('Harus bisa mengedit nama pengguna', () => {
    const newName = `User Diedit Cypress ${Date.now()}`;

    // Cari pengguna yang bukan admin (misalnya di baris kedua) dan klik Edit
    cy.get('tbody tr').eq(1).within(() => {
      cy.contains('button', 'Edit').click();
    });

    // Pastikan masuk ke mode edit
    cy.contains('h2', 'Manajemen Akun Pengguna').should('be.visible');

    // Ubah nama
    cy.get('form').within(() => {
      cy.get('input').first().clear().type(newName);
      cy.contains('button', 'Simpan').click();
    });

    // Verifikasi: Kembali ke daftar dan nama sudah terupdate
    cy.contains('td', newName).should('be.visible');
  });

  it('Harus bisa melakukan Ban dan Un-ban pengguna', () => {
    // Cari pengguna yang bisa di-ban (bukan admin saat ini)
    // Kita cari baris yang memiliki tombol 'Ban' atau 'Un-ban'
    cy.get('tbody tr').contains('button', 'Ban', { matchCase: false }).first().parents('tr').as('userRow');

    // --- TAHAP 1: BAN PENGGUNA ---
    cy.get('@userRow').within(() => {
      cy.contains('button', 'Ban').click();
    });

    // Verifikasi modal muncul
    cy.contains('h2', 'Apakah Anda yakin ingin menangguhkan akun ini?').should('be.visible');

    // Klik tombol konfirmasi di modal
    cy.get('div[role="dialog"]').within(() => {
      cy.contains('button', 'Ya, Tangguhkan Akun').click();
    });

    // Verifikasi: Tombol di baris pengguna sekarang berubah menjadi 'Un-ban'
    cy.get('@userRow').should('contain.text', 'Un-ban');

    // --- TAHAP 2: UN-BAN PENGGUNA ---
    cy.get('@userRow').within(() => {
      cy.contains('button', 'Un-ban').click();
    });

    // Verifikasi modal muncul
    cy.contains('h2', 'Apakah Anda yakin ingin mengaktifkan kembali akun ini?').should('be.visible');

    // Klik tombol konfirmasi di modal
    cy.get('div[role="dialog"]').within(() => {
      cy.contains('button', 'Ya, Aktifkan Kembali').click();
    });

    // Verifikasi: Tombol kembali menjadi 'Ban'
    cy.get('@userRow').should('contain.text', 'Ban');
  });
});