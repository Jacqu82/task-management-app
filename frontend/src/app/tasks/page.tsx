"use client";

import {Suspense, useEffect, useState} from "react";
import Link from "next/link";
import {Edit, Trash2} from "lucide-react";
import {useRouter, useSearchParams} from "next/navigation";
import {format} from 'date-fns';
import styles from "./tasks.module.css";

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
    createdAt: string;
}

interface Meta {
    totalCount: number;
    itemsPerPage: number;
    currentPage: number;
    totalPages: number;
}

interface Links {
    self: string;
    first: string;
    last: string;
    next?: string;
    prev?: string;
}

interface ValidationError {
    status: number;
    title: string;
    detail: string;
    source: {
        pointer: string;
    };
}

function TasksContent() {
    const [tasks, setTasks] = useState<Task[]>([]);
    const [meta, setMeta] = useState<Meta | null>(null);
    const [links, setLinks] = useState<Links | null>(null);
    const [errors, setErrors] = useState<{ [key: number]: string }>({});
    const searchParams = useSearchParams();
    const router = useRouter();
    const currentPage = Number(searchParams.get("page")) || 1;
    type TaskStatusDto = {
        value: string;
        label: string;
    };
    const [statuses, setStatuses] = useState<TaskStatusDto[]>([]);

    const fetchStatuses = async () => {
        const response = await fetch('/api/task-statuses', {
            credentials: 'include',
        });
        const data = await response.json();
        setStatuses(data);
    };

    const fetchTasks = async (page: number) => {
        const response = await fetch(`/api/tasks?page=${page}`);
        const data = await response.json();
        setTasks(data.data);
        setMeta(data.meta);
        setLinks(data.links);
    };

    const handleStatusChange = async (id: number, status: Task['status']) => {
        try {
            const response = await fetch(`/api/tasks/${id}/status`, {
                method: "PATCH",
                headers: { "Content-Type": "application/json" },
                credentials: "include",
                body: JSON.stringify({ status }),
            });

            if (!response.ok) {
                const data = await response.json();
                const statusError = (data.errors as ValidationError[] | undefined)?.find(
                    e => e.source?.pointer === "/data/attributes/status"
                );
                setErrors(prev => ({ ...prev, [id]: statusError?.detail || "Nieznany błąd" }));
                return;
            }

            setTasks(prev =>
                prev.map(task => task.id === id ? { ...task, status } : task)
            );
            setErrors(prev => {
                const copy = { ...prev };
                delete copy[id];
                return copy;
            });

        } catch (err) {
            console.error(err);
        }
    };


    useEffect(() => {
        fetchTasks(currentPage);
        fetchStatuses();
    }, [currentPage]);

    const changePage = (page: number) => {
        router.push(`?page=${page}`, { scroll: false });
    };

    return (
        <main className={styles.main}>
            <h1 className={styles.title}>Lista Zadań</h1>
            <Link href="/tasks/new" className={styles.addButton}>+ Dodaj zadanie</Link>

            <div className={styles.taskList}>
                <div className={styles.taskHeader}>
                    <span>Tytuł</span>
                    <span>Opis</span>
                    <span>Status</span>
                    <span>Data utworzenia</span>
                    <span>Akcje</span>
                </div>

                {Array.isArray(tasks) && tasks.length > 0 ? (
                    tasks.map(task => (
                        <div key={task.id} className={styles.taskRow}>
                            <span className={styles.taskTitle}>
                                <Link href={`/tasks/${task.id}/edit`}>{task.title}</Link>
                            </span>
                            <span className={styles.taskDescription}>{task.description}</span>
                            <span className={styles.taskStatus}>
                                <select
                                    value={task.status}
                                    onChange={e => handleStatusChange(task.id, e.target.value)}
                                    className={styles.select}
                                >
                                    {statuses.map(status => (
                                        <option key={status.value} value={status.value}>
                                            {status.label}
                                        </option>
                                    ))}
                                </select>
                                {errors[task.id] && (
                                    <p className={styles.error}>{errors[task.id]}</p>
                                )}
                            </span>
                            <span className={styles.taskDate}>{format(new Date(task.createdAt), 'dd.MM.yyyy HH:mm')}</span>
                            <div className={styles.taskActions}>
                                <Link href={`/tasks/${task.id}/edit`} className={styles.iconButton}>
                                    <Edit size={20} />
                                </Link>
                                <button className={styles.iconButton} onClick={() => handleDelete(task.id)}>
                                    <Trash2 size={20} />
                                </button>
                            </div>
                        </div>
                    ))
                ) : (
                    <p>Brak zadań do wyświetlenia.</p>
                )}
                {meta && links && (
                    <div className={styles.pagination}>
                        <button
                            disabled={!links?.prev}
                            onClick={() => changePage(currentPage - 1)}
                            className={`${styles.pageBtn} ${!links?.prev ? styles.disabled : ""}`}>
                            Poprzednia
                        </button>
                        <span className={styles.pageInfo}>
                            Strona {meta?.currentPage} z {meta?.totalPages}
                        </span>
                        <button
                            disabled={!links?.next}
                            onClick={() => changePage(currentPage + 1)}
                            className={`${styles.pageBtn} ${!links?.next ? styles.disabled : ""}`}>
                            Następna
                        </button>
                    </div>
                )}
            </div>
        </main>
    );
}

export default function Tasks() {
    return (
        <Suspense fallback={<div>Loading tasks...</div>}>
            <TasksContent />
        </Suspense>
    );
}

function handleDelete(id: number) {
    if (confirm("Czy na pewno chcesz usunąć to zadanie?")) {
        fetch(`/api/tasks/${id}`, { method: "DELETE", credentials: "include" })
            .then(() => location.reload());
    }
}
