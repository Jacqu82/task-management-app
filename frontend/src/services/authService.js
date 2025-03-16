export async function checkAuth() {
    try {
        let response = await fetch(`/api/me`, {
            method: 'POST',
            credentials: 'include',
        });

        if (response.ok) {
            return await response.json();
        }

        if (response.status === 401) {
            const refreshed = await refreshToken();
            if (refreshed) {
                response = await fetch(`/api/me`, {
                    method: 'POST',
                    credentials: 'include',
                });

                if (response.ok) {
                    return await response.json();
                }
            }
        }
    } catch (error) {
        return null;
    }

    return null;
}

export async function refreshToken() {
    try {
        const response = await fetch(`/api/refresh-token`, {
            method: 'POST',
            credentials: 'include',
        });

        return response.ok;
    } catch (error) {
        return false;
    }
}

export async function logout() {
    try {
        await fetch(`/api/logout`, {
            method: 'POST',
            credentials: 'include',
        });
        window.location.href = '/';
    } catch (error) {
        console.error("Błąd podczas wylogowania", error);
    }
}
