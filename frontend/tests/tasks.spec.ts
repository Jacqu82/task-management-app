import { test, expect } from '@playwright/test';

test.describe('Strona główna', () => {
    test('Posiada poprawny tag h1', async ({ page }) => {
        await page.goto('http://localhost:3000');
        await expect(page.locator('h1')).toHaveText('Task Management App');
    });

    test('Ma poprawny opis', async ({ page }) => {
        await page.goto('http://localhost:3000');
        await expect(page.locator('p.page-module___8aEwW__description')).toHaveText('Zarządzaj swoimi zadaniami efektywnie');
    });

    test('Pokazuje nawigację na stronie głównej', async ({ page }) => {
        await page.goto('http://localhost:3000');
        const loginLink = page.locator('a.page-module___8aEwW__navLink[href="/login"]');
        const registerLink = page.locator('a.page-module___8aEwW__navLink[href="/register"]');
        await expect(loginLink).toBeVisible();
        await expect(registerLink).toBeVisible();
    });

    test('Sprawdza, czy formularz logowania jest widoczny', async ({ page }) => {
        await page.goto('http://localhost:3000/login');

        const form = await page.locator('form');
        await expect(form).toBeVisible();

        const emailField = await page.locator('input[name="email"]');
        const passwordField = await page.locator('input[name="password"]');
        await expect(emailField).toBeVisible();
        await expect(passwordField).toBeVisible();

        const submitButton = await page.locator('button[type="submit"]');
        await expect(submitButton).toBeVisible();
    });
});

