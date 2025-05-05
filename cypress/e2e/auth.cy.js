describe('Authentication', () => {
  beforeEach(() => {
    cy.fixture('users').as('users')
  })

  describe('Login', () => {
    it('should login successfully with valid credentials', function() {
      const { validUser } = this.users
      cy.login(validUser.email, validUser.password)
      
      cy.findByRole('heading', { name: /dashboard/i }).should('exist')
      cy.url().should('include', '/dashboard')
    })

    it('should show error with invalid credentials', function() {
      cy.visit('/login')
      cy.findByLabelText(/email/i).type('wrong@example.com')
      cy.findByLabelText(/password/i).type('wrongpassword')
      cy.findByRole('button', { name: /sign in/i }).click()
      
      cy.findByText(/invalid credentials/i).should('exist')
      cy.url().should('include', '/login')
    })

    it('should maintain session after login', function() {
      const { validUser } = this.users
      cy.login(validUser.email, validUser.password)
      cy.visit('/dashboard')
      cy.findByRole('heading', { name: /dashboard/i }).should('exist')
    })
  })
})
