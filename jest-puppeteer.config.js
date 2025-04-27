// jest-puppeteer.config.js
module.exports = {
  server: {
    command: 'php -S localhost:8000 -t public',
    port: 8000,
    launchTimeout: 20000,
    debug: true
  },
  launch: {
    headless: true
  }
};
