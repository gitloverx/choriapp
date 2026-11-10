const API_URL = window.location.origin + '/api';
let token = localStorage.getItem('token');

export async function apiFetch(endpoint, options = {}) {
    const headers = { 'Content-Type': 'application/json', ...options.headers };
    if (token) headers['Authorization'] = `Bearer ${token}`;
    
    const res = await fetch(`${API_URL}${endpoint}`, { ...options, headers });
    
    if (res.status === 401 || res.status === 403) {
        localStorage.clear();
        window.location.href = 'login.php';
        return;
    }

    const text = await res.text();
    try {
        const data = JSON.parse(text);
        if (!res.ok) throw new Error(data.error || 'Error en el servidor');
        return data;
    } catch (e) {
        throw new Error("El servidor devolvió un error (no es JSON)");
    }
}

export function setToken(t) { token = t; localStorage.setItem('token', t); }
export function logout() { localStorage.clear(); window.location.href = 'login.php'; }
export function formatMoney(n) { return '$' + Number(n).toLocaleString('es-CO'); }
