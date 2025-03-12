'use client';

import { useEffect, useState } from "react";
import Cookies from "js-cookie";
import Link from "next/link";
import styles from "./page.module.css";

export default function Home() {
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    useEffect(() => {
        const token = Cookies.get("jwt_token");
        setIsLoggedIn(!!token);
    }, []);
    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Task Management App</h1>
            <p className={styles.description}>Zarządzaj swoimi zadaniami efektywnie</p>

            {!isLoggedIn && (
                <>
                    <nav>
                        <ul className={styles.navList}>
                            <li className={styles.navItem}>
                                <Link href="/login" className={styles.navLink}>Logowanie</Link>
                            </li>
                            <li className={styles.navItem}>
                                <Link href="/register" className={styles.navLink}>Rejestracja</Link>
                            </li>
                        </ul>
                    </nav>
                </>
            )}
        </main>
    );
}
