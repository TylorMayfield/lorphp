describe('Login Page UI', () => {
  beforeAll(async () => {
    jest.setTimeout(20000);
  });

  beforeEach(async () => {
    await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle0' });
  });

  test('login page loads and shows heading', async () => {
    const heading = await page.$eval('h2', el => el.textContent);
    expect(heading).toMatch(/login|sign in/i);
  });

  test('login form fields are present', async () => {
    const emailField = await page.$('#email');
    const passwordField = await page.$('#password');
    const submitButton = await page.$('button[type="submit"]');
    expect(emailField).not.toBeNull();
    expect(passwordField).not.toBeNull();
    expect(submitButton).not.toBeNull();
  });

  test('navigation to registration is available', async () => {
    const registerLink = await page.$eval('a[href="/register"]', el => el.textContent);
    expect(registerLink).toMatch(/register/i);
  });
});
