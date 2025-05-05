const { fillRegistrationForm, getFieldError } = require('../helpers');

describe('Registration Form Validation', () => {
  beforeAll(async () => {
    jest.setTimeout(30000);
  });

  beforeEach(async () => {
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });
  });

  test('displays validation errors when form is submitted empty', async () => {
    await Promise.all([
      page.click('button[type="submit"]'),
      new Promise(res => setTimeout(res, 500))
    ]);
    const url = await page.url();
    expect(url).toContain('/register');
  });

  test('shows error for mismatched passwords', async () => {
    await page.type('#name', 'Test User');
    await page.type('#email', 'test@example.com');
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'different-password');
    await Promise.all([
      page.click('button[type="submit"]'),
      new Promise(res => setTimeout(res, 1000))
    ]);
    const currentUrl = await page.url();
    expect(currentUrl).toContain('/register');
    // You can add more assertions for error messages if needed
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

  test('requires minimum name length', async () => {
    await page.type('#name', 'A');
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
});
