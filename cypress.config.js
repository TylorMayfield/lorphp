const { defineConfig } = require('cypress')

module.exports = defineConfig({
  e2e: {
    baseUrl: `http://localhost:${process.env.PORT || 8000}`,
    supportFile: 'cypress/support/e2e.js',
    screenshotsFolder: 'cypress/screenshots',
    videosFolder: 'cypress/videos',
    viewportWidth: 1280,
    viewportHeight: 720,
    defaultCommandTimeout: 10000,
  },
})
