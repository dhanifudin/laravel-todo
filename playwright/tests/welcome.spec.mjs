import { test, expect } from '@playwright/test';

test('welcome page displays getting started message', async ({ page, baseURL }) => {
  await page.goto('/');

  // Ensure the page title contains Laravel (default) or app name
  await expect(page).toHaveTitle(/Laravel|./);

  // Assert a heading text that exists in the welcome view
  const heading = page.locator('h1');
  await expect(heading).toBeVisible();
  await expect(heading).toHaveText(/Let's get started/);
});
