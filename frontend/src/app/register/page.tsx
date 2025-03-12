'use client';

import { useState } from "react";
import styles from "./register.module.css";

export default function Register() {
    const [formData, setFormData] = useState({
        email: "",
        password: "",
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const [validationErrors, setErrors] = useState<{ email?: string; password?: string }>({});
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;

    const getCsrfToken = async () => {
        const response = await fetch(`${API_BASE_URL}/api/csrf-token`);
        const data = await response.json();
        return data.csrf_token;
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        const response = await fetch(`${API_BASE_URL}/api/users`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": await getCsrfToken(),
            },
            body: JSON.stringify(formData),
        });

        const responseData = await response.json();

        if (response.ok) {
            setFormData({ email: "", password: "" });
            setErrors({});
            setSuccessMessage(responseData.message);
            setErrorMessage(null);
        } else {
            setErrors(responseData.validation_errors || {});
            setSuccessMessage(null);
            setErrorMessage(responseData.error);
        }
    };

    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Rejestracja</h1>
            {successMessage && <p className={styles.successMessage}>{successMessage}</p>}
            {errorMessage && <p className={styles.errorMessage}>{errorMessage}</p>}
            <form onSubmit={handleSubmit} className={styles.form}>
                <div className={styles.formGroup}>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder={`Adres e-mail`}
                        value={formData.email}
                        onChange={handleChange}
                        className={styles.input}
                    />
                    {validationErrors.email && <p className={styles.error}>{validationErrors.email}</p>}
                </div>

                <div className={styles.formGroup}>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder={`Hasło`}
                        value={formData.password}
                        onChange={handleChange}
                        className={styles.input}
                    />
                    {validationErrors.password && <p className={styles.error}>{validationErrors.password}</p>}
                </div>

                <button type="submit" className={styles.submitBtn}>Zarejestruj</button>
            </form>
        </main>
    );
}
