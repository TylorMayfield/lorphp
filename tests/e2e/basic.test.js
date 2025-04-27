/**
 * Basic test for the registration page
 */

describe('Registration Page', () => {
  test('should load the registration page', async () => {
    // Navigate to the registration page
    await page.goto('http://localhost:8000/register');
    
    // Take a screenshot
    await page.screenshot({ path: 'tests/e2e/screenshots/registration-page.png' });
    
    // Check if the page title includes "Register"
    const title = await page.title();
    expect(title).toMatch(/Register/i);
  });
});
