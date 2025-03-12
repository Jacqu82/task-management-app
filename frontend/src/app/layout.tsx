'use client';

import { useEffect, useState } from "react";
import Cookies from "js-cookie";
import Link from "next/link";
import './globals.css';

export default function Layout({ children }: { children: React.ReactNode }) {
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    useEffect(() => {
        const token = Cookies.get("jwt_token");
        setIsLoggedIn(!!token);
    }, []);

    const handleLogout = () => {
        Cookies.remove("jwt_token");
        setIsLoggedIn(false);
        window.location.href = "/";
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
                {!isLoggedIn && (
                    <>
                        <Link href="/register" style={{ margin: '0 15px', fontSize: '1.2rem', color: '#fff' }}>
                            Rejestracja
                        </Link>
                        <Link href="/login" style={{ margin: '0 15px', fontSize: '1.2rem', color: '#fff' }}>
                            Logowanie
                        </Link>
                    </>
                )}
                {isLoggedIn && (
                    <a
                        onClick={handleLogout}
                        style={{
                            margin: '0 15px',
                            fontSize: '1.2rem',
                            color: '#fff',
                            cursor: 'pointer',
                        }}
                    >
                        Wyloguj
                    </a>
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
