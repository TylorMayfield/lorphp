describe('Landing Page UI', () => {
  beforeAll(async () => {
    jest.setTimeout(20000);
  });

  beforeEach(async () => {
    await page.goto('http://localhost:8000/', { waitUntil: 'networkidle0' });
  });

  test('landing page loads and shows main heading', async () => {
    const heading = await page.$eval('h1, h2', el => el.textContent);
    expect(heading).toMatch(/lorphp|welcome/i);
  });

  test('navigation links are present', async () => {
    const navLinks = await page.$$eval('nav a', els => els.map(e => e.textContent.toLowerCase()));
    expect(navLinks).toEqual(expect.arrayContaining(['login', 'register']));
  });

  test('footer is visible', async () => {
    const footer = await page.$eval('footer', el => el.textContent);
    expect(footer).toMatch(/lorphp/i);
  });
});
