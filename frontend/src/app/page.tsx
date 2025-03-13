import Link from "next/link";
import styles from "./page.module.css";

export default function Home() {
    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Task Management App</h1>
            <p className={styles.description}>Zarządzaj swoimi zadaniami efektywnie</p>
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
        </main>
    );
}
