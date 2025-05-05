describe('Registration', () => {
  beforeEach(() => {
    cy.visit('/register')
  })

  it('should display registration form', () => {
    cy.get('form').should('exist')
    cy.get('input[name="name"]').should('exist')
    cy.get('input[name="email"]').should('exist')
    cy.get('input[name="password"]').should('exist')
    cy.get('input[name="password_confirmation"]').should('exist')
  })

  it('should register a new user successfully', () => {
    const userData = {
      name: 'Test User',
      email: 'test@example.com',
      password: 'password123'
    }

    cy.register(userData)
    cy.url().should('include', '/dashboard')
    cy.get('.toast').should('contain', 'Registration successful')
  })

  it('should show error on password mismatch', () => {
    cy.get('input[name="name"]').type('Test User')
    cy.get('input[name="email"]').type('test@example.com')
    cy.get('input[name="password"]').type('password123')
    cy.get('input[name="password_confirmation"]').type('different-password')
    cy.get('form').submit()
    
    cy.get('.error-message').should('contain', 'Passwords do not match')
    cy.url().should('include', '/register')
  })

  it('should prevent duplicate email registration', () => {
    const userData = {
      name: 'Test User',
      email: 'existing@example.com',
      password: 'password123'
    }

    // First registration
    cy.register(userData)
    cy.visit('/register')
    
    // Try to register again with same email
    cy.register(userData)
    cy.get('.error-message').should('contain', 'Email already exists')
    cy.url().should('include', '/register')
  })
})
