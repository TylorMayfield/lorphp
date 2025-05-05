const { generateTestEmail, fillRegistrationForm, getFormSubmissionResult } = require('../helpers');

describe('Registration Success & Duplicate Handling', () => {
  beforeAll(async () => {
    jest.setTimeout(30000);
  });

  beforeEach(async () => {
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });
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

  test('prevents duplicate email registration', async () => {
    const email = generateTestEmail();
    // Register first user
    await fillRegistrationForm(page, {
      name: 'Test User 1',
      email,
      password: 'password123'
    });
    await page.click('button[type="submit"]');
    await page.waitForNavigation({ waitUntil: 'networkidle0' });
    // Go back to registration page
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });
    // Try to register with same email
    await fillRegistrationForm(page, {
      name: 'Test User 2',
      email,
      password: 'password123'
    });
    await page.click('button[type="submit"]');
    await new Promise(res => setTimeout(res, 1000));
    const pageContent = await page.content();
    expect(pageContent).toMatch(/email.*already.*registered|already.*exists/i);
  });
});
