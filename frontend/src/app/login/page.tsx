'use client';

import { useState } from "react";
import styles from "./login.module.css";

export default function Login() {
    const [formData, setFormData] = useState({
        email: "",
        password: "",
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const response = await fetch(`${API_BASE_URL}/api/login`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        });

        const responseData = await response.json();

        if (response.ok) {
            window.location.href = '/';
        } else {
            setErrorMessage(responseData.error);
        }
    };

    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Logowanie</h1>
            {errorMessage && <p className={styles.errorMessage}>{errorMessage}</p>}
            <form onSubmit={handleSubmit} className={styles.form}>
                <div className={styles.formGroup}>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Adres e-mail"
                        value={formData.email}
                        onChange={handleChange}
                        className={styles.input}
                    />
                </div>

                <div className={styles.formGroup}>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Hasło"
                        value={formData.password}
                        onChange={handleChange}
                        className={styles.input}
                    />
                </div>

                <button type="submit" className={styles.submitBtn}>Zaloguj</button>
            </form>
        </main>
    );
}
