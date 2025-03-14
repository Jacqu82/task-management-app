"use client";

import Link from "next/link";
import {useEffect, useState} from "react";
import {Edit, Trash2} from "lucide-react"; // Ikony
import styles from "./tasks.module.css";

interface Task {
    id: number;
    title: string;
    description: string;
    statusName: string;
    createdAt: string;
}

export default function Tasks() {
    const [tasks, setTasks] = useState<Task[]>([]);

    useEffect(() => {
        async function fetchTasks() {
            const response = await fetch('/api/tasks', {
                method: 'GET',
                credentials: 'include',
            });
            const data = await response.json();
            setTasks(data);
        }

        fetchTasks();
    }, []);

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

                {tasks.length > 0 ? (
                    tasks.map(task => (
                        <div key={task.id} className={styles.taskRow}>
                            <span className={styles.taskTitle}>
                                <Link href={`/tasks/${task.id}/edit`}>{task.title}</Link>
                            </span>
                            <span className={styles.taskDescription}>{task.description}</span>
                            <span className={styles.taskStatus}>{task.statusName}</span>
                            <span className={styles.taskDate}>{task.createdAt}</span>
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
            </div>
        </main>
    );
}

function handleDelete(id: number) {
    if (confirm("Czy na pewno chcesz usunąć to zadanie?")) {
        fetch(`/api/tasks/${id}`, { method: "DELETE", credentials: "include" })
            .then(() => location.reload());
    }
}
