describe("Manajemen Tugas Asesmen", () => {
    const sertificationId = 2;
    const taskContent = `Ini adalah rincian tugas asesmen yang dibuat oleh Cypress pada ${new Date().toLocaleString()}.`;

    beforeEach(() => {
        cy.loginAsAdmin();
        cy.visit(`/admin/sertifikasi/${sertificationId}/assessment/edit`);
        // cy.get(
        //     'h3:contains("Edit Rincian"), p:contains("Asesor belum memberikan"), [data-cy="edit-button"]',
        //     { timeout: 10000 }
        // ).should("be.visible");
    });

    it("Harus bisa membuat/mengedit, mempublikasikan, dan melampirkan file pada tugas asesmen", () => {
        const taskContent = `Ini adalah rincian tugas asesmen yang dibuat oleh Cypress pada ${new Date().toLocaleString()}.`;
        cy.get("body").then(($body) => {
            if ($body.find('[data-cy="edit-button"]').length > 0) {
                cy.log(
                    'Tombol "edit-button" ditemukan, menjalankan proses edit'
                );

                cy.get('[data-cy="edit-button"]').click();
                cy.contains("h3", "Edit Rincian Tugas Asesmen").should(
                    "be.visible"
                );
                cy.get("textarea").clear().type(taskContent);

                cy.get('input[type="datetime-local"]').type("2028-12-31T23:59");
                cy.get('input[type="file"]').selectFile(
                    "cypress/fixtures/dummy.pdf"
                );
                cy.get("#is_published").click();
                cy.contains("button", "Simpan").click();
                cy.contains("h3", "Edit Rincian Tugas Asesmen").should(
                    "not.exist"
                );
                cy.contains(taskContent.split(".")[0]).should("be.visible");
                cy.contains("Batas Akhir Pengumpulan :")
                    .parent()
                    .should("contain.text", "2028-12-31");
                cy.contains("a", "dummy").should("be.visible");
            } else {
                cy.get('[data-cy="edit-button"]').click();
                cy.log(
                    'Tombol "edit-button" tidak ditemukan, berarti langsung mode edit'
                );
                cy.contains("h3", "Edit Rincian Tugas Asesmen").should(
                    "be.visible"
                );
                cy.get("textarea").clear().type(taskContent);

                cy.get('input[type="datetime-local"]').type("2028-12-31T23:59");
                cy.get('input[type="file"]').selectFile(
                    "cypress/fixtures/dummy.pdf"
                );
                cy.get("#is_published").parent().click();
                cy.contains("button", "Simpan").click();
                cy.contains("h3", "Edit Rincian Tugas Asesmen").should(
                    "not.exist"
                );
                cy.contains(taskContent.split(".")[0]).should("be.visible");
                cy.contains("Batas Akhir Pengumpulan :")
                    .parent()
                    .should("contain.text", "2028-12-31");
                cy.contains("a", "dummy").should("be.visible");
            }
        });

        
        cy.log(
            "Pengecekan edit selesai, melanjutkan ke langkah tes berikutnya."
        );
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
