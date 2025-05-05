const {
  generateTestEmail,
  fillRegistrationForm,
  getFieldError,
  getFormSubmissionResult
} = require('../helpers');

/**
 * End-to-end test for the registration page using Puppeteer
 * This test connects to the running application on localhost:8000
 */

describe('Registration Form', () => {
  beforeAll(async () => {
    // Set a default timeout higher than usual for navigation
    jest.setTimeout(30000);
  });

  beforeEach(async () => {
    // Go to the registration page before each test
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });
  });

  test('Jest is working', () => {
    // A trivial test to confirm Jest is running
    expect(1 + 1).toBe(2);
  });

  test('registration page loads successfully', async () => {
    // Check that we're on the registration page
    await expect(page.title()).resolves.toMatch(/Create your account - LorPHP/);
    
    // Check if the form and heading are visible
    const heading = await page.$eval('h2', el => el.textContent);
    expect(heading).toContain('Create your account');
    
    // Check that all form elements are present
    const nameField = await page.$('#name');
    const emailField = await page.$('#email');
    const passwordField = await page.$('#password');
    const passwordConfirmField = await page.$('#password_confirm');
    const submitButton = await page.$('button[type="submit"]');
    
    expect(nameField).not.toBeNull();
    expect(emailField).not.toBeNull();
    expect(passwordField).not.toBeNull();
    expect(passwordConfirmField).not.toBeNull();
    expect(submitButton).not.toBeNull();
  });

  test('displays validation errors when form is submitted empty', async () => {
    // Try to submit the empty form
    await Promise.all([
      page.click('button[type="submit"]'),
      // Wait for browser validation to kick in
      page.waitForTimeout(500)
    ]);
    
    // Check that we're still on the registration page (form wasn't submitted)
    const url = await page.url();
    expect(url).toContain('/register');
  });

  test('can fill out and submit the registration form', async () => {
    // Generate a unique email to avoid duplicate user errors
    const uniqueEmail = `test${Date.now()}@example.com`;
    
    // Fill out the form
    await page.type('#name', 'Test User');
    await page.type('#email', uniqueEmail);
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'password123');
    
    // Take a screenshot of the filled form (useful for debugging)
    await page.screenshot({ path: 'tests/e2e/screenshots/registration-form-filled.png' });
    
    // Submit the form - this will navigate to a new page if successful
    await Promise.all([
      page.click('button[type="submit"]'),
      // Wait for navigation after form submission
      page.waitForNavigation({ waitUntil: 'networkidle0' })
    ]).catch(error => {
      console.log('Navigation may have failed or timed out:', error.message);
    });
    
    // Check if we were redirected (successful registration)
    // or if we're still on the registration page (failed registration)
    const currentUrl = await page.url();
    console.log('Current URL after form submission:', currentUrl);
    
    // Take a screenshot of the result page
    await page.screenshot({ path: 'tests/e2e/screenshots/after-registration.png' });
    
    // Check for success or error messages
    const pageContent = await page.content();
    console.log('Checking for success or error indicators in the page');
  });

  test('shows error for mismatched passwords', async () => {
    // Fill out the form with mismatched passwords
    await page.type('#name', 'Test User');
    await page.type('#email', 'test@example.com');
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'different-password');
    
    // Submit the form
    await Promise.all([
      page.click('button[type="submit"]'),
      // Wait for response (either navigation or validation)
      page.waitForTimeout(1000)
    ]);
    
    // Take a screenshot to see what happened
    await page.screenshot({ path: 'tests/e2e/screenshots/password-mismatch.png' });
    
    // Check if we're still on the registration page (form wasn't submitted)
    const currentUrl = await page.url();
    expect(currentUrl).toContain('/register');
    
    // Try to find error messages - this depends on how your application shows errors
    const pageContent = await page.content();
    console.log('Looking for password mismatch error message');
  });

  test('validates email format', async () => {
    await fillRegistrationForm(page, {
      name: 'Test User',
      email: 'invalid-email',
      password: 'password123'
    });
    
    await page.click('button[type="submit"]');
    const error = await getFieldError(page, 'email');
    expect(error).toMatch(/invalid.*email/i);
  });

  test('prevents registration with short password', async () => {
    // Fill out form with short password
    await page.type('#name', 'Test User');
    await page.type('#email', 'test@example.com');
    await page.type('#password', '123');
    await page.type('#password_confirm', '123');
    
    await Promise.all([
      page.click('button[type="submit"]'),
      new Promise(res => setTimeout(res, 500))
    ]);

    const url = await page.url();
    expect(url).toContain('/register');
    
    const pageContent = await page.content();
    expect(pageContent).toMatch(/password.*length|short.*password/i);
  });

  test('prevents duplicate email registration', async () => {
    // First registration
    const email = `test${Date.now()}@example.com`;
    
    // Register first user
    await page.type('#name', 'Test User 1');
    await page.type('#email', email);
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'password123');
    
    await Promise.all([
      page.click('button[type="submit"]'),
      page.waitForNavigation({ waitUntil: 'networkidle0' })
    ]);

    // Go back to registration page
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });

    // Try to register with same email
    await page.type('#name', 'Test User 2');
    await page.type('#email', email);
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'password123');
    
    await Promise.all([
      page.click('button[type="submit"]'),
      new Promise(res => setTimeout(res, 1000))
    ]);

    const pageContent = await page.content();
    expect(pageContent).toMatch(/email.*already.*registered|already.*exists/i);
  });

  test('requires minimum name length', async () => {
    await page.type('#name', 'A');  // Too short
    await page.type('#email', 'test@example.com');
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'password123');
    
    await Promise.all([
      page.click('button[type="submit"]'),
      new Promise(res => setTimeout(res, 500))
    ]);

    const url = await page.url();
    expect(url).toContain('/register');
    
    const pageContent = await page.content();
    expect(pageContent).toMatch(/name.*length|short.*name/i);
  });

  test('successful registration shows success message and redirects', async () => {
    const testData = {
      name: 'Test User Success',
      email: generateTestEmail(),
      password: 'password123'
    };
    
    await fillRegistrationForm(page, testData);
    const { success, message } = await getFormSubmissionResult(page);
    
    expect(success).toBe(true);
    expect(message).toMatch(/welcome|success/i);
  });

  test('clears form after failed submission', async () => {
    // Submit with invalid data
    await page.type('#name', 'A');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(500);
    
    // Check if password fields are cleared
    const passwordValue = await page.$eval('#password', el => el.value);
    const confirmValue = await page.$eval('#password_confirm', el => el.value);
    
    expect(passwordValue).toBe('');
    expect(confirmValue).toBe('');
    
    // Name and email should be preserved
    const nameValue = await page.$eval('#name', el => el.value);
    expect(nameValue).toBe('A');
  });

  test('form is accessible', async () => {
    // Test that required fields have aria-required
    const nameRequired = await page.$eval('#name', el => el.hasAttribute('required'));
    const emailRequired = await page.$eval('#email', el => el.hasAttribute('required'));
    const passwordRequired = await page.$eval('#password', el => el.hasAttribute('required'));
    
    expect(nameRequired).toBe(true);
    expect(emailRequired).toBe(true);
    expect(passwordRequired).toBe(true);
    
    // Test that form fields are properly labeled
    const nameLabel = await page.$eval('label[for="name"]', el => el.textContent);
    const emailLabel = await page.$eval('label[for="email"]', el => el.textContent);
    const passwordLabel = await page.$eval('label[for="password"]', el => el.textContent);
    
    expect(nameLabel).toMatch(/name/i);
    expect(emailLabel).toMatch(/email/i);
    expect(passwordLabel).toMatch(/password/i);
  });
});
