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
        <header style={{
            padding: '20px',
            backgroundColor: '#333',
            textAlign: 'center',
            color: '#fff',
        }}>
            <nav>
                <Link href="/" style={{ margin: '0 15px', fontSize: '1.2rem', color: '#fff' }}>
                    Strona Główna
                </Link>
                {!user ? (
                    <>
                        <Link href="/register" style={{ margin: '0 15px', fontSize: '1.2rem', color: '#fff' }}>
                            Rejestracja
                        </Link>
                        <Link href="/login" style={{ margin: '0 15px', fontSize: '1.2rem', color: '#fff' }}>
                            Logowanie
                        </Link>
                    </>
                ) : (
                    <>
                        <span style={{ margin: '0 15px', fontSize: '1.2rem', color: '#fff' }}>
                            Witaj, {user.email}
                        </span>
                        <a
                            onClick={() => handleLogout()}
                            style={{ margin: '0 15px', fontSize: '1.2rem', color: '#fff', cursor: 'pointer' }}
                        >
                            Wyloguj
                        </a>
                    </>
                )}
            </nav>
        </header>

        <main>{children}</main>

        <footer style={{
            padding: '20px',
            backgroundColor: '#333',
            textAlign: 'center',
            color: '#fff',
        }}>
            <p>&copy; 2025 Your Company</p>
        </footer>
        </body>
        </html>
    );
}
