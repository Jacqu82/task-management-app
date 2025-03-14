'use client';

import {useEffect, useState} from "react";
import Link from "next/link";
import './globals.css';
import {checkAuth, logout} from "@/services/authService";

export default function Layout({ children }: { children: React.ReactNode }) {
    interface User {
        email: string;
    }
    const [user, setUser] = useState<User | null>(null);

    useEffect(() => {
        async function loadUser() {
            const authenticatedUser = await checkAuth();
            setUser(authenticatedUser);
        }

        loadUser();
    }, []);

    const handleLogout = async () => {
        await logout();
        setUser(null);
    };

    return (
        <html lang="pl">
        <body>
        <header className="header">
            <nav className="nav">
                <Link href="/">Strona Główna</Link>
                {!user ? (
                    <>
                        <Link href="/register">Rejestracja</Link>
                        <Link href="/login">Logowanie</Link>
                    </>
                ) : (
                    <>
                        <span>Witaj, {user.email}</span>
                        <a onClick={() => handleLogout()}>Wyloguj</a>
                    </>
                )}
            </nav>
        </header>
        <main className="main">{children}</main>
        <footer className="footer">
            <p>&copy; 2025 Your Company</p>
        </footer>
        </body>
        </html>
    );
}
