// Standalone Puppeteer script for registration page E2E tests
// Run with: node tests/e2e/registration.puppeteer.js

const puppeteer = require('puppeteer');

(async () => {
  try {
    console.log('Launching browser...');
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    console.log('Navigating to registration page...');
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });

    // 1. Check page title and heading
    const title = await page.title();
    console.log('Page title:', title);
    const heading = await page.$eval('h2', el => el.textContent);
    console.log('Heading:', heading);

    // 2. Check that all form elements are present
    const nameField = await page.$('#name');
    const emailField = await page.$('#email');
    const passwordField = await page.$('#password');
    const passwordConfirmField = await page.$('#password_confirm');
    const submitButton = await page.$('button[type="submit"]');
    console.log('Form fields present:', !!nameField, !!emailField, !!passwordField, !!passwordConfirmField, !!submitButton);

    // 3. Try to submit the empty form
    await Promise.all([
      page.click('button[type="submit"]'),
   
    ]);
    const urlAfterEmpty = await page.url();
    console.log('URL after empty submit:', urlAfterEmpty);

    // 4. Fill out and submit the registration form
    const uniqueEmail = `test${Date.now()}@example.com`;
    await page.type('#name', 'Test User');
    await page.type('#email', uniqueEmail);
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'password123');
    await page.screenshot({ path: 'tests/e2e/screenshots/registration-form-filled.png' });
    await Promise.all([
      page.click('button[type="submit"]'),
      page.waitForNavigation({ waitUntil: 'networkidle0' })
    ]).catch(error => {
      console.log('Navigation may have failed or timed out:', error.message);
    });
    const currentUrl = await page.url();
    console.log('Current URL after form submission:', currentUrl);
    await page.screenshot({ path: 'tests/e2e/screenshots/after-registration.png' });

    // 5. Test mismatched passwords
    await page.goto('http://localhost:8000/register', { waitUntil: 'networkidle0' });
    await page.type('#name', 'Test User');
    await page.type('#email', 'test@example.com');
    await page.type('#password', 'password123');
    await page.type('#password_confirm', 'different-password');
    await Promise.all([
      page.click('button[type="submit"]'),
      page.waitForSelector('.text-red-600') // Wait for error message to appear
    ]);
    await page.screenshot({ path: 'tests/e2e/screenshots/password-mismatch.png' });
    const mismatchUrl = await page.url();
    console.log('URL after mismatched passwords:', mismatchUrl);

    await browser.close();
    console.log('Test completed successfully.');
  } catch (error) {
    console.error('Error during Puppeteer script:', error);
    process.exit(1);
  }
})();
