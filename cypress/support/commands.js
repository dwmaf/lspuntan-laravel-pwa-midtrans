// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
// ... (komentar yang sudah ada)

/**
 * Logs in a user with a specific role by making a POST request.
 * @param {string} email - The user's email.
 * @param {string} password - The user's password.
 */
Cypress.Commands.add('login', (email, password) => {
  cy.visit('/login');

  cy.get('meta[name="csrf-token"]').then((meta) => {
    const csrfToken = meta.attr('content');
    cy.request({
      method: 'POST',
      url: '/login',
      form: true,
      body: {
        email: email,
        password: password,
        _token: csrfToken,
      },
    });
  });
});

/**
 * Logs in as an admin user.
 */
Cypress.Commands.add('loginAsAdmin', () => {
  cy.login('admin@g.c', '1234');
});
Cypress.Commands.add('loginAsAsesi', () => {
  cy.login('mahasiswa1@student.c', '1234'); 
});