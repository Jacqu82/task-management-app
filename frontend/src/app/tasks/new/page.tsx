'use client';

import {useState} from "react";
import styles from "./new-task.module.css";
import Link from "next/link";
import {useRouter} from "next/navigation";

export default function NewTask() {
    const [formData, setFormData] = useState({
        title: "",
        description: "",
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const [validationErrors, setErrors] = useState<{ title?: string }>({});
    const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;
    const router = useRouter();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        const response = await fetch(`${API_BASE_URL}/api/tasks`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        });

        const responseData = await response.json();

        if (response.ok) {
            setFormData({ title: "", description: "" });
            setErrors({});
            router.push("/tasks");
        } else {
            setErrors(responseData.validation_errors || {});
        }
    };

    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Dodaj nowe zadanie</h1>
            <Link href="/tasks" className={styles.navLink}>
                Powrót do zadań
            </Link>
            <form onSubmit={handleSubmit} className={styles.form}>
                <div className={styles.formGroup}>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="Tytuł"
                        value={formData.title}
                        onChange={handleChange}
                        className={styles.input}
                    />
                    {validationErrors.title && <p className={styles.error}>{validationErrors.title}</p>}
                </div>
                <div className={styles.formGroup}>
                    <textarea
                        id="description"
                        name="description"
                        placeholder="Opis"
                        value={formData.description}
                        onChange={handleChange}
                        className={styles.textarea}
                    />
                </div>
                <button type="submit" className={styles.submitBtn}>Zapisz</button>
            </form>
        </main>
    );
}
