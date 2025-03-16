'use client';

import {useState} from "react";
import styles from "./register.module.css";

export default function Register() {
    const [formData, setFormData] = useState({
        email: "",
        password: "",
    });
    const [validationErrors, setErrors] = useState<{ email?: string; password?: string }>({});
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const getCsrfToken = async (context: string) => {
        const response = await fetch(`/api/csrf-token/${context}`, {
            method: "POST",
        });
        const csrfToken = response.headers.get("CSRF-TOKEN");

        if (!csrfToken) {
            throw new Error("CSRF token not found");
        }

        return csrfToken;
    };
    interface ErrorSource {
        pointer: string;
    }

    interface ErrorObject {
        source: ErrorSource;
        detail: string;
    }

    const parseErrors = (errors: ErrorObject[]): { [key: string]: string } => {
        return errors.reduce((acc: { [key: string]: string }, error) => {
            if (error.source && error.source.pointer) {
                const field = error.source.pointer.replace("/data/attributes/", "");
                acc[field] = error.detail;
            }
            return acc;
        }, {});
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        try {
            const response = await fetch(`/api/users`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "CSRF-TOKEN": await getCsrfToken("register"),
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
                setErrors(parseErrors(responseData.errors || []));
                setSuccessMessage(null);
                setErrorMessage(responseData.error);
            }
        } catch (error) {
            setErrorMessage("Błąd serwera. Spróbuj ponownie później.");
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
