describe('Dashboard Page UI', () => {
  beforeAll(async () => {
    jest.setTimeout(20000);
  });

  beforeEach(async () => {
    // You may need to log in first if dashboard is protected
    await page.goto('http://localhost:8000/dashboard', { waitUntil: 'networkidle0' });
  });

  test('dashboard loads and shows heading', async () => {
    const heading = await page.$eval('h1, h2', el => el.textContent);
    expect(heading).toMatch(/dashboard|welcome/i);
  });

  test('shows user info or stats', async () => {
    const content = await page.content();
    expect(content).toMatch(/user|stats|activity|welcome/i);
  });

  test('navigation bar is present', async () => {
    const nav = await page.$('nav');
    expect(nav).not.toBeNull();
  });
});
