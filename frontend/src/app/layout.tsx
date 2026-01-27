import './globals.css';
import Header from './header';

import type { Metadata } from 'next';

export const metadata: Metadata = {
    title: 'Task Management App',
    description: 'Aplikacja do zarządzania zadaniami',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
    return (
        <html lang="pl">
        <body>
        <Header />
        <main className="main">{children}</main>
        <footer className="footer">
            <p>&copy; 2025 Your Company</p>
        </footer>
        </body>
        </html>
    );
}
