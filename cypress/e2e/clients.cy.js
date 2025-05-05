describe('Client Management', () => {
  beforeEach(() => {
    cy.fixture('users').as('users')
  })

  describe('Client Creation', () => {
    beforeEach(function() {
      const { validUser } = this.users
      cy.login(validUser.email, validUser.password)
    })

    it('should create a new client successfully', () => {
      const newClient = {
        name: 'Test Client',
        email: 'client@example.com',
        phone: '123-456-7890',
        notes: 'Test client notes'
      }

      cy.createClient(newClient)
      
      cy.findByRole('heading', { name: /clients/i }).should('exist')
      cy.findByText(/client created successfully/i).should('exist')
      cy.findByText(newClient.name).should('exist')
    })

    it('should show validation errors for missing required fields', () => {
      cy.visit('/clients/create')
      cy.findByRole('button', { name: /create/i }).click()
      
      cy.findByText(/name is required/i).should('exist')
      cy.findByText(/email is required/i).should('exist')
    })
  })

  describe('Client Organization Isolation', () => {
    it('should not show clients from different organizations', function() {
      const { orgOneUser, orgTwoUser } = this.users
      
      // Login as org one user and create a client
      cy.login(orgOneUser.email, orgOneUser.password)
      cy.createClient({
        name: 'Org One Client',
        email: 'client@orgone.com'
      })
      cy.findByText('Org One Client').should('exist')
      
      // Logout
      cy.visit('/logout')
      
      // Login as org two user
      cy.login(orgTwoUser.email, orgTwoUser.password)
      cy.visit('/clients')
      
      // Should not see org one's client
      cy.findByText('Org One Client').should('not.exist')
      
      // Create org two's client
      cy.createClient({
        name: 'Org Two Client',
        email: 'client@orgtwo.com'
      })
      
      // Should see only org two's client
      cy.findByText('Org Two Client').should('exist')
      cy.findByText('Org One Client').should('not.exist')
    })

    it('should not allow direct access to clients from different organizations', function() {
      const { orgOneUser, orgTwoUser } = this.users
      let clientId;
      
      // Login as org one user and create a client
      cy.login(orgOneUser.email, orgOneUser.password)
      cy.createClient({
        name: 'Org One Client',
        email: 'client@orgone.com'
      })
      
      // Get the client ID from the URL
      cy.url().then(url => {
        clientId = url.split('/').pop()
        
        // Logout
        cy.visit('/logout')
        
        // Login as org two user
        cy.login(orgTwoUser.email, orgTwoUser.password)
        
        // Try to access org one's client directly
        cy.visit(`/clients/${clientId}`)
        
        // Should be redirected with an error
        cy.url().should('include', '/clients')
        cy.findByText(/client not found/i).should('exist')
      })
    })
  })
})
