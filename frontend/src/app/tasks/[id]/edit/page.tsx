"use client";

import {useEffect, useState} from "react";
import {useParams, useRouter} from "next/navigation";
import Link from "next/link";
import styles from "../../new/new-task.module.css";

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
}

export default function EditTask() {
    const { id } = useParams();
    const [task, setTask] = useState<Task | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [validationErrors, setErrors] = useState<{ title?: string }>({});
    const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;
    const router = useRouter();

    useEffect(() => {
        async function fetchTask() {
            try {
                const response = await fetch(`/api/tasks/${id}`, {
                    method: "GET",
                    credentials: "include",
                });

                if (!response.ok) throw new Error("Nie znaleziono zadania");

                const data = await response.json();
                setTask(data);
            } catch (err) {
                setError("Nie udało się załadować zadania");
            } finally {
                setLoading(false);
            }
        }

        fetchTask();
    }, [id]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        if (task) {
            setTask({ ...task, [e.target.name]: e.target.value });
        }
    };

    const parseErrors = (errors: any[]) => {
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
            const response = await fetch(`${API_BASE_URL}/api/tasks/${id}`, {
                method: "PUT",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(task),
            });

            const responseData = await response.json();

            if (response.ok) {
                setErrors({});
                router.push("/tasks");
            } else {
                setErrors(parseErrors(responseData.errors || []));
            }
        } catch (error) {
            console.error(error);
        }
    };

    if (loading) return <p>Ładowanie...</p>;
    if (error) return <p>{error}</p>;
    if (!task) return <p>Zadanie nie istnieje</p>;

    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Edycja zadania</h1>
            <Link href="/tasks" className={styles.navLink}>
                Powrót do zadań
            </Link>
            <form onSubmit={handleSubmit} className={styles.form}>
                <div className={styles.formGroup}>
                    <label className={styles.label} htmlFor="title">Tytuł</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value={task.title}
                        onChange={handleChange}
                        className={styles.input}
                    />
                    {validationErrors.title && <p className={styles.error}>{validationErrors.title}</p>}
                </div>
                <div className={styles.formGroup}>
                    <label className={styles.label} htmlFor="description">Opis</label>
                    <textarea
                        id="description"
                        name="description"
                        value={task.description}
                        onChange={handleChange}
                        className={styles.textarea}
                    />
                </div>
                <div className={styles.formGroup}>
                    <label className={styles.label} htmlFor="status">Status</label>
                    <select
                        id="status"
                        name="status"
                        value={task.status}
                        onChange={handleChange}
                        className={styles.select}
                    >
                        <option value="pending">Oczekujące</option>
                        <option value="in_progress">W trakcie</option>
                        <option value="completed">Zakończone</option>
                    </select>
                </div>
                <button type="submit" className={styles.submitBtn}>Zapisz zmiany</button>
            </form>
        </main>
    );
}
