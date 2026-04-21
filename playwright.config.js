const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/e2e',
  testIgnore: ['**/*'],
  timeout: 30_000,
  use: {
    headless: true,
    browserName: 'chromium'
  }
});
