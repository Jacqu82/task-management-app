import {expect, test} from '@playwright/test';

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
});

test.describe('Strona logowania', () => {
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

test.describe('Strona rejestracji', () => {
    test('Sprawdza, czy formularz rejestracji jest widoczny', async ({ page }) => {
        await page.goto('http://localhost:3000/register');

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

test.describe('Lista zadań', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('http://localhost:3000/tasks');
    });

    test('Wyświetla listę zadań', async ({ page }) => {
        await page.route('**/api/tasks?page=1', async route => {
            await route.fulfill({
                status: 200,
                body: JSON.stringify({
                    data: [
                        { id: 1, title: 'Test Task', description: 'Opis', statusName: 'Nowe', createdAt: '2025-03-16T12:00:00Z' }
                    ],
                    meta: { currentPage: 1, totalPages: 1 },
                    links: { self: '', first: '', last: '' }
                }),
            });
        });

        await page.reload();
        await expect(page.locator('text=Test Task')).toBeVisible();
    });

    test('Nawigacja do dodawania nowego zadania', async ({ page }) => {
        await page.click('text=+ Dodaj zadanie');
        await expect(page).toHaveURL('http://localhost:3000/tasks/new');
    });

    test('Usuwanie zadania', async ({ page }) => {
        await page.route('**/api/tasks/1', async route => {
            if (route.request().method() === 'DELETE') {
                await route.fulfill({ status: 204 });
            }
        });

        page.on('dialog', dialog => dialog.accept());

        await page.click('button >> nth=0');
        await expect(page.locator('text=Test Task')).not.toBeVisible();
    });
});

test.describe('Formularz dodawania zadania', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('http://localhost:3000/tasks/new');
    });

    test('Wyświetla formularz', async ({ page }) => {
        await expect(page.locator('h1')).toHaveText('Dodaj nowe zadanie');
        await expect(page.locator('input[name="title"]')).toBeVisible();
        await expect(page.locator('textarea[name="description"]')).toBeVisible();
        await expect(page.locator('button[type="submit"]')).toBeVisible();
    });

    test('Dodaje zadanie poprawnie', async ({ page }) => {
        await page.fill('input[name="title"]', 'Nowe zadanie');
        await page.fill('textarea[name="description"]', 'Opis zadania');

        await page.route('**/api/tasks', route => {
            route.fulfill({
                status: 201,
                body: JSON.stringify({ message: 'Task created successfully' }),
            });
        });

        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('http://localhost:3000/tasks');
    });
});