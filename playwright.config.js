const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/e2e',
  timeout: 30_000,
  use: {
    baseURL: 'http://127.0.0.1:8000',
    headless: true,
    browserName: 'chromium'
  },
  webServer: {
    command: 'php -S 127.0.0.1:8000 -t public public/index.php',
    url: 'http://127.0.0.1:8000/healthz',
    reuseExistingServer: true,
    timeout: 30_000
  }
});
