const { test, expect } = require('@playwright/test');

test('health endpoint responds', async ({ page }) => {
  const response = await page.goto('/healthz');
  expect(response).not.toBeNull();
  expect(response.status()).toBe(200);
  await expect(page.locator('body')).toContainText('ok');
});
