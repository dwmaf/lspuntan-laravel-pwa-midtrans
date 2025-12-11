describe('Halaman Login', () => {

  
  beforeEach(() => {
    
    cy.visit('/login');
  });

  it('Harus berhasil login sebagai asesi dengan kredensial yang benar', () => {
    
    const userEmail = 'mahasiswa1@student.c';
    const userPassword = '1234';

    cy.get('#email').type(userEmail);
    cy.get('#password').type(userPassword);
    cy.contains('button','Log in').click();
    cy.url().should('include', '/asesi/dashboard');
    cy.contains('Dashboard').should('be.visible');
  });

  it('Harus berhasil login sebagai admin dengan kredensial yang benar', () => {
    
    const userEmail = 'admin@g.c';
    const userPassword = '1234';

    cy.get('#email').type(userEmail);
    cy.get('#password').type(userPassword);
    cy.contains('button','Log in').click();
    cy.url().should('include', '/admin/dashboard');
    cy.contains('Dashboard').should('be.visible');
  });

  it('Harus berhasil login sebagai asesor dengan kredensial yang benar', () => {
    
    const userEmail = 'bomo@asesor.c';
    const userPassword = '1234';

    cy.get('#email').type(userEmail);
    cy.get('#password').type(userPassword);
    cy.contains('button','Log in').click();
    cy.url().should('include', '/admin/dashboard');
    cy.contains('Dashboard').should('be.visible');
  });

  it('Harus menampilkan pesan error dengan kredensial yang salah', () => {
    cy.get('#email').type('email-salah@example.com');
    cy.get('#password').type('password-yang-salah.');
    cy.contains('button','Log in').click();
    cy.url().should('include', '/login');
    cy.contains('Identitas tersebut tidak cocok dengan data kami.').should('be.visible');
  });

});