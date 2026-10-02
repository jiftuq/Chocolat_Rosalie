// Échanges asynchrones avec le backend (FE-60) : JSON, jeton CSRF, erreurs typées.

const csrfMeta = document.querySelector('meta[name="csrf-token"]');

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.status = status;
        this.errors = errors;
    }
}

export function setCsrfToken(token) {
    if (token) {
        csrfMeta.content = token;
    }
}

/**
 * Appelle api.php?route=... et renvoie la réponse { success, message, data }.
 * Lève une ApiError (message lisible + erreurs par champ) en cas d'échec.
 */
export async function api(route, { method = 'GET', params = {}, body } = {}) {
    const url = new URL('api.php', window.location.href);
    url.searchParams.set('route', route);
    Object.entries(params).forEach(([key, value]) => url.searchParams.set(key, value));

    const options = { method, headers: { Accept: 'application/json' }, credentials: 'same-origin' };
    if (method !== 'GET') {
        options.headers['X-CSRF-Token'] = csrfMeta.content;
    }
    if (body !== undefined) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    let response;
    let payload;
    try {
        response = await fetch(url, options);
        payload = await response.json();
    } catch {
        throw new ApiError('Impossible de joindre le serveur. Vérifiez votre connexion puis réessayez.', 0);
    }

    if (!response.ok || !payload.success) {
        throw new ApiError(payload.message || 'Une erreur est survenue.', response.status, payload.errors || {});
    }
    if (payload.data && payload.data.csrfToken) {
        setCsrfToken(payload.data.csrfToken);
    }
    return payload;
}
