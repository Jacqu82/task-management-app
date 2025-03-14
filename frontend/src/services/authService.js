export async function checkAuth() {
    const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;

    try {
        let response = await fetch(`${API_BASE_URL}/api/me`, {
            method: 'POST',
            credentials: 'include',
        });

        if (response.ok) {
            return await response.json();
        }

        if (response.status === 401) {
            const refreshed = await refreshToken();
            if (refreshed) {
                response = await fetch(`${API_BASE_URL}/api/me`, {
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
    const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;

    try {
        const response = await fetch(`${API_BASE_URL}/api/refresh-token`, {
            method: 'POST',
            credentials: 'include',
        });

        if (response.ok) {
            return true;
        } else {
            return false;
        }
    } catch (error) {
        return false;
    }
}

export async function logout() {
    const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;

    try {
        await fetch(`${API_BASE_URL}/api/logout`, {
            method: 'POST',
            credentials: 'include',
        });
        window.location.href = '/';
    } catch (error) {
        console.error("Błąd podczas wylogowania", error);
    }
}
