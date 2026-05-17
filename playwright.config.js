const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/E2E',
  testIgnore: ['**/*'],
  timeout: 30_000,
  use: {
    headless: true,
    browserName: 'chromium'
  }
});
