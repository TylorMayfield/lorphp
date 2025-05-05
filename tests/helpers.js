/**
 * Test helper functions
 */

/**
 * Generate a random email for testing
 * @returns {string} A random email address
 */
const generateTestEmail = () => `test${Date.now()}${Math.random().toString(36).substring(2)}@example.com`;

/**
 * Fill out the registration form
 * @param {Page} page Puppeteer page object
 * @param {Object} data Form data
 */
const fillRegistrationForm = async (page, data) => {
    if (data.name) await page.type('#name', data.name);
    if (data.email) await page.type('#email', data.email);
    if (data.password) {
        await page.type('#password', data.password);
        await page.type('#password_confirm', data.password);
    }
};

/**
 * Fill out the login form
 * @param {Page} page Puppeteer page object
 * @param {Object} data Login data
 */
const fillLoginForm = async (page, data) => {
    if (data.email) await page.type('#email', data.email);
    if (data.password) await page.type('#password', data.password);
};

/**
 * Get form field error message
 * @param {Page} page Puppeteer page object
 * @param {string} fieldId The field ID to check for errors
 * @returns {Promise<string|null>} The error message or null if no error
 */
const getFieldError = async (page, fieldId) => {
    const error = await page.$eval(`#${fieldId} + .error-message`, el => el.textContent).catch(() => null);
    return error;
};

/**
 * Wait for and check form submission result
 * @param {Page} page Puppeteer page object
 * @returns {Promise<{success: boolean, message: string}>}
 */
const getFormSubmissionResult = async (page) => {
    await new Promise(res => setTimeout(res, 500));
    const success = !(await page.url()).includes('/register');
    const message = await page.$eval('.alert', el => el.textContent).catch(() => '');
    return { success, message };
};

module.exports = {
    generateTestEmail,
    fillRegistrationForm,
    fillLoginForm,
    getFieldError,
    getFormSubmissionResult
};
