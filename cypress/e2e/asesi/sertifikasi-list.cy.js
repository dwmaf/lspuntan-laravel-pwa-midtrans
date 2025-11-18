describe('Daftar Sertifikasi (Asesi)', () => {
  // Helper untuk membuat tanggal dinamis
  const today = new Date();
  const yesterday = new Date(new Date().setDate(today.getDate() - 1));
  const tomorrow = new Date(new Date().setDate(today.getDate() + 1));
  const nextWeek = new Date(new Date().setDate(today.getDate() + 7));
  const lastWeek = new Date(new Date().setDate(today.getDate() - 7));

  beforeEach(() => {
    cy.loginAsAsesi();
  });

  it('Harus menampilkan tombol "Daftar" untuk sertifikasi yang sedang dibuka', () => {
    // Simulasikan sertifikasi yang sedang dibuka
    const mockSertification = {
      id: 1,
      skema: { nama_skema: 'Web Developer' },
      tgl_apply_dibuka: lastWeek.toISOString(),
      tgl_apply_ditutup: nextWeek.toISOString(),
      payment_instruction: { biaya: 500000 },
      created_at: today.toISOString(),
    };

    cy.intercept('GET', '/asesi/sertifikasi', (req) => {
      req.reply((res) => {
        res.body.props.sertifications = [mockSertification];
        res.body.props.asesi = {}; // Asesi belum mendaftar
        res.send(res.body);
      });
    }).as('getOpenSertifikasi');

    cy.visit('/asesi/sertifikasi');
    cy.wait('@getOpenSertifikasi');

    cy.contains('h3', 'Web Developer').parent().parent().within(() => {
      cy.contains('a', 'Daftar').should('be.visible').click();
    });

    // Verifikasi redirect ke halaman pendaftaran
    cy.url().should('include', '/asesi/sertifikasi/apply/1');
  });

  it('Harus menampilkan tombol "Lihat Status" untuk sertifikasi yang sudah didaftar', () => {
    // Simulasikan asesi sudah mendaftar
    const mockSertification = {
      id: 2,
      skema: { nama_skema: 'Network Engineer' },
      tgl_apply_dibuka: lastWeek.toISOString(),
      tgl_apply_ditutup: nextWeek.toISOString(),
      payment_instruction: { biaya: 750000 },
    };

    cy.intercept('GET', '/asesi/sertifikasi', (req) => {
      req.reply((res) => {
        res.body.props.sertifications = [mockSertification];
        res.body.props.asesi = { '2': { id: 99 } }; // Asesi sudah mendaftar sertifikasi ID 2
        res.send(res.body);
      });
    }).as('getAppliedSertifikasi');

    cy.visit('/asesi/sertifikasi');
    cy.wait('@getAppliedSertifikasi');

    cy.contains('h3', 'Network Engineer').parent().parent().within(() => {
      cy.contains('a', 'Lihat Status').should('be.visible').click();
    });

    // Verifikasi redirect ke halaman detail
    cy.url().should('include', '/asesi/sertifikasi/applied/2/99');
  });

  it('Harus menampilkan status "Ditutup" untuk sertifikasi yang sudah lewat', () => {
    const mockSertification = {
      id: 3,
      skema: { nama_skema: 'Data Analyst' },
      tgl_apply_dibuka: lastWeek.toISOString(),
      tgl_apply_ditutup: yesterday.toISOString(), // Pendaftaran sudah ditutup kemarin
      payment_instruction: { biaya: 600000 },
    };

    cy.intercept('GET', '/asesi/sertifikasi', (req) => {
      req.reply((res) => {
        res.body.props.sertifications = [mockSertification];
        res.body.props.asesi = {};
        res.send(res.body);
      });
    }).as('getClosedSertifikasi');

    cy.visit('/asesi/sertifikasi');
    cy.wait('@getClosedSertifikasi');

    cy.contains('h3', 'Data Analyst').parent().parent().within(() => {
      cy.contains('span', 'Pendaftaran Ditutup').should('be.visible');
      cy.get('a').should('not.exist'); // Pastikan tidak ada link/tombol yang bisa diklik
    });
  });

  it('Harus menampilkan status "Belum Dibuka" untuk sertifikasi yang akan datang', () => {
    const mockSertification = {
      id: 4,
      skema: { nama_skema: 'UI/UX Designer' },
      tgl_apply_dibuka: tomorrow.toISOString(), // Pendaftaran baru dibuka besok
      tgl_apply_ditutup: nextWeek.toISOString(),
      payment_instruction: { biaya: 400000 },
    };

    cy.intercept('GET', '/asesi/sertifikasi', (req) => {
      req.reply((res) => {
        res.body.props.sertifications = [mockSertification];
        res.body.props.asesi = {};
        res.send(res.body);
      });
    }).as('getSoonSertifikasi');

    cy.visit('/asesi/sertifikasi');
    cy.wait('@getSoonSertifikasi');

    cy.contains('h3', 'UI/UX Designer').parent().parent().within(() => {
      cy.contains('span', 'Belum Dibuka').should('be.visible');
      cy.get('a').should('not.exist');
    });
  });

  it('Harus menampilkan pesan jika tidak ada sertifikasi yang dibuka', () => {
    cy.intercept('GET', '/asesi/sertifikasi', (req) => {
      req.reply((res) => {
        res.body.props.sertifications = []; // Tidak ada sertifikasi
        res.body.props.asesi = {};
        res.send(res.body);
      });
    }).as('getEmptySertifikasi');

    cy.visit('/asesi/sertifikasi');
    cy.wait('@getEmptySertifikasi');

    cy.contains('Saat ini belum ada sertifikasi yang dibuka.').should('be.visible');
  });
});