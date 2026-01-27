'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { checkAuth, logout } from '@/services/authService';

interface User {
    email: string;
}

export default function Header() {
    const [user, setUser] = useState<User | null>(null);
    const pathname = usePathname();

    useEffect(() => {
        async function loadUser() {
            const authenticatedUser = await checkAuth();
            setUser(authenticatedUser);
        }
        loadUser();
    }, [pathname]);

    const handleLogout = async () => {
        await logout();
        setUser(null);
    };

    return (
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
                        <a onClick={handleLogout}>Wyloguj</a>
                    </>
                )}
            </nav>
        </header>
    );
}
