const { page } = global;

/**
 * UI and accessibility tests for the registration page
 */
describe('Registration Form UI', () => {
  beforeAll(async () => {
    jest.setTimeout(30000);
  });

  beforeEach(async () => {
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });
  });

  test('registration page loads successfully', async () => {
    await expect(page.title()).resolves.toMatch(/Create your account - LorPHP/);
    const heading = await page.$eval('h2', el => el.textContent);
    expect(heading).toContain('Create your account');
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

  test('form is accessible', async () => {
    const nameRequired = await page.$eval('#name', el => el.hasAttribute('required'));
    const emailRequired = await page.$eval('#email', el => el.hasAttribute('required'));
    const passwordRequired = await page.$eval('#password', el => el.hasAttribute('required'));
    expect(nameRequired).toBe(true);
    expect(emailRequired).toBe(true);
    expect(passwordRequired).toBe(true);
    const nameLabel = await page.$eval('label[for="name"]', el => el.textContent);
    const emailLabel = await page.$eval('label[for="email"]', el => el.textContent);
    const passwordLabel = await page.$eval('label[for="password"]', el => el.textContent);
    expect(nameLabel).toMatch(/name/i);
    expect(emailLabel).toMatch(/email/i);
    expect(passwordLabel).toMatch(/password/i);
  });
});
