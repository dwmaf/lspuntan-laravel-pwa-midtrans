describe("Manajemen Tugas Asesmen", () => {
    const sertificationId = 2;
    const taskContent = `Ini adalah rincian tugas asesmen yang dibuat oleh Cypress pada ${new Date().toLocaleString()}.`;

    beforeEach(() => {
        cy.loginAsAdmin();
        cy.visit(`/admin/sertifikasi/${sertificationId}/assessment/edit`);
    });
    const createInstruction = (content, deadline) => {
        cy.contains('button', 'Buat Asesmen').click();
        cy.contains('h3', 'Buat Rincian Tugas Asesmen').should('be.visible');
        cy.contains('button', 'Batal').click();
        cy.contains('h3', 'Buat Rincian Tugas Asesmen').should('not.exist');
        cy.contains('button', 'Buat Asesmen').should('be.visible').click();
        cy.contains('h3', 'Buat Rincian Tugas Asesmen').should('be.visible');
        cy.get('textarea').clear().type(content);
        cy.get('div').contains('label', 'Batas Pengumpulan').parent().find('input').clear().type(deadline);
        cy.get('input[type="file"]').selectFile("cypress/fixtures/dummy-jpg.jpg");
        cy.get('input[type="file"]').selectFile("cypress/fixtures/dummy-png.png");
        cy.get('input[type="file"]').selectFile("cypress/fixtures/dummy-jpeg.jpeg");
        cy.get('input[type="file"]').selectFile("cypress/fixtures/dummy-docx.docx");
        cy.get('input[type="file"]').selectFile("cypress/fixtures/dummy-doc.doc");
        cy.contains('button', 'Preview').click();
        cy.contains('h2', 'Preview Tugas Asesmen').should('be.visible');
        cy.contains('div', content).should('be.visible');
        cy.contains('button', 'Tutup Preview').click();
        cy.contains('button', 'Simpan').click();
        cy.contains('h3', 'Buat Rincian Tugas Asesmen').should('not.exist');
        cy.contains('div', content).should('be.visible');
        cy.contains('a', 'dummy-jpg').should('be.visible');
        cy.contains('a', 'dummy-jpeg').should('be.visible');
        cy.contains('a', 'dummy-png').should('be.visible');
        cy.contains('a', 'dummy-docx').should('be.visible');
        cy.contains('a', 'dummy-doc').should('be.visible');
    };

    const editInstruction = (content, deadline) => {
        cy.contains('button', 'Edit').click();
        cy.contains('h3', 'Edit Rincian Tugas Asesmen').should('be.visible');
        cy.contains('button', 'Batal').click();
        cy.contains('h3', 'Edit Rincian Tugas Asesmen').should('not.exist');
        cy.contains('button', 'Edit').should('be.visible').click();
        cy.contains('h3', 'Edit Rincian Tugas Asesmen').should('be.visible');
        cy.get('textarea').clear().type(content);
        cy.get('div').contains('label', 'Batas Pengumpulan').parent().find('input').clear().type(deadline);
        cy.contains('dummy-jpg').parent('div.flex').within(() => {
            cy.get('button').click();
        });
        cy.contains('dummy-png').parent('div.flex').within(() => {
            cy.get('button').click();
        });
        cy.contains('dummy-jpeg').parent('div.flex').within(() => {
            cy.get('button').click();
        });
        cy.contains('dummy-docx').parent('div.flex').within(() => {
            cy.get('button').click();
        });
        cy.contains('dummy-doc').parent('div.flex').within(() => {
            cy.get('button').click();
        });
        cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-pdf.pdf');
        cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-xls.xls');
        cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-xlsx.xlsx');
        cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-ppt.ppt');
        cy.get('input[type="file"]').selectFile('cypress/fixtures/dummy-pptx.pptx');
        cy.contains('button', 'Preview').click();
        cy.contains('h2', 'Preview Tugas Asesmen').should('be.visible');
        cy.contains('div', content).should('be.visible');
        cy.contains('button', 'Tutup Preview').click();
        cy.contains('button', 'Simpan').click();
        cy.contains('h3', 'Edit Rincian Tugas Asesmen').should('not.exist');
        cy.contains('div', content).should('be.visible');
        cy.contains('span', 'Diedit').should('be.visible');
        cy.contains('a', 'dummy-jpg').should('not.exist');
        cy.contains('a', 'dummy-jpeg').should('not.exist');
        cy.contains('a', 'dummy-png').should('not.exist');
        cy.contains('a', 'dummy-docx').should('not.exist');
        cy.contains('a', 'dummy-doc').should('not.exist');
        cy.contains('a', 'dummy-pdf').should('be.visible');
        cy.contains('a', 'dummy-xls').should('be.visible');
        cy.contains('a', 'dummy-xlsx').should('be.visible');
        cy.contains('a', 'dummy-ppt').should('be.visible');
        cy.contains('a', 'dummy-pptx').should('be.visible');
    };

    const deleteInstruction = () => {
        cy.contains('button', 'Edit').click();
        cy.contains('h3', 'Edit Rincian Tugas Asesmen').should('be.visible');
        cy.contains('button', 'Hapus Asesmen').click();
        cy.on('window:confirm', () => true);
        cy.contains('Asesor belum memberikan rincian tugas asesmen').should('be.visible');
        cy.contains('button', 'Buat Asesmen').should('be.visible');
    };

    const ensureNoInstruction = () => {
        cy.wait(500);
        cy.get('#view-asesmen', { timeout: 5000 }).then(($viewAsesmen) => {
            if ($viewAsesmen.find('button:contains("Edit")').length > 0) {
                deleteInstruction();
            }
        });
    };

    it("Harus bisa membuat/mengedit, mempublikasikan, dan melampirkan file pada tugas asesmen", () => {
        ensureNoInstruction();
        const timestamp = new Date().toLocaleString();
        const taskContent = `Ini adalah rincian tugas asesmen yang dibuat oleh Cypress pada ${timestamp}.`;
        const createDeadline = '2029-12-15T23:59';
        createInstruction(taskContent, createDeadline);
        cy.contains('dd', '15 Desember 2029 , 23:59 WIB').should('be.visible');
        const editContent = `Tugas Asesmen DIPERBARUI oleh Cypress pada ${timestamp}.`;
        const editDeadline = '2029-12-18T18:00';
        editInstruction(editContent, editDeadline);
        cy.contains('dd', '18 Desember 2029 , 18:00 WIB').should('be.visible');
        deleteInstruction();

    });

    it("Harus bisa melihat tugas yang dikumpulkan oleh asesi dan kembali ke daftar", () => {
        cy.get("table").should("be.visible");
        cy.get("body").then(($body) => {
            if ($body.find('button:contains("Lihat")').length > 0) {
                cy.get("tbody tr").contains("button", "Lihat").first().click();

                cy.contains("h3", "Tugas Terkumpul -").should("be.visible");
                cy.get('a[href*="/storage/"]').should(
                    "have.length.at.least",
                    1
                );

                cy.contains("button", "Kembali ke Daftar").click();
                cy.get("table").should("be.visible");
                cy.contains("h3", "Tugas Terkumpul -").should("not.exist");
            } else {
                cy.contains(
                    "Belum ada pendaftar yang memenuhi kriteria"
                ).should("be.visible");
            }
        });
    });
});
