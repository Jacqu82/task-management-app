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

interface TaskStatusDto {
    value: string;
    label: string;
}

export default function EditTask() {
    const { id } = useParams();
    const [task, setTask] = useState<Task | null>(null);
    const [loading, setLoading] = useState(true);
    const [error] = useState("");
    const [validationErrors, setErrors] = useState<{ title?: string, description?: string, status?: string }>({});
    const router = useRouter();
    const [statuses, setStatuses] = useState<TaskStatusDto[]>([]);

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
            } catch (error: unknown) {
                if (error instanceof Error) {
                    console.error("Błąd:", error.message);
                }
            } finally {
                setLoading(false);
            }
        }

        async function fetchStatuses() {
            const response = await fetch('/api/task-statuses', {
                credentials: 'include',
            });
            const data = await response.json();
            setStatuses(data);
        }

        fetchTask();
        fetchStatuses();
    }, [id]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        if (task) {
            setTask({ ...task, [e.target.name]: e.target.value });
        }
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
            const response = await fetch(`/api/tasks/${id}`, {
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
                    {validationErrors.description && <p className={styles.error}>{validationErrors.description}</p>}
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
                        {statuses.map(status => (
                            <option key={status.value} value={status.value}>
                                {status.label}
                            </option>
                        ))}
                    </select>
                    {validationErrors.status && <p className={styles.error}>{validationErrors.status}</p>}
                </div>
                <button type="submit" className={styles.submitBtn}>Zapisz zmiany</button>
            </form>
        </main>
    );
}
