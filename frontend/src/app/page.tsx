'use client';

import Link from "next/link";
import styles from "./page.module.css";
import {checkAuth} from "@/services/authService";
import {useEffect, useState} from "react";

export default function Home() {
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

    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Task Management App</h1>
            <p className={styles.description}>Zarządzaj swoimi zadaniami efektywnie</p>
            <>
                <nav>
                    <ul className={styles.navList}>
                        {!user ? (
                            <>
                                <li className={styles.navItem}>
                                    <Link href="/login" className={styles.navLink}>Logowanie</Link>
                                </li>
                                <li className={styles.navItem}>
                                    <Link href="/register" className={styles.navLink}>Rejestracja</Link>
                                </li>
                            </>
                        ) : (
                            <>
                                <li className={styles.navItem}>
                                    <Link href="/tasks" className={styles.navLink}>Zadania</Link>
                                </li>
                            </>
                        )}
                    </ul>
                </nav>
            </>
        </main>
    );
}
