// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************

Cypress.Commands.add('login', (email, password) => {
  cy.visit('/login')
  cy.findByLabelText(/email/i).type(email)
  cy.findByLabelText(/password/i).type(password)
  cy.findByRole('button', { name: /sign in/i }).click()
})

Cypress.Commands.add('register', (userData) => {
  cy.visit('/register')
  cy.findByLabelText(/name/i).type(userData.name)
  cy.findByLabelText(/email/i).type(userData.email)
  cy.findByLabelText(/^password$/i).type(userData.password)
  cy.findByLabelText(/confirm password/i).type(userData.password)
  cy.findByRole('button', { name: /register/i }).click()
})

Cypress.Commands.add('createClient', (clientData) => {
  cy.visit('/clients/create')
  cy.findByLabelText(/name/i).type(clientData.name)
  cy.findByLabelText(/email/i).type(clientData.email)
  cy.findByLabelText(/phone/i).type(clientData.phone || '')
  cy.findByLabelText(/notes/i).type(clientData.notes || '')
  cy.findByRole('button', { name: /create/i }).click()
})
