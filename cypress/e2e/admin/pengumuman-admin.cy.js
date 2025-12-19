describe('Manajemen Pengumuman Sertifikasi', () => {
  const sertificationId = 1;

  beforeEach(() => {
    cy.loginAsAdmin();

    cy.visit(`/admin/sertifikasi/${sertificationId}/announcement/index`);
  });


  it('Harus bisa menguggah semua jenis file yg diizinkan', () => {
    const initialContent = `Pengumuman dibuat oleh Cypress pada ${new Date().toLocaleString()}.`;
    const updatedContent = `Pengumuman ini telah di-update oleh Cypress pada ${new Date().toLocaleString()}.`;
    // tambah
    cy.contains('button', 'Tambah Pengumuman').click();
    cy.contains('h2', 'Buat Pengumuman').should('be.visible');
    cy.get('textarea').type(initialContent);
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-pdf.pdf');
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-docx.docx');
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-doc.doc');
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-pptx.pptx');
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-ppt.ppt');
    cy.contains('button', 'Preview').click();
    cy.contains('h2', 'Preview Pengumuman').should('be.visible');
    cy.contains('div', initialContent).should('be.visible');
    cy.contains('button', 'Tutup Preview').click();
    cy.contains('button', 'Simpan').click();
    cy.contains('h6', initialContent).should('be.visible');
    cy.contains('a', 'dummy-pdf').should('be.visible');
    cy.contains('a', 'dummy-docx').should('be.visible');
    cy.contains('a', 'dummy-doc').should('be.visible');
    cy.contains('a', 'dummy-pptx').should('be.visible');
    cy.contains('a', 'dummy-ppt').should('be.visible');
    // edit
    cy.contains('div.py-3', initialContent).within(() => {
      cy.contains('button', 'Edit').click();
    });
    cy.contains('h2', 'Edit Pengumuman').should('be.visible');
    cy.get('textarea').clear().type(updatedContent);
    cy.contains('dummy-pdf').parent('div.flex').within(() => {
      cy.get('button').click();
    });
    cy.contains('dummy-docx').parent('div.flex').within(() => {
      cy.get('button').click();
    });
    cy.contains('dummy-doc').parent('div.flex').within(() => {
      cy.get('button').click();
    });
    cy.contains('dummy-pptx').parent('div.flex').within(() => {
      cy.get('button').click();
    });
    cy.contains('dummy-ppt').parent('div.flex').within(() => {
      cy.get('button').click();
    });
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-xls.xls');
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-xlsx.xlsx');
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-jpg.jpg');
    cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-png.png');
    cy.contains('button', 'Preview').click();
    cy.contains('h2', 'Preview Pengumuman').should('be.visible');
    cy.contains('div', updatedContent).should('be.visible');
    cy.contains('button', 'Tutup Preview').click();
    cy.contains('button', 'Update').click();
    cy.contains('h6', initialContent).should('not.exist');
    cy.contains('div.py-3', updatedContent).should('be.visible').within(() => {
      cy.contains('a', 'dummy-pdf').should('not.exist');
      cy.contains('a', 'dummy-doc').should('not.exist');
      cy.contains('a', 'dummy-docx').should('not.exist');
      cy.contains('a', 'dummy-pptx').should('not.exist');
      cy.contains('a', 'dummy-ppt').should('not.exist');
      cy.contains('a', 'dummy-xls').should('be.visible');
      cy.contains('a', 'dummy-xlsx').should('be.visible');
      cy.contains('a', 'dummy-jpg').should('be.visible');
      cy.contains('a', 'dummy-png').should('be.visible');
      cy.contains('span', '(diedit)').should('be.visible');
    });
    // hapus
    cy.on('window:confirm', () => true);
    cy.contains('div.py-3', updatedContent).within(() => {
      cy.contains('button', 'Hapus').click();
    });
    cy.contains('h6', updatedContent).should('not.exist');
    cy.contains('Berhasil menghapus pengumuman').should('be.visible');
  });


});