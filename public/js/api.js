const API_URL = window.location.origin + '/api';
let token = localStorage.getItem('token');

export async function apiFetch(endpoint, options = {}) {
    const headers = { 'Content-Type': 'application/json', ...options.headers };
    if (token) headers['Authorization'] = `Bearer ${token}`;
    
    const res = await fetch(`${API_URL}${endpoint}`, { ...options, headers });
    const text = await res.text();
    
    try {
        const data = JSON.parse(text);
        if (!res.ok) throw new Error(data.error || data.detalle_real || 'Error en el servidor');
        return data;
    } catch (e) {
        console.error("Error del servidor (no es JSON):", text);
        throw new Error("Error de PHP en el servidor. Revisa Termux.");
    }
}

export function setToken(t) { token = t; localStorage.setItem('token', t); }
export function logout() { localStorage.clear(); window.location.href = 'index.html'; }
export function formatMoney(n) { return '$' + Number(n).toLocaleString('es-CO'); }
