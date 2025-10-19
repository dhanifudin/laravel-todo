import { test, expect } from '@playwright/test';

function uniqueEmail() {
  const ts = Date.now();
  return `e2e-${ts}@example.test`;
}

test.describe('todos CRUD (E2E)', () => {
  // Helper: create a test user via API and perform a UI login
  async function createAndLogin(page, request) {
    const email = uniqueEmail();
    const password = 'password';

    const apiRes = await request.post('/_e2e/create-user', {
      data: { name: 'E2E User', email, password },
    });

    if (apiRes.status() !== 200) {
      throw new Error(`Failed to create test user: ${apiRes.status()} ${await apiRes.text()}`);
    }

    await page.goto('/login');
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', password);
    await Promise.all([
      page.click('button:has-text("Log in")'),
      page.waitForLoadState('networkidle'),
    ]);
  }

  test('create a todo', async ({ page, request }) => {
    await createAndLogin(page, request);
    await page.goto('/todos');

    const todoText = 'Create todo ' + Math.random().toString(36).slice(2, 7);
    await page.fill('form:has(input[name="name"]) input[name="name"]', todoText);
    await Promise.all([
      page.click('form:has(input[name="name"]) button:has-text("Add Todo")'),
      page.waitForLoadState('networkidle'),
    ]);

    await expect(page.locator('p', { hasText: todoText })).toBeVisible();
  });

  test('complete (mark done) a todo', async ({ page, request }) => {
    await createAndLogin(page, request);
    await page.goto('/todos');

    const todoText = 'Complete todo ' + Math.random().toString(36).slice(2, 7);
    await page.fill('form:has(input[name="name"]) input[name="name"]', todoText);
    await Promise.all([
      page.click('form:has(input[name="name"]) button:has-text("Add Todo")'),
      page.waitForLoadState('networkidle'),
    ]);

    const todoLocator = page.locator('p', { hasText: todoText });
    await expect(todoLocator).toBeVisible();

    const todoContainer = todoLocator.locator('..').locator('..');
    const completeButton = todoContainer.locator('form').first().locator('button');
    await Promise.all([
      completeButton.click(),
      page.waitForLoadState('networkidle'),
    ]);

    await expect(page.locator('p.line-through', { hasText: todoText })).toBeVisible();
  });

  test('mark a todo as undone', async ({ page, request }) => {
    await createAndLogin(page, request);
    await page.goto('/todos');

    const todoText = 'Undone todo ' + Math.random().toString(36).slice(2, 7);
    await page.fill('form:has(input[name="name"]) input[name="name"]', todoText);
    await Promise.all([
      page.click('form:has(input[name="name"]) button:has-text("Add Todo")'),
      page.waitForLoadState('networkidle'),
    ]);

    const todoLocator = page.locator('p', { hasText: todoText });
    await expect(todoLocator).toBeVisible();

    const todoContainer = todoLocator.locator('..').locator('..');
    const toggleButton = todoContainer.locator('form').first().locator('button');

    // Mark complete
    await Promise.all([
      toggleButton.click(),
      page.waitForLoadState('networkidle'),
    ]);
    await expect(page.locator('p.line-through', { hasText: todoText })).toBeVisible();

    // Toggle back to undone
    await Promise.all([
      toggleButton.click(),
      page.waitForLoadState('networkidle'),
    ]);

    // Ensure the line-through class is removed and the todo is visible
    await expect(page.locator('p.line-through', { hasText: todoText })).toHaveCount(0);
    await expect(page.locator('p', { hasText: todoText })).toBeVisible();
  });

  test('delete a todo', async ({ page, request }) => {
    await createAndLogin(page, request);
    await page.goto('/todos');

    const todoText = 'Delete todo ' + Math.random().toString(36).slice(2, 7);
    await page.fill('form:has(input[name="name"]) input[name="name"]', todoText);
    await Promise.all([
      page.click('form:has(input[name="name"]) button:has-text("Add Todo")'),
      page.waitForLoadState('networkidle'),
    ]);

    const todoLocator = page.locator('p', { hasText: todoText });
    await expect(todoLocator).toBeVisible();

    const todoContainer = todoLocator.locator('..').locator('..');
    const deleteButton = todoContainer.locator('form').nth(1).locator('button');
    await Promise.all([
      deleteButton.click(),
      page.waitForLoadState('networkidle'),
    ]);

    await expect(page.locator('p', { hasText: todoText })).toHaveCount(0);
  });
});
