const { fillLoginForm, getFormSubmissionResult } = require('../helpers');

describe('Login Authentication', () => {
  beforeAll(async () => {
    jest.setTimeout(20000);
  });

  beforeEach(async () => {
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle0' });
  });

  test('shows error for invalid credentials', async () => {
    await fillLoginForm(page, { email: 'notfound@example.com', password: 'wrongpass' });
    await page.click('button[type="submit"]');
    await new Promise(res => setTimeout(res, 500));
    const pageContent = await page.content();
    expect(pageContent).toMatch(/invalid|incorrect|not found/i);
  });

  test('successful login redirects to dashboard', async () => {
    // You may need to register a user first or use a known test user
    const testUser = { email: 'testuser@example.com', password: 'password123' };
    await fillLoginForm(page, testUser);
    await Promise.all([
      page.click('button[type="submit"]'),
      page.waitForNavigation({ waitUntil: 'networkidle0' })
    ]);
    const url = await page.url();
    expect(url).toMatch(/dashboard|\/$/i);
  });
});
