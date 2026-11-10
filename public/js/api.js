const API_URL = window.location.origin + '/api';
let token = localStorage.getItem('token');

export async function apiFetch(endpoint, options = {}) {
    const headers = { 'Content-Type': 'application/json', ...options.headers };
    if (token) headers['Authorization'] = `Bearer ${token}`;
    
    const res = await fetch(`${API_URL}${endpoint}`, { ...options, headers });
    
    // Si el servidor dice que no estamos autorizados (Token vencido)
    if (res.status === 401 || res.status === 403) {
        localStorage.clear();
        window.location.href = 'login.php';
        return;
    }

    const text = await res.text();
    let data;
    try {
        data = JSON.parse(text);
    } catch (e) {
        throw new Error("Respuesta no válida del servidor");
    }
    
    if (!res.ok) {
        throw new Error(data.error || 'Error en el servidor');
    }
    return data;
}

export function setToken(t) { token = t; localStorage.setItem('token', t); }
export function logout() { localStorage.clear(); window.location.href = 'login.php'; }
export function formatMoney(n) { return '$' + Number(n).toLocaleString('es-CO'); }
