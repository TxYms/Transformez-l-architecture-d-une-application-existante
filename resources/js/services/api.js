let baseUrl = '';
let token = localStorage.getItem('token');

export function setBaseUrl(url) {
    baseUrl = url;
}

export function getToken() {
    return token;
}

export function setToken(value) {
    token = value;

    if (value) {
        localStorage.setItem('token', value);
    } else {
        localStorage.removeItem('token');
    }
}

async function request(method, path, body) {
    const headers = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    };

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(baseUrl + path, {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined,
    });

    const payload = await response.json().catch(() => ({ message: 'Server error.', data: null }));

    if (!response.ok) {
        throw {
            status: response.status,
            message: payload.message,
            errors: payload.data?.errors ?? {},
        };
    }

    return payload;
}

export const api = {
    register: (data) => request('POST', '/auth/register', data),
    login: (data) => request('POST', '/auth/login', data),
    logout: () => request('POST', '/auth/logout'),
    forgotPassword: (data) => request('POST', '/auth/forgot-password', data),
    resetPassword: (data) => request('POST', '/auth/reset-password', data),
    getUser: () => request('GET', '/user'),
    updateProfile: (data) => request('PATCH', '/user', data),
    updatePassword: (data) => request('PUT', '/user/password', data),
    deleteAccount: (data) => request('DELETE', '/user', data),
    getNotes: () => request('GET', '/notes'),
    createNote: (data) => request('POST', '/notes', data),
    deleteNote: (id) => request('DELETE', `/notes/${id}`),
    getTags: () => request('GET', '/tags'),
    createTag: (data) => request('POST', '/tags', data),
};
